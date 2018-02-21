<?php

/**
 * Class to process Klant in Mediwe
 *
 * @author Erik Hommel (CiviCooP) <erik.hommel@civicoop.org>
 * @author Klaas Eikelboom (CiviCooP) <klaas.eikelboom@civicoop.org>
 * @author Christophe Deman <christophe.deman@mediwe.be>
 * @date 31 May 2017
 * @license AGPL-3.0
 */
class CRM_Basis_Klant extends CRM_Basis_MediweContact {
  private $_klantContactSubTypeName = NULL;
  private $_klantLocationType = NULL;

  /**
   * CRM_Basis_Klant method to migrate data from existing systems.
   */
  public function migrate($params) {
    set_time_limit(0);

    $this->migrateFromJoomla($params);
  }

  /**
   * CRM_Basis_Klant constructor.
   */
  public function __construct() {
    $config = CRM_Basis_Config::singleton();
    $contactSubType = $config->getKlantContactSubType();
    $this->_klantContactSubTypeName = $contactSubType['name'];
    $this->_klantLocationType = $config->getKlantLocationType();
  }

  /**
   * Method om een klant te maken
   *
   * @param $params
   * @return array
   * @throws API_Exception when error from api Contact Create
   */
  public function create($params) {
    // ensure contact_type and contact_sub_type are set correctly
    $params['contact_type'] = 'Organization';
    $params['contact_sub_type'] = $this->_klantContactSubTypeName;
    if (isset($params['id'])) {
      return $this->update($params);
    }
    else {
      if ($this->exists($params) === FALSE) {
        return $this->saveKlant($params);
      }
      else {
        CRM_Core_Error::debug_log_message(ts('Trying to add klant that already exists in ' . __METHOD__));
      }
    }
  }

  /**
   * Method om een klant bij te werken
   *
   * @param $params
   * @return array
   */
  public function update($params) {
    $exists = $this->exists(array('id' => $params['id']));
    if ($exists) {
      return $this->saveKlant($params);
    }
    else {
      CRM_Core_Error::debug_log_message('Trying to update klant that does not exist (id ' . $params['id'] . ' in ' . __METHOD__);
    }
  }

  /**
   * Method om te controleren of een klant bestaat
   *
   * @param $params
   * @return bool
   */
  public function exists($params) {
    // ensure that contact sub type is set correctly
    $params['contact_sub_type'] = $this->_klantContactSubTypeName;
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
   * Method om alle klanten op te halen
   *
   * @param $params
   * @return array
   */
  public function get($params) {
    $klanten = array();
    // ensure that contact sub type is set
    $params['contact_sub_type'] = $this->_klantContactSubTypeName;
    $params['sequential'] = 1;

    $config = CRM_Basis_Config::singleton();
    CRM_Basis_SingleCustomData::fixCustomSearchFields($config->getKlantBoekhoudingCustomGroup(),$params);


    // zet limiet indien ingevuld
    if (isset($params['limit'])) {
      $params['options'] = array('limit' => $params['limit']);
      unset($params['limit']);
    }
    try {
      $klanten = civicrm_api3('Contact', 'get', $params)['values'];
      if ($klanten) {
        $this->getKlantCustomFields($klanten);
      }
    }
    catch (CiviCRM_API3_Exception $ex) {
    }
    return $klanten;
  }

  /**
   * Method om klant met naam op te halen
   *
   * @param $organizationName
   * @return array
   */
  public function getByName($organizationName) {
    $params = array(
      'sequential' => 1,
      'organization_name' => $organizationName,
      'contact_sub_type' => $this->_klantContactSubTypeName,
    );
    return $this->get($params);
  }

  /**
   * Method om klant op te slaan
   *
   * @param $params
   * @return array
   * @throws API_Exception
   */
  private function saveKlant($params) {
    $config = CRM_Basis_Config::singleton();
    // rename klant custom fields for api  ($customFields, $data, &$params)
    $this->replaceCustomFieldsParams($config->getKlantBoekhoudingCustomGroup('custom_fields'), $params);
    $this->replaceCustomFieldsParams($config->getKlantProcedureCustomGroup('custom_fields'), $params);
    $this->replaceCustomFieldsParams($config->getKlantOrganisatieCustomGroup('custom_fields'), $params);
    try {
      $contact = civicrm_api3('Contact', 'create', $params);
      $this->saveExpertSysteem($contact['id'], $params);
      $klant = civicrm_api3('Klant', 'getsingle', array('id' => $contact['id']));
      return $klant;
    }
    catch (CiviCRM_API3_Exception $ex) {
      throw new API_Exception(ts('Could not create a contact in ' . __METHOD__
       . ', contact your system administrator! Error from API Contact create: ' . $ex->getMessage()));
    }
  }

  /**
   * Method om expert systeem op te slaan
   *
   * @param int $contactId
   * @param $data
   */
  public function saveExpertSysteem($contactId, $data) {
    $expertSysteemFields = CRM_Basis_Config::singleton()->getCustomFieldByCustomGroupName('mediwe_expert_systeem');
    // store in arrays if not arrays
    foreach ($expertSysteemFields as $expertSysteemFieldId => $expertSysteemField) {
      if (isset($data[$expertSysteemField['name']]) && !is_array($data[$expertSysteemField['name']])) {
        $data[$expertSysteemField['name']] = array($data[$expertSysteemField['name']]);
      }
      if (isset($data[$expertSysteemField['name']])) {
        $customData[$expertSysteemField['name']] = $data[$expertSysteemField['name']];
      }
      else {
        $customData[$expertSysteemField['name']] = NULL;
      }
    }
    if ($customData) {
      CRM_Basis_RepeatingCustomData::save('mediwe_expert_tellers', $contactId, $customData);
    }
  }

  /**
   * Method om custom velden aan klantdata toe te voegen
   *
   * @param $klanten
   */
  private function getKlantCustomFields(&$klanten) {
    $config = CRM_Basis_Config::singleton();
    foreach ($klanten as $rowId => $klant) {
      if (isset($klant['id'])) {
        $boekhouding = CRM_Basis_SingleCustomData::addSingleDaoData($config->getKlantBoekhoudingCustomGroup(), $klant['id']);
        $organisatie = CRM_Basis_SingleCustomData::addSingleDaoData($config->getKlantOrganisatieCustomGroup(), $klant['id']);
        $klantProcedure = CRM_Basis_SingleCustomData::addSingleDaoData($config->getKlantProcedureCustomGroup(), $klant['id']);
        $expert = CRM_Basis_RepeatingCustomData::get('mediwe_expert_systeem', $klant['id']);
        $klanten[$rowId] = array_merge($klant, $boekhouding, $organisatie, $expert, $klantProcedure);
      }
    }
  }

  /**
   * Method om klant met id te verwijderen
   *
   * @param $klantId
   * @return bool|array
   * @throws API_Exception
   */
  public function deleteWithId($klantId) {
    // ensure that contact sub type is set
    $params['contact_sub_type'] = $this->_klantContactSubTypeName;
    $params['contact_id'] = $klantId;
    try {
      if ($this->exists($params)) {
        $klant = civicrm_api3('Contact', 'delete', $params);
        return $klant;
      }
      else {
        return FALSE;
      }
    }
    catch (CiviCRM_API3_Exception $ex) {
      throw new API_Exception(ts('Could not delete a contact in ' . __METHOD__
        . ', contact your system administrator! Error from API Contact delete: ' . $ex->getMessage()));
    }
  }

  /**
   * CRM_Basis_Klant get billing addresses from previous civicrm application (for migration only)
   */
  private function getFromCivi($externalIdentifier) {
    $config = CRM_Basis_Config::singleton();
    $sql = "SELECT * FROM " . $config->getSourceCiviDbName() . ".migratie_facturatie_adressen WHERE external_identifier = '$externalIdentifier' AND location_type_id = %1";
    $dao = CRM_Core_DAO::executeQuery($sql, array(
      2 => array(5, 'Integer'),
    ));
    if ($dao->fetch()) {
      return CRM_Basis_Utils::moveDaoToArray($dao);
    }
  }

  /**
   * CRM_Basis_Klant migrate addresses pointing to another customer.
   */
  private function migrateMasterAddressesFromCivi() {
    $config = CRM_Basis_Config::singleton();
    $sql = "SELECT * FROM " . $config->getSourceCiviDbName() . ".migratie_facturatie_adressen
      WHERE  location_type_id = %1 AND master_identifier IS NOT NULL ORDER BY master_identifier";
    $dao = CRM_Core_DAO::executeQuery($sql, array(1 => array(5, 'Integer')));
    while ($dao->fetch()) {
      $adres = array();
      $params = CRM_Basis_Utils::moveDaoToArray($dao);
      // look for the right contact
      $klant = civicrm_api3('Klant', 'get', array('external_identifier' => $params['external_identifier']));
      if ($params['master_identifier']) {
        $master = civicrm_api3('Klant', 'get', array('external_identifier' => $params['master_identifier']));
        $masterId = reset($master['values'])['contact_id'];
        $masterAddress = civicrm_api3('Adres', 'get', array(
          'contact_id' => $masterId,
          'location_type_id'  => $this->_klantLocationType['name'],
         ))['values'];
        $masterAddress['master_id'] = $masterAddress['id'];
        unset($masterAddress['contact_id']);
        unset($masterAddress['id']);
        $params = $masterAddress;
      }
      if ($klant['count'] == 1) {
        $params['contact_id'] = reset($klant['values'])['contact_id'];
        try {
          // look for existing address (avoid to make another one)
          $adres['location_type_id'] = $this->_klantLocationType['name'];
          $adres['contact_id'] = $params['contact_id'];
          $return = civicrm_api3('Adres', 'get', $adres);
          if (isset($return['count']) && $return['count'] > 0) {
            $params['id'] = $return['values']['id'];
          }
          $params['is_billing'] = 1;
          $params['location_type_id'] = $this->_klantLocationType['name'];
          civicrm_api3('Adres', 'create', $params);
        }
        catch (CiviCRM_API3_Exception $ex) {
          throw new API_Exception(ts('Could not create a Mediwe adres in ' . __METHOD__
            . ', contact your system administrator! Error from API Adres create: ' . $ex->getMessage()));
        }
      }
    }
  }

  /**
   * CRM_Basis_Klant migrate invoicing info of a customer from previous civicrm application
   *
   * @param $oldId
   * @param $newId
   * @throws CiviCRM_API3_Exception
   */
  private function migrateInvoicingInfo($oldId, $newId) {
    $config = CRM_Basis_Config::singleton();
    $sql = "SELECT * FROM " . $config->getSourceCiviDbName() . ".migratie_facturatiegegevens WHERE contact_id = %1";
    $dao = CRM_Core_DAO::executeQuery($sql, array(1 => array($oldId, 'Integer')));
    if ($dao->fetch()) {
      $params = CRM_Basis_Utils::moveDaoToArray($dao);
    }
    $params['id'] = $newId;
    unset($params['contact_id']);
    // update de facturatiegegevens
    $this->replaceCustomFieldsParams($config->getKlantBoekhoudingCustomGroup('custom_fields'), $params);
    civicrm_api3('Klant', 'create', $params);
  }

  /**
   *   CRM_Basis_Klant migrate invoicing mail address of a customer from previous civicrm application
   *
   * @param $oldId
   * @param $newId
   */
  private function migrateBillingMail($oldId, $newId) {
    $config = CRM_Basis_Config::singleton();
    $locationTypeId = $this->_klantLocationType['id'];
    $deleteQuery = " DELETE FROM civicrm_email WHERE contact_id = %1 AND location_type_id = %2";
    CRM_Core_DAO::executeQuery($deleteQuery, array(
      1 => array($newId, 'Integer'),
      2 => array($locationTypeId, 'Integer'),
    ));
    $sql = "INSERT INTO civicrm_email (contact_id, location_type_id, email, is_primary, is_billing, on_hold,
     is_bulkmail, hold_date, reset_date, signature_text, signature_html)
     SELECT %1, %2, email, is_primary, is_billing, on_hold, is_bulkmail, hold_date, reset_date, signature_text, signature_html 
     FROM " . $config->getSourceCiviDbName() . ".civicrm_email WHERE contact_id = %3 AND location_type_id = %4";
    CRM_Core_DAO::executeQuery($sql, array(
      1 => array($newId, 'Integer'),
      2 => array($locationTypeId, 'Integer'),
      3 => array($oldId, 'Integer'),
      4 => array(6, 'Integer'),
    ));
  }


  /**
   * CRM_Basis_Klant migrate tags of a customer from previous civicrm application
   *
   * @param $oldId
   * @param $newId
   */
  private function migrateTags($oldId, $newId) {
    $config = CRM_Basis_Config::singleton();
    $query = "DELETE FROM civicrm_entity_tag WHERE entity_id = %1 AND entity_table = %2";
    CRM_Core_DAO::executeQuery($query, array(
      1 => array($newId, 'Integer'),
      2 => array('civicrm_contact', 'String'),
    ));
    $sql = " INSERT INTO civicrm_entity_tag (entity_table, entity_id, tag_id) 
      SELECT %1, %2, tag_id FROM " . $config->getSourceCiviDbName() . ".civicrm_entity_tag
      WHERE entity_id = %3 AND entity_table = %1";
    CRM_Core_DAO::executeQuery($sql, array(
      1 => array('civicrm_contact', 'String'),
      2 => array($newId, 'Integer'),
      3 => array($oldId, 'Integer'),
    ));
  }

  /**
   * CRM_Basis_Klant migrate notes of a customer from previous civicrm application
   *
   * @param $oldId
   * @param $newId
   */
  private function migrateNotes($oldId, $newId) {
    $config = CRM_Basis_Config::singleton();
    CRM_Core_DAO::executeQuery("DELETE FROM civicrm_note WHERE entity_id = %1 AND entity_table = %2", array(
      1 => array($newId, 'Integer'),
      2 => array('civicrm_contact', 'String'),
    ));
    $sql = "INSERT INTO civicrm_note (entity_table, entity_id, note, modified_date, subject, privacy) 
      SELECT entity_table, %1, note, modified_date, subject, privacy
      FROM " . $config->getSourceCiviDbName() . ".civicrm_note WHERE entity_id = %2 AND entity_table = %3";
    CRM_Core_DAO::executeQuery($sql, array(
      1 => array($newId, 'Integer'),
      2 => array($oldId, 'Integer'),
      3 => array('civicrm_contact', 'String'),
    ));
  }

  /**
   * Method om mijn mediwe contracten te migreren
   *
   * @param $oldContactId
   * @param $contactId
   * @return array|bool
   */
  private function migratieMijnMediweContracten($oldContactId, $contactId) {
    $config = CRM_Basis_Config::singleton();
    $createdMembership = FALSE;
    $saveParams = array(
      'sequential' => 1,
      'membership_type_id' => $config->getMijnMediweMembershipType()['id'],
      'contact_id' => $contactId,
      'status_id' => 2,
    );
    // get existing membership
    try {
      $membership = civicrm_api3('Membership', 'getsingle', $saveParams);
      if (isset($membership['id'])) {
        $saveParams['id'] = $membership['id'];
      }
    }
    catch (CiviCRM_API3_Exception $ex) {
    }
    $sql = "SELECT * FROM " . $config->getSourceCiviDbName() .  ".migratie_mijnmediwe_voorwaarden WHERE contact_id = %1";
    $dao = CRM_Core_DAO::executeQuery($sql, array(1 => array($oldContactId, 'Integer')));
    if ($dao->fetch()) {
      $params = CRM_Basis_Utils::moveDaoToArray($dao);
      // convert names of custom fields
      $this->replaceCustomFieldsParams($config->getVoorwaardenMijnMediweCustomGroup('custom_fields'), $saveParams);
      // create membership
      $createdMembership = civicrm_api3('Membership', 'create', $saveParams);
    }
    return $createdMembership;
  }

  /**
   * Method voor migratie controle contracten
   *
   * @param $oldContactId
   * @param $contactId
   * @return array|bool
   * @throws CiviCRM_API3_Exception
   */
  private function migratieControleContracten($oldContactId, $contactId) {
    $config = CRM_Basis_Config::singleton();
    $saveParams = array(
      'sequential' => 1,
      'membership_type_id' => array($config->getMaandelijksMembershipType()['id'], $config->getVoorafbetaaldMembershipType()['id']),
      'contact_id' => $contactId,
      'status_id' => 2,
    );
    // get existing membership
    try  {
      $membership = civicrm_api3('Membership', 'getsingle', $saveParams);
      if (isset($membership['id'])) {
        $saveParams['id'] = $membership['id'];
        $saveParams['membership_type_id'] = $membership['membership_type_id'];
      }
    }
    catch (CiviCRM_API3_Exception $ex) {
    }
    $sql = "SELECT * FROM " . $config->getSourceCiviDbName() .  ".migratie_controle_voorwaarden WHERE contact_id = %1";
    $dao = CRM_Core_DAO::executeQuery($sql, array(1 => array($oldContactId, 'Integer')));
    if ($dao->fetch()) {
      $params = CRM_Basis_Utils::moveDaoToArray($dao);
    }
    // take over membership id
    switch ($params['membership_type_id']) {
      case "1":
        $saveParams['membership_type_id'] = $config->getVoorafbetaaldMembershipType()['id'];
        break;

      default:
        $saveParams['membership_type_id'] = $config->getMaandelijksMembershipType()['id'];
        break;

    }
    // convert names of custom fields
    $this->replaceCustomFieldsParams($config->getVoorwaardenControleCustomGroup('custom_fields'), $saveParams);
    // create membership
    $createdMembership = civicrm_api3('Membership', 'create', $saveParams);
    return $createdMembership;
  }

  /**
   * Method om is klant via te migreren
   *
   * @param $oldContactId
   * @param $contactId
   */
  private function migrateIsKlantVia($oldContactId, $contactId) {
    $config = CRM_Basis_Config::singleton();
    // get relationship in previous civi environment one way
    $sql = "SELECT * FROM " . $config->getSourceCiviDbName() .  ".migratie_is_klant_via WHERE contact_id_a = %1";
    $dao = CRM_Core_DAO::executeQuery($sql, array(1 => array($oldContactId, 'Integer')));
    if ($dao->fetch()) {
      // get the other customer
      $params = array(
        'sequential' => 1,
        'external_identifier' => $dao->external_id_b,
        'contact_sub_type' => $this->_klantContactSubTypeName,
      );
      try {
        $klantB = civicrm_api3('Contact', 'getsingle', $params);
        if (isset($klantB['id'])) {
          $params = array(
            'sequential' => 1,
            'contact_id_a' => $contactId,
            'contact_id_b' => $klantB['id'],
            'relation_type_id' => $config->getIsKlantViaRelationshipTypeId(),
          );
          civicrm_api3('Relationship', 'create', $params);
        }
      }
      catch (CiviCRM_API3_Exception $ex) {
      }
    }
    // get relationship in previous civi environment the other way
    $sql = "SELECT * FROM " . $config->getSourceCiviDbName() .  ".migratie_is_klant_via WHERE contact_id_b = %1";
    $dao = CRM_Core_DAO::executeQuery($sql, array(1 => array($oldContactId, 'Integer')));
    if ($dao->fetch()) {
      // get the other customer
      $params = array(
        'sequential' => 1,
        'external_identifier' => $dao->external_id_a,
      );
      try {
        $klantA = civicrm_api3('Contact', 'getsingle', $params);
        if (isset($klantA['id'])) {
          $params = array(
            'sequential' => 1,
            'contact_id_b' => $contactId,
            'contact_id_a' => $klantA['id'],
            'relation_type_id' => $config->getIsKlantViaRelationshipTypeId(),
          );
          civicrm_api3('Relationship', 'create', $params);
        }
      }
      catch (CiviCRM_API3_Exception $ex) {
      }
    }
  }

  /**
   * Method voor migratie vanuit Joomla
   *
   * @throws API_Exception
   * @throws CiviCRM_API3_Exception
   */
  private function migrateFromJoomla($params) {

    if (!is_array($params)) {
      $sql = " SELECT * FROM mediwe_joomla.migratie_customer LIMIT 0, 100";
      $dao = CRM_Core_DAO::executeQuery($sql);
    }

    if ($dao) {
      while ($dao->fetch()) {
        $params = CRM_Basis_Utils::moveDaoToArray($dao);
        $this->doMigrate($params);
      }

      // migrate billing addresses pointing to another customer
      $this->migrateMasterAddressesFromCivi();

      // confirm migration
      $config = CRM_Basis_Config::singleton();

      $sql = "INSERT INTO " . $config->getJoomlaDbName() . ".migration_customer (id) VALUES (%1)";
      CRM_Core_DAO::executeQuery($sql, array(1 => array($params['source_contact'], 'Integer')));
    }
    else {
      $this->doMigrate($params);
    }

  }


  private function doMigrate($params) {

    $config = CRM_Basis_Config::singleton();

    $adres = array();
    $mesData = array();

    foreach ($params as $key => $value) {
      if ($key == 'email') {
        if (strpos($value, '@') == FALSE) {
          unset($params[$key]);
          $params['phone'] = $value;
        }
      }
      // split data of repeating group
      if (substr($key, 0, 3) == "mes") {
        $mesData[0][$key] = $value;
        unset($params[$key]);
      }
    }


    // zoek klant met dat nummer van joomla
    $klant = $this->get(array('external_identifier' => $params['external_identifier']));
    if (!isset($klant['count'])) {
      $params['id'] = reset($klant)['contact_id'];
    }
    // update de controle procedure gegevens
    $this->replaceCustomFieldsParams($config->getKlantProcedureCustomGroup('custom_fields'), $params);
    // update de interne organisatie gegevens
    $this->replaceCustomFieldsParams($config->getKlantOrganisatieCustomGroup('custom_fields'), $params);
    // voeg de klant toe
    $klant = $this->create($params);


    // update de expert systeem gegevens (repeating!)
    CRM_Basis_Utils::setRepeatingData(
      $config->getKlantExpertsysteemCustomGroup('custom_fields'), $klant['id'], $mesData, array('mes_periode', 'mes_populatie', 'mes_actie'));
    $adres['contact_id'] = $klant['id'];
    $adres['is_billing'] = 1;
    $adres['location_type_id'] = $this->_klantLocationType['name'];
    $return = civicrm_api3('Adres', 'get', $adres);
    if (isset($return['count']) && $return['count'] > 0) {
      $adres['id'] = $return['values']['id'];
    }
    $adres['street_address'] = $params['street_address'];
    $adres['supplemental_address_1'] = $params['supplemental_address_1'];
    $adres['postal_code'] = $params['postal_code'];
    $adres['city'] = $params['city'];
    try {
      civicrm_api3('Adres', 'create', $adres);
    }
    catch (CiviCRM_API3_Exception $ex) {
      throw new API_Exception(ts('Could not create a Mediwe adres in ' . __METHOD__
        . ', contact your system administrator! Error from API Adres create: ' . $ex->getMessage()));
    }

    // zoek deze klant op in civi produktie
    $civiCustomer = $this->getFromCivi($params['external_identifier']);

    if (isset($civiCustomer['contact_id'])) {
      $oldId = $civiCustomer['contact_id'];

      // migrate tags from civi production
      $this->migrateTags($oldId, $klant['id']);

      // migrate notes from civi production
      $this->migrateNotes($oldId, $klant['id']);
      // migrate billing email addresses
      $this->migrateBillingMail($oldId, $klant['id']);
      // migrate accounting data
      $this->migrateInvoicingInfo($oldId, $klant['id']);
      // migrate Mijn Mediwe contracten
      $this->migratieMijnMediweContracten($oldId, $klant['id']);
      // migrate Controle contracten
      $this->migratieControleContracten($oldId, $klant['id']);
      // migratie relaties is klant via
      $this->migrateIsKlantVia($oldId, $klant['id']);
    }

  }

}
