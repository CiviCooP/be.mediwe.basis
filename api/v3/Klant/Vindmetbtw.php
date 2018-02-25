<?php
use CRM_Basis_ExtensionUtil as E;

/**
 * Klant.Vindmetbtw API specification (optional)
 * This is used for documentation and validation.
 *
 * @param array $spec description of fields supported by this API call
 * @return void
 * @see http://wiki.civicrm.org/confluence/display/CRMDOC/API+Architecture+Standards
 */
function _civicrm_api3_klant_Vindmetbtw_spec(&$spec) {
  $spec['mf_btw_nummer'] = array(
    'name' => 'mf_btw_nummer',
    'title' => 'BTW nummer',
    'description' => 'BTW nummer',
    'api.required' => 1,
    'type' => CRM_Utils_Type::T_STRING,
  );
}

/**
 * Klant.Vindmetbtw API
 *
 * @param array $params
 * @return array API result descriptor
 * @see civicrm_api3_create_success
 */
function civicrm_api3_klant_Vindmetbtw($params) {
  $klant = new CRM_Basis_Klant();
  $klanten = $klant->vindMetBtw($params['mf_btw_nummer']);
  return civicrm_api3_create_success($klanten, $params, 'Klant', 'Vindmetbtw');
}
