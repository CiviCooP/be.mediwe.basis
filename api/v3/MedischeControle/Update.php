<?php

/**
 * Ziekmelding.Create API specification (optional)
 * This is used for documentation and validation.
 *
 * @param array $spec description of fields supported by this API call
 * @return void
 * @see http://wiki.civicrm.org/confluence/display/CRMDOC/API+Architecture+Standards
 */
function _civicrm_api3_medische_controle_Update_spec(&$spec) {
  //$spec['magicword']['api.required'] = 1;
}

/**
 * Ziekmelding.Create API
 *
 * @param array $params
 * @return array API result descriptor
 * @see civicrm_api3_create_success
 * @see civicrm_api3_create_error
 * @throws API_Exception
 */
function civicrm_api3_medische_controle_Update($params) {

    $medische_controle = new CRM_Basis_MedischeControle();
    $returnValues = $medische_controle->update($params);
    return civicrm_api3_create_success($returnValues, $params, 'MedischeControle', 'Update');

}
