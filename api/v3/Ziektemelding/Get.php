<?php

/**
 * Ziekmelding.Get API specification (optional)
 * This is used for documentation and validation.
 *
 * @param array $spec description of fields supported by this API call
 * @return void
 * @see http://wiki.civicrm.org/confluence/display/CRMDOC/API+Architecture+Standards
 */
function _civicrm_api3_ziektemelding_Get_spec(&$spec) {
  $spec['id'] = array(
    'name' => 'id',
    'title' => 'case_id',
    'description' => 'ID dossier ziektemelding',
    'api.required' => 0,
    'type' => CRM_Utils_Type::T_INT,
  );
  $spec['employee_id'] = array(
    'name' => 'employee_id',
    'title' => 'klant medewerker ID',
    'description' => 'ID van de medewerker die ziek gemeld wordt',
    'api.required' => 0,
    'type' => CRM_Utils_Type::T_INT,
  );
  $spec['contact_id'] = array(
    'name' => 'contact_id',
    'title' => 'klant ID',
    'description' => 'ID van de klant die de ziektemelding doet',
    'api.required' => 0,
    'type' => CRM_Utils_Type::T_INT,
  );
  $spec['start_date'] = array(
    'name' => 'start_date',
    'title' => 'Datum van',
    'description' => 'Startdatum van de ziekteperiode',
    'api.required' => 1,
    'type' => CRM_Utils_Type::T_DATE,
  );
  $spec['end_date'] = array(
    'name' => 'end_date',
    'title' => 'Datum tot',
    'description' => 'Einddatum van de ziekteperiode',
    'api.required' => 0,
    'type' => CRM_Utils_Type::T_DATE,
  );
  $spec['status_id'] = array(
    'name' => 'status_id',
    'title' => 'Dossier status',
    'description' => 'Status ID van het dossier ziektemelding',
    'api.required' => 0,
    'type' => CRM_Utils_Type::T_INT,
  );
  $spec['mzp_reden_ziekte'] = array(
    'name' => 'mzp_reden_ziekte',
    'title' => 'Reden ziekte',
    'description' => 'Reden ziekte',
    'api.required' => 0,
    'type' => CRM_Utils_Type::T_STRING,
  );
  $spec['mzp_vermoedelijke_reden'] = array(
    'name' => 'mzp_vermoedelijke_reden',
    'title' => 'Vermoedelijke reden ziekte',
    'description' => 'Vermoedelijke reden ziekte',
    'api.required' => 0,
    'type' => CRM_Utils_Type::T_STRING,
  );
  $spec['mzp_spontane_werkhervatting'] = array(
    'name' => 'mzp_spontane_werkhervatting',
    'title' => 'Datum spontane werkhervatting',
    'description' => 'Datum spontane werkhervatting',
    'api.required' => 0,
    'type' => CRM_Utils_Type::T_DATE,
  );
  $spec['mzp_is_verlenging'] = array(
    'name' => 'mzp_is_verlenging',
    'title' => 'Is verlenging',
    'description' => 'Is verlenging?',
    'api.required' => 0,
    'type' => CRM_Utils_Type::T_BOOLEAN,
  );
  $spec['mzp_is_prive_ongeval'] = array(
    'name' => 'mzp_is_prive_ongeval',
    'title' => 'Is privé ongeval',
    'description' => 'Is privé ongeval?',
    'api.required' => 0,
    'type' => CRM_Utils_Type::T_BOOLEAN,
  );
  $spec['mzp_mag_huis_verlaten'] = array(
    'name' => 'mzp_mag_huis_verlaten',
    'title' => 'Mag huis verlaten',
    'description' => 'Mag huis verlaten?',
    'api.required' => 0,
    'type' => CRM_Utils_Type::T_BOOLEAN,
  );
  $spec['mzp_is_ziekenhuisopname'] = array(
    'name' => 'mzp_is_ziekenhuisopname',
    'title' => 'Ziekenhuisopname',
    'description' => 'Is ziekenhuisopname?',
    'api.required' => 0,
    'type' => CRM_Utils_Type::T_BOOLEAN,
  );
  $spec['mzp_ziekenhuisopname_van'] = array(
    'name' => 'mzp_ziekenhuisopname_van',
    'title' => 'startdatum ziekenhuisopname',
    'description' => 'Startdatum ziekenhuisopname',
    'api.required' => 0,
    'type' => CRM_Utils_Type::T_DATE,
  );
  $spec['mzp_ziekenhuisopname_tot'] = array(
    'name' => 'mzp_ziekenhuisopname_tot',
    'title' => 'einddatum ziekenhuisopname',
    'description' => 'Einddatum ziekenhuisopname',
    'api.required' => 0,
    'type' => CRM_Utils_Type::T_DATE,
  );
  $spec['mzp_behandelende_arts'] = array(
    'name' => 'mzp_behandelende_arts',
    'title' => 'Behandelend arts',
    'description' => 'Behandelend arts',
    'api.required' => 0,
    'type' => CRM_Utils_Type::T_STRING,
  );
  $spec['mzp_is_zonder_attest'] = array(
    'name' => 'mzp_is_zonder_attest',
    'title' => 'Zonder attest',
    'description' => 'Is zonder attest?',
    'api.required' => 0,
    'type' => CRM_Utils_Type::T_BOOLEAN,
  );
  $spec['mzp_werd_ziek_op_werk'] = array(
    'name' => 'mzp_werd_ziek_op_werk',
    'title' => 'Werd ziek op werk',
    'description' => 'Werd ziek op werk?',
    'api.required' => 0,
    'type' => CRM_Utils_Type::T_BOOLEAN,
  );
  $spec['mzp_is_klant_ingelicht'] = array(
    'name' => 'mzp_is_klant_ingelicht',
    'title' => 'Is klant ingelicht',
    'description' => 'Is klant ingelicht?',
    'api.required' => 0,
    'type' => CRM_Utils_Type::T_BOOLEAN,
  );
  $spec['mzp_uur_shift'] = array(
    'name' => 'mzp_uur_shift',
    'title' => 'Vertrokken van shift',
    'description' => 'Vertrokken van shift',
    'api.required' => 0,
    'type' => CRM_Utils_Type::T_STRING,
  );
}

/**
 * Ziekmelding.Get API
 *
 * @param array $params
 * @return array API result descriptor
 * @see civicrm_api3_create_success
 * @see civicrm_api3_create_error
 */
function civicrm_api3_ziektemelding_Get($params) {
  $ziektemelding = new CRM_Basis_Ziektemelding();
  return civicrm_api3_create_success($ziektemelding->get($params), $params, 'Ziektemelding', 'Get');
}
