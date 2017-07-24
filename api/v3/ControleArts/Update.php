<?php
/**
 * Created by PhpStorm.
 * User: CDeman
 * Date: 20/07/2017
 * Time: 23:46
 */

function _civicrm_api3_controlearts_Update_spec(&$spec) {
    $spec['id']['api.required'] = 1;
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
function civicrm_api3_controlearts_Update($params) {
    $controlearts = new CRM_Basis_ControleArts();
    $returnValues = $controlearts->update($params);
    return civicrm_api3_update_success($returnValues, $params, 'ControleArts', 'Update');
}
