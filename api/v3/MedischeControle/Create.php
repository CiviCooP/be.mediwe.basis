<?php

/**
 * MedischeControle.Create API specification (optional)
 * This is used for documentation and validation.
 *
 * @param array $spec description of fields supported by this API call
 * @return void
 * @see http://wiki.civicrm.org/confluence/display/CRMDOC/API+Architecture+Standards
 */
function _civicrm_api3_medische_controle_Create_spec(&$spec) {
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
  $spec['klant_adres'] = array(
    'name' => 'klant_adres',
    'title' => 'Klantadres',
    'description' => 'Klantadres (straat en huisnummer)',
    'api.required' => 0,
    'type' => CRM_Utils_Type::T_STRING,
  );
  $spec['klant_postcode'] = array(
    'name' => 'klant_postcode',
    'title' => 'Klant postcode',
    'description' => 'Klant postcode',
    'api.required' => 0,
    'type' => CRM_Utils_Type::T_STRING,
  );
  $spec['klant_gemeente'] = array(
    'name' => 'klant_gemeente',
    'title' => 'Klant gemeente',
    'description' => 'Klant gemeente',
    'api.required' => 0,
    'type' => CRM_Utils_Type::T_STRING,
  );
  $spec['klant_land'] = array(
    'name' => 'klant_land',
    'title' => 'Klant land',
    'description' => 'Klant land (iso code)',
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
  $spec['job_title'] = array(
    'name' => 'job_title',
    'title' => 'Functie medewerker',
    'description' => 'Functie van de medewerker',
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
  $spec['voornaam_contactpersoon'] = array(
    'name' => 'voornaam_contactpersoon',
    'title' => 'Voornaam contactpersoon klant',
    'description' => 'Voornaam contactpersoon bij de klant',
    'api.required' => 0,
    'type' => CRM_Utils_Type::T_STRING,
  );
  $spec['achternaam_contactpersoon'] = array(
    'name' => 'achternaam_contactpersoon',
    'title' => 'Achternaam contactpersoon klant',
    'description' => 'Achternaam contactpersoon bij de klant',
    'api.required' => 1,
    'type' => CRM_Utils_Type::T_STRING,
  );
  $spec['telefoon_contactpersoon'] = array(
    'name' => 'telefoon_contactpersoon',
    'title' => 'Telefoon contactpersoon klant',
    'description' => 'Telefoon contactpersoon bij de klant',
    'api.required' => 0,
    'type' => CRM_Utils_Type::T_STRING,
  );
  $spec['email_contactpersoon'] = array(
    'name' => 'email_contactpersoon',
    'title' => 'E-mailadres contactpersoon klant',
    'description' => 'E-mailadres contactpersoon bij de klant',
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
    'api.required' => 1,
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
 * MedischeControle.Create API
 *
 * @param array $params
 * @return array API result descriptor
 * @see civicrm_api3_create_success
 * @see civicrm_api3_create_error
 * @throws API_Exception
 */
function civicrm_api3_medische_controle_Create($params) {
  $medischeControle = new CRM_Basis_MedischeControle();
  $created = $medischeControle->create($params);
  return array(
    'is_error' => 0,
    'version' => 3,
    'count' => 1,
    'id' => $created['id'],
    'values' => $created,
  );
}
