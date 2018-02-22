<?php

/**
 * MedischeControle.Update API specification (optional)
 * This is used for documentation and validation.
 *
 * @param array $spec description of fields supported by this API call
 * @return void
 * @see http://wiki.civicrm.org/confluence/display/CRMDOC/API+Architecture+Standards
 */
function _civicrm_api3_medische_controle_Update_spec(&$spec) {
  $spec['id'] = array(
    'name' => 'id',
    'title' => 'ID van het dossier medische controle',
    'description' => 'ID van het dossier medische controle',
    'api.required' => 1,
    'type' => CRM_Utils_Type::T_STRING,
  );
  $spec['verblijfsadres'] = array(
    'name' => 'verblijfsadres',
    'title' => 'Verblijfsadres medewerker',
    'description' => 'Verblijfsadres van de medewerker',
    'api.required' => 0,
    'type' => CRM_Utils_Type::T_STRING,
  );
  $spec['verblijfspostcode'] = array(
    'name' => 'verblijfspostcode',
    'title' => 'Verblijfspostcode medewerker',
    'description' => 'Verblijfspostcode van de medewerker',
    'api.required' => 0,
    'type' => CRM_Utils_Type::T_STRING,
  );
  $spec['verblijfsgemeente'] = array(
    'name' => 'verblijfsgemeente',
    'title' => 'Verblijfsgemeente medewerker',
    'description' => 'Verblijfsgemeente van de medewerker',
    'api.required' => 0,
    'type' => CRM_Utils_Type::T_STRING,
  );
  $spec['verblijfsland'] = array(
    'name' => 'verblijfsland',
    'title' => 'Verblijfsland medewerker',
    'description' => 'Verblijfsland (iso code) van de medewerker',
    'api.required' => 0,
    'type' => CRM_Utils_Type::T_STRING,
  );
  $spec['mmc_result_email1'] = array(
    'name' => 'mmc_result_email1',
    'title' => '1e email resultaatt',
    'description' => '1e e-mailadres voor resultaat controle',
    'api.required' => 0,
    'type' => CRM_Utils_Type::T_STRING,
  );
  $spec['mmc_result_email2'] = array(
    'name' => 'mmc_result_email2',
    'title' => '2e email resultaatt',
    'description' => '2e e-mailadres voor resultaat controle',
    'api.required' => 0,
    'type' => CRM_Utils_Type::T_STRING,
  );
  $spec['mmc_result_email3'] = array(
    'name' => 'mmc_result_email3',
    'title' => '3e email resultaatt',
    'description' => '3e e-mailadres voor resultaat controle',
    'api.required' => 0,
    'type' => CRM_Utils_Type::T_STRING,
  );
  $spec['naam_aanvrager'] = array(
    'name' => 'naam_aanvrager',
    'title' => 'Naam medewerker klant die controle aanvraagt',
    'description' => 'Naam medewerker klant die de controle aanvraagt',
    'api.required' => 0,
    'type' => CRM_Utils_Type::T_STRING,
  );
  $spec['mmc_controle_datum'] = array(
    'name' => 'mmc_controle_datum',
    'title' => 'Datum van de controle',
    'description' => 'Datum van de controle',
    'api.required' => 0,
    'type' => CRM_Utils_Type::T_DATE,
  );
  $spec['mmc_reden_ziekte_kort'] = array(
    'name' => 'mmc_reden_ziekte_kort',
    'title' => 'Reden korte ziekteperiode',
    'description' => 'Reden korte ziekteperiode',
    'api.required' => 0,
    'type' => CRM_Utils_Type::T_STRING,
  );
  $spec['mmc_type_controle'] = array(
    'name' => 'mmc_type_controle',
    'title' => 'Type controle',
    'description' => 'Type controle',
    'api.required' => 0,
    'type' => CRM_Utils_Type::T_STRING,
  );
  $spec['mmc_job_beschrijving'] = array(
    'name' => 'mmc_job_beschrijving',
    'title' => 'Job omschrijving',
    'description' => 'Job omschrijving',
    'api.required' => 0,
    'type' => CRM_Utils_Type::T_TEXT,
  );
  $spec['mmc_opmerking_mediwe'] = array(
    'name' => 'mmc_opmerking_mediwe',
    'title' => 'Opmerking voor Mediwe',
    'description' => 'Opmerking voor Mediwe',
    'api.required' => 0,
    'type' => CRM_Utils_Type::T_TEXT,
  );
  $spec['mmc_opmerking_controlearts'] = array(
    'name' => 'mmc_opmerking_controlearts',
    'title' => 'Opmerking voor de controlearts',
    'description' => 'Opmerking voor de controlearts',
    'api.required' => 0,
    'type' => CRM_Utils_Type::T_TEXT,
  );
  $spec['mmc_info_delen_patient'] = array(
    'name' => 'mmc_info_delen_patient',
    'title' => 'Info delen met patiënt',
    'description' => 'Mag deze info gedeeld worden met de patiënt?',
    'api.required' => 0,
    'type' => CRM_Utils_Type::T_BOOLEAN,
  );
  $spec['mmc_controle_criterium'] = array(
    'name' => 'mmc_controle_criterium',
    'title' => 'Controle criterium',
    'description' => 'Controle criterium',
    'api.required' => 0,
    'type' => CRM_Utils_Type::T_INT,
  );
  $spec['mmc_aankoop_prijs_arts'] = array(
    'name' => 'mmc_aankoop_prijs_arts',
    'title' => 'Aankoopprijs arts',
    'description' => 'Aankoopprijs controlearts',
    'api.required' => 0,
    'type' => CRM_Utils_Type::T_MONEY,
  );
  $spec['mmc_aankoop_supplement_arts'] = array(
    'name' => 'mmc_aankoop_supplement_arts',
    'title' => 'Aankoopprijs supplement arts',
    'description' => 'Aankoopprijs supplement controlearts',
    'api.required' => 0,
    'type' => CRM_Utils_Type::T_MONEY,
  );
  $spec['mmc_verkoop_prijs_klant'] = array(
    'name' => 'mmc_verkoop_prijs_klant',
    'title' => 'Verkoopprijs aan klant',
    'description' => 'Verkoopprijs aan klant',
    'api.required' => 0,
    'type' => CRM_Utils_Type::T_MONEY,
  );
  $spec['mmc_verkoop_korting_klant'] = array(
    'name' => 'mmc_verkoop_korting_klant',
    'title' => 'Korting aan klant',
    'description' => 'Korting aan klant',
    'api.required' => 0,
    'type' => CRM_Utils_Type::T_MONEY,
  );
  $spec['mmc_po_nummer_klant'] = array(
    'name' => 'mmc_po_nummer_klant',
    'title' => 'PO nummer klant',
    'description' => 'Inkooporder nummer klant',
    'api.required' => 0,
    'type' => CRM_Utils_Type::T_STRING,
  );
  $spec['mmc_resultaat'] = array(
    'name' => 'mmc_resultaat',
    'title' => 'Resultaat',
    'description' => 'Resultaat controle',
    'api.required' => 0,
    'type' => CRM_Utils_Type::T_STRING,
  );
  $spec['mmc_memo_mediwe'] = array(
    'name' => 'mmc_memo_mediwe',
    'title' => 'PostIt Mediwe',
    'description' => 'PostIt Mediwe',
    'api.required' => 0,
    'type' => CRM_Utils_Type::T_TEXT,
  );
  $spec['mmc_memo_klant'] = array(
    'name' => 'mmc_memo_klant',
    'title' => 'PostIt klant',
    'description' => 'PostIt klant',
    'api.required' => 0,
    'type' => CRM_Utils_Type::T_TEXT,
  );
}

/**
 * MedischeControle.Update API
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
