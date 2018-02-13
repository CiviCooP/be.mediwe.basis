<?php
/**
 * Created by PhpStorm.
 * User: CDeman
 * Date: 20/07/2017
 * Time: 23:46
 */
function _civicrm_api3_controle_arts_Update_spec(&$spec) {
  $spec['id'] = array(
    'name' => 'id',
    'title' => 'contact id',
    'description' => 'ContactID van de controle arts',
    'api.required' => 1,
    'type' => CRM_Utils_Type::T_INT,
  );
  $spec['organization_name'] = array(
    'name' => 'organization_name',
    'title' => 'naam controle arts',
    'description' => 'naam controle arts',
    'api.required' => 0,
    'type' => CRM_Utils_Type::T_STRING,
  );
  $spec['external_identifier'] = array(
    'name' => 'external_identifier',
    'title' => 'externe id',
    'description' => 'externe id van de controle arts',
    'api.required' => 0,
    'type' => CRM_Utils_Type::T_STRING,
  );
  $spec['legal_name'] = array(
    'name' => 'legal_name',
    'title' => 'officiële wettelijke naam',
    'description' => 'officiële wettelijke naam',
    'api.required' => 0,
    'type' => CRM_Utils_Type::T_STRING,
  );
  $spec['ml_aansluitingsnummer'] = array(
    'name' => 'ml_aansluitingsnummer',
    'title' => 'aansluitingsnummer',
    'description' => 'Aansluitingsnummer',
    'api.required' => 0,
    'type' => CRM_Utils_Type::T_STRING,
  );
  $spec['ml_venice'] = array(
    'name' => 'ml_venice',
    'title' => 'venice nummer',
    'description' => 'Leveranciersnummer in Venice',
    'api.required' => 0,
    'type' => CRM_Utils_Type::T_STRING,
  );
  $spec['ml_btw_nummer'] = array(
    'name' => 'ml_btw_nummer',
    'title' => 'btw nummer',
    'description' => 'BTW nummer',
    'api.required' => 0,
    'type' => CRM_Utils_Type::T_STRING,
  );
  $spec['ml_btw_plichtig'] = array(
    'name' => 'ml_btw_plichtig',
    'title' => 'btw plichtig',
    'description' => 'BTW plichtig?',
    'api.required' => 0,
    'type' => CRM_Utils_Type::T_BOOLEAN,
  );
  $spec['ml_iban'] = array(
    'name' => 'ml_iban',
    'title' => 'iban',
    'description' => 'IBAN rekeningnummer',
    'api.required' => 0,
    'type' => CRM_Utils_Type::T_STRING,
  );
  $spec['ml_bestelnummer'] = array(
    'name' => 'ml_bestelnummer',
    'title' => 'bestel nummer',
    'description' => 'Bestel nummer',
    'api.required' => 0,
    'type' => CRM_Utils_Type::T_STRING,
  );
  $spec['ml_csv_toevoegen'] = array(
    'name' => 'ml_csv_toevoegen',
    'title' => 'csv toevoegen aan factuur',
    'description' => 'CSV toevoegen aan factuur?',
    'api.required' => 0,
    'type' => CRM_Utils_Type::T_BOOLEAN,
  );
  $spec['mcc_arts_riziv'] = array(
    'name' => 'mcc_arts_riziv',
    'title' => 'riziv nummer',
    'description' => 'RIZIV nummer',
    'api.required' => 0,
    'type' => CRM_Utils_Type::T_STRING,
  );
  $spec['mcc_arts_gebruikt_app'] = array(
    'name' => 'mcc_arts_gebruikt_app',
    'title' => 'gebruikt app',
    'description' => 'Gebruikt de app?',
    'api.required' => 0,
    'type' => CRM_Utils_Type::T_BOOLEAN,
  );
  $spec['mcc_arts_bel_moment'] = array(
    'name' => 'mcc_arts_bel_moment',
    'title' => 'bel moment',
    'description' => 'Moment waarop de controle arts gebeld wil worden',
    'api.required' => 0,
    'type' => CRM_Utils_Type::T_STRING,
  );
  $spec['mcc_arts_opdracht'] = array(
    'name' => 'mcc_arts_opdracht',
    'title' => 'wijze opdracht bevestiging',
    'description' => 'Wijze waarop eventuele opdrachtbevestiging gestuurd moet worden',
    'api.required' => 0,
    'type' => CRM_Utils_Type::T_STRING,
  );
  $spec['mcc_arts_overzicht'] = array(
    'name' => 'mcc_arts_overzicht',
    'title' => 'overzicht op de middag',
    'description' => 'Overzicht op de middag?',
    'api.required' => 0,
    'type' => CRM_Utils_Type::T_BOOLEAN,
  );
  $spec['mcc_arts_opmerkingen'] = array(
    'name' => 'mcc_arts_opmerkingen',
    'title' => 'opmerkingen',
    'description' => 'Opmerkingen',
    'api.required' => 0,
    'type' => CRM_Utils_Type::T_TEXT,
  );
  $spec['mvp_vakantie_van'] = array(
    'name' => 'mvp_vakantie_van',
    'title' => 'vakantieperiode van datum',
    'description' => 'Vakantieperiode - datum van',
    'api.required' => 0,
    'type' => CRM_Utils_Type::T_DATE,
  );
  $spec['mvp_vakantie_tot'] = array(
    'name' => 'mvp_vakantie_tot',
    'title' => 'vakantieperiode tot datum',
    'description' => 'Vakantieperiode - datum tot',
    'api.required' => 0,
    'type' => CRM_Utils_Type::T_DATE,
  );
  $spec['mw_postcode'] = array(
    'name' => 'mw_postcode',
    'title' => 'werkgebied postcode',
    'description' => 'Werkgebied postcode',
    'api.required' => 0,
    'type' => CRM_Utils_Type::T_STRING,
  );
  $spec['mw_gemeente'] = array(
    'name' => 'mw_gemeente',
    'title' => 'werkgebied gemeente',
    'description' => 'Werkgebied gemeente',
    'api.required' => 0,
    'type' => CRM_Utils_Type::T_STRING,
  );
  $spec['mw_prioriteit'] = array(
    'name' => 'mw_prioriteit',
    'title' => 'werkgebied prioriteit',
    'description' => 'Werkgebied prioriteit',
    'api.required' => 0,
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
 */
function civicrm_api3_controle_arts_Update($params) {
  $controlearts = new CRM_Basis_ControleArts();
  $returnValues = $controlearts->update($params);
  return civicrm_api3_create_success($returnValues, $params, 'ControleArts', 'Update');
}
