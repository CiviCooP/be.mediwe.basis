<?php

/**
 * KlantMedewerker.Create API specification (optional)
 * This is used for documentation and validation.
 *
 * @param array $spec description of fields supported by this API call
 * @return void
 * @see http://wiki.civicrm.org/confluence/display/CRMDOC/API+Architecture+Standards
 */
function _civicrm_api3_klant_medewerker_Create_spec(&$spec) {
  $spec['first_name'] = array(
    'name' => 'first_name',
    'title' => 'Voornaam',
    'description' => 'Voornaam',
    'api.required' => 1,
    'type' => CRM_Utils_Type::T_STRING,
  );
  $spec['last_name'] = array(
    'name' => 'last_name',
    'title' => 'Achternaam',
    'description' => 'Achternaam',
    'api.required' => 1,
    'type' => CRM_Utils_Type::T_STRING,
  );
  $spec['klant_id'] = array(
    'name' => 'klant_id',
    'title' => 'Klant ID',
    'description' => 'Klant ID',
    'api.required' => 1,
    'type' => CRM_Utils_Type::T_INT,
  );
  $spec['prefix_id'] = array(
    'name' => 'prefix_id',
    'title' => 'Voorvoegsel ID',
    'description' => 'Voorvoegsel ID',
    'api.required' => 0,
    'type' => CRM_Utils_Type::T_INT,
  );
  $spec['birth_date'] = array(
    'name' => 'birth_date',
    'title' => 'Geboortedatum',
    'description' => 'Geboortedatum (dd-mm-jjjj)',
    'api.required' => 0,
    'type' => CRM_Utils_Type::T_DATE,
  );
  $spec['email'] = array(
    'name' => 'email',
    'title' => 'E-mailadres',
    'description' => 'E-mailadres',
    'api.required' => 0,
    'type' => CRM_Utils_Type::T_STRING,
  );
  $spec['phone'] = array(
    'name' => 'phone',
    'title' => 'Telefoon (vast)',
    'description' => 'Telefoon (vast)',
    'api.required' => 0,
    'type' => CRM_Utils_Type::T_STRING,
  );
  $spec['mobile'] = array(
    'name' => 'mobile',
    'title' => 'Mobiel',
    'description' => 'Mobiel',
    'api.required' => 0,
    'type' => CRM_Utils_Type::T_STRING,
  );
  $spec['gender_id'] = array(
    'name' => 'gender_id',
    'title' => 'Geslacht ID',
    'description' => 'Geslacht ID',
    'api.required' => 0,
    'type' => CRM_Utils_Type::T_INT,
  );
  $spec['job_title'] = array(
    'name' => 'job_title',
    'title' => 'Functie omschrijving',
    'description' => 'Functie omschrijving',
    'api.required' => 0,
    'type' => CRM_Utils_Type::T_STRING,
  );
  $spec['nick_name'] = array(
    'name' => 'nick_name',
    'title' => 'Roepnaam',
    'description' => 'Roepnaam',
    'api.required' => 0,
    'type' => CRM_Utils_Type::T_STRING,
  );
  $spec['external_identifier'] = array(
    'name' => 'external_identifier',
    'title' => 'Extern ID',
    'description' => 'Extern ID',
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
  $spec['mkm_personeelsnummer'] = array(
    'name' => 'mkm_personeelsnummer',
    'title' => 'Personeelsnummer',
    'description' => 'Personeelsnummer',
    'api.required' => 0,
    'type' => CRM_Utils_Type::T_STRING,
  );
  $spec['mkm_partner'] = array(
    'name' => 'mkm_partner',
    'title' => 'Naam partner',
    'description' => 'Naam partner',
    'api.required' => 0,
    'type' => CRM_Utils_Type::T_STRING,
  );
  $spec['mkm_niveau1'] = array(
    'name' => 'mkm_niveau1',
    'title' => 'Medewerker eerste niveau',
    'description' => 'Medewerker eerste niveau',
    'api.required' => 0,
    'type' => CRM_Utils_Type::T_STRING,
  );
  $spec['mkm_code_niveau2'] = array(
    'name' => 'mkm_code_niveau2',
    'title' => 'Code medewerker tweede niveau',
    'description' => 'Code medewerker tweede niveau',
    'api.required' => 0,
    'type' => CRM_Utils_Type::T_STRING,
  );
  $spec['mkm_niveau2'] = array(
    'name' => 'mkm_niveau2',
    'title' => 'Medewerker tweede niveau',
    'description' => 'Medewerker tweede niveau',
    'api.required' => 0,
    'type' => CRM_Utils_Type::T_STRING,
  );
  $spec['mkm_niveau3'] = array(
    'name' => 'mkm_niveau3',
    'title' => 'Medewerker derde niveau',
    'description' => 'Medewerker derde niveau',
    'api.required' => 0,
    'type' => CRM_Utils_Type::T_STRING,
  );
  $spec['mkm_functie'] = array(
    'name' => 'mkm_functie',
    'title' => 'Functie',
    'description' => 'Functie',
    'api.required' => 0,
    'type' => CRM_Utils_Type::T_STRING,
  );
  $spec['mkm_statuut'] = array(
    'name' => 'mkm_statuut',
    'title' => 'Statuut',
    'description' => 'Statuut',
    'api.required' => 0,
    'type' => CRM_Utils_Type::T_STRING,
  );
  $spec['mkm_contract'] = array(
    'name' => 'mkm_contract',
    'title' => 'Contract',
    'description' => 'Contract',
    'api.required' => 0,
    'type' => CRM_Utils_Type::T_STRING,
  );
  $spec['mkm_contract_omschrijving'] = array(
    'name' => 'mkm_contract_omschrijving',
    'title' => 'Omschrijving contract',
    'description' => 'Omschrijving contract',
    'api.required' => 0,
    'type' => CRM_Utils_Type::T_STRING,
  );
  $spec['mkm_ploegensysteem'] = array(
    'name' => 'mkm_ploegensysteem',
    'title' => 'Ploegensysteem',
    'description' => 'Ploegensysteem',
    'api.required' => 0,
    'type' => CRM_Utils_Type::T_STRING,
  );
  $spec['mkm_bezetting'] = array(
    'name' => 'mkm_bezetting',
    'title' => 'Bezetting',
    'description' => 'Bezetting',
    'api.required' => 0,
    'type' => CRM_Utils_Type::T_STRING,
  );
  $spec['mkm_kostenplaats'] = array(
    'name' => 'mkm_kostenplaats',
    'title' => 'Kostenplaats',
    'description' => 'Kostenplaats',
    'api.required' => 0,
    'type' => CRM_Utils_Type::T_STRING,
  );
  $spec['mkm_datum_in_dienst'] = array(
    'name' => 'mkm_datum_in_dienst',
    'title' => 'Datum in dienst',
    'description' => 'Datum in dienst',
    'api.required' => 0,
    'type' => CRM_Utils_Type::T_DATE,
  );
  $spec['mkm_datum_uit_dienst'] = array(
    'name' => 'mkm_datum_uit_dienst',
    'title' => 'Datum uit dienst',
    'description' => 'Datum uit dienst',
    'api.required' => 0,
    'type' => CRM_Utils_Type::T_DATE,
  );
  $spec['mkm_opmerkingen'] = array(
    'name' => 'mkm_opmerkingen',
    'title' => 'Opmerkingen medewerker',
    'description' => 'Opmerkingen medewerker',
    'api.required' => 0,
    'type' => CRM_Utils_Type::T_TEXT,
  );
  $spec['mkm_vrij_veld1'] = array(
    'name' => 'mkm_vrij_veld1',
    'title' => 'Vrij veld medewerker',
    'description' => 'Vrij veld medewerker',
    'api.required' => 0,
    'type' => CRM_Utils_Type::T_TEXT,
  );
  $spec['mkm_is_controlevrij'] = array(
    'name' => 'mkm_is_controlevrij',
    'title' => 'Is controlevrij',
    'description' => 'Is controlevrij?',
    'api.required' => 0,
    'type' => CRM_Utils_Type::T_BOOLEAN,
  );
  $spec['mkm_controlevrij_tot'] = array(
    'name' => 'mkm_controlevrij_tot',
    'title' => 'Is controlevrij tot',
    'description' => 'Is controlevrij tot',
    'api.required' => 0,
    'type' => CRM_Utils_Type::T_DATE,
  );
  $spec['mkm_steeds_aangewezen'] = array(
    'name' => 'mkm_steeds_aangewezen',
    'title' => 'Controle steeds aangewezen',
    'description' => 'Controle steeds aangewezen?',
    'api.required' => 0,
    'type' => CRM_Utils_Type::T_BOOLEAN,
  );
  $spec['mkm_aangewezen_tot'] = array(
    'name' => 'mkm_aangewezen_tot',
    'title' => 'Controle steeds aangewezen tot',
    'description' => 'Controle steeds aangewezen tot',
    'api.required' => 0,
    'type' => CRM_Utils_Type::T_DATE,
  );
  $spec['mte_periode'] = array(
    'name' => 'mte_periode',
    'title' => 'Expert teller periode',
    'description' => 'Expert teller periode',
    'api.required' => 0,
    'type' => CRM_Utils_Type::T_STRING,
  );
  $spec['mte_ziekteperiodes'] = array(
    'name' => 'mte_ziekteperiodes',
    'title' => 'Aantal ziekteperiodes',
    'description' => 'Aantal ziekteperiodes',
    'api.required' => 0,
    'type' => CRM_Utils_Type::T_INT,
  );
  $spec['mte_maandag_ziektes'] = array(
    'name' => 'mte_maandag_ziektes',
    'title' => 'Aantal ziektes maandag',
    'description' => 'Aantal ziektes op maandag',
    'api.required' => 0,
    'type' => CRM_Utils_Type::T_INT,
  );
  $spec['mte_aantal_ziektedagen'] = array(
    'name' => 'mte_aantal_ziektedagen',
    'title' => 'Aantal ziektedagen',
    'description' => 'Aantal ziektedagen',
    'api.required' => 0,
    'type' => CRM_Utils_Type::T_INT,
  );
  $spec['mte_bradford'] = array(
    'name' => 'mte_bradford',
    'title' => 'bradford',
    'description' => 'bradford',
    'api.required' => 0,
    'type' => CRM_Utils_Type::T_INT,
  );
  $spec['mte_ziekteperiodes_zonder_attest'] = array(
    'name' => 'mte_ziekteperiodes_zonder_attest',
    'title' => 'Aantal zonder attest',
    'description' => 'Aantal ziekteperiodes zonder attest',
    'api.required' => 0,
    'type' => CRM_Utils_Type::T_INT,
  );
  $spec['mte_korte_ziekteperiodes'] = array(
    'name' => 'mte_korte_ziekteperiodes',
    'title' => 'Aantal korte ziekteperiodes',
    'description' => 'Aantal korte ziekteperiodes',
    'api.required' => 0,
    'type' => CRM_Utils_Type::T_INT,
  );
  $spec['mte_ziekteverzuim_percentage'] = array(
    'name' => 'mte_ziekteverzuim_percentage',
    'title' => 'Ziekteverzuim percentage',
    'description' => 'Ziekteverzuim percentage',
    'api.required' => 0,
    'type' => CRM_Utils_Type::T_FLOAT,
  );
}

/**
 * KlantMedewerker.Create API
 *
 * @param array $params
 * @return array API result descriptor
 * @see civicrm_api3_create_success
 * @see civicrm_api3_create_error
 * @throws API_Exception
 */
function civicrm_api3_klant_medewerker_Create($params) {
  $klantMedewerker = new CRM_Basis_KlantMedewerker();
  $returnValues = $klantMedewerker->create($params);
  return civicrm_api3_create_success($returnValues, $params, 'KlantMedewerker', 'Create');
}
