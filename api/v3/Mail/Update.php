<?php
/**
 * Created by PhpStorm.
 * User: CDeman
 * Date: 20/07/2017
 * Time: 23:46
 */

function _civicrm_api3_mail_Update_spec(&$spec) {
    $spec['id']['api.required'] = 1;
}

/**
 * Mail.Create API
 *
 * @param array $params
 * @return array API result descriptor
 * @see civicrm_api3_create_success
 * @see civicrm_api3_create_error
 * @throws API_Exception
 */
function civicrm_api3_mail_Update($params) {
    $mail = new CRM_Basis_Mail();
    $returnValues = $mail->update($params);
    return civicrm_api3_update_success($returnValues, $params, 'Mail', 'Update');
}
