<?php

/**
 * Helper class for custom fields
 *
 * @author Erik Hommel (CiviCooP) <erik.hommel@civicoop.org>
 * @date 21 Feb 2018
 * @license AGPL-3.0
 */

class CRM_Basis_SingleCustomData {

  /**
   * Method to strip the obsolete 'custom_' fields, after adding them with their
   * functional names.
   *
   * @param $customFields
   * @param $result
   */
  public static function stripCustomFieldsResult($customFields, &$result) {
    foreach ($customFields['custom_fields'] as $fieldId => $field) {
      if (isset($result['custom_' . $fieldId]) && isset($result[$field['name']])) {
        unset($result['custom_' . $fieldId]);
      }
    }
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

  /**
   * Generic method to add custom data using CRM_Core_DAO::executeQuery
   *
   * @param  array $params
   *
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
   * Method om select en from voor custom group samen te stellen
   *
   * @param  $customGroupArray
   *
   * @return string
   */
  public static function createCustomDataQuery($customGroupArray) {
    if (!$customGroupArray['custom_fields']) {
      $select = 'SELECT *';
    }
    else {
      $columns = [];
      foreach ($customGroupArray['custom_fields'] as $customFieldId => $customField) {
        $columns[] = $customField['column_name'] . ' AS ' . $customField['name'];
      }
      $select = 'SELECT ' . implode(", ", $columns);
    }
    $result = $select . ' FROM ' . $customGroupArray['table_name'];
    return $result;
  }

}
