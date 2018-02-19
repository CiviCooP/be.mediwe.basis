<?php

/**
 * Abstract class voor MediweContact
 *
 * @author  Erik Hommel (CiviCooP) <erik.hommel@civicoop.org>
 * @author  Klaas Eikelboom (CiviCooP) <klaas.eikelboom@civicoop.org>
 * @author  Christophe Deman <christophe.deman@mediwe.be>
 * @date    19 Feb 2018
 * @license AGPL-3.0
 */
abstract class CRM_Basis_MediweContact {

  /**
   * Method om custom velden aan een params array toe te voegen
   * @param $customFields
   * @param $params
   */
  protected function replaceCustomFieldsParams($customFields, &$params) {
    foreach ($customFields as $field) {
      $fieldName = $field['name'];
      if (isset($params[$fieldName])) {
        $customFieldName = 'custom_' . $field['id'];
        $params[$customFieldName] = $params[$fieldName];
        unset($params[$fieldName]);
      }
    }
  }

  /**
   * Method om enkelvoudige custom velden toe te voegen aan mediwe contact
   *
   * @param  $customGroup
   * @param  $entityId
   * @return array
   */
  protected function addSingleDaoData($customGroup, $entityId) {
    $result = array();
    $tableName = $customGroup['table_name'];
    if (!empty($tableName)) {
      $customFields = $customGroup['custom_fields'];
      $sql = 'SELECT * FROM ' . $tableName . ' WHERE entity_id = %1';
      $dao = CRM_Core_DAO::executeQuery($sql, array(
        1 => array($entityId, 'Integer'),
      ));
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
