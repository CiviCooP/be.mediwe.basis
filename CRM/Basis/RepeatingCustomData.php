<?php

/**
 * Class to process repeating custom data for contact
 *
 * @author Erik Hommel (CiviCooP) <erik.hommel@civicoop.org>
 * @date 19 Feb 2018
 * @license AGPL-3.0
 */
class CRM_Basis_RepeatingCustomData {

  private $_customGroupName = array();
  private $_entityId = NULL;
  private $_entityTable = NULL;

  public function __construct($customGroupName, $entityId) {
    $this->_customGroupName = $customGroupName;
    $this->_entityId = $entityId;
    $this->_entityTable = 'civicrm_contact';
  }

  /**
   * Method om repeating custom data op te slaan
   *
   * @param string $customGroupName
   * @param int $entityId
   * @param array $data
   * @return bool
   */
  public static function save($customGroupName, $entityId, $data) {
    $apiParamsSets = array();
    if (empty($customGroupName) || empty($data) || empty($entityId)) {
      return FALSE;
    }
    $repeatingCustomData = new CRM_Basis_RepeatingCustomData($customGroupName, $entityId);
    $customFields = $repeatingCustomData->getSaveCustomFieldIds();
    foreach ($data as $customFieldName => $dataValues) {
      foreach ($dataValues as $key => $value) {
        if ($value) {
          $apiParamsSets[$key][$customFields[$customFieldName]] = $value;
        }
      }
    }

    foreach ($apiParamsSets as $apiParams) {
      // add empty fields if required
      foreach ($customFields as $customFieldName => $customFieldId) {
        if (!isset($apiParams[$customFieldId])) {
          $apiParams[$customFieldId] = NULL;
        }
      }
      $apiParams['entity_id'] = $entityId;
      $apiParams['entity_table'] = $repeatingCustomData->_entityTable;
      // check of er sprake is van een update van werkgebied
      if ($customGroupName == 'mediwe_werkgebied') {
        $repeatingCustomData->checkWerkgebiedUpdate($apiParams);
      }
      try {
        civicrm_api3('CustomValue', 'create', $apiParams);
      }
      catch (CiviCRM_API3_Exception $ex) {
        CRM_Core_Error::debug_log_message(ts('Could not add repeating custom values in custom group with name ' . $customGroupName . ' in ' . __METHOD__));
      }
    }
  }

  /**
   * Method om te kijken of er al een werkgebied is voor postcode/gemeente combinatie en als dat zo is de id van de regel terug te geven
   *
   * @param array $apiParams
   * @return bool/int
   */
  private function checkWerkgebiedUpdate(&$apiParams) {
    // alleen als postcode en gemeente
    $postCodeCustomField = CRM_Basis_Config::singleton()->getPostcodeCustomField();
    $postCodeCustomId = 'custom_' . $postCodeCustomField['id'];
    $gemeenteCustomField = CRM_Basis_Config::singleton()->getGemeenteCustomField();
    $gemeenteCustomId = 'custom_' . $gemeenteCustomField['id'];
    if (isset($apiParams[$postCodeCustomId]) && isset($apiParams[$gemeenteCustomId])) {
      $whereClauses = array();
      $index = 1;
      $query = "SELECT id FROM " . CRM_Basis_Config::singleton()->getWerkgebiedCustomGroup('table_name') .
        " WHERE entity_id = %1 AND ";
      $queryParams = array(
        1 => array($this->_entityId, 'Integer'),
      );
      if (!empty($apiParams[$postCodeCustomId])) {
        $index++;
        $whereClauses[$index] = $postCodeCustomField['column_name'] . ' = %' . $index;
        $queryParams[$index] = array($apiParams[$postCodeCustomId], 'String');
      }
      else {
        $whereClauses[$index] = $postCodeCustomField['column_name'] . ' IS NULL';
      }
      if (!empty($apiParams[$gemeenteCustomId])) {
        $index++;
        $whereClauses[$index] = $gemeenteCustomField['column_name'] . ' = %' . $index;
        $queryParams[$index] = array($apiParams[$gemeenteCustomId], 'String');
      }
      else {
        $whereClauses[$index] = $gemeenteCustomField['column_name'] . ' IS NULL';
      }
      if (!empty($whereClauses)) {
        $prioriteitCustomId = 'custom_' . CRM_Basis_Config::singleton()->getPrioriteitCustomField('id');
        $query .= implode(' AND ', $whereClauses);
        $werkgebiedId = CRM_Core_DAO::singleValueQuery($query, $queryParams);
        if ($werkgebiedId) {
          $apiParams = array(
            'entity_id' => $this->_entityId,
            'entity_table' => $this->_entityTable,
            $postCodeCustomId . ':' . $werkgebiedId => $apiParams[$postCodeCustomId],
            $gemeenteCustomId . ':' . $werkgebiedId => $apiParams[$gemeenteCustomId],
            $prioriteitCustomId . ':' . $werkgebiedId => $apiParams[$prioriteitCustomId],
          );
        }
      }
    }
    return FALSE;
  }

  /**
   * Method om custom velden voor de custom groep op te halen en terug te geven in patroon naam => id
   *
   * @return array
   */
  private function getSaveCustomFieldIds() {
    $result = array();
    $customFields = CRM_Basis_Config::singleton()->getCustomFieldByCustomGroupName($this->_customGroupName);
    foreach ($customFields as $customFieldId => $customField) {
      $result[$customField['name']] = 'custom_' . $customFieldId;
    }
    return $result;
  }

  /**
   * Method om repeating custom data values op te halen
   *
   * @param string $customGroupName
   * @param int $entityId
   * @return array|bool
   */
  public static function get($customGroupName, $entityId) {
    if (empty($customGroupName) || empty($entityId)) {
      return FALSE;
    }
    $repeatingCustomData = new CRM_Basis_RepeatingCustomData($customGroupName, $entityId);
    $getParams = $repeatingCustomData->setGetParams();
    try {
      $result = civicrm_api3('CustomValue', 'get', $getParams);
      return $repeatingCustomData->rearrangeRepeatingData($result['values']);
    }
    catch (CiviCRM_API3_Exception $ex) {
      return FALSE;
    }
  }

  /**
   * Method om custom velden aan parameter array voor get samen te stellen
   * @return array
   */
  private function setGetParams() {
    $result = array(
      'options' => array('limit' => 0),
      'entity_table' => $this->_entityTable,
      'entity_id' => $this->_entityId,
    );
    // ophalen custom velden in custom groep zodat die als terug te geven velden in parameter array gestopt kunnen worden
    $customFieldIds = $this->getCustomFieldIds();
    foreach ($customFieldIds as $customFieldId) {
      $result['return.custom_' . $customFieldId] = 1;
    }
    return $result;
  }

  /**
   * Method om custom field ids op te halen
   *
   * @return array
   */
  private function getCustomFieldIds() {
    $result = array();
    $query = 'SELECT cf.id AS custom_field_id FROM civicrm_custom_group cg JOIN civicrm_custom_field cf ON cg.id = cf.custom_group_id 
      WHERE cg.name = %1';
    $dao = CRM_Core_DAO::executeQuery($query, array(
      1 => array($this->_customGroupName, 'String'),
    ));
    while ($dao->fetch()) {
      $result[] = $dao->custom_field_id;
    }
    return $result;
  }

  /**
   * Method om waarden uit CustomValue repeating groups om te bouwen van alle waarden per custom veld naar
   * alle custom velden per occurrence
   *
   * @param  $dataValues
   * @return array|bool
   */
  public function rearrangeRepeatingData($dataValues) {
    $result = array();
    // ignore all non-data elements
    $ignores = array('entity_table', 'entity_id', 'id', 'latest');
    foreach ($dataValues as $customFieldId => $customData) {
      foreach ($customData as $key => $value) {
        if (!in_array($key, $ignores)) {
          // get custom field name
          try {
            $customFieldName = (string) civicrm_api3('CustomField', 'getvalue', array(
              'return' => 'name',
              'id' => $customFieldId,
            ));
            $result[$customFieldName] = $value;
          }
          catch (CiviCRM_API3_Exception $ex) {
            CRM_Core_Error::debug_log_message(ts('Could not find custom field name with id ') . $customFieldId . ' in ' . __METHOD__);
            return FALSE;
          }
        }
      }
    }
    return $result;
  }

  /**
   * Method to save repeating custom field data
   *
   * @param object $customFields (dao)
   * @param int $entityId
   * @param array $array (id, fieldname/value)
   * @param array $arrayKeys array of key values to be used for search existing values
   *
   * @return int
   */
  public static function setRepeatingData($customFields, $entityId, $array, $arrayKeys) {
    $rv = FALSE;
    $newLine = -1;
    $params = [
      'sequential' => 1,
      'entity_id' => $entityId,
    ];
    $count = 0;
    foreach ($array as $data) {
      // get existing ids
      $getParams = [
        'sequential' => 1,
        'entity_id' => $entityId,
      ];
      foreach ($arrayKeys as $key) {
        if (isset($data[$key])) {
          $getParams[$key] = $data[$key];
        }
      }
      $existingData = CRM_Basis_RepeatingCustomData::getRepeatingData($customFields, $getParams);
      foreach ($existingData as $existing) {
        $array[$count]['id'] = $existing['id'];
      }
      if (!isset($array[$count]['id'])) {
        $array[$count]['id'] = $newLine;
        $newLine--;
      }
      foreach ($customFields as $field) {
        if (isset($data[$field['name']])) {
          $key = "custom_" . $field['id'] . ":" . $array[$count]['id'];
          if ($field['data_type'] == 'Date') {
            if (CRM_Basis_Utils::apiDate($data[$field['name']]) != "") {
              $params[$key] = CRM_Basis_Utils::apiDate($data[$field['name']]);
            }
          }
          else {
            $params[$key] = $data[$field['name']];
          }
        }
      }
      $count++;
    }
    if (count($params) > 2) {
      $rv = civicrm_api3('CustomValue', 'create', $params);
    }
    return $rv;
  }

  /**
   * @param $customFields
   * @param $params
   *
   * @return array
   * @throws CiviCRM_API3_Exception
   */
  public static function getRepeatingData($customFields, $params) {
    $myArray = [];
    foreach ($customFields as $field) {
      $key = 'return.custom_' . $field['id'];
      $params[$key] = "1";
    }
    $values = civicrm_api3('CustomValue', 'get', $params)['values'];
    foreach ($customFields as $field) {
      foreach ($values as $value) {
        if ($value['id'] == $field['id']) {
          foreach ($value as $key => $Valuevalue) {
            if (is_numeric($key)) {
              $myArray[$key]['id'] = $key;
              $myArray[$key][$field['name']] = $Valuevalue;
            }
          }
        }
      }
    }
    return $myArray;
  }

}
