<?php

/**
 * Huisbezoek.Create API specification (optional)
 * This is used for documentation and validation.
 *
 * @param array $spec description of fields supported by this API call
 * @return void
 * @see http://wiki.civicrm.org/confluence/display/CRMDOC/API+Architecture+Standards
 */
function _civicrm_api3_huisbezoek_Create_spec(&$spec) {
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
function civicrm_api3_huisbezoek_Create($params) {

    $huisbezoek = new CRM_Basis_Huisbezoek();
    $returnValues = $huisbezoek->create($params);
    return civicrm_api3_create_success($returnValues, $params, 'Huisbezoek', 'Create');

}
