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


}
