<?php

/**
 * Class to process KlantMedewerker in Mediwe
 *
 * @author  Erik Hommel (CiviCooP) <erik.hommel@civicoop.org>
 * @author  Klaas Eikelboom (CiviCooP) <klaas.eikelboom@civicoop.org>
 * @author  Christophe Deman <christophe.deman@mediwe.be>
 * @date    31 May 2017
 * @license AGPL-3.0
 */
class CRM_Basis_KlantMedewerker extends CRM_Basis_MediweContact {

  private $_klantMedewerkerContactSubTypeName = NULL;

  private $_klantMedewerkerContactTypeName = NULL;

  private $_employerRelationshipTypeId = NULL;

  /**
   * CRM_Basis_KlantMedewerker constructor.
   */
  public function __construct() {
    $config = CRM_Basis_Config::singleton();
    $this->_employerRelationshipTypeId = $config->getIsWerknemerVanRelationshipTypeId();
    $this->_klantMedewerkerContactSubTypeName = $config->getKlantMedewerkerContactSubType()['name'];
    $this->_klantMedewerkerContactTypeName = 'Individual';
  }

  /**
   * Method to migrate a klant medewerker from the joomla application
   *
   * @param  $params
   */
  public function migrate($params) {
    $config = CRM_Basis_Config::singleton();
    $domicilie = [];
    $verblijf = [];
    $phone = [];
    $mobile = [];
    $employer = [];
    $sql = " SELECT *  FROM " . $config->getJoomlaDbName() . ".migratie_klantmedewerker LIMIT 0,100";
    $dao = CRM_Core_DAO::executeQuery($sql);
    while ($dao->fetch()) {
      $params = CRM_Basis_Utils::moveDaoToArray($dao);
      $idPersonnel = $params['external_identifier'];
      foreach ($params as $key => $value) {
        $newKey = $key;
        if (substr($key, 0, 9) == 'domicilie') {
          $newKey = substr($key, 10);
          $domicilie[$newKey] = $value;
          unset($params[$key]);
        }
        if (substr($key, 0, 8) == 'verblijf') {
          if ($value) {
            $newKey = substr($key, 9);
            $verblijf[$newKey] = $value;
          }
          unset($params[$key]);
        }
        if ($key == 'phone') {
          $phone['phone'] = $value;
          $phone['phone_type_id'] = '1';
          $phone['location_type_id'] = $config->getKlantMedewerkerDomicilieLocationType();
        }
        if ($key == 'mobile') {
          $mobile['phone'] = $value;
          $mobile['phone_type_id'] = '2';
          $mobile['location_type_id'] = $config->getKlantMedewerkerDomicilieLocationType();
        }
        if (substr($key, 0, 9) == 'employer_') {
          $newKey = substr($key, 9);
          $employer[$newKey] = $value;
        }
        if ($value == '1900-01-01 00:00:00') {
          $params[$newKey] = FALSE;
        }
      }
      // create the klantmedewerker
      $medewerker = $this->create($params);
      if (isset($medewerker['id'])) {
        $id = $medewerker['id'];
      }
      // create domicilie adres
      $domicilie['contact_id'] = $id;
      $domicilie['location_type_id'] = $config->getKlantMedewerkerDomicilieLocationType();
      civicrm_api3('Adres', 'create', $domicilie);
      // create verblijf adres
      if (isset($verblijf['zip'])) {
        $verblijf['contact_id'] = $id;
        $verblijf['location_type_id'] = $config->getKlantMedewerkerVerblijfLocationType();
        civicrm_api3('Adres', 'create', $verblijf);
      }
      // create phone
      $phone['contact_id'] = $id;
      if ($phone['phone']) {
        civicrm_api3('Telefoon', 'create', $phone);
      }
      else {
        unset($phone['phone']);
        $existPhone = civicrm_api3('Telefoon', 'get', $phone);
        if ($existPhone['count'] == 1) {
          civicrm_api3('Telefoon', 'delete', ['id' => $existPhone['id']]);
        }
      }
      // create mobile
      $mobile['contact_id'] = $id;
      if ($mobile['phone']) {
        civicrm_api3('Telefoon', 'create', $mobile);
      }
      else {
        unset($mobile['phone']);
        $existMobile = civicrm_api3('Telefoon', 'get', $mobile);
        if ($existMobile['count'] == 1) {
          civicrm_api3('Telefoon', 'delete', ['id' => $existMobile['id']]);
        }
      }
      // add employer relationship
      $employerId = reset(civicrm_api3('Klant', 'get', $employer)['values'])['contact_id'];
      if ($employerId) {
        $employerRelation = [];
        $employerRelation['contact_id_a'] = $id;
        $employerRelation['relationship_type_id'] = $config->getIsWerknemerVanRelationshipTypeId();
        // get the existing relation
        $existRelationship = reset(civicrm_api3('Relatie', 'get', $employerRelation)['values']);
        if (isset($existRelationship['id'])) {
          $existRelationship['contact_id_b'] = $employerId;
          $employerRelation = $existRelationship;
        }
        else {
          $employerRelation['contact_id_b'] = $employerId;
          $employerRelation['is_active'] = 1;
        }
        // create the relationship
        civicrm_api3('Relationship', 'create', $employerRelation);
      }
      // confirm migration
      $sql = "INSERT INTO " . $config->getJoomlaDbName() . ".migration_personnel (id) VALUES (%1)";
      CRM_Core_DAO::executeQuery($sql, [1 => [$idPersonnel, 'Integer']]);
    }
  }

  /**
   * Method om klant medewerker toe te voegen
   *
   * @param  $params
   *
   * @return array|bool
   */
  public function create($params) {
    // contact type en sub type goed zetten
    $params['contact_type'] = $this->_klantMedewerkerContactTypeName;
    $params['contact_sub_type'] = $this->_klantMedewerkerContactSubTypeName;
    if (isset($params['id'])) {
      return $this->update($params);
    }
    else {
      if ($this->exists($params) === FALSE) {
        return $this->saveKlantMedewerker($params);
      }
      else {
        CRM_Core_Error::debug_log_message(ts('Trying to add klant medewerker that already exists in ' . __METHOD__));
        return FALSE;
      }
    }
  }

  /**
   * Method om klant medewerker bij te werken
   *
   * @param  $params
   *
   * @return array|bool
   */
  public function update($params) {
    // contact type en sub type goed zetten
    $params['contact_type'] = $this->_klantMedewerkerContactTypeName;
    $params['contact_sub_type'] = $this->_klantMedewerkerContactSubTypeName;
    $exists = $this->exists(['id' => $params['id']]);
    if ($exists) {
      return $this->saveKlantMedewerker($params);
    }
    else {
      CRM_Core_Error::debug_log_message('Trying to update klant medewerker that does not exist (id ' . $params['id'] . ' in ' . __METHOD__);
      return FALSE;
    }
  }

  /**
   * Method to check if a klant medewerker exists
   *
   * @param  $params
   *
   * @return bool
   */
  public function exists($params) {
    // contact type en sub type goed zetten
    $params['contact_type'] = $this->_klantMedewerkerContactTypeName;
    $params['contact_sub_type'] = $this->_klantMedewerkerContactSubTypeName;
    try {
      $count = civicrm_api3('Contact', 'getcount', $params);
      if ($count > 0) {
        return TRUE;
      }
    }
    catch (CiviCRM_API3_Exception $ex) {
    }
    return FALSE;
  }

  /**
   * Methodom klant medewerkers op te halen
   *
   * @param  $params
   *
   * @return array
   */
  public function get($params) {
    $medewerkers = [];
    $config = CRM_Basis_Config::singleton();

    // contact type en sub type goed zetten
    $params['contact_type'] = $this->_klantMedewerkerContactTypeName;
    $params['contact_sub_type'] = $this->_klantMedewerkerContactSubTypeName;
    $params['sequential'] = 1;

    CRM_Basis_SingleCustomData::fixCustomSearchFields($config->getKlantMedewerkerMedewerkerCustomGroup(), $params);
    try {
      $medewerkers = civicrm_api3('Contact', 'get', $params)['values'];
      if ($medewerkers) {
        $this->getKlantMedewerkersCustomFields($medewerkers);
        $this->getKlantMedewerkersKlantIds($medewerkers);
      }
    }
    catch (CiviCRM_API3_Exception $ex) {
      CRM_Core_Error::debug_log_message(ts('Unexpected problem retrieving contacts with api Contact Get in ' . __METHOD__));
    }
    return $medewerkers;
  }

  /**
   * Method om klant op te slaan
   *
   * @param  $params
   *
   * @return array
   * @throws API_Exception
   */
  private function saveKlantMedewerker($params) {
    $config = CRM_Basis_Config::singleton();
    // rename klant medewerker custom fields for api ($customFields, $data, &$params)
    $this->replaceCustomFieldsParams($config->getKlantMedewerkerMedewerkerCustomGroup('custom_fields'), $params);
    try {
      $contact = civicrm_api3('Contact', 'create', $params);
      // verwerk de klant/medewerker relatie
      $this->saveWerknemerRelatie($params['klant_id'], $contact['id']);
      // verwerk de expert tellers
      $this->saveExpertTellers($contact['id'], $params);
      $medewerker = civicrm_api3('KlantMedewerker', 'getsingle', ['id' => $contact['id']]);
      return $medewerker;
    } catch (CiviCRM_API3_Exception $ex) {
      throw new API_Exception(ts('Could not create a contact in ' . __METHOD__
        . ', contact your system administrator! Error from API Contact create: ' . $ex->getMessage()
      ));
    }
  }

  /**
   * Method om werknemer relatie toe te voegen
   *
   * @param $klantId
   * @param $medewerkerId
   *
   * @return bool
   */
  public function saveWerknemerRelatie($klantId, $medewerkerId) {
    if (empty($klantId) || empty($medewerkerId)) {
      return FALSE;
    }
    //check of de relatie al bestaat
    $params = [
      'contact_id_a' => $medewerkerId,
      'contact_id_b' => $klantId,
      'relationship_type_id' => $this->_employerRelationshipTypeId,
    ];
    try {
      $relationshipCount = civicrm_api3('Relationship', 'getcount', $params);
      // voeg toe als niet bestaat
      if ($relationshipCount == 0) {
        $params['start_date'] = $this->setWerknemerStartDate($medewerkerId);
        $params['is_active'] = 1;
        try {
          civicrm_api3('Relationship', 'create', $params);
        }
        catch (CiviCRM_API3_Exception $ex) {
          CRM_Core_Error::debug_log_message(ts('Could not employee relationship between ') . $medewerkerId
            . ts(' and ') . $medewerkerId . ' in ' . __METHOD__);
        }
      }
    }
    catch (CiviCRM_API3_Exception $ex) {
      CRM_Core_Error::debug_log_message(ts('Error from api relationship getcount in ' . __METHOD__));
      return FALSE;
    }
    return TRUE;
  }

  /**
   * Method om expert tellers op te slaan
   *
   * @param int $contactId
   * @param $data
   */
  public function saveExpertTellers($contactId, $data) {
    $expertTellersFields = CRM_Basis_Config::singleton()
      ->getCustomFieldByCustomGroupName('mediwe_expert_systeem');
    // store in arrays if not arrays
    foreach ($expertTellersFields as $expertTellersFieldId => $expertTellersField) {
      if (isset($data[$expertTellersField['name']]) && !is_array($data[$expertTellersField['name']])) {
        $data[$expertTellersField['name']] = [$data[$expertTellersField['name']]];
      }
      if (isset($data[$expertTellersField['name']])) {
        $customData[$expertTellersField['name']] = $data[$expertTellersField['name']];
      }
      else {
        $customData[$expertTellersField['name']] = NULL;
      }
    }
    if ($customData) {
      CRM_Basis_RepeatingCustomData::save('mediwe_expert_tellers', $contactId, $customData);
    }
  }


  /**
   * Method om start datum van werknemer relatie te bepalen
   *
   * @param $medewerkerId
   *
   * @return string
   */
  private function setWerknemerStartDate($medewerkerId) {
    // haal eventueel start datum contract op
    try {
      $datumInDienst = civicrm_api3('KlantMedewerker', 'getvalue', [
        'id' => $medewerkerId,
        'return' => CRM_Basis_Config::singleton()
          ->getMedewerkerDatumInDienstCustomField('name'),
      ]);
      $startDate = new DateTime($datumInDienst);
      return $startDate->format('d-m-Y');
    }
    catch (CiviCRM_API3_Exception $ex) {
    }
    // als geen start datum contact, gebruik datum vandaag
    $startDate = new DateTime();
    return $startDate->format('d-m-Y');
  }

  /**
   * Method om custom velden aan klant medewerker data toe te voegen
   *
   * @param $medewerkers
   */
  private function getKlantMedewerkersCustomFields(&$medewerkers) {
    $config = CRM_Basis_Config::singleton();
    foreach ($medewerkers as $rowId => $medewerker) {
      if (isset($medewerker['id'])) {
        $extra = CRM_Basis_SingleCustomData::addSingleDaoData($config->getKlantMedewerkerMedewerkerCustomGroup(), $medewerker['id']);
        $expert = CRM_Basis_RepeatingCustomData::get('mediwe_expert_teller', $medewerker['id']);
        $medewerkers[$rowId] = array_merge($medewerker, $extra, $expert);
        CRM_Basis_SingleCustomData::stripCustomFieldsResult($config->getKlantMedewerkerMedewerkerCustomGroup(), $medewerkers[$rowId]);
      }
    }
  }

  /**
   * Methode om het klant id toe te voegen aan de medewerker
   *
   * @param $medewerkers
   */
  private function getKlantMedewerkersKlantIds(&$medewerkers) {
    foreach ($medewerkers as $rowId => $medewerker) {
      if (isset($medewerker['id'])) {
        $klantId = $this->findKlantId($medewerker['id']);
        if (isset($klantId)) {
          $medewerkers[$rowId]['klant_id'] = $klantId;
        }
      }
    }
  }

  /**
   * Method om klant medewerker met id te verwijderen
   *
   * @param  $medewerkerId
   *
   * @return bool|array
   * @throws API_Exception
   */
  public function deleteWithId($medewerkerId) {
    // contact type en sub type goed zetten
    $params['contact_type'] = $this->_klantMedewerkerContactTypeName;
    $params['contact_sub_type'] = $this->_klantMedewerkerContactSubTypeName;
    $params['contact_id'] = $medewerkerId;
    try {
      if ($this->exists($params)) {
        return civicrm_api3('Contact', 'delete', $params);
      }
      else {
        CRM_Core_Error::debug_log_message(ts('Trying to delete klant medewerker ' . $medewerkerId . ' but it does not exist in ' . __METHOD__));
        return FALSE;
      }
    }
    catch (CiviCRM_API3_Exception $ex) {
      throw new API_Exception(ts('Could not delete a contact in ' . __METHOD__
        . ', contact your system administrator! Error from API Contact delete: ' . $ex->getMessage()
      ));
    }
  }

  /**
   * Methode om het klant id op te zoeken aan de hand van de werkgevers
   * releatie
   * Let wel niet actief, afgesloten, of werknemers relaties in de toekomst
   * worden niet meegenomen. Mocht een werknemer bij meerdere werkgevers in
   * dienst zijn dan wordt een willekeurige werkgever gekozen.
   *
   * @param $medewerkerId
   *
   * @return null|string
   */
  public function findKlantId($medewerkerId) {
    $config = CRM_Basis_Config::singleton();
    return CRM_Core_DAO::singleValueQuery("
    SELECT contact_id_b FROM civicrm_relationship rel
    WHERE  rel.relationship_type_id = {$config->getIsWerknemerVanRelationshipTypeId()}
    AND    is_active = 1
    AND    (start_date is null or start_date <= CURRENT_DATE())
    AND    (end_date is null or end_date >= CURRENT_DATE())
    AND    contact_id_a = %1", [
      1 => [$medewerkerId, 'Integer'],
    ]);
  }

}
