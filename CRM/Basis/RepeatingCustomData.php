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
    // als mediwe_werkgebied: haal eventueel sleutel op voor update
    if ($customGroupName == 'mediwe_werkgebied') {
      $apiKey = $repeatingCustomData->getWerkgebiedUpdateKey($entityId, $data);
    }
    $customFields = $repeatingCustomData->getSaveCustomFieldIds();
    foreach ($data as $customFieldName => $dataValues) {
      foreach ($dataValues as $key => $value) {
        if ($apiKey) {
          $apiParamsSets[$key][$customFields[$customFieldName] . ':' . $apiKey] = $value;
        }
        else {
          $apiParamsSets[$key][$customFields[$customFieldName]] = $value;
        }
      }
    }
    foreach ($apiParamsSets as $apiParams) {
      $apiParams['entity_id'] = $entityId;
      $apiParams['entity_table'] = $repeatingCustomData->_entityTable;
      try {
        civicrm_api3('CustomValue', 'create', $apiParams);
      }
      catch (CiviCRM_API3_Exception $ex) {
        CRM_Core_Error::debug_log_message(ts('Could not add repeating custom values in custom group with name ' . $customGroupName . ' in ' . __METHOD__));
      }
    }
  }
  private function getWerkgebiedUpdateKey {
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
      $result[$customField['name']] = 'custom_'.$customFieldId;
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

}
