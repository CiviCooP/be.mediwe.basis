<?php

/**
 * MedischeControle.Get API specification (optional)
 * This is used for documentation and validation.
 *
 * @param array $spec description of fields supported by this API call
 * @return void
 * @see http://wiki.civicrm.org/confluence/display/CRMDOC/API+Architecture+Standards
 */
function _civicrm_api3_medische_controle_Get_spec(&$spec) {
  $spec['klant_naam'] = array(
    'name' => 'klant_naam',
    'title' => 'Naam van de klant',
    'description' => 'Naam van de klant',
    'api.required' => 0,
    'type' => CRM_Utils_Type::T_STRING,
  );
  $spec['klant_id'] = array(
    'name' => 'klant_id',
    'title' => 'Contact ID van de klant',
    'description' => 'Contact ID van de klant',
    'api.required' => 0,
    'type' => CRM_Utils_Type::T_STRING,
  );
  $spec['klant_external_identifier'] = array(
    'name' => 'klant_external_identifier',
    'title' => 'External ID van de klant',
    'description' => 'External ID van de klant',
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
  $spec['mf_bestelnummer'] = array(
    'name' => 'mf_bestelnummer',
    'title' => 'Bestelnummer',
    'description' => 'Bestelnummer',
    'api.required' => 0,
    'type' => CRM_Utils_Type::T_STRING,
  );
  $spec['voornaam_medewerker'] = array(
    'name' => 'voornaam',
    'title' => 'Voornaam van de medewerker',
    'description' => 'Voornaam van de te controleren medewerker',
    'api.required' => 0,
    'type' => CRM_Utils_Type::T_STRING,
  );
  $spec['achternaam_medewerker'] = array(
    'name' => 'achternaam_medewerker',
    'title' => 'Achternaam van de medewerker',
    'description' => 'Achternaam van de te controleren medewerker',
    'api.required' => 0,
    'type' => CRM_Utils_Type::T_STRING,
  );
  $spec['mkm_personeelsnummer'] = array(
    'name' => 'mkm_personeelsnummer',
    'title' => 'Personeelsnummer medewerker',
    'description' => 'Personeelsnummer van de te controleren medewerker',
    'api.required' => 0,
    'type' => CRM_Utils_Type::T_STRING,
  );
  $spec['mkm_rijksregister_nummer'] = array(
    'name' => 'mkm_rijksregister_nummer',
    'title' => 'Rijksregisternummer',
    'description' => 'Rijksregisternummer',
    'api.required' => 0,
    'type' => CRM_Utils_Type::T_STRING,
  );
  $spec['medewerker_external_identifier'] = array(
    'name' => 'medewerker_external_identifier',
    'title' => 'External ID medewerker',
    'description' => 'External ID van de medewerker',
    'api.required' => 0,
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
  $spec['woonadres'] = array(
    'name' => 'woonadres',
    'title' => 'Woonadres medewerker',
    'description' => 'Woonadres (straat en huisnummer) van de medewerker',
    'api.required' => 0,
    'type' => CRM_Utils_Type::T_STRING,
  );
  $spec['woonpostcode'] = array(
    'name' => 'woonpostcode',
    'title' => 'Woonpostcode medewerker',
    'description' => 'Woonpostcode van de medewerker',
    'api.required' => 0,
    'type' => CRM_Utils_Type::T_STRING,
  );
  $spec['woongemeente'] = array(
    'name' => 'woongemeente',
    'title' => 'Woongemeente medewerker',
    'description' => 'Woongemeente van de medewerker',
    'api.required' => 0,
    'type' => CRM_Utils_Type::T_STRING,
  );
  $spec['woonland'] = array(
    'name' => 'woonland',
    'title' => 'Woonland medewerker',
    'description' => 'Woonland (iso code) van de medewerker',
    'api.required' => 0,
    'type' => CRM_Utils_Type::T_STRING,
  );
  $spec['email_medewerker'] = array(
    'name' => 'email_medewerker',
    'title' => 'E-mailadres medewerker',
    'description' => 'E-mailadres medewerker',
    'api.required' => 0,
    'type' => CRM_Utils_Type::T_STRING,
  );
  $spec['telefoon_medewerker'] = array(
    'name' => 'telefoon_medewerker',
    'title' => 'Telefoon medewerker',
    'description' => 'Telefoon medewerker',
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
}

/**
 * MedischeControle.Get API
 *
 * @param array $params
 * @return array API result descriptor
 * @see civicrm_api3_create_success
 * @see civicrm_api3_create_error
 * @throws API_Exception
 */
function civicrm_api3_medische_controle_Get($params) {
  $medischeControle = new CRM_Basis_MedischeControle();
  $returnValues = $medischeControle->get($params);
  return civicrm_api3_create_success($returnValues, $params, 'MedischeControle', 'Get');
}
