<?php
/**
 * MedischeControle.Delete API specification (optional)
 * This is used for documentation and validation.
 *
 * @param array $spec description of fields supported by this API call
 * @return void
 * @see http://wiki.civicrm.org/confluence/display/CRMDOC/API+Architecture+Standards
 */
function _civicrm_api3_medische_controle_Delete_spec(&$spec) {
  $spec['id'] = array(
    'name' => 'id',
    'title' => 'ID van het dossier medische controle',
    'description' => 'ID van het dossier medische controle',
    'api.required' => 1,
    'type' => CRM_Utils_Type::T_STRING,
  );
}

/**
 * MedischeControle.Delete API
 *
 * @param array $params
 * @return array API result descriptor
 * @see civicrm_api3_create_success
 * @see civicrm_api3_create_error
 * @throws API_Exception
 */
function civicrm_api3_medische_controle_Delete($params) {
  $medischeControle = new CRM_Basis_MedischeControle();
  $returnValues = $medischeControle->deleteWithId($params['id']);
  return civicrm_api3_create_success($returnValues, $params, 'MedischeControle', 'Delete');
}
