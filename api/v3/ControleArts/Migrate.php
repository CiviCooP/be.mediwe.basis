<?php

/**
 * ControleArts.Create API specification (optional)
 * This is used for documentation and validation.
 *
 * @param array $spec description of fields supported by this API call
 * @return void
 * @see http://wiki.civicrm.org/confluence/display/CRMDOC/API+Architecture+Standards
 */
function _civicrm_api3_controle_arts_migrate_spec(&$spec) {
  //$spec['organization_name']['api.required'] = 1;
}

/**
 * ControleArts.Create API
 *
 * @param array $params
 * @return array API result descriptor
 * @see civicrm_api3_create_success
 * @see civicrm_api3_create_error
 * @throws API_Exception
 */
function _civicrm_api3_controle_arts_migrate($params) {
  $ControleArts = new CRM_Basis_ControleArts();
  $returnValues = $ControleArts->migrate($params);
  return civicrm_api3_create_success($returnValues, $params, 'ControleArts', 'Migrate');
}
