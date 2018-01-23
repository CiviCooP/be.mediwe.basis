<?php
use CRM_Basis_ExtensionUtil as E;

/**
 * ControleArts.Voorstel API specification (optional)
 * This is used for documentation and validation.
 *
 * @param array $spec description of fields supported by this API call
 * @return void
 * @see http://wiki.civicrm.org/confluence/display/CRMDOC/API+Architecture+Standards
 */
function _civicrm_api3_controle_arts_Voorstel_spec(&$spec) {
  $spec['postcode'] = array(
    'name' => 'postcode',
    'title' => 'postcode',
    'api.required' => 1,
    'type' => CRM_Utils_Type::T_INT,
  );
  $spec['voorstel_datum'] = array(
    'name' => 'voorstel_datum',
    'title' => 'voorstel_datum',
    'api.required' => 0,
    'type' => CRM_Utils_Type::T_STRING,
  );
  $spec['limiet'] = array(
    'name' => 'limiet',
    'title' => 'limiet',
    'api.required' => 0,
    'type' => CRM_Utils_Type::T_INT,
  );
  $spec['huisbezoek_id'] = array(
    'name' => 'huisbezoek_id',
    'title' => 'huisbezoek_id',
    'api.required' => 0,
    'type' => CRM_Utils_Type::T_INT,
  );
}

/**
 * ControleArts.Voorstel API
 *
 * @param array $params
 * @return array API result descriptor
 * @see civicrm_api3_create_success
 * @see civicrm_api3_create_error
 * @throws API_Exception
 */
function civicrm_api3_controle_arts_Voorstel($params) {
  $controleArts = new CRM_Basis_ControleArts();
  $returnValues = $controleArts->getVoorstel($params);
  return civicrm_api3_create_success($returnValues, $params, 'ControleArts', 'Voorstel');
}
