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
   * CRM_Basis_ControleArts constructor.
   */
  public function __construct() {

      $config = CRM_Basis_Config::singleton();
      $contactSubType = $config->getControleArtsContactSubType();
      $this->_controleArtsContactSubTypeName = $contactSubType['organization_name'];

  }

  /**
   * Method to create a new controlearts
   *
   * @param $params
   * @return array
   */
  public function create($data) {

      $config = CRM_Basis_Config::singleton();

      // ensure contact_type and contact_sub_type are set correctly
      $params = array(
          'sequential' => 1,
          'contact_type' => 'Organization',
          'contact_sub_type' => $this->_controleArtsContactSubTypeName,
          'organization_name' => $data['organization_name'],
      );

      // if id is set, then update
      if (isset($data['id']) || $this->exists($params)) {
          $this->update($data);
      } else {

          // rename klant custom fields for api  ($customFields, $data, &$params)
          $this->_addToParamsCustomFields($config->getControleArtsLeverancierCustomGroup('custom_fields'), $data, $params);
          $this->_addToParamsCustomFields($config->getControleArtsVakantieperiodeCustomGroup('custom_fields'), $data, $params);
          $this->_addToParamsCustomFields($config->getControleArtsCommunicatieCustomGroup('custom_fields'), $data, $params);
          $this->_addToParamsCustomFields($config->getControleArtsWerkgebiedCustomGroup('custom_fields'), $data, $params);

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
                  $this->_createPhone($controlearts['id'], "Primair", "1", $data['phone']);
              }
              if (isset($data['mobile']) && strlen($data['mobile']) > 5) {
                  $this->_createPhone($controlearts['id'], "Primair", "2", $data['mobile']);
              }


              return $controlearts;
          }
          catch (CiviCRM_API3_Exception $ex) {
              throw new API_Exception(ts('Could not create a contact in '.__METHOD__
                  .', contact your system administrator! Error from API Contact create: '.$ex->getMessage()));
          }

      }
  }

  /**
   * Method to update a controlearts
   *
   * @param $params
   * @return array
   */
  public function update($data) {
      $config = CRM_Basis_Config::singleton();
      $controlearts = array();

      // ensure contact_type and contact_sub_type are set correctly
      $params = array(
          'sequential' => 1,
          'contact_type' => 'Organization',
          'contact_sub_type' => $this->_controleArtsContactSubTypeName,
          'organization_name' => $data['organization_name'],
      );

      if (isset($data['id'])) {
          $params['id'] = $data['id'];
      }

      $exists = $this->exists($params);

      if ($exists) {

          $params['id'] = $exists['contact_id'];

          // rename klant custom fields for api  ($customFields, $data, &$params)
          $this->_addToParamsCustomFields($config->getControleArtsLeverancierCustomGroup('custom_fields'), $data, $params);
          $this->_addToParamsCustomFields($config->getControleArtsVakantieperiodeCustomGroup('custom_fields'), $data, $params);
          $this->_addToParamsCustomFields($config->getControleArtsCommunicatieCustomGroup('custom_fields'), $data, $params);
          $this->_addToParamsCustomFields($config->getControleArtsWerkgebiedCustomGroup('custom_fields'), $data, $params);
          
          try {

              $updatedContact = civicrm_api3('Contact', 'create', $params);
              $controlearts = $updatedContact['values'][0];

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
                  $this->_createPhone($controlearts['id'], "Primair", "1", $data['phone']);
              }
              if (isset($data['mobile']) && strlen($data['mobile']) > 5) {
                  $this->_createPhone($controlearts['id'], "Primair", "2", $data['mobile']);
              }

          }
          catch (CiviCRM_API3_Exception $ex) {
              throw new API_Exception(ts('Could not create a contact in '.__METHOD__
                  .', contact your system administrator! Error from API Contact create: '.$ex->getMessage()));
          }

      }
      return $controlearts;
  }

  /**
   * Method to check if a controlearts exists
   *
   * @param $params
   * @return bool
   */
    public function exists($search_params) {
        $controlearts = array();
        $params = array();

        // ensure that contact sub type is set
        $params['contact_sub_type'] = $this->_controleArtsContactSubTypeName;

        // take over search params
        if (isset($search_params['organization_name'])) {
            $params['organization_name'] = $search_params['organization_name'];
        }

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
       
        $controleartss = array();
        // ensure that contact sub type is set
        $params['contact_sub_type'] = $this->_controleArtsContactSubTypeName;
        $params['sequential'] = 1;

        try {

            $contacts = civicrm_api3('Contact', 'get', $params);
            $controleartss = $contacts['values'];

            $this->_addControleArtsAllFields($controleartss);

            return $controleartss;
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

    private function _existsPhone($contact_id, $location_type) {
        $phone = array();

        $params = array(
            'sequential' => 1,
            'location_type_id' => $location_type,
            'contact_id' => $contact_id,
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

        $phone = $this->_existsPhone($contact_id, $location_type);

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
                    $contacts[$arrayRowId] = $config->addDaoData($config->getInspecteurLeverancierCustomGroup(), $contact);
                    // werkgebied custom fields
                    $contacts[$arrayRowId] = $config->addDaoData($config->getInspecteurWerkgebiedCustomGroup(), $contact);
                    // vakantiedagen custom fields
                    $contacts[$arrayRowId] = $config->addDaoData($config->getControleArtsVakantieperiodeCustomGroup(), $contact);
                }
            }
        }
    }

}