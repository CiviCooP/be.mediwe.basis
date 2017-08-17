<?php

/**
 * Mail.Create API specification (optional)
 * This is used for documentation and validation.
 *
 * @param array $spec description of fields supported by this API call
 * @return void
 * @see http://wiki.civicrm.org/confluence/display/CRMDOC/API+Architecture+Standards
 */
function _civicrm_api3_mail_Create_spec(&$spec) {
  $spec['organization_name']['api.required'] = 1;
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
function civicrm_api3_mail_Create($params) {
  $mail = new CRM_Basis_Mail();
  $returnValues = $mail->create($params);
  return civicrm_api3_create_success($returnValues, $params, 'Mail', 'Create');
}
