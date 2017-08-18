<?php

/**
 * Relatie.Get API specification (optional)
 * This is used for documentation and validation.
 *
 * @param array $spec description of fields supported by this API call
 * @return void
 * @see http://wiki.civicrm.org/confluence/display/CRMDOC/API+Architecture+Standards
 */
function _civicrm_api3_telefoon_Get_spec(&$spec) {
/*  $spec['id'] = array(
    'name' => 'id',
    'title' => 'id',
    'type' => CRM_Utils_Type::T_INT
  );
  $spec['organization_name'] = array(
    'name' => 'organization_name',
    'title' => 'organization_name',
    'type' => CRM_Utils_Type::T_STRING
  );
  $spec['legal_name'] = array(
    'name' => 'legal_name',
    'title' => 'legal_name',
    'type' => CRM_Utils_Type::T_STRING
  );
  $spec['is_active'] = array(
    'name' => 'is_active',
    'title' => 'is_active',
    'type' => CRM_Utils_Type::T_INT
  );
*/
}

/**
 * Relatie.Get API
 *
 * @param array $params
 * @return array API result descriptor
 * @see civicrm_api3_create_success
 * @see civicrm_api3_create_error
 * @throws API_Exception
 */
function civicrm_api3_telefoon_Get($params) {
  $telefoon = new CRM_Basis_Relatie();
  return civicrm_api3_create_success($telefoon->get($params), $params, 'Relatie', 'Get');
}
