<?php
/**
 * Ziektemelding.Delete API specification (optional)
 * This is used for documentation and validation.
 *
 * @param array $spec description of fields supported by this API call
 * @return void
 * @see http://wiki.civicrm.org/confluence/display/CRMDOC/API+Architecture+Standards
 */
function _civicrm_api3_ziektemelding_Delete_spec(&$spec) {
  $spec['id'] = array(
    'name' => 'id',
    'title' => 'case_id',
    'description' => 'ID dossier ziektemelding',
    'api.required' => 1,
    'type' => CRM_Utils_Type::T_INT,
  );
}

/**
 * Klant.Delete API
 *
 * @param array $params
 * @return array API result descriptor
 * @see civicrm_api3_create_success
 * @see civicrm_api3_create_error
 * @throws API_Exception
 */
function civicrm_api3_ziektemelding_Delete($params) {
  $ziektemelding = new CRM_Basis_Ziektemelding();
  $returnValues = $ziektemelding->deleteWithId($params['id']);
  return civicrm_api3_create_success($returnValues, $params, 'Ziektemelding', 'Delete');
}
