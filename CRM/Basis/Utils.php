<?php
/**
 * Class with extension specific util functions
 *
 * @author Erik Hommel (CiviCooP) <erik.hommel@civicoop.org>
 * @date 31 May 2017
 * @license AGPL-3.0
 */

class CRM_Basis_Utils {

  /**
   * Public function to generate label from name
   *
   * @param $name
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
   * @param array $params
   * @throws Exception when unable to execute query
   * @access public
   * @static
   */
  public static function addCustomData($params) {
    $queryData = new CRM_Generic_ConfigItems_CustomDataQuery($params);
    $query = $queryData->getQuery();
    $queryParams = $queryData->getQueryParams();
    if (!empty($query)) {
      try {
        CRM_Core_DAO::executeQuery($query, $queryParams);
      } catch (Exception $ex) {
        throw new Exception(ts('Unable to add custom data in '.__METHOD__.', error message :')
          . $ex->getMessage());
      }
    }
  }

  /**
   * Method to retrieve the group id with group name
   *
   * @param $groupName
   * @return array|bool
   * @static
   */
  public static function getGroupIdWithName($groupName) {
    try {
      return civicrm_api3('Group', 'Getvalue', array('name' => (string) $groupName, 'return' => 'id'));
    } catch (CiviCRM_API3_Exception $ex) {
      return FALSE;
    }
  }

  /**
   * Method om waarden uit CustomValue repeating groups om te bouwen van alle waarden per custom veld naar
   * alle custom velden per occurrence
   *
   * @param $dataValues
   * @return array
   */
  public static function rearrangeRepeatingData($dataValues) {
    $result = array();
    // ignore all non-data elements
    $ignores = array('entity_table', 'entity_id', 'id', 'latest');
    foreach ($dataValues as $customFieldId => $customData) {
      foreach ($customData as $key => $value) {
        if (!in_array($key, $ignores)) {
          $result[$key][$customFieldId] = $value;
        }
      }
    }
    return $result;
  }

}