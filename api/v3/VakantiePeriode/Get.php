<?php
use CRM_Basis_ExtensionUtil as E;

/**
 * VakantiePeriode.Get API specification (optional)
 * This is used for documentation and validation.
 *
 * @param array $spec description of fields supported by this API call
 * @return void
 * @see http://wiki.civicrm.org/confluence/display/CRMDOC/API+Architecture+Standards
 */
function _civicrm_api3_vakantie_periode_Get_spec(&$spec) {
  $spec['contact_id'] = array(
    'name' => 'contact_id',
    'title' => 'contact_id',
    'description' => 'ContactID waarvoor vakantieperiode gehaald wordt',
    'api.required' => 0,
    'type' => CRM_Utils_Type::T_STRING,
  );
}

/**
 * VakantiePeriode.Get API
 *
 * @param array $params
 * @return array API result descriptor
 * @see civicrm_api3_create_success
 * @see civicrm_api3_create_error
 * @throws API_Exception
 */
function civicrm_api3_vakantie_periode_Get($params) {
  $customFields = CRM_Basis_Config::singleton()->getVakantieperiodeCustomGroup('custom_fields');
  $repeatingData = CRM_Basis_Utils::getRepeatingData($customFields, array('entity_id'=> 6));
  CRM_Core_Error::debug('data', $repeatingData);
  exit();

}
