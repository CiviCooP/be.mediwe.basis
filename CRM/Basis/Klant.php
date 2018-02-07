<?php

/**
 * Class to process Klant in Mediwe
 *
 * @author Erik Hommel (CiviCooP) <erik.hommel@civicoop.org>
 * @date 31 May 2017
 * @license AGPL-3.0
 */
class CRM_Basis_Klant {

   private $_klantContactSubTypeName = NULL;
   private $_klantLocationType = NULL;


    /**
     * CRM_Basis_Klant method to migrate data from existing systems.
     */
   public function migrate() {

     set_time_limit(0);

        $this->migrate_from_joomla();

   }

    /**
     * CRM_Basis_Klant get billing addresses from previous civicrm application.
     */
   private function getFromCivi($external_identifier) {

       $config = CRM_Basis_Config::singleton();

       $sql = "SELECT * FROM " . $config->getSourceCiviDbName() . ".migratie_facturatie_adressen WHERE external_identifier = '$external_identifier' AND location_type_id = 5";

       $dao = CRM_Core_DAO::executeQuery($sql);

       if ($dao->fetch()) {
           $params = (array)$dao;
           foreach ($params as $key => $value) {
               if (   substr($key, 0, 1 ) == "_" || $key == 'N'  )  {
                   unset($params[$key]);
               }
           }

           return $params;
       }


   }

    /**
     * CRM_Basis_Klant migrate addresses pointing to another customer.
     */
   private function migrate_master_addresses_from_civi() {

       $config = CRM_Basis_Config::singleton();

       $sql = "SELECT * 
                FROM " . $config->getSourceCiviDbName() . ".migratie_facturatie_adressen 
                WHERE  location_type_id = 5 
                AND master_identifier IS NOT NULL 
                ORDER BY master_identifier; ";
       $dao = CRM_Core_DAO::executeQuery($sql);

       while ($dao->fetch()) {
           $adres = array();
           $params = array();

           $params = (array)$dao;
           foreach ($params as $key => $value) {
               if (   substr($key, 0, 1 ) == "_" || $key == 'N'  )  {
                   unset($params[$key]);
               }
           }

           $old_id = $params['contact_id'];

           // look for the right contact
           $klant = civicrm_api3('Klant', 'get', array ('external_identifier' => $params['external_identifier']));

           if ($params['master_identifier']) {
               $master =  civicrm_api3('Klant', 'get', array ('external_identifier' => $params['master_identifier']));
               $master_id = reset($master['values'])['contact_id'];

               $master_address = civicrm_api3('Adres', 'get',
                   array (
                     'contact_id' => $master_id,
                     'location_type_id'  => $config->getKlantLocationType()['name'],
               ))['values'];

               $master_address['master_id'] = $master_address['id'];

               unset($master_address['contact_id']);
               unset($master_address['id']);
               $params = $master_address;

           }

           if ($klant['count'] == 1) {
               $params['contact_id'] = reset($klant['values'])['contact_id'];
               $new_id = $params['contact_id'];

               try {

                   // look for existing address (avoid to make another one)
                   $adres['location_type_id'] = $config->getKlantLocationType()['name'];
                   $adres['contact_id'] = $params['contact_id'];
                   $return = civicrm_api3('Adres', 'get', $adres);

                   if (isset($return['count']) && $return['count'] > 0) {
                       $params['id'] = $return['values']['id'];
                   }

                   $params['is_billing'] = 1;
                   $params['location_type_id'] = $config->getKlantLocationType()['name'];
                   $return = civicrm_api3('Adres', 'create', $params);

               }
               catch (CiviCRM_API3_Exception $ex) {
                   throw new API_Exception(ts('Could not create a Mediwe adres in '.__METHOD__
                       .', contact your system administrator! Error from API Adres create: '.$ex->getMessage()));
               }


           }

       }
   }

    /*
     *   CRM_Basis_Klant migrate invoicing info of a customer from previous civicrm application
     */
   private function migrate_invoicing_info($old_id, $new_id) {

        $config = CRM_Basis_Config::singleton();

        $sql = " SELECT * FROM " . $config->getSourceCiviDbName() . ".migratie_facturatiegegevens WHERE contact_id = $old_id ";

        $dao = CRM_Core_DAO::executeQuery($sql);

        if ($dao->fetch()) {
            $params = (array)$dao;
            foreach ($params as $key => $value) {
                if (   substr($key, 0, 1 ) == "_" || $key == 'N'  )  {
                    unset($params[$key]);
                }
            }
        }

        $params['id'] = $new_id;
        unset($params['contact_id']);

       // update de facturatiegegevens
       $this->addToParamsCustomFields($config->getKlantBoekhoudingCustomGroup('custom_fields'), $params);

        civicrm_api3('Klant', 'create', $params);

   }

     /*
      *   CRM_Basis_Klant migrate invoicing mail address of a customer from previous civicrm application
      */
   private function migrate_billing_mail($old_id, $new_id) {

       $config = CRM_Basis_Config::singleton();

       $location_type_id = $config->getKlantLocationType()['id'];

       CRM_Core_DAO::executeQuery(" DELETE FROM civicrm_email WHERE contact_id = $new_id AND location_type_id = $location_type_id;");


       $sql = "INSERT INTO civicrm_email (
                                  contact_id,
                                  location_type_id,
                                  email,
                                  is_primary,
                                  is_billing,
                                  on_hold,
                                  is_bulkmail,
                                  hold_date,
                                  reset_date,
                                  signature_text,
                                  signature_html )
                SELECT $new_id,  
                  $location_type_id,
                  email,
                  is_primary,
                  is_billing,
                  on_hold,
                  is_bulkmail,
                  hold_date,
                  reset_date,
                  signature_text,
                  signature_html 
                FROM " . $config->getSourceCiviDbName() . ".civicrm_email
                WHERE contact_id = $old_id AND location_type_id = 6 ;                                          
       
            ";

       CRM_Core_DAO::executeQuery($sql);
   }

   /*
    *   CRM_Basis_Klant migrate tags of a customer from previous civicrm application
    */
   private function migrate_tags($old_id, $new_id) {

       $config = CRM_Basis_Config::singleton();

       CRM_Core_DAO::executeQuery(" DELETE FROM civicrm_entity_tag WHERE entity_id = $new_id AND entity_table = 'civicrm_contact';");

       $sql = " INSERT INTO civicrm_entity_tag (entity_table, entity_id, tag_id)
                SELECT 'civicrm_contact', $new_id, tag_id FROM " . $config->getSourceCiviDbName() . ".civicrm_entity_tag
                WHERE entity_id = $old_id AND entity_table = 'civicrm_contact'; ";
       CRM_Core_DAO::executeQuery($sql);
       
   }

   /*
  *   CRM_Basis_Klant migrate notes of a customer from previous civicrm application
  */
   private function migrate_notes($old_id, $new_id) {

       $config = CRM_Basis_Config::singleton();

       CRM_Core_DAO::executeQuery(" DELETE FROM civicrm_note WHERE entity_id = $new_id AND entity_table = 'civicrm_contact';");

       $sql = " INSERT INTO civicrm_note (
                                                  entity_table,
                                                  entity_id,
                                                  note,
                                                  modified_date,
                                                  subject,
                                                  privacy
                                                )
                SELECT  entity_table,
                        $new_id,
                        note,
                        modified_date,
                        subject,
                        privacy
                FROM " . $config->getSourceCiviDbName() . ".civicrm_note
                WHERE entity_id = $old_id AND entity_table = 'civicrm_contact'; ";

       CRM_Core_DAO::executeQuery($sql);

   }

  private function migratie_mijnmediwe_contracten($old_contact_id, $contact_id )   {

    $config = CRM_Basis_Config::singleton();
    $createdMembership = false;

    $save_params = array(
      'sequential' => 1,
      'membership_type_id' => $config->getMijnMediweMembershipType()['id'],
      'contact_id' => $contact_id,
      'status_id' => 2,
    );

    // get existing membership
    try {
      $membership = civicrm_api3('Membership', 'getsingle', $save_params);
      if (isset($membership['id'])) {
        $save_params['id'] = $membership['id'];
      }
    }
    catch (CiviCRM_API3_Exception $ex) {

    }

    $sql = "SELECT * FROM " . $config->getSourceCiviDbName() .  ".migratie_mijnmediwe_voorwaarden WHERE contact_id = $old_contact_id ";
    $dao = CRM_Core_DAO::executeQuery($sql);

    if ($dao->fetch()) {
      $params = (array)$dao;
      foreach ($params as $key => $value) {
        if (   substr($key, 0, 1 ) == "_" || $key == 'N' || $key == "contact_id" )  {
          unset($params[$key]);
        }
      }

      // convert names of custom fields
      $this->addToParamsCustomFields($config->getVoorwaardenMijnMediweCustomGroup('custom_fields'), $params, $save_params);

      // create membership
      $createdMembership = civicrm_api3('Membership', 'create', $save_params);

    }

    return $createdMembership;

  }

  private function migratie_controle_contracten($old_contact_id, $contact_id )   {

    $config = CRM_Basis_Config::singleton();
    $createdMembership = false;

    $save_params = array(
      'sequential' => 1,
      'membership_type_id' => array($config->getMaandelijksMembershipType()['id'], $config->getVoorafbetaaldMembershipType()['id'] ),
      'contact_id' => $contact_id,
      'status_id' => 2,
    );

    // get existing membership
    try  {
      $membership = civicrm_api3('Membership', 'getsingle', $save_params);
      if (isset($membership['id'])) {
        $save_params['id'] = $membership['id'];
        $save_params['membership_type_id'] = $membership['membership_type_id'];
      }
    }
    catch (CiviCRM_API3_Exception $ex) {

    }

    $sql = "SELECT * FROM " . $config->getSourceCiviDbName() .  ".migratie_controle_voorwaarden WHERE contact_id = $old_contact_id ";
    $dao = CRM_Core_DAO::executeQuery($sql);

    if ($dao->fetch()) {
      $params = (array)$dao;
      foreach ($params as $key => $value) {
        if (substr($key, 0, 1) == "_" || $key == 'N' || $key == "contact_id") {
          unset($params[$key]);
        }
      }

        // take over membership id
        switch ($params['membership_type_id']) {
          case "1":
            $save_params['membership_type_id'] = $config->getVoorafbetaaldMembershipType()['id'];
            break;
          default:
            $save_params['membership_type_id'] = $config->getMaandelijksMembershipType()['id'];
            break;
        }

        // convert names of custom fields
        $this->addToParamsCustomFields($config->getVoorwaardenControleCustomGroup('custom_fields'), $params, $save_params);

        // create membership
        $createdMembership = civicrm_api3('Membership', 'create', $save_params);


    }

    return $createdMembership;

  }


  private function migrate_is_klant_via($old_contact_id, $contact_id) {

    $config = CRM_Basis_Config::singleton();

    // get relationship in previous civi environment one way
    $sql = "SELECT * FROM " . $config->getSourceCiviDbName() .  ".migratie_is_klant_via WHERE contact_id_a = $old_contact_id ";
    $dao = CRM_Core_DAO::executeQuery($sql);

    if ($dao->fetch()) {
       // get the other customer
      $params = array (
        'sequential' => 1,
        'external_identifier' => $dao->external_id_b,
        'contact_sub_type' => $this->klantContactSubTypeName,
      );

      try {
        $klant_b = civicrm_api3('Contact', 'getsingle', $params);
        if (isset($klant_b['id'])) {
          $params = array (
            'sequential' => 1,
            'contact_id_a' => $contact_id,
            'contact_id_b' => $$klant_b['id'],
            'relation_type_id' => $this->klantContactSubTypeName,
          );
          civicrm_api3('Relationship', 'create', $params);
        }
      }
      catch (CiviCRM_API3_Exception $ex) {

      }

    }

    // get relationship in previous civi environment the other way
    $sql = "SELECT * FROM " . $config->getSourceCiviDbName() .  ".migratie_is_klant_via WHERE contact_id_b = $old_contact_id ";
    $dao = CRM_Core_DAO::executeQuery($sql);

    if ($dao->fetch()) {
      // get the other customer
      $params = array (
        'sequential' => 1,
        'external_identifier' => $dao->external_id_a,
      //  'contact_sub_type' => $config->getKlantContactSubType(),
      );

      try {
        $klant_a = civicrm_api3('Contact', 'getsingle', $params);
        if (isset($klant_a['id'])) {
          $params = array (
            'sequential' => 1,
            'contact_id_b' => $contact_id,
            'contact_id_a' => $$klant_a['id'],
            'relation_type_id' => $config->getIsKlantViaRelationshipTypeId(),
          );
          civicrm_api3('Relationship', 'create', $params);
        }
      }
      catch (CiviCRM_API3_Exception $ex) {

      }

    }


  }

  /*
  *   CRM_Basis_Klant migrate info  joomla application of a customer from previous civicrm application
  */
   private function migrate_from_joomla() {

       $config = CRM_Basis_Config::singleton();

       $sql = " SELECT * FROM mediwe_joomla.migratie_customer LIMIT 0, 100;";  // WHERE external_id = '03/00126'

       $dao = CRM_Core_DAO::executeQuery($sql);

       while ($dao && $dao->fetch()) {

           $adres = array();
           $params = array();
           $mes_data = array();
           $old_id = false;

           $params = (array)$dao;
           $id_company = $params['source_contact'];

           foreach ($params as $key => $value) {
               if (   substr($key, 0, 1 ) == "_" || $key == 'N'  )  {
                   unset($params[$key]);
               }
               if ($key == 'email') {
                   if (strpos($value, '@') == FALSE) {
                       unset($params[$key]);
                       $params['phone'] = $value;
                   }
               }

               // split data of repeating group
               if (substr($key, 0, 3) == "mes") {
                   $mes_data[0][$key] = $value;
                   unset($params[$key]);
               }
           }

           // zoek klant met dat nummer van joomla
           $klant = $this->get(array ( 'external_identifier' => $params['external_id']));


           if (!isset($klant['count'])) {
               $params['id'] = reset($klant)['contact_id'];
           }

           // update de controle procedure gegevens
           $this->addToParamsCustomFields($config->getKlantProcedureCustomGroup('custom_fields'), $params);

           // update de interne organisatie gegevens
           $this->addToParamsCustomFields($config->getKlantOrganisatieCustomGroup('custom_fields'), $params);

           // voeg de klant toe
           $klant = $this->create($params);

           // update de expert systeem gegevens (repeating!)
           $config->setRepeatingData(
             $config->getKlantExpertsysteemCustomGroup('custom_fields'),
             $klant['id'],
             $mes_data,
             array('mes_periode', 'mes_populatie', 'mes_actie')
           );

           $adres['contact_id'] = $klant['id'];
           $adres['is_billing'] = 1;
           $adres['location_type_id'] = $config->getKlantLocationType()['name'];

           $return = civicrm_api3('Adres', 'get', $adres);

           if (isset($return['count']) && $return['count'] > 0) {
               $adres['id'] = $return['values']['id'];
           }

           $adres['street_address'] = $params['street_address'];
           $adres['supplemental_address_1'] = $params['supplemental_address_1'];
           $adres['postal_code'] = $params['postal_code'];
           $adres['city'] = $params['city'];

           try {

               $return = civicrm_api3('Adres', 'create', $adres);
           }
           catch (CiviCRM_API3_Exception $ex) {
               throw new API_Exception(ts('Could not create a Mediwe adres in '.__METHOD__
                   .', contact your system administrator! Error from API Adres create: '.$ex->getMessage()));
           }

           // zoek deze klant op in civi produktie
           $civi_customer = $this->getFromCivi($params['external_id']);

           if (isset($civi_customer['contact_id'])) {

               $old_id = $civi_customer['contact_id'];

               // migrate tags from civi production
               $this->migrate_tags($old_id, $klant['id']);

               // migrate notes from civi production
               $this->migrate_notes($old_id, $klant['id']);

               // migrate billing email addresses
               $this->migrate_billing_mail($old_id, $klant['id']);

               // migrate accounting data
               $this->migrate_invoicing_info($old_id, $klant['id']);

               // migrate Mijn Mediwe contracten
               $this->migratie_mijnmediwe_contracten($old_id, $klant['id']);

               // migrate Controle contracten
               $this->migratie_controle_contracten($old_id, $klant['id']);

               // migratie relaties is klant via
               $this->migrate_is_klant_via($old_id, $klant['id']);

           }
       }

       // migrate billing addresses pointing to another customer
       $this->migrate_master_addresses_from_civi();

     // confirm migration
     $sql = "INSERT INTO " . $config->getJoomlaDbName() . ".migration_customer (`id`) VALUES  ($id_company);";
     CRM_Core_DAO::executeQuery($sql);

   }

    /**
     * CRM_Basis_Klant constructor.
     */
   public function __construct()
   {
     $config = CRM_Basis_Config::singleton();
     $contactSubType = $config->getKlantContactSubType();
     $this->klantContactSubTypeName = $contactSubType['name'];

     $locationType = $config->getKlantLocationType();
     $this->klantLocationType = $locationType['name'];

   }

  /**
   * Method to create a new klant
   *
   * @param $params
   * @return array
   * @throws API_Exception when error from api Contact Create
   */
  public function create($params) {

    // ensure contact_type and contact_sub_type are set correctly
   $params['contact_type'] = 'Organization';
   $params['contact_sub_type'] = $this->klantContactSubTypeName;

   if (isset($params['id'])) {
         return $this->update($params);
   }
   else {
        if ($this->exists($params) === FALSE) {
            return $this->saveKlant($params);
        }
        else {
            // some activity
        }
   }

  }

  /**
   * Method to update a klant
   *
   * @param $params
   * @return array
   */
  public function update($params) {

    $exists = $this->exists(array( 'id' => $params['id']));

    if ($exists) {
        try {
            return $this->saveKlant($params);
        }
        catch (CiviCRM_API3_Exception $ex) {
            throw new API_Exception(ts('Could not create a Mediwe Klant in '.__METHOD__
                .', contact your system administrator! Error from API Address create: '.$ex->getMessage()));
        }

    }
  }

  /**
   * Method to check if a klant exists
   *
   * @param $params
   * @return bool
   */
  public function exists($search_params) {

      $klant = array();

      // ensure that contact sub type is set
      $search_params['contact_sub_type'] = $this->klantContactSubTypeName;

      try {
          $klant = civicrm_api3('Contact', 'getsingle', $search_params);
      }
      catch (CiviCRM_API3_Exception $ex) {
          return false;
      }

      return true;


  }


  /**
   * Method to get all klanten that meet the selection criteria based on incoming $params
   *
   * @param $params
   * @return array
   */
  public function get($params) {
    $klanten = array();
    // ensure that contact sub type is set
    $params['contact_sub_type'] = $this->klantContactSubTypeName;
    $params['sequential'] = 1;

    try {

      $contacts = civicrm_api3('Contact', 'get', $params);
      $klanten = $contacts['values'];

      if ($klanten) {
          $this->addKlantAllFields($klanten);
      }


      return $klanten;
    }
    catch (CiviCRM_API3_Exception $ex) {
    }

  }

  public function getByName($organization_name) {
      $params = array (
          'sequential' => 1,
          'organization_name' => $organization_name,
          'contact_sub_type' => $this->klantContactSubTypeName,
      );

      return $this->get($params);
  }

  public function getLocationType() {
      return $this->klantLocationType;
  }

  /**
   * Method to add custom fields to an array of contacts
   *
   * @param $contacts
   */

  private function saveKlant($params) {

      $config = CRM_Basis_Config::singleton();


      // rename klant custom fields for api  ($customFields, $data, &$params)
      $this->addToParamsCustomFields($config->getKlantBoekhoudingCustomGroup('custom_fields'),  $params);
      $this->addToParamsCustomFields($config->getKlantExpertsysteemCustomGroup('custom_fields'),  $params);
      $this->addToParamsCustomFields($config->getKlantProcedureCustomGroup('custom_fields'),  $params);
      $this->addToParamsCustomFields($config->getKlantOrganisatieCustomGroup('custom_fields'), $params);

      try {

          $createdContact = civicrm_api3('Contact', 'create', $params);

          $klant = reset($createdContact['values']);

          return $klant;
      }
      catch (CiviCRM_API3_Exception $ex) {
          throw new API_Exception(ts('Could not create a contact in '.__METHOD__
              .', contact your system administrator! Error from API Contact create: '.$ex->getMessage()));
      }
  }

    /*
   *   CRM_Basis_Klant method to rename custom fields for create/update to "custom_<FieldId>"
   */
  private function addToParamsCustomFields($customFields, &$params) {

      foreach ($customFields as $field) {
          $fieldName = $field['name'];
          if (isset($params[$fieldName])) {
              $customFieldName = 'custom_' . $field['id'];
              $params[$customFieldName] = $params[$fieldName];
          }
      }

  }


    /*
     *   CRM_Basis_Klant method to add custom data in customer data array (read)
     */

  private function addKlantAllFields(&$contacts) {
    $config = CRM_Basis_Config::singleton();

    foreach ($contacts as $arrayRowId => $contact) {

      if (isset($contact['id'])) {
          // boekhouding custom fields
          $contacts[$arrayRowId] = $config->addDaoData( $config->getKlantBoekhoudingCustomGroup(), $contacts[$arrayRowId] );

          // organisatie klant custom fields
          $contacts[$arrayRowId] = $config->addDaoData( $config->getKlantOrganisatieCustomGroup(), $contacts[$arrayRowId] );

          // expert systeem custom fields
          $contacts[$arrayRowId] = $config->addDaoData( $config->getKlantExpertsysteemCustomGroup(), $contacts[$arrayRowId] );

          // controleprocedure klant custom fields
          $contacts[$arrayRowId] = $config->addDaoData( $config->getKlantProcedureCustomGroup(), $contacts[$arrayRowId] );

      }
    }
  }


  /**
   * Method to delete klant with id (set to is_deleted in CiviCRM)
   *
   * @param $klantId
   * @return bool (if delete was succesfull or not)
   */
  public function deleteWithId($klantId) {
      $klant = array();

      // ensure that contact sub type is set
      $params['contact_sub_type'] = $this->klantContactSubTypeName;
      $params['contact_id'] = $klantId;
      try {
          if ($this->exists($params)) {
              $klant = civicrm_api3('Contact', 'delete', $params);
          }
      }
      catch (CiviCRM_API3_Exception $ex) {
          throw new API_Exception(ts('Could not delete a contact in '.__METHOD__
              .', contact your system administrator! Error from API Contact delete: '.$ex->getMessage()));
      }

      return $klant;
  }

  private function getKlantExpertsysteem($params) {

    $config = CRM_Basis_Config::singleton();
    $vakantieCustomFields = $config->getKlantExpertsysteemCustomGroup('custom_fields');

    return $config->getRepeatingData($vakantieCustomFields, $params);

  }

}