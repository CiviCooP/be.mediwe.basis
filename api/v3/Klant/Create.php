<?php

/**
 * Klant.Create API specification (optional)
 * This is used for documentation and validation.
 *
 * @param array $spec description of fields supported by this API call
 * @return void
 * @see http://wiki.civicrm.org/confluence/display/CRMDOC/API+Architecture+Standards
 */
function _civicrm_api3_klant_Create_spec(&$spec) {
  $spec['organization_name'] = array(
    'name' => 'organization_name',
    'title' => 'naam klant',
    'description' => 'Naam klant',
    'api.required' => 1,
    'type' => CRM_Utils_Type::T_STRING,
  );
  $spec['external_identifier'] = array(
    'name' => 'external_identifier',
    'title' => 'Externe id',
    'description' => 'Externe id van de klant',
    'api.required' => 0,
    'type' => CRM_Utils_Type::T_STRING,
  );
  $spec['legal_name'] = array(
    'name' => 'legal_name',
    'title' => 'Officiële wettelijke naam',
    'description' => 'Officiële wettelijke naam van de klant',
    'api.required' => 0,
    'type' => CRM_Utils_Type::T_STRING,
  );
  $spec['mio_niveau1'] = array(
    'name' => 'mio_niveau1',
    'title' => 'Naam niveau 1',
    'description' => 'Naam niveau 1',
    'api.required' => 0,
    'type' => CRM_Utils_Type::T_STRING,
  );
  $spec['mio_niveau2'] = array(
    'name' => 'mio_niveau2',
    'title' => 'Naam niveau 2',
    'description' => 'Naam niveau 2',
    'api.required' => 0,
    'type' => CRM_Utils_Type::T_STRING,
  );
  $spec['mio_niveau3'] = array(
    'name' => 'mio_niveau3',
    'title' => 'Naam niveau 3',
    'description' => 'Naam niveau 3',
    'api.required' => 0,
    'type' => CRM_Utils_Type::T_STRING,
  );
  $spec['mio_email_goedkeuring'] = array(
    'name' => 'mio_email_goedkeuring',
    'title' => 'Email goedkeuring controle',
    'description' => 'Email goedkeuring controle',
    'api.required' => 0,
    'type' => CRM_Utils_Type::T_STRING,
  );
  $spec['mio_aanspreking_goedkeuring'] = array(
    'name' => 'mio_aanspreking_goedkeuring',
    'title' => 'Aanspreking goedkeuring controle',
    'description' => 'Aanspreking goedkeuring controle',
    'api.required' => 0,
    'type' => CRM_Utils_Type::T_STRING,
  );
  $spec['mio_max_dagen_kort'] = array(
    'name' => 'mio_max_dagen_kort',
    'title' => 'Maximum dagen korte ziekteperiode',
    'description' => 'Maximum dagen korte ziekteperiode',
    'api.required' => 0,
    'type' => CRM_Utils_Type::T_INT,
  );
  $spec['mio_max_maanden_nieuw'] = array(
    'name' => 'mio_max_maanden_nieuw',
    'title' => 'Maximum maanden nieuwe medewerker',
    'description' => 'Maximum maanden nieuwe medewerker',
    'api.required' => 0,
    'type' => CRM_Utils_Type::T_INT,
  );
  $spec['mf_venice'] = array(
    'name' => 'mf_venice',
    'title' => 'Venice nummer',
    'description' => 'Nummer klant in Venice',
    'api.required' => 0,
    'type' => CRM_Utils_Type::T_STRING,
  );
  $spec['mf_btw_nummer'] = array(
    'name' => 'mf_btw_nummer',
    'title' => 'BTW nummer',
    'description' => 'BTW nummer',
    'api.required' => 0,
    'type' => CRM_Utils_Type::T_STRING,
  );
  $spec['mf_btw_plichtig'] = array(
    'name' => 'mf_btw_plichtig',
    'title' => 'BTW plichtig',
    'description' => 'BTW plichtig?',
    'api.required' => 0,
    'type' => CRM_Utils_Type::T_BOOLEAN,
  );
  $spec['mf_onderbreek_tot_contract'] = array(
    'name' => 'mf_onderbreek_tot_contract',
    'title' => 'Onderbreek facturatie tot contract',
    'description' => 'mf_onderbreek_tot_contract?',
    'api.required' => 0,
    'type' => CRM_Utils_Type::T_BOOLEAN,
  );
  $spec['mf_iban'] = array(
    'name' => 'mf_iban',
    'title' => 'iban',
    'description' => 'IBAN',
    'api.required' => 0,
    'type' => CRM_Utils_Type::T_STRING,
  );
  $spec['mf_bestelnummer'] = array(
    'name' => 'mf_bestelnummer',
    'title' => 'Bestelnummer',
    'description' => 'Bestelnummer',
    'api.required' => 0,
    'type' => CRM_Utils_Type::T_STRING,
  );
  $spec['mf_toevoegen_csv'] = array(
    'name' => 'mf_toevoegen_csv',
    'title' => 'Toevoegen csv bestand',
    'description' => 'CSV bestand toevoegen bij factuur?',
    'api.required' => 0,
    'type' => CRM_Utils_Type::T_BOOLEAN,
  );
  $spec['mcpk_opmerkingen'] = array(
    'name' => 'mcpk_opmerkingen',
    'title' => 'Opmerkingen controle procedure',
    'description' => 'Opmerkingen controle procedure',
    'api.required' => 0,
    'type' => CRM_Utils_Type::T_TEXT,
  );
  $spec['mcpk_vrij_veld'] = array(
    'name' => 'mcpk_vrij_veld',
    'title' => 'Vrij veld controle procedure',
    'description' => 'Vrij veld controle procedure',
    'api.required' => 0,
    'type' => CRM_Utils_Type::T_TEXT,
  );
  $spec['mcpk_sector_omschrijving'] = array(
    'name' => 'mcpk_sector_omschrijving',
    'title' => 'Sector omschrijving',
    'description' => 'Sector omschrijving',
    'api.required' => 0,
    'type' => CRM_Utils_Type::T_TEXT,
  );
  $spec['mcpk_sector_id'] = array(
    'name' => 'mcpk_sector_id',
    'title' => 'Sector ID',
    'description' => 'Sector ID',
    'api.required' => 0,
    'type' => CRM_Utils_Type::T_INT,
  );
  $spec['mcpk_fte_berekeningswijze'] = array(
    'name' => 'mcpk_fte_berekeningswijze',
    'title' => 'FTE Berekeningswijze',
    'description' => 'Berekeningswijze voor FTE',
    'api.required' => 0,
    'type' => CRM_Utils_Type::T_STRING,
  );
  $spec['mcpk_huis_niet_verlaten_van'] = array(
    'name' => 'mcpk_huis_niet_verlaten_van',
    'title' => 'Huis niet verlaten van',
    'description' => 'Huis niet verlaten van',
    'api.required' => 0,
    'type' => CRM_Utils_Type::T_STRING,
  );
  $spec['mcpk_huis_niet_verlaten_tot'] = array(
    'name' => 'mcpk_huis_niet_verlaten_tot',
    'title' => 'Huis niet verlaten tot',
    'description' => 'Huis niet verlaten tot',
    'api.required' => 0,
    'type' => CRM_Utils_Type::T_STRING,
  );
  $spec['mcpk_controle_procedure'] = array(
    'name' => 'mcpk_controle_procedure',
    'title' => 'Controle procedure',
    'description' => 'Controle procedure',
    'api.required' => 0,
    'type' => CRM_Utils_Type::T_TEXT,
  );
  $spec['mcpk_verzuimbeleid_visie'] = array(
    'name' => 'mcpk_verzuimbeleid_visie',
    'title' => 'Verzuimbeleid visie',
    'description' => 'Verzuimbeleid visie',
    'api.required' => 0,
    'type' => CRM_Utils_Type::T_TEXT,
  );
  $spec['mcpk_doel_controle'] = array(
    'name' => 'mcpk_doel_controle',
    'title' => 'Doel controle',
    'description' => 'Doel controle',
    'api.required' => 0,
    'type' => CRM_Utils_Type::T_TEXT,
  );
  $spec['mcpk_email_resultaten'] = array(
    'name' => 'mcpk_email_resultaten',
    'title' => 'Resultaten naar e-mailadres',
    'description' => 'Resultaten naar e-mailadres',
    'api.required' => 0,
    'type' => CRM_Utils_Type::T_STRING,
  );
  $spec['mcpk_sms_medewerker'] = array(
    'name' => 'mcpk_sms_medewerker',
    'title' => 'SMS naar medewerker',
    'description' => 'Stuur een SMS naar medewerker?',
    'api.required' => 0,
    'type' => CRM_Utils_Type::T_BOOLEAN,
  );
  $spec['mas_email_goedkeuring'] = array(
    'name' => 'mas_email_goedkeuring',
    'title' => 'Goedkeuring naar e-mailadres',
    'description' => 'Goedkeuring naare-mailadres selectie',
    'api.required' => 0,
    'type' => CRM_Utils_Type::T_STRING,
  );
  $spec['mas_aanspreking_goedkeuring'] = array(
    'name' => 'mas_aanspreking_goedkeuring',
    'title' => 'Aanspreking goedkeuring',
    'description' => 'Aanspreking goedkeuring selectie',
    'api.required' => 0,
    'type' => CRM_Utils_Type::T_STRING,
  );
}

/**
 * Klant.Create API
 *
 * @param array $params
 * @return array API result descriptor
 * @see civicrm_api3_create_success
 * @see civicrm_api3_create_error
 * @throws API_Exception
 */
function civicrm_api3_klant_Create($params) {
  $klant = new CRM_Basis_Klant();
  $returnValues = $klant->create($params);
  return civicrm_api3_create_success($returnValues, $params, 'Klant', 'Create');
}
