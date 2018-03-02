<?php

/**
 * Class to process MedischeControle in Mediwe
 *
 * @author Erik Hommel (CiviCooP) <erik.hommel@civicoop.org>
 * @author Klaas Eikelboom (CiviCooP) <klaas.eikelboom@civicoop.org>
 * @author Christophe Deman <christophe.deman@mediwe.be>
 * @date 22 Feb 2018
 * @license AGPL-3.0
 */
class CRM_Basis_MedischeControle {

  private $_todaysDate = NULL;

  /**
   * CRM_Basis_Klant constructor.
   */
  public function __construct() {
    $this->_todaysDate = new DateTime();
  }

  /**
   * Method valideert de params voor de create. Het gaat hierbij om de velden
   * waarmee zowel een klant als een medewerjer gevonden kan worden.
   *
   * @ param $params
   */
  public function validateCreate($params) {
    /* validatie van de klant gegevens */
    if ((isset($params['mf_btw_nummer']) && strlen($params['mf_btw_nummer']) >= 10)
      || isset($params['klant_id'])
      || isset($params['klant_external_identifier'])
      || isset($params['klant_naam'])
    ) {
       // een van de velden is kennelijk gevuld - we hoeven niets te doen
    }
    else {
      throw new Exception("Verplicht BTW nr of Klant Id of Klant external identifier if Klant naam");
    }

    /* validatie van de medewerkers gegevens */

    if ((isset($params['voornaam_medewerker']) && isset($params['achternaam_medewerker']))
      || isset($params['medewerker_id'])
      || isset($params['medewerker_external_identifier'])
    ) {
      // een van de velden is kennelijk gevuld - we hoeven niets te doen
    }
    else {
      throw new Exception("Verplicht Medewerker Id of Medewerker external identifier of Voor en Achternaam");
    }
  }


  /**
   * Method om medische controle toe te voegen
   *
   * @param $params
   * @return array|bool
   */
  public function create($params) {
    $this->validateCreate($params);

    // ensure mandatory data
    if (!isset($params['mmc_controle_datum'])) {
      CRM_Core_Error::createError(ts('Controledatum ziekte ontbreekt in ' . __METHOD__));
      return FALSE;
    }
    $params['case_type_id'] = CRM_Basis_Config::singleton()->getmedischeControleCaseType()['id'];
    if (isset($params['id'])) {
      return $this->update($params);
    }
    else {
      if ($this->exists($params)) {
        CRM_Core_Error::createError(ts('Probeert medische controle toe te voegen die al bestaat in ' . __METHOD__));
        return FALSE;
      }
      else {
        return $this->saveMedischeControle($params);
      }
    }
  }

  /**
   * Method om medische controle bij te werken
   *
   * @param $params
   * @return array|bool
   */
  public function update($params) {
    if (!$params['id']) {
      CRM_Core_Error::debug_log_message(ts('Poging medische controle dossier bij te werken zonder een ID mee te geven in ') > __METHOD__);
      return FALSE;
    }
    return $this->saveMedischeControle($params);
  }

  /**
   * Method om te checken of medische controle al bestaat
   *
   * @param $params
   * @return bool
   */
  public function exists($params) {
    if (isset($params['id'])) {
      return $params['id'];
    }
    else {
      if (isset($params['medewerker_id']) && isset($params['mcc_controle_datum'])) {
        $controleDatum = new DateTime($params['mcc_controle_datum']);
        $sql = "SELECT ca.id FROM civicrm_case ca INNER JOIN civicrm_case_contact cc ON ca.id = cc.case_id
        WHERE ca.case_type_id = %1 AND ca.is_deleted = %2 AND cc.contact_id = %3 AND ca.start_date = %4";
        return CRM_Core_DAO::singleValueQuery($sql, array(
          1 => array(CRM_Basis_Config::singleton()->getmedischeControleCaseType()['id'], 'Integer'),
          2 => array(0, 'Integer'),
          3 => array($params['medewerker_id'], 'Integer'),
          4 => array($controleDatum->format('Ymd'), 'String'),
        ));
      }
    }
    return FALSE;
  }

  /**
   * Method om medische controles op te halen
   *
   * @param $params
   * @return array
   */
  public function get($params) {
    $medischeControles = array();
    try {
      $medischeControles = civicrm_api3('Case', 'get', $params)['values'];
      // custom velden ophalen
    }
    catch (CiviCRM_API3_Exception $ex) {
    }
    return $medischeControles;
  }

  /**
   * Method om medische controle met id te verwijderen
   *
   * @param $medischeControleId
   * @return array|bool
   */
  public function deleteWithId($medischeControleId) {
    if (!isset($params['id'])) {
      CRM_Core_Error::debug_log_message(ts('Poging om medische controle te verwijderen zonder id in ') . __METHOD__);
      return FALSE;
    }
    try {
      civicrm_api3('Case', 'delete', array('id' => $medischeControleId));
    }
    catch (CiviCRM_API3_Exception $ex) {
      CRM_Core_Error::debug_log_message(ts('Fout bij verwijderen medische controle dossier met ID ') . $medischeControleId . ts(' in ') . __METHOD__);
      return FALSE;
    }
  }

  /**
   * zoekt een klantmedewerker op of maakt hem aan en zet he
   *
   * @param $params
   * @return integer contact id van de opgezochte of aangemaakte medewerker
   */
  public function haalOfMaakKlantMedewerker($params) {
    /* haal strategie : heb je een medewerkers id dan is dat het contactId
       zo niet zoek dan met medewerker_external_identifier
       we zoeken niet met eerst en laatste naam (dat is wellicht te weinig identificerend)

       als de zoektocht niets oplevert dan wordt de medewerker aangemaakt.
       eventuele meegeleverde medewerker_id en external_identifier worden
       toegevoegd.
    */

    if(isset($params['medewerker_id'])){
      /* verzeker dat het om een valide medewerks id gaat */
      civicrm_api3('KlantMedewerker','getsingle',array(
        'id' => $params['medewerker_id'],
      ));
      return $params['medewerker_id'];
    }

    if(isset($params['medewerker_external_identifier'])){

      try {
        $contactId = civicrm_api3('KlantMedewerker', 'getsingle', [
          'external_identifier' => $params['medewerker_external_identifier'],
        ])['id'];
      }
      catch (Exception $ex){

      }
    }

    if(isset($contactId)){
      return $contactId;
    }

    // op dit punt is het duidelijk dat de medewerker niet te vinden is
    // hij moet aangemaakt worden

    $result = civicrm_api3('KlantMedewerker', 'create',array(
      'first_name' => $params['voornaam_medewerker'],
      'last_name'  => $params['achternaam_medewerker']
    ) + $params);

    if($result['is_error']){
      throw new CiviCRM_API3_Exception($result['message']);
    }
    else {
      return $result['id'];
    }
  }

  /**
   * Method om medische controle op te slaan
   *
   * @param $data
   * @return mixed
   * @throws API_Exception
   */
  private function saveMedischeControle($data) {
    $config = CRM_Basis_Config::singleton();
    $params = array();
    foreach ($data as $key => $value) {
      if ($value) {
        $params[$key] = $value;
      }
    }
    // maak eventueel ook een ziektemelding aan
    // haal of maak de klant
    $params['contact_id'] = $this->haalOfMaakKlantMedewerker($params);
    try {
      $params['subject'] = $this->setDefaultSubject($params['contact_id'], $params['mmc_controle_datum']);
      $params['start_date'] = $params['mmc_controle_datum'];
      if (isset($params['end_date'])) {
        unset($params['end_date']);
      }
      // To Do - voor het aanmaken van een Case is een creator_id nodig
      // weet niet precies wat hij doet maar in geval van twijfel is team mediwe een goede kandidaat
      $createdCase = civicrm_api3('Case', 'create', $params+array(
        'creator_id' => $config->getMediweTeamContactId(),
        'sequential' => 1
        ));
      // custom data voor medische controle opslaan
      return $createdCase['values'][0];
    }
    catch (CiviCRM_API3_Exception $ex) {
      throw new API_Exception(ts('Could not create a contact in ' . __METHOD__
         . ', contact your system administrator! Error from API Contact create: ' . $ex->getMessage()));
    }
  }

  /**
   * Method om default subject voor case samen te stellen
   *
   * @param $medewerkerId
   * @param $controleDatum
   * @return string
   */
  private function setDefaultSubject($medewerkerId, $controleDatum) {
    $subject = "Medische controle";
    if (!empty($medewerkerId)) {
      try {
        $name = civicrm_api3('Contact', 'getvalue', array(
          'id' => $medewerkerId,
          'return' => 'display_name',
        ));
        $subject .= ' van ' . (string) $name;
      }
      catch (CiviCRM_API3_Exception $ex) {
      }
    }
    if (!empty($controleDatum)) {
      $datum = new DateTime($controleDatum);
      $subject .= ' op ' . $datum->format('d-m-Y');
    }
    return $subject;
  }

}
