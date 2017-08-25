<?php

/**
 * Adres.Get API specification (optional)
 * This is used for documentation and validation.
 *
 * @param array $spec description of fields supported by this API call
 * @return void
 * @see http://wiki.civicrm.org/confluence/display/CRMDOC/API+Architecture+Standards
 */
function _civicrm_api3_adres_Get_spec(&$spec) {
  /*
    $spec['id'] = array(
    'name' => 'id',
    'title' => 'id',
    'type' => CRM_Utils_Type::T_INT
  );
  */
}

/**
 * Adres.Get API
 *
 * @param array $params
 * @return array API result descriptor
 * @see civicrm_api3_create_success
 * @see civicrm_api3_create_error
 * @throws API_Exception
 */
function civicrm_api3_adres_Get($params) {
  $adres = new CRM_Basis_Adres();
  return civicrm_api3_create_success($adres->get($params), $params, 'Adres', 'Get');
}
