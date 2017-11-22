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
        $vakantieCustomFields = $config->getControleArtsVakantieperiodeCustomGroup('custom_fields');

        return $this->_getRepeatingData($vakantieCustomFields, $params);

    }

    public function saveVakantiePeriodes($contact_id, $data) {
        $config = CRM_Basis_Config::singleton();
        $vakantieCustomFields = $config->getControleArtsVakantieperiodeCustomGroup('custom_fields');

        return $this->_saveRepeatingData($vakantieCustomFields, $contact_id, $data);
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
                        $params[$key] = $this->_apidate($data[$field['name']]);
                    }
                    else {
                        $params[$key] = $data[$field['name']];
                    }
                }
            }
        }

        return civicrm_api3('CustomValue', 'create', $params);

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
   * Method to delete all medewerkers from a klant with klantId (set to is_deleted in CiviCRM)
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
        $this->_addToParamsCustomFields($config->getControleArtsLeverancierCustomGroup('custom_fields'), $data, $params);
        $this->_addToParamsCustomFields($config->getControleArtsCommunicatieCustomGroup('custom_fields'), $data, $params);

        // TODO: What to do with repeating groups?
        //$this->_addToParamsCustomFields($config->getControleArtsWerkgebiedCustomGroup('custom_fields'), $data, $params);
        //$this->_addToParamsCustomFields($config->getControleArtsVakantieperiodeCustomGroup('custom_fields'), $data, $params);

        try {

            $createdContact = civicrm_api3('Contact', 'create', $params);
            $controlearts = $createdContact['values'][0];

            // process address fields
            $address = $this->_createAddress($controlearts['id'], $data);

            // process email fields
            $email = $this->_createEmail($controlearts['id'], 'Billing', $data['email']);

            if (isset($data['email_primair'])) {
                $email_primair = $this->_createEmail($controlearts['id'], 'Primair', $data['email_primair']);
            } else {
                $email_primair = $this->_createEmail($controlearts['id'], 'Primair', $data['email']);
            }

            if (isset($data['email_werk'])) {
                $email_werk = $this->_createEmail($controlearts['id'], 'Werk', $data['email_werk']);
            } else {
                $email_werk = $this->_createEmail($controlearts['id'], 'Werk', $data['email']);
            }

            // process phone fields
            if (isset($data['phone']) && strlen($data['phone']) > 5) {
                $this->_createPhone($controlearts['id'], "Primair", "Phone", $data['phone']);
            }
            if (isset($data['mobile']) && strlen($data['mobile']) > 5) {
                $this->_createPhone($controlearts['id'], "Primair", "Mobile", $data['mobile']);
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
                    $contacts[$arrayRowId] = $config->addDaoData($config->getControleArtsLeverancierCustomGroup(),  $contacts[$arrayRowId]);
                    // communicatie custom fields
                    $contacts[$arrayRowId] = $config->addDaoData($config->getControleArtsCommunicatieCustomGroup(),  $contacts[$arrayRowId]);
                    // werkgebied custom fields
                    //$contacts[$arrayRowId] = $config->addDaoData($config->getControleArtsWerkgebiedCustomGroup(),  $contacts[$arrayRowId]);
                    // vakantiedagen custom fields
                    //$contacts[$arrayRowId] = $config->addDaoData($config->getControleArtsVakantieperiodeCustomGroup(),  $contacts[$arrayRowId]);

                    // get mobile phone
                    $mobile = $this->_existsPhone($contact['id'], "Primair", "Mobile");
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
                if (   substr($key, 0, 1 ) == "_" || $key == 'N'  )  {
                    unset($params[$key]);
                }

            }

            // zoek controlearts met dat nummer van joomla
            $arts = $this->get(array ( 'external_identifier' => $params['external_identifier']));

            if (!isset($klant['count'])) {
                $params['id'] = reset($arts)['contact_id'];
            }

            // voeg de controlearts toe
            $arts = $this->create($params);

            $adres['contact_id'] = $arts['id'];
            $adres['is_billing'] = 1;
            $adres['location_type_id'] = 'Billing';

            $return = civicrm_api3('Adres', 'get', $adres);

            if (isset($return['count']) && $return['count'] > 0) {
                $adres['id'] = $return['values']['id'];
            }


            $adres['street_address'] = $params['street_address'];
            $adres['postal_code'] = $params['postal_code'];
            $adres['city'] = $params['city'];

            try {

                $return = civicrm_api3('Adres', 'create', $adres);
            }
            catch (CiviCRM_API3_Exception $ex) {
                throw new API_Exception(ts('Could not create a Mediwe adres in '.__METHOD__
                    .', contact your system administrator! Error from API Adres create: '.$ex->getMessage()));
            }


            if ($key == 'phone') {
                $phone['phone'] = $value;
                $phone['phone_type_id'] = '1';
                $phone['location_type_id'] = 'Werk';

                // create phone
                $phone['contact_id'] = $arts['id'];
                $return = civicrm_api3('Telefoon', 'create', $phone);
                }


            }
            if ($key == 'mobile') {
                $mobile['phone'] = $value;
                $mobile['phone_type_id'] = '2';
                $mobile['location_type_id'] = 'Werk';

                // create phone
                $mobile['contact_id'] = $arts['id'];
                $return = civicrm_api3('Telefoon', 'create', $mobile);
            }

            if ($key == 'email') {
                $return = $this->_createEmail($arts['id'], 'Werk', $value );
            }

        }


}