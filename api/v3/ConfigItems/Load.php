<?php
/**
 * ConfigItems.Load API
 *
 * @param array $params
 * @return array API result descriptor
 * @see civicrm_api3_create_success
 * @see civicrm_api3_create_error
 * @throws API_Exception
 */
function civicrm_api3_config_items_Load($params) {
  CRM_Basis_ConfigItems_ConfigItems::singleton();
  return civicrm_api3_create_success(array(), $params, 'ConfigItems', 'Load');
}
