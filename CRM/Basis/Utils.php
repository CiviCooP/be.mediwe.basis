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
   * Method to select a list of  location types for adress email of phone
   *
   * @return array
   */
  public static function locationTypes() {
    $result = array();
    $dao = CRM_Core_DAO::executeQuery("select id,display_name from civicrm_location_type where is_active=1");
    while ($dao->fetch()) {
      $result[$dao->id] = $dao->display_name;
    }
    return $result;
  }

  /**
   * Method om land voor contact op te halen (country_id primaire adres)
   *
   * @param $contactId
   * @return array|bool
   */
  public static function getLandIdContact($contactId) {
    $params = array(
      'contact_id' => $contactId,
      'is_primary' => 1,
      'return' => 'country_id',
    );
    try {
      return civicrm_api3('Address', 'getvalue', $params);
    }
    catch (CiviCRM_API3_Exception $ex) {
      return NULL;
    }
  }

}
