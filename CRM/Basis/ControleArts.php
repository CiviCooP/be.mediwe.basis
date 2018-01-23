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

        $this->_migrate_from_joomla($params);

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
   * Method to create a new controlearts
   *
   * @param $params
   * @return array
   */
  public function create($data) {

      // ensure contact_type and contact_sub_type are set correctly
      $params = array(
          'sequential' => 1,
          'contact_type' => 'Organization',
          'contact_sub_type' => $this->_controleArtsContactSubTypeName,
      );


      if (isset($data['id']) ) {
          $params['id'] = $data['id'];
      }
      else {
          $params['organization_name'] = $data['organization_name'];
          $params['street_address'] = $data['street_address'];
          $params['postal_code'] = $data['postal_code'];
      }

      // if id is set, then update
      if ( isset($data['id']) || $this->exists($params)) {
          $this->update($data);
      } else {
          return $this->_saveControleArts($params,$data);
      }
  }

  /**
   * Method to update a controlearts
   *
   * @param $params
   * @return array
   */
  public function update($data) {

      $controlearts = array();

      // ensure contact_type and contact_sub_type are set correctly
      $params = array(
          'sequential' => 1,
          'contact_type' => 'Organization',
          'contact_sub_type' => $this->_controleArtsContactSubTypeName,
      );

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
          return $this->_saveControleArts($params, $data);
      }

      return $controlearts;
  }

  /**
   * Method to check if a controlearts exists
   *
   * @param $params
   * @return bool
   */
    public function exists($params) {
        $controlearts = array();

        // ensure that contact sub type is set
        $params['contact_sub_type'] = $this->_controleArtsContactSubTypeName;

        try {
            $controlearts = civicrm_api3('Contact', 'getsingle', $params);
        }
        catch (CiviCRM_API3_Exception $ex) {
            return false;
        }

        return $controlearts;
    }

  /**
   * Method to get all controleartss that meet the selection criteria based on incoming $params
   *
   * @param $params
   * @return array
   */
    public function get($params) {
       
        $controlearts = array();
        // ensure that contact sub type is set
        $params['contact_sub_type'] = $this->_controleArtsContactSubTypeName;
        $params['sequential'] = 1;

        try {

            $contacts = civicrm_api3('Contact', 'get', $params);
            $controlearts = $contacts['values'];

            $this->_addControleArtsAllFields($controlearts);

            return $controlearts;
        }
        catch (CiviCRM_API3_Exception $ex) {
        }

    }

    public function getByName($organization_name) {
        $params = array (
            'sequential' => 1,
            'organization_name' => $organization_name,
            'contact_sub_type' => $this->_controleArtsContactSubTypeName,
        );

        return $this->get($params);
    }

    public function getVakantieperiodes($params) {

        $config = CRM_Basis_Config::singleton();
        $vakantieCustomFields = $config->getVakantieperiodeCustomGroup('custom_fields');

        return $this->_getRepeatingData($vakantieCustomFields, $params);

    }

    public function saveVakantiePeriodes($contact_id, $data) {
        $config = CRM_Basis_Config::singleton();
        $vakantieCustomFields = $config->getVakantieperiodeCustomGroup('custom_fields');

        return $this->_saveRepeatingData($vakantieCustomFields, $contact_id, $data);
    }

    public function saveWerkgebied($contact_id, $data) {
        $config = CRM_Basis_Config::singleton();
        $WerkgebiedCustomFields = $config->getWerkgebiedCustomGroup('custom_fields');

        return $this->_saveRepeatingData($WerkgebiedCustomFields, $contact_id, $data);
    }

    private function _getRepeatingData($customFields, $params) {

        $my_array = array();

        foreach ($customFields as $field) {
            $key = 'return.custom_' . $field['id'];
            $params[$key] = "1";
        }
        $values = civicrm_api3('CustomValue', 'get', $params)['values'];

        foreach ($customFields as $field) {
            foreach ($values[$field['id']] as $key => $value) {
                if (is_numeric($key)) {
                    $my_array[$key]['id'] = $key;
                    $my_array[$key][$field['name']] = $value;
                }
            }
        }
        return $my_array;
    }

    private function _saveRepeatingData($customFields, $entity_id, $array) {

        $rv = false;

        $params = array(
            'sequential' => 1,
            'entity_id' => $entity_id,
        );
        $newline = -1;
        foreach ($array as $data) {
            if (!isset($data['id'])) {
                $data['id'] = $newline;
                $newline = $newline - 1;
            }
            foreach ($customFields as $field) {

                if (isset($data[$field['name']])) {
                    $key = "custom_" . $field['id'] . ":" . $data['id'];
                    if ($field['data_type'] == 'Date') {
                        if ($this->_apidate($data[$field['name']]) != "") {
                            $params[$key] = $this->_apidate($data[$field['name']]);
                        }
                    }
                    else {
                        $params[$key] = $data[$field['name']];
                    }
                }
            }
        }

        if (count($params) > 2) {
            $rv = civicrm_api3('CustomValue', 'create', $params);
        }

        return $rv;

    }

    private function _apidate($date)
    {
        if (substr($date, 0, 4) == 1900 || substr($date, 0, 4) == 0 ) {
            $rv = "";
        }
        else {
            $rv = str_replace(' ', '', $date);
            $rv = str_replace(':', '', $rv);
            $rv = str_replace('-', '', $rv);
            $rv = str_replace('/', '', $rv);
        }

        return $rv;
    }
  /**
   * Method to delete all medeWorkers from a klant with klantId (set to is_deleted in CiviCRM)
   *
   * @param $klantId
   * @return array
   */
  public function deleteWithId($controleArtsId) {
      $controlearts = array();

      // ensure that contact sub type is set
      $params['contact_sub_type'] = $this->_controleArtsContactSubTypeName;
      $params['contact_id'] = $controleArtsId;
      try {
          if ($this->exists($params)) {
              $controlearts = civicrm_api3('Contact', 'delete', $params);
          }
      }
      catch (CiviCRM_API3_Exception $ex) {
          throw new API_Exception(ts('Could not create a contact in '.__METHOD__
              .', contact your system administrator! Error from API Contact delete: '.$ex->getMessage()));
      }

      return $controlearts;
  }

    private function _saveControleArts($params, $data) {

        $config = CRM_Basis_Config::singleton();
        $controlearts = array();


        foreach ($data as $key => $value) {
            $params[$key] = $value;
        }

        // rename klant custom fields for api  ($customFields, $data, &$params)
        $this->_addToParamsCustomFields($config->getLeverancierCustomGroup('custom_fields'), $data, $params);
        $this->_addToParamsCustomFields($config->getCommunicatieCustomGroup('custom_fields'), $data, $params);

        // TODO: What to do with repeating groups?
        //$this->_addToParamsCustomFields($config->getWorkgebiedCustomGroup('custom_fields'), $data, $params);


        try {

            $createdContact = civicrm_api3('Contact', 'create', $params);
            $controlearts = $createdContact['values'][0];

            // process address fields
            $address = $this->_createAddress($controlearts['id'], $data);

            // process email fields
            if (isset($data['email']) && strlen($data['email']) > 5 ) {
                $email = $this->_createEmail($controlearts['id'], 'Billing', $data['email']);

                if (isset($data['email_Main'])) {
                    $email_Main = $this->_createEmail($controlearts['id'], 'Main', $data['email_Main']);
                } else {
                    $email_Main = $this->_createEmail($controlearts['id'], 'Main', $data['email']);
                }

                if (isset($data['email_Work'])) {
                    $email_Work = $this->_createEmail($controlearts['id'], 'Work', $data['email_Work']);
                } else {
                    $email_Work = $this->_createEmail($controlearts['id'], 'Work', $data['email']);
                }
            }


            // process phone fields
            if (isset($data['phone']) && strlen($data['phone']) > 5) {
                $this->_createPhone($controlearts['id'], "Main", "Phone", $data['phone']);
            }
            if (isset($data['mobile']) && strlen($data['mobile']) > 5) {
                $this->_createPhone($controlearts['id'], "Main", "Mobile", $data['mobile']);
            }

            if (isset($data['mvp_vakantie_van'])) {
                $holiday = array(
                    'mvp_vakantie_van' => $data['mvp_vakantie_van'],
                    'mvp_vakantie_tot' => $data['mvp_vakantie_tot'],
                );

                $vakantie_params = array(
                    'entity_id' => $controlearts['id'],
                    'mvp_vakantie_van' => $data['mvp_vakantie_van'],
                    'mvp_vakantie_tot' => $data['mvp_vakantie_tot'],
                );

                $old_periods = $this->getVakantieperiodes($vakantie_params);
                foreach ($old_periods as $period) {
                    if (substr($period['mvp_vakantie_van'], 0, 10) == substr($data['mvp_vakantie_van'], 0, 10)) {
                        $holiday['id'] = $period['id'];
                    }
                }

                $this->saveVakantiePeriodes($controlearts['id'], array($holiday));

                if (isset($data['regios'])) {
                    $this->saveWerkgebied($controlearts['id'], $data['regios']);
                }


            }



            return $controlearts;
        }
        catch (CiviCRM_API3_Exception $ex) {
            throw new API_Exception(ts('Could not create a contact in '.__METHOD__
                .', contact your system administrator! Error from API Contact create: '.$ex->getMessage()));
        }

    }

    private function _addToParamsCustomFields($customFields, $data, &$params) {

        foreach ($customFields as $field) {
            $fieldName = $field['name'];
            if (isset($data[$fieldName])) {
                $customFieldName = 'custom_' . $field['id'];
                $params[$customFieldName] = $data[$fieldName];
            }
        }
    }

    private function _createAddress($contact_id, $data) {

        $adres = $this->_existsAddress($contact_id);

        if (!$adres) {
            $adres = array(
                'sequential' => 1,
                'location_type_id' => 'Billing',
                'contact_id' => $contact_id,
            );
        }

        $adres['street_address'] = $data['street_address'];
        $adres['supplemental_address_1'] = $data['supplemental_address_1'];
        $adres['postal_code'] = $data['postal_code'];
        $adres['city'] = $data['city'];

        $createdAddress = civicrm_api3('Address', 'create', $adres);

        return $createdAddress['values'];

    }

    private function _existsAddress($contact_id) {
        $adres = array();

        $params = array(
            'sequential' => 1,
            'location_type_id' => 'Billing',
            'contact_id' => $contact_id,
        );

        try {
            $adres = civicrm_api3('Address', 'getsingle', $params);
        }
        catch (CiviCRM_API3_Exception $ex) {
            return false;
        }

        return $adres;
    }

    private function _existsEmail($contact_id, $location_type) {
        $email = array();

        $params = array(
            'sequential' => 1,
            'location_type_id' => $location_type,
            'contact_id' => $contact_id,
        );

        try {
            $email = civicrm_api3('Email', 'getsingle', $params);
        }
        catch (CiviCRM_API3_Exception $ex) {
            return false;
        }

        return $email;
    }

    private function _createEmail($contact_id, $location_type, $emailaddress) {

        $email = $this->_existsEmail($contact_id, $location_type);

        if (!$email) {
            $email = array(
                'sequential' => 1,
                'contact_id' => $contact_id,
                'location_type_id' => $location_type,
            );
        }

        $email['email'] = $emailaddress;

        $createdEmail = civicrm_api3('Email', 'create', $email);

        return $createdEmail['values'];

    }

    private function _existsPhone($contact_id, $location_type, $phone_type = "Phone") {
        $phone = array();

        $params = array(
            'sequential' => 1,
            'location_type_id' => $location_type,
            'contact_id' => $contact_id,
            'phone_type_id' => $phone_type,
        );

        try {
            $phone = civicrm_api3('Phone', 'getsingle', $params);
        }
        catch (CiviCRM_API3_Exception $ex) {
            return false;
        }

        return $phone;
    }

    private function _createPhone($contact_id, $location_type, $phone_type, $phoneNbr) {

        $phone = $this->_existsPhone($contact_id, $location_type, $phone_type);

        if (!$phone) {
            $phone = array(
                'sequential' => 1,
                'contact_id' => $contact_id,
                'location_type_id' => $location_type,
                'phone_type_id' => $phone_type,
            );
        }

        $phone['phone'] = $phoneNbr;

        $createdPhone = civicrm_api3('Phone', 'create', $phone);

        return $createdPhone['values'];

    }

    private function _addControleArtsAllFields(&$contacts) {
        $config = CRM_Basis_Config::singleton();

        foreach ($contacts as $arrayRowId => $contact) {
            if (isset($contact['id'])) {
                if (isset($contact['id'])) {
                    // leverancier custom fields
                    $contacts[$arrayRowId] = $config->addDaoData($config->getLeverancierCustomGroup(),  $contacts[$arrayRowId]);
                    // communicatie custom fields
                    $contacts[$arrayRowId] = $config->addDaoData($config->getControleArtsCommunicatieCustomGroup(),  $contacts[$arrayRowId]);
                    // Workgebied custom fields
                    //$contacts[$arrayRowId] = $config->addDaoData($config->getWorkgebiedCustomGroup(),  $contacts[$arrayRowId]);
                    // vakantiedagen custom fields
                    //$contacts[$arrayRowId] = $config->addDaoData($config->getVakantieperiodeCustomGroup(),  $contacts[$arrayRowId]);

                    // get mobile phone
                    $mobile = $this->_existsPhone($contact['id'], "Main", "Mobile");
                    if (!$mobile) {
                        $contacts[$arrayRowId]['mobile'] = false;
                    }
                    else {
                        $contacts[$arrayRowId]['mobile'] = $mobile['phone'];
                    }


                }
            }
        }
    }

    private function _getFromCivi($external_identifier) {

        $sql = "SELECT * FROM mediwe_old_civicrm.migratie_leveranciersgegevens WHERE external_identifier = '$external_identifier' ";
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

    /*
 *   CRM_Basis_ControleArts migrate tags of a customer from previous civicrm application
 */
    private function _migrate_tags($old_id, $new_id) {

        CRM_Core_DAO::executeQuery(" DELETE FROM civicrm_entity_tag WHERE entity_id = $new_id AND entity_table = 'civicrm_contact';");

        $sql = " INSERT INTO civicrm_entity_tag (entity_table, entity_id, tag_id)
                SELECT 'civicrm_contact', $new_id, tag_id FROM mediwe_old_civicrm.civicrm_entity_tag
                WHERE entity_id = $old_id AND entity_table = 'civicrm_contact'; ";
        CRM_Core_DAO::executeQuery($sql);

    }


    /*
    *   CRM_Basis_ControleArts migrate info from previous civicrm application
    */
    private function _migrate_from_civi($contact_id, $data) {

        $config = CRM_Basis_Config::singleton();

        $params = array (
                    'sequential' => 1,
                    'contact_type' => 'Organization',
                    'contact_sub_type' => $this->_controleArtsContactSubTypeName,
                    'id' => $contact_id,
        );

        // zoek deze klant op in civi produktie
        $civi_arts = $this->_getFromCivi($data['supplier_aansluitingsnummer']);
var_dump($civi_arts);exit;
        // update de leveranciersgegevens
        $this->_addToParamsCustomFields($config->getLeverancierCustomGroup('custom_fields'), $civi_arts, $params);

        // save the data
        $createdContact = civicrm_api3('Contact', 'create', $params);

        // migrate tag info
        $this->_migrate_tags($civi_arts['contact_id'], $contact_id);

        return $createdContact;
    }

/*
*   CRM_Basis_Klant migrate info  joomla application of a customer from previous civicrm application
*/
    private function _migrate_from_joomla($params)
    {

        $config = CRM_Basis_Config::singleton();

        $sql = "SELECT * FROM mediwe_joomla.migratie_controlearts ";

        $dao = CRM_Core_DAO::executeQuery($sql);

        while ($dao->fetch()) {

            $adres = array();
            $params = array();
            $old_id = false;

            $params = (array)$dao;
            foreach ($params as $key => $value) {
                if (substr($key, 0, 1) == "_" || $key == 'N') {
                    unset($params[$key]);
                }

                if ($key == 'external_identifier') {
                    $id_doctor = $params[$key];
                    $params[$key] = "Arts-" . $params[$key];
                }
            }

            // zoek controlearts met dat nummer van joomla
            $arts = $this->get(array('external_identifier' => $params['external_identifier']));

            if ($arts && !isset($arts['count'])) {
                $params['id'] = reset($arts)['contact_id'];
            }

            // zoek de regio data op
            $sql_regio = " SELECT *
                          FROM 
                            mediwe_joomla.jos_mediwe_doctor_regions 
                          WHERE id_doctor = " . $id_doctor;

            $dao_regio = CRM_Core_DAO::executeQuery($sql_regio);
            $regios = array();
            
            while ($dao_regio->fetch()) {
                $regios[] = array(
                    $config->getPostcodeCustomField('name') =>  $dao_regio->zip,
                    $config->getGemeenteCustomField('name') => $dao_regio->city,
                    $config->getPrioriteitCustomField('name') => $dao_regio->sequence_nbr,
                );
            }

            $params['regios'] = $regios;

            // voeg de controlearts toe
            $arts = $this->create($params);
            if (!isset($params['id'])) {
                $params['id'] = $arts['id'];
            }

            // migreer de leveranciersgegevens uit civi produktie
            $this->_migrate_from_civi($params['id'], $params);
die('Eerste arts OK');
        }
    }

}