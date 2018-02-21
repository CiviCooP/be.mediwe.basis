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

}
