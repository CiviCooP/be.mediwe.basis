<?php
/**
 * Class with extension specific util functions
 *
 * @author  Erik Hommel (CiviCooP) <erik.hommel@civicoop.org>
 * @author  Klaas Eikelboom (CiviCooP) <klaas.eikelboom@civicoop.org>
 * @author  Christophe Deman <christophe.deman@mediwe.be>
 * @date    31 May 2017
 * @license AGPL-3.0
 */

class CRM_Basis_Utils {

  /**
   * Public function to generate label from name
   *
   * @param  $name
   * @return string
   * @access public
   * @static
   */
  public static function buildLabelFromName($name) {
    $nameParts = explode('_', strtolower($name));
    foreach ($nameParts as $key => $value) {
      $nameParts[$key] = ucfirst($value);
    }
    return implode(' ', $nameParts);
  }

  /**
   * Generic method to add custom data using CRM_Core_DAO::executeQuery
   *
   * @param  array $params
   * @throws Exception when unable to execute query
   * @access public
   * @static
   */
  public static function addCustomData($params) {
    $queryData = new CRM_Basis_ConfigItems_CustomDataQuery($params);
    $query = $queryData->getQuery();
    $queryParams = $queryData->getQueryParams();
    if (!empty($query)) {
      try {
        CRM_Core_DAO::executeQuery($query, $queryParams);
      }
      catch (Exception $ex) {
        throw new Exception(ts('Unable to add custom data in ' . __METHOD__ . ', error message :') . $ex->getMessage());
      }
    }
  }

  /**
   * Method to retrieve the group id with group name
   *
   * @param  $groupName
   * @return array|bool
   * @static
   */
  public static function getGroupIdWithName($groupName) {
    try {
      return civicrm_api3('Group', 'Getvalue', array('name' => (string) $groupName, 'return' => 'id'));
    }
    catch (CiviCRM_API3_Exception $ex) {
      return FALSE;
    }
  }

  /**
   * Method om preferred communication labels in een string te plaatsen
   *
   * @param  $prefCommMethods
   * @return string
   */
  public static function getPreferredCommunicationLabels($prefCommMethods) {
    $result = array();
    foreach ($prefCommMethods as $prefCommMethod) {
      try {
        $result[] = civicrm_api3('OptionValue', 'getvalue', array(
          'option_group_id' => 'preferred_communication_method',
          'value' => $prefCommMethod,
          'return' => 'label',
        ));
      }
      catch (CiviCRM_API3_Exception $ex) {
        CRM_Core_Error::debug_log_message('Not able to retrieve preferred_communication_method with value ' .
          $prefCommMethod . 'in ' . __METHOD__  . ' (extension be.mediwe.basis)');
      }
      return implode(', ', $result);
    }
  }

  /**
   * Method om dao in array te stoppen en de 'overbodige' data er uit te slopen
   *
   * @param  $dao
   * @return array
   */
  public static function moveDaoToArray($dao) {
    $ignores = array('N', 'id', 'entity_id');
    $columns = get_object_vars($dao);
    // first remove all columns starting with _
    foreach ($columns as $key => $value) {
      if (substr($key, 0, 1) == '_') {
        unset($columns[$key]);
      }
      if (in_array($key, $ignores)) {
        unset($columns[$key]);
      }
    }
    return $columns;
  }

  /**
   * Method om select en from voor custom group samen te stellen
   *
   * @param  $customGroupArray
   * @return string
   */
  public static function createCustomDataQuery($customGroupArray) {
    if (!$customGroupArray['custom_fields']) {
      $select = 'SELECT *';
    }
    else {
      $columns = array();
      foreach ($customGroupArray['custom_fields'] as $customFieldId => $customField) {
        $columns[] = $customField['column_name'] . ' AS ' . $customField['name'];
      }
      $select = 'SELECT ' . implode(", ", $columns);
    }
    $result = $select . ' FROM ' . $customGroupArray['table_name'];
    return $result;
  }

  /**
   * Method to select a list of email templates (purpose use it in a settings form
   *
   * @return array
   */
  public static function messageTemplates() {
    $result = array();
    $dao = CRM_Core_DAO::executeQuery("SELECT id, msg_title FROM civicrm_msg_template WHERE workflow_id IS NULL");
    while ($dao->fetch()) {
      $result[$dao->id] = $dao->msg_title;
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
   * @return int
   */
  public static function setRepeatingData($customFields, $entityId, $array, $arrayKeys) {
    $rv = FALSE;
    $newLine = -1;
    $params = array(
      'sequential' => 1,
      'entity_id' => $entityId,
    );
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
      $existingData = CRM_Basis_Utils::getRepeatingData($customFields, $getParams);
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
   * @return array
   * @throws CiviCRM_API3_Exception
   */
  public static function getRepeatingData($customFields, $params) {
    $myArray = array();
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

  /**
   * Method om enkelvoudige custom velden toe voegen onder de functionele
   * naam. (Dit voorkomt dat custom_ gebruikt moet worden
   *
   * @param  $customGroup
   * @param  $entityId
   *
   * @return array
   */
  public static function addSingleDaoData($customGroup, $entityId) {
    $result = [];
    $tableName = $customGroup['table_name'];
    if (!empty($tableName)) {
      $customFields = $customGroup['custom_fields'];
      $sql = 'SELECT * FROM ' . $tableName . ' WHERE entity_id = %1';
      $dao = CRM_Core_DAO::executeQuery($sql, [
        1 => [$entityId, 'Integer'],
      ]);
      if ($dao->fetch()) {
        $data = CRM_Basis_Utils::moveDaoToArray($dao);
      }
      foreach ($customFields as $customFieldId => $customField) {
        if (isset($data[$customField['column_name']])) {
          $result[$customField['name']] = $data[$customField['column_name']];
        }
        else {
          $result[$customField['name']] = NULL;
        }
      }
    }
    return $result;
  }

}
