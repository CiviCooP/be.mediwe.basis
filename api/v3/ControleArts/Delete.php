<?php
/**
 * Created by PhpStorm.
 * User: CDeman
 * Date: 20/07/2017
 * Time: 23:43
 */
function _civicrm_api3_controle_arts_Delete_spec(&$spec) {
  $spec['id'] = array(
    'name' => 'id',
    'title' => 'contact id',
    'description' => 'ContactID van de controle arts',
    'api.required' => 1,
    'type' => CRM_Utils_Type::T_INT,
  );
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
function civicrm_api3_controle_arts_Delete($params) {
  $controlearts = new CRM_Basis_ControleArts();
  $returnValues = $controlearts->deleteWithId($params['id']);
  return civicrm_api3_create_success($returnValues, $params, 'ControleArts', 'Delete');
}
