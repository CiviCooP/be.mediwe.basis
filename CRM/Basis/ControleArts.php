<?php

/**
 * Class to process ControleArts in Mediwe
 *
 * @author Erik Hommel (CiviCooP) <erik.hommel@civicoop.org>
 * @date 31 May 2017
 * @license AGPL-3.0
 */
class CRM_Basis_ControleArts {

  private $_controleArtsContactSubTypeName = NULL;

  /**
   * CRM_Basis_Klant method to migrate data from existing systems.
   */
  public function migrate($params) {
    set_time_limit(0);
    $this->migrateFromJoomla($params);
  }

  /**
   * CRM_Basis_ControleArts constructor.
   */
  public function __construct() {
    $config = CRM_Basis_Config::singleton();
    $contactSubType = $config->getControleArtsContactSubType();
    $this->_controleArtsContactSubTypeName = $contactSubType['name'];
  }

  /**
   * Method om data van voor te stellen artsen op te halen
   *
   * @param $params
   *
   * @return bool|array
   * @throws API_Exception als er foutieve parameters zijn
   */
  public function getVoorstel($params) {
    // verwerk alleen als valide parameters
    if ($this->validVoorstelParams($params)) {
      // zoek alle artsen binnen postcode, limit en peildatum
      $postcodeCustom = 'custom_' . CRM_Basis_Config::singleton()
          ->getPostcodeCustomField('id');
      $contactParams = [
        'options' => [
          'limit' => $params['limiet'],
        ],
        $postcodeCustom => $params['postcode'],
      ];
      try {
        $artsen = civicrm_api3('ControleArts', 'get', $contactParams);
        $result = $this->generateVoorstelArtsenData($artsen['values'], $params);
        // als huisbezoek_id meegegeven, bepaal afstand
      }
      catch (CiviCRM_API3_Exception $ex) {
      }
      return $result;
    }
    else {
      return FALSE;
    }
  }

  /**
   * Method om data voor voorstel artsen te ordenen
   *
   * @param array $artsen
   * @param array $params
   *
   * @return array
   */
  private function generateVoorstelArtsenData($artsen, $params) {
    $result = [];
    foreach ($artsen as $artsId => $artsData) {
      // als arts nu op vakantie mag hij achterwege blijven
      if (!isset($artsData['vakantie_periodes']) || !$this->isOpVakantie($artsData['vakantie_periodes'], $params['voorstel_datum'])) {
        $suggestie = [];
        $suggestie['contact_id'] = $artsData['id'];
        $suggestie['naam_arts'] = $artsData['display_name'];
        $suggestie['gebruikt_app'] = $artsData['custom_' . CRM_Basis_Config::singleton()
          ->getArtsGebruiktAppCustomField('id')];
        $suggestie['akkoord_percentage'] = $artsData['custom_' . CRM_Basis_Config::singleton()
          ->getArtsPercentageAkkoordCustomField('id')];
        $suggestie['bellen'] = $artsData['custom_' . CRM_Basis_Config::singleton()
          ->getArtsBelMomentCustomField('id')];
        $suggestie['opdracht_per'] = $artsData['custom_' . CRM_Basis_Config::singleton()
          ->getArtsOpdrachtPerCustomField('id')];
        $suggestie['overzicht_middag'] = $artsData['custom_' . CRM_Basis_Config::singleton()
          ->getArtsOverzichtCustomField('id')];
        if ($artsData['street_address']) {
          $suggestie['adres'] = $artsData['street_address'];
        }
        if ($artsData['postal_code']) {
          $suggestie['postal_code'] = $artsData['postal_code'];
        }
        if ($artsData['city']) {
          $suggestie['plaats'] = $artsData['city'];
        }
        if ($artsData['phone']) {
          $suggestie['telefoon'] = $artsData['phone'];
        }
        // communicatievoorkeuren label ophalen
        if ($artsData['preferred_communication_method']) {
          $suggestie['communicatie_voorkeur'] = CRM_Basis_Utils::getPreferredCommunicationLabels($artsData['preferred_communication_method']);
        }
        // berekenen aantal opdrachten vandaag voor arts
        $suggestie['huisbezoeken_vandaag'] = $this->getHuisbezoekenArtsOpPeilDatum($artsId, $params['voorstel_datum']);
        // afstand toevoegen als huisbezoek_id bekend
        if ($params['huisbezoek_id']) {
          $afstand = $this->getAfstandVoorHuisbezoek($params['huisbezoek_id'], $artsId, $artsData);
          if (!empty($afstand)) {
            $suggestie['km'] = $afstand['km'];
            $suggestie['afstand_tijd'] = $afstand['tijd'];
          }
        }
        // toevoegen vakantie periodes
        if (isset($artsData['vakantie_periodes'])) {
          foreach ($artsData['vakantie_periodes'] as $vakantiePeriode) {
            $suggestie['vakantie_periodes'][] = [
              'datum_van' => $vakantiePeriode[CRM_Basis_Config::singleton()
                ->getVakantieVanCustomField('name')],
              'datum_tot' => $vakantiePeriode[CRM_Basis_Config::singleton()
                ->getVakantieTotCustomField('name')],
            ];
          }
        }
        $result[$artsId] = $suggestie;
      }
    }
    return $result;
  }

  /**
   * Method om afstand voor huisbezoek te bepalen
   *
   * @param $huisbezoekId
   * @param $artsId
   * @param array $artsData
   *
   * @return array
   */
  public function getAfstandVoorHuisbezoek($huisbezoekId, $artsId, $artsData = []) {
    $result = [];
    // fout als artsId en artsData leeg
    if (empty($artsId && empty($artsData))) {
      return $result;
    }
    // als artsData leeg, haal gegevens arts op met artsId
    if (empty($artsData)) {
      try {
        $artsData = civicrm_api3('Address', 'getsingle', [
          'return' => ["street_address", "city", "postal_code"],
          'contact_id' => $artsId,
          'is_primary' => 1,
        ]);

        // haal gegevens huisbezoek op met huisbezoekId
        $huisbezoek = civicrm_api3('Huisbezoek', 'getsingle', [
          'id' => $huisbezoekId,
        ]);

        $afstand = civicrm_api3('Google', 'afstand', array(
          'adres' => $huisbezoek['mh_huisbezoek_adres'],
          'postcode' => $huisbezoek['mh_huisbezoek_postcode'],
          'gemeente' => $huisbezoek['mh_huisbezoek_gemeente'],
          'adres_arts' => $artsData['street_address'],
          'postcode_arts' => $artsData['postal_code'],
          'gemeente_arts' => $artsData['city'],
        ));

        if (isset($afstand['values'])) {
          $result = $afstand['values'];
        }
      }
      catch (CiviCRM_API3_Exception $ex) {
      }
      return $result;
    }
    // haal gegevens huisbezoek op met huisbezoekId
    // bereken afstand
  }

  /**
   * Method om aantal huisbezoeken voor de arts op de peildatum te tellen
   *
   * @param $artsId
   * @param $peilDatum
   *
   * @return mixed
   */
  private function getHuisbezoekenArtsOpPeilDatum($artsId, $peilDatum) {
    if (!$peilDatum) {
      $peilDatum = new DateTime();
    }
    $huisbezoekActivityType = CRM_Basis_Config::singleton()
      ->getHuisbezoekActivityType();
    // tel het aantal actieve activiteiten van het type huisbezoek toegewezen aan de arts met datum is peildatum
    try {
      return civicrm_api3('Activity', 'getcount', [
        'activity_type_id' => $huisbezoekActivityType['value'],
        'assignee_contact_id' => $artsId,
        'is_deleted' => 0,
        'is_test' => 0,
        'is_current_revision' => 1,
        'activity_date_time' => [
          'BETWEEN' => [
            $peilDatum->format('Y-m-d') . ' 00:00:00',
            $peilDatum->format('Y-m-d') . ' 23:59:59',
          ],
        ],
      ]);
    }
    catch (CiviCRM_API3_Exception $ex) {
    }
    return 0;
  }


  /**
   * Method om te controleren of de arts op vakantie is.
   *
   * @param array $vakantiePeriodes (verwacht: id, mvp_vakantie_van (Y-m-d),
   *   mvp_vakantie_tot (Y-m-d) - bv. 1:2018-03-05:2018-03-07
   * @param $peilDatum
   * @param int $artsId
   *
   * @return bool
   */
  public function isOpVakantie($vakantiePeriodes = [], $peilDatum, $artsId = NULL) {
    // als artsId, vakantieperiodes van arts ophalen
    if (!empty($artsId) && empty($vakantiePeriodes)) {
      $vakantiePeriodes = $this->getVakantiePeriodesCustomFields($artsId);
    }
    // zeker stellen dat peildatum een DateTime object is
    if (!$peilDatum instanceof DateTime) {
      $peilDatum = new DateTime($peilDatum);
    }
    foreach ($vakantiePeriodes as $periodeId => $periodeData) {
      if ($periodeData['mvp_vakantie_van']) {
        $vanDatum = new DateTime($periodeData['mvp_vakantie_van']);
      }
      if ($periodeData['mvp_vakantie_tot']) {
        $totDatum = new DateTime($periodeData['mvp_vakantie_tot']);
      }
      if ($vanDatum && $totDatum) {
        if ($peilDatum >= $vanDatum && $peilDatum <= $totDatum) {
          return TRUE;
        }
      }
    }
    return FALSE;
  }

  /**
   * Method om te beoordelen of ik valide parameters for Voorstel heb, en deze
   * aan te vullen waar nodig
   *
   * @param $params
   *
   * @return bool
   * @throws API_Exception
   */
  private function validVoorstelParams(&$params) {
    // postcode is mandatory
    if (!isset($params['postcode']) || empty($params['postcode'])) {
      throw new API_Exception(ts('Kan geen postcode in parameters vinden') . ' in ' . __METHOD__, 0010);
    }
    // default limiet = 0
    if (!isset($params['limiet'])) {
      $params['limiet'] = 0;
    }
    // default voorstel datum is vandaag
    if (!isset($params['voorstel_datum']) || empty($params['voorstel_datum'])) {
      $params['voorstel_datum'] = new DateTime();
    }
    else {
      $params['voorstel_datum'] = new DateTime($params['voorstel_datum']);
    }
    return TRUE;
  }

  /**
   * Method to create a new controlearts
   *
   * @param $data
   *
   * @return array
   */
  public function create($data) {
    // ensure contact_type and contact_sub_type are set correctly
    $params = [
      'contact_type' => 'Organization',
      'contact_sub_type' => $this->_controleArtsContactSubTypeName,
    ];
    if (isset($data['id'])) {
      $params['id'] = $data['id'];
    }
    else {
      $params['organization_name'] = $data['organization_name'];
      $params['street_address'] = $data['street_address'];
      $params['postal_code'] = $data['postal_code'];
    }
    // if id is set, then update
    if (isset($data['id']) || $this->exists($params)) {
      $this->update($data);
    }
    else {
      return $this->saveControleArts($params, $data);
    }
  }

  /**
   * Method to update a controlearts
   *
   * @param $data
   *
   * @return array
   */
  public function update($data) {
    $controlearts = [];
    // ensure contact_type and contact_sub_type are set correctly
    $params = [
      'sequential' => 1,
      'contact_type' => 'Organization',
      'contact_sub_type' => $this->_controleArtsContactSubTypeName,
    ];
    if (isset($data['id'])) {
      $params['id'] = $data['id'];
    }
    else {
      $params['organization_name'] = $data['organization_name'];
      $params['street_address'] = $data['street_address'];
      $params['postal_code'] = $data['postal_code'];
    }
    $exists = $this->exists($params);
    if ($exists) {
      $params['id'] = $exists['contact_id'];
      return $this->saveControleArts($params, $data);
    }
    return $controlearts;
  }

  /**
   * Method to check if a controlearts exists
   *
   * @param $params
   *
   * @return bool
   */
  public function exists($params) {
    // ensure that contact sub type is set
    $params['contact_sub_type'] = $this->_controleArtsContactSubTypeName;
    try {
      $controleArts = civicrm_api3('Contact', 'getsingle', $params);
      return $controleArts;
    }
    catch (CiviCRM_API3_Exception $ex) {
      return FALSE;
    }
  }

  /**
   * Method to get all controleartss that meet the selection criteria based on
   * incoming $params
   *
   * @param $params
   *
   * @return array
   */
  public function get($params) {
    $controleArts = [];
    // ensure that contact sub type is set
    $params['contact_sub_type'] = $this->_controleArtsContactSubTypeName;
    $params['sequential'] = 1;
    // zet limiet indien ingevuld
    if (isset($params['limit'])) {
      $params['options'] = ['limit' => $params['limit']];
      unset($params['limit']);
    }
    try {
      $contacts = civicrm_api3('Contact', 'get', $params);
      $controleArts = $contacts['values'];
      $telefoon = new CRM_Basis_Telefoon();
      // standaard contact api geeft geen custom velden, dus extra functie om custom velden toe te voegen
      foreach ($controleArts as $controleArtsId => $controleArtsData) {
        $communicatie = $this->getCommunicatieCustomFields($controleArtsData['id']);
        if ($communicatie) {
          $controleArts[$controleArtsId] = array_merge($controleArts[$controleArtsId], $communicatie);
        }
        $vakanties['vakantie_periodes'] = CRM_Basis_RepeatingCustomData::get('mediwe_vakantie_periode', $controleArtsData['id']);
        if ($vakanties) {
          $controleArts[$controleArtsId] = array_merge($controleArts[$controleArtsId], $vakanties);
        }
        $werkgebieden['werkgebieden'] = CRM_Basis_RepeatingCustomData::get('mediwe_werkgebied', $controleArtsData['id']);
        if ($werkgebieden) {
          $controleArts[$controleArtsId] = array_merge($controleArts[$controleArtsId], $werkgebieden);
        }
        $mobiel = $telefoon->getMobielForContact($controleArtsData['id']);
        if ($mobiel) {
          $controleArts[$controleArtsId]['mobiel'] = $mobiel;
        }
      }
    }
    catch (CiviCRM_API3_Exception $ex) {
    }
    return $controleArts;
  }

  /**
   * Method om communicatie custom data te halen
   *
   * @param $artsId
   *
   * @return array
   */
  private function getCommunicatieCustomFields($artsId) {
    $result = [];
    if ($artsId) {
      $queryParams = [1 => [$artsId['id'], 'Integer']];
      $select = CRM_Basis_SingleCustomData::createCustomDataQuery(CRM_Basis_Config::singleton()
        ->getCommunicatieCustomGroup());
      if ($select) {
        $query = $select . ' WHERE entity_id = %1';
        $dao = CRM_Core_DAO::executeQuery($query, $queryParams);
        if ($dao->fetch()) {
          return CRM_Basis_Utils::moveDaoToArray($dao);
        }
      }
    }
    return $result;
  }

  /**
   * Method om vakantie periodes op te slaan
   *
   * @param int $contactId
   * @param $data
   */
  public function saveVakantiePeriodes($contactId, $data) {
    $vakantiePeriodeFields = CRM_Basis_Config::singleton()
      ->getCustomFieldByCustomGroupName('mediwe_vakantie_periode');
    // store in arrays if not arrays
    foreach ($vakantiePeriodeFields as $vakantiePeriodeFieldId => $vakantiePeriodeField) {
      if (isset($data[$vakantiePeriodeField['name']]) && !is_array($data[$vakantiePeriodeField['name']])) {
        $data[$vakantiePeriodeField['name']] = [$data[$vakantiePeriodeField['name']]];
      }
      if (isset($data[$vakantiePeriodeField['name']])) {
        $customData[$vakantiePeriodeField['name']] = $data[$vakantiePeriodeField['name']];
      }
      else {
        $customData[$vakantiePeriodeField['name']] = NULL;
      }
    }
    if ($customData) {
      CRM_Basis_RepeatingCustomData::save('mediwe_vakantie_periode', $contactId, $customData);
    }
  }

  /**
   * Method om werkgebieden op te slaan
   *
   * @param $contactId
   * @param $data
   */
  public function saveWerkgebieden($contactId, $data) {
    $werkgebiedFields = CRM_Basis_Config::singleton()
      ->getCustomFieldByCustomGroupName('mediwe_werkgebied');
    foreach ($werkgebiedFields as $werkgebiedFieldId => $werkgebiedField) {
      if (isset($data[$werkgebiedField['name']]) && !is_array($data[$werkgebiedField['name']])) {
        $data[$werkgebiedField['name']] = [$data[$werkgebiedField['name']]];
      }
      if (isset($data[$werkgebiedField['name']])) {
        $customData[$werkgebiedField['name']] = $data[$werkgebiedField['name']];
      }
      else {
        $customData[$werkgebiedField['name']] = NULL;
      }
    }
    if ($customData) {
      CRM_Basis_RepeatingCustomData::save('mediwe_werkgebied', $contactId, $customData);
    }
  }

  /**
   * Method om voorwaarden controlearts op te slaan (voorlopig alleen gebruikt
   * in migratie)
   *
   * @param $oldContactId
   * @param $contactId
   *
   * @return array
   * @throws CiviCRM_API3_Exception
   */
  public function saveVoorwaarden($oldContactId, $contactId) {
    $config = CRM_Basis_Config::singleton();
    $saveParams = [
      'membership_type_id' => $config->getArtsMembershipType()['id'],
      'contact_id' => $contactId,
    ];
    // get existing membership
    try {
      $membership = civicrm_api3('Membership', 'getsingle', $saveParams);
      if (isset($membership['id'])) {
        $saveParams['id'] = $membership['id'];
      }
    }
    catch (CiviCRM_API3_Exception $ex) {
    }
    $sql = "SELECT * FROM " . $config->getSourceCiviDbName() . ".migratie_controlearts_voorwaarden WHERE contact_id = %1";
    $dao = CRM_Core_DAO::executeQuery($sql, [1 => [$oldContactId, 'Integer']]);
    if ($dao->fetch()) {
      $params = CRM_Basis_Utils::moveDaoToArray($dao);
    }
    // convert names of custom fields
    $this->_addToParamsCustomFields($config->getArtsVoorwaardenCustomGroup('custom_fields'), $params, $saveParams);
    // create membership
    $createdMembership = civicrm_api3('Membership', 'create', $saveParams);
    return $createdMembership;
  }

  /**
   * Method to delete all medeWorkers from a klant with klantId (set to
   * is_deleted in CiviCRM)
   *
   * @param $controleArtsId
   *
   * @return array
   */
  public function deleteWithId($controleArtsId) {
    $controleArts = [];
    // ensure that contact sub type is set
    $params['contact_sub_type'] = $this->_controleArtsContactSubTypeName;
    $params['contact_id'] = $controleArtsId;
    try {
      if ($this->exists($params)) {
        $controleArts = civicrm_api3('Contact', 'delete', $params);
      }
    }
    catch (CiviCRM_API3_Exception $ex) {
      throw new API_Exception(ts('Could not delete a contact in ' . __METHOD__
        . ', contact your system administrator! Error from API Contact delete: ' . $ex->getMessage()));
    }
    return $controleArts;
  }

  /**
   * Method om controleArts op te slaan
   *
   * @param $params
   * @param $data
   *
   * @return array
   * @throws API_Exception
   */
  private function saveControleArts($params, $data) {
    // todo mag wel iets kleiner, wellicht adres, mail en phone in aparte methods
    $config = CRM_Basis_Config::singleton();
    foreach ($data as $key => $value) {
      $params[$key] = $value;
    }
    // rename klant custom fields for api  ($customFields, $data, &$params)
    $this->addToParamsCustomFields($config->getLeverancierCustomGroup('custom_fields'), $data, $params);
    $this->addToParamsCustomFields($config->getCommunicatieCustomGroup('custom_fields'), $data, $params);
    try {
      $createdContact = civicrm_api3('Contact', 'create', $params);
      $controleArts = $createdContact['values'][0];
      // process address fields
      $this->createAddress($controleArts['id'], $data);
      // process email fields
      if (isset($data['email']) && strlen($data['email']) > 5) {
        $this->createEmail($controleArts['id'], 'Billing', $data['email']);
        if (isset($data['email_Main'])) {
          $this->createEmail($controleArts['id'], 'Main', $data['email_Main']);
        }
        else {
          $this->createEmail($controleArts['id'], 'Main', $data['email']);
        }
        if (isset($data['email_Work'])) {
          $this->createEmail($controleArts['id'], 'Work', $data['email_Work']);
        }
        else {
          $this->createEmail($controleArts['id'], 'Work', $data['email']);
        }
      }
      // process phone fields
      if (isset($data['phone']) && strlen($data['phone']) > 5) {
        $this->createPhone($controleArts['id'], "Main", "Phone", $data['phone']);
      }
      if (isset($data['mobile']) && strlen($data['mobile']) > 5) {
        $this->createPhone($controleArts['id'], "Main", "Mobile", $data['mobile']);
      }
      // save vakantieperiodes
      $this->saveVakantiePeriodes($controleArts['id'], $data);
      $this->saveWerkgebieden($controleArts['id'], $data);
      return $controleArts;
    }
    catch (CiviCRM_API3_Exception $ex) {
      throw new API_Exception(ts('Could not create a contact in ' . __METHOD__
        . ', contact your system administrator! Error from API Contact create: ' . $ex->getMessage()));
    }
  }

  /**
   * Method om custom field params toe te voegen aan save
   *
   * @param $customFields
   * @param $data
   * @param $params
   */
  private function addToParamsCustomFields($customFields, $data, &$params) {
    foreach ($customFields as $field) {
      $fieldName = $field['name'];
      if (isset($data[$fieldName])) {
        $customFieldName = 'custom_' . $field['id'];
        $params[$customFieldName] = $data[$fieldName];
      }
    }
  }

  /**
   * Method om adres op te slaan
   *
   * @param $contactId
   * @param $data
   *
   * @return mixed
   * @throws CiviCRM_API3_Exception
   */
  private function createAddress($contactId, $data) {
    // todo wat gebeur hier precies met bestaande adressen?
    // todo hele methode moet eigenlijk naar class Adres
    $adres = $this->existsAddress($contactId);
    if (!$adres) {
      // todo haal Billing uit Config
      $adres = [
        'location_type_id' => 'Billing',
        'contact_id' => $contactId,
      ];
    }
    $adres['street_address'] = $data['street_address'];
    // todo rekening houden met meerdere supplementals?
    if (isset($data['supplemental_address_1'])) {
      $adres['supplemental_address_1'] = $data['supplemental_address_1'];
    }
    $adres['postal_code'] = $data['postal_code'];
    $adres['city'] = $data['city'];
    $adres['sequential'] = 1;
    $createdAddress = civicrm_api3('Address', 'create', $adres);
    return $createdAddress['values'];
  }

  /**
   * Method om te checken of adres al bestaat
   *
   * @param $contactId
   *
   * @return array|bool
   */
  private function existsAddress($contactId) {
    // todo method moet eigenlijk naar class Adres
    $params = [
      'location_type_id' => 'Billing',
      'contact_id' => $contactId,
    ];
    try {
      $adres = civicrm_api3('Address', 'getsingle', $params);
      return $adres;
    }
    catch (CiviCRM_API3_Exception $ex) {
      return FALSE;
    }
  }

  /**
   * Method om te checken of email al bestaat
   *
   * @param $contactId
   * @param $locationType
   *
   * @return array|bool
   */
  private function existsEmail($contactId, $locationType) {
    // todo method moet eigenlijk naar class Mail
    $params = [
      'location_type_id' => $locationType,
      'contact_id' => $contactId,
    ];
    try {
      $email = civicrm_api3('Email', 'getsingle', $params);
      return $email;
    }
    catch (CiviCRM_API3_Exception $ex)
    {
      return FALSE;
    }
  }

  /**
   * Method om email op te slaan
   *
   * @param $contactId
   * @param $locationType
   * @param $emailAddress
   *
   * @return mixed
   */
  private function createEmail($contactId, $locationType, $emailAddress) {
    // todo method moet eigenlijk naar class Mail
    // todo wat gebeurt hier precies als er meerdere emailadressen zijn
    $email = $this->existsEmail($contactId, $locationType);
    if (!$email) {
      $email = [
        'sequential' => 1,
        'contact_id' => $contactId,
        'location_type_id' => $locationType,
      ];
    }
    $email['email'] = $emailAddress;
    try {
      $createdEmail = civicrm_api3('Email', 'create', $email);
      return $createdEmail['values'];
    }
    catch (CiviCRM_API3_Exception $ex) {
      return FALSE;
    }
  }

  /**
   * @param $contactId
   * @param $locationType
   * @param string $phoneType
   *
   * @return array|bool
   */
  private function existsPhone($contactId, $locationType, $phoneType = "Phone") {
    $params = [
      'location_type_id' => $locationType,
      'contact_id' => $contactId,
      'phone_type_id' => $phoneType,
    ];
    try {
      $phone = civicrm_api3('Phone', 'getsingle', $params);
      return $phone;
    }
    catch (CiviCRM_API3_Exception $ex) {
      return FALSE;
    }
  }

  /**
   * Method om telefoon op te slaan
   *
   * @param $contactId
   * @param $locationType
   * @param $phoneType
   * @param $phoneNbr
   *
   * @return mixed
   * @throws CiviCRM_API3_Exception
   */
  private function createPhone($contactId, $locationType, $phoneType, $phoneNbr) {
    // todo method moet eigenlijk in class Telefoon
    // todo wat gebeurt er precies bij meerdere telefoons
    $phone = $this->existsPhone($contactId, $locationType, $phoneType);
    if (!$phone) {
      $phone = [
        'sequential' => 1,
        'contact_id' => $contactId,
        'location_type_id' => $locationType,
        'phone_type_id' => $phoneType,
      ];
    }
    if (!empty($phoneNbr)) {
      $phone['phone'] = $phoneNbr;
      $createdPhone = civicrm_api3('Phone', 'create', $phone);
      return $createdPhone['values'];
    }
  }

  /**
   * Method voor migratie om gegevens uit het oude civi te halen met
   * aansluitingsNummer
   *
   * @param $aansluitingsNummer
   *
   * @return array
   */
  private function getFromCivi($aansluitingsNummer) {
    $config = CRM_Basis_Config::singleton();
    $sql = "SELECT * FROM " . $config->getSourceCiviDbName() . ".migratie_leveranciersgegevens WHERE ml_aansluitingsnummer = %1";
    $dao = CRM_Core_DAO::executeQuery($sql, [
      1 => [
        $aansluitingsNummer,
        'String',
      ],
    ]);
    if ($dao->fetch()) {
      return CRM_Basis_Utils::moveDaoToArray($dao);
    }
  }

  /**
   * Method voor migratie van tags uit oude CiviCRM
   *
   * @param $oldId
   * @param $newId
   */
  private function migrateTags($oldId, $newId) {
    $config = CRM_Basis_Config::singleton();
    $dao = CRM_Core_DAO::executeQuery("SELECT * FROM civicrm_tag WHERE id = %1", [
      1 => [
        $oldId,
        'Integer',
      ],
    ]);
    if (!$dao->fetch()) {
      $sql = "INSERT INTO `mediwe_civicrm`.`civicrm_tag` (
        `id`,
        `name`,
        `description`,
        `parent_id`,
        `is_selectable`,
        `is_reserved`,
        `is_tagset`,
        `used_for`,
        `created_date`
      )
      SELECT
        `id`,
        `name`,
        `description`,
        `parent_id`,
        `is_selectable`,
        `is_reserved`,
        `is_tagset`,
        `used_for`,
        `created_date`
      FROM " . $config->getSourceCiviDbName() . ".`civicrm_tag`";
      CRM_Core_DAO::executeQuery($sql);
    }
    CRM_Core_DAO::executeQuery(" DELETE FROM civicrm_entity_tag WHERE entity_id = %1 AND entity_table = %2", [
      1 => [$newId, 'Integer'],
      2 => ['civicrm_contact', 'String'],
    ]);
    $sql = "INSERT INTO civicrm_entity_tag (entity_table, entity_id, tag_id)
      SELECT %1, %2, tag_id FROM " . $config->getSourceCiviDbName() . ".civicrm_entity_tag WHERE entity_id = %3 AND entity_table = %1";
    CRM_Core_DAO::executeQuery($sql, [
      1 => ['civicrm_contact', 'String'],
      2 => [$newId, 'Integer'],
      3 => [$oldId, 'Integer'],
    ]);
  }

  /**
   * Method om vanuit de oude civicrm te migreren
   *
   * @param $contactId
   * @param $data
   *
   * @return array
   * @throws CiviCRM_API3_Exception
   */
  private function migrateFromCivi($contactId, $data) {
    $config = CRM_Basis_Config::singleton();
    $params = [
      'sequential' => 1,
      'contact_type' => 'Organization',
      'contact_sub_type' => $this->_controleArtsContactSubTypeName,
      'id' => $contactId,
    ];
    // zoek deze klant op in civi produktie
    $civiArts = $this->getFromCivi($data['supplier_aansluitingsnummer']);
    // update de leveranciersgegevens
    $this->addToParamsCustomFields($config->getLeverancierCustomGroup('custom_fields'), $civiArts, $params);
    // save the data
    $createdContact = civicrm_api3('Contact', 'create', $params);
    // migrate tag info
    if (isset($civiArts['contact_id'])) {
      $this->migrateTags($civiArts['contact_id'], $contactId);
      $this->saveVoorwaarden($civiArts['contact_id'], $contactId);
    }
    return $createdContact;
  }

  /**
   * Method om vanuit Joomla te migreren
   *
   * @param $params
   *
   * @throws CiviCRM_API3_Exception
   */
  private function migrateFromJoomla($params) {
    $config = CRM_Basis_Config::singleton();
    $sql = "SELECT * FROM mediwe_joomla.migratie_controlearts LIMIT 0,80;";
    $dao = CRM_Core_DAO::executeQuery($sql);
    while ($dao->fetch()) {
      $params = CRM_Basis_Utils::moveDaoToArray($dao);
      foreach ($params as $key => $value) {
        if ($key == 'external_identifier') {
          $idDoctor = $params[$key];
          $params[$key] = "Arts-" . $params[$key];
        }
        // reformat bellen vooraf/achteraf  mcc_arts_bel_moment
        $params['mcc_arts_bel_moment'] = CRM_Core_DAO::VALUE_SEPARATOR;
        if ($key == 'arts_bellen_vooraf' && $value == '1') {
          $params['mcc_arts_bel_moment'] .= "3" . CRM_Core_DAO::VALUE_SEPARATOR;
        }
        if ($key == 'arts_bellen_achteraf' && $value == '1') {
          $params['mcc_arts_bel_moment'] .= "2" . CRM_Core_DAO::VALUE_SEPARATOR;
        }
        if ($params['mcc_arts_bel_moment'] == CRM_Core_DAO::VALUE_SEPARATOR) {
          $params['mcc_arts_bel_moment'] .= "1" . CRM_Core_DAO::VALUE_SEPARATOR;
        }
        // reformat opdracht per
        $params['mcc_arts_opdracht'] = CRM_Core_DAO::VALUE_SEPARATOR;
        if ($key == 'arts_opdracht_fax' && $value == '1') {
          $params['mcc_arts_opdracht'] .= "3" . CRM_Core_DAO::VALUE_SEPARATOR;
        }
        if ($key == 'arts_opdracht_mail' && $value == '1') {
          $params['mcc_arts_opdracht'] .= "2" . CRM_Core_DAO::VALUE_SEPARATOR;
        }
        if ($params['mcc_arts_opdracht'] == CRM_Core_DAO::VALUE_SEPARATOR) {
          $params['mcc_arts_opdracht'] = FALSE;
        }
      }
      // zoek controlearts met dat nummer van joomla
      $arts = $this->get(['external_identifier' => $params['external_identifier']]);
      if ($arts && !isset($arts['count'])) {
        $params['id'] = reset($arts)['contact_id'];
      }
      // zoek de regio data op
      $sqlRegio = " SELECT * FROM mediwe_joomla.jos_mediwe_doctor_regions WHERE id_doctor = %1";
      $daoRegio = CRM_Core_DAO::executeQuery($sqlRegio, [
        1 => [
          $idDoctor,
          'Integer',
        ],
      ]);
      $regios = [];
      while ($daoRegio->fetch()) {
        $regios[] = [
          $config->getPostcodeCustomField('name') => $daoRegio->zip,
          $config->getGemeenteCustomField('name') => $daoRegio->city,
          $config->getPrioriteitCustomField('name') => $daoRegio->sequence_nbr,
        ];
      }
      $params['regios'] = $regios;
      // voeg de controlearts toe
      $arts = $this->create($params);
      if (!isset($params['id'])) {
        $params['id'] = $arts['id'];
      }
      // migreer de leveranciersgegevens uit civi produktie
      $this->migrateFromCivi($params['id'], $params);
      // confirm migration
      $sql = "INSERT INTO `mediwe_joomla`.`migration_doctor` (`id`) VALUES  (%1)";
      CRM_Core_DAO::executeQuery($sql, [1 => [$idDoctor, 'Integer']]);
    }
  }

}
