<?php

/**
 * Class to process KlantMedewerker in Mediwe
 *
 * @author Erik Hommel (CiviCooP) <erik.hommel@civicoop.org>
 * @date 31 May 2017
 * @license AGPL-3.0
 */
class CRM_Basis_KlantMedewerker {

  private $_klantMedewerkerContactSubTypeName = NULL;


  /**
   * CRM_Basis_KlantMedewerker constructor.
   */
  public function __construct() {

      $config = CRM_Basis_Config::singleton();
      $contactSubType = $config->getKlantMedewerkerContactSubType();
      $this->_klantMedewerkerContactSubTypeName = $contactSubType['name'];

  }

  /**
   * Method to create a new klant medewerker
   *
   * @param $params
   * @return array
   */
  public function create($data) {

      $config = CRM_Basis_Config::singleton();

      // ensure contact_type and contact_sub_type are set correctly
      $params = array(
          'sequential' => 1,
          'contact_type' => 'Individual',
          'contact_sub_type' => $this->_klantMedewerkerContactSubTypeName,
          'display_name' => $data['display_name'],
      );

      // if id is set, then update
      if (isset($data['id']) || $this->exists($params)) {
          $this->update($data);
      } else {

          // rename klant custom fields for api  ($customFields, $data, &$params)
          $this->_addToParamsCustomFields($config->getKlantMedewerkerExpertsysteemTellersCustomGroup('custom_fields'), $data, $params);
          $this->_addToParamsCustomFields($config->getKlantMedewerkerMedewerkerCustomGroup('custom_fields'), $data, $params);

          try {

              $createdContact = civicrm_api3('Contact', 'create', $params);
              $medewerker = $createdContact['values'][0];

              // process address fields
              $address = $this->_createAddress($medewerker['id'], "Thuis", $data);
              if (isset($data['street_address_residence']) && strlen($data['street_address_residence']) > 3) {
                  $address = $this->_createAddress($medewerker['id'], "Andere", $data);
              }

              // process phone fields
              if (isset($data['phone']) && strlen($data['phone']) > 5) {
                  $this->_createPhone($medewerker['id'], "Thuis", "1", $data['phone']);
              }
              if (isset($data['mobile']) && strlen($data['mobile']) > 5) {
                  $this->_createPhone($medewerker['id'], "Thuis", "2", $data['mobile']);
              }

              return $medewerker;
          }
          catch (CiviCRM_API3_Exception $ex) {
              throw new API_Exception(ts('Could not create a contact in '.__METHOD__
                  .', contact your system administrator! Error from API Contact create: '.$ex->getMessage()));
          }

      }
  }

  /**
   * Method to update a klant medewerker
   *
   * @param $params
   * @return array
   */
  public function update($data) {
      $config = CRM_Basis_Config::singleton();
      $medewerker = array();

      // ensure contact_type and contact_sub_type are set correctly
      $params = array(
          'sequential' => 1,
          'contact_type' => 'Individual',
          'contact_sub_type' => $this->_klantMedewerkerContactSubTypeName,
          'name' => $data['name'],
      );

      if (isset($data['id'])) {
          $params['id'] = $data['id'];
      }

      $exists = $this->exists($params);

      if ($exists) {

          $params['id'] = $exists['contact_id'];

          // rename klant custom fields for api  ($customFields, $data, &$params)
          $this->_addToParamsCustomFields($config->getKlantMedewerkerExpertsysteemTellersCustomGroup('custom_fields'), $data, $params);
          $this->_addToParamsCustomFields($config->getKlantMedewerkerMedewerkerCustomGroup('custom_fields'), $data, $params);

          try {

              $updatedContact = civicrm_api3('Contact', 'create', $params);
              $medewerker = $updatedContact['values'][0];

              // process address fields
              $address = $this->_createAddress($medewerker['id'], "Thuis", $data);
              if (isset($data['street_address_residence']) && strlen($data['street_address_residence']) > 3) {
                  $address = $this->_createAddress($medewerker['id'], "Andere", $data);
              }

              // process phone fields
              if (isset($data['phone']) && strlen($data['phone']) > 5) {
                  $this->_createPhone($medewerker['id'], "Thuis", "1", $data['phone']);
              }
              if (isset($data['mobile']) && strlen($data['mobile']) > 5) {
                  $this->_createPhone($medewerker['id'], "Thuis", "2", $data['mobile']);
              }
          }
          catch (CiviCRM_API3_Exception $ex) {
              throw new API_Exception(ts('Could not create a contact in '.__METHOD__
                  .', contact your system administrator! Error from API Contact create: '.$ex->getMessage()));
          }

      }
      return $medewerker;
  }

  /**
   * Method to check if a klant medewerker exists
   *
   * @param $params
   * @return bool
   */
    public function exists($search_params) {
        $medewerker = array();
        $params = array();

        // ensure that contact sub type is set
        $params['contact_sub_type'] = $this->_klantMedewerkerContactSubTypeName;

        // take over search params
        if (isset($search_params['name'])) {
            $params['name'] = $search_params['name'];
        }

        try {
            $medewerker = civicrm_api3('Contact', 'getsingle', $params);
        }
        catch (CiviCRM_API3_Exception $ex) {
            return false;
        }

        return $medewerker;
    }

  /**
   * Method to get all klant medewerkers that meet the selection criteria based on incoming $params
   *
   * @param $params
   * @return array
   */
    public function get($params) {
       
        $medewerkers = array();
        // ensure that contact sub type is set
        $params['contact_sub_type'] = $this->_klantMedewerkerContactSubTypeName;
        $params['sequential'] = 1;

        try {

            $contacts = civicrm_api3('Contact', 'get', $params);
            $medewerkers = $contacts['values'];

            $this->_addKlantMedewerkerAllFields($medewerkers);

            return $medewerkers;
        }
        catch (CiviCRM_API3_Exception $ex) {
        }

    }

    public function getByName($name) {
        $params = array (
            'sequential' => 1,
            'name' => $name,
            'contact_sub_type' => $this->_klantMedewerkerContactSubTypeName,
        );

        return $this->get($params);
    }

  /**
   * Method to delete all medewerkers from a klant with klantId (set to is_deleted in CiviCRM)
   *
   * @param $klantId
   * @return array
   */
  public function deleteWithId($klantMedewerkerId) {
      $medewerker = array();

      // ensure that contact sub type is set
      $params['contact_sub_type'] = $this->_klantMedewerkerContactSubTypeName;
      $params['contact_id'] = $klantMedewerkerId;
      try {
          if ($this->exists($params)) {
              $medewerker = civicrm_api3('Contact', 'delete', $params);
          }
      }
      catch (CiviCRM_API3_Exception $ex) {
          throw new API_Exception(ts('Could not create a contact in '.__METHOD__
              .', contact your system administrator! Error from API Contact delete: '.$ex->getMessage()));
      }

      return $medewerker;
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

    private function _createAddress($contact_id, $locationtype,  $data) {

        $adres = $this->_existsAddress($contact_id, $locationtype,  $data);

        if (!$adres) {
            $adres = array(
                'sequential' => 1,
                'location_type_id' => $locationtype,
                'contact_id' => $contact_id,
            );
        }

        if ($locationtype == "Thuis") {
            $adres['street_address'] = $data['street_address'];
            $adres['supplemental_address_1'] = $data['supplemental_address_1'];
            $adres['postal_code'] = $data['postal_code'];
            $adres['city'] = $data['city'];
        } else {
            $adres['street_address'] = $data['street_address_residence'];
            $adres['supplemental_address_1'] = $data['supplemental_address_1_residence'];
            $adres['postal_code'] = $data['postal_code_residence'];
            $adres['city'] = $data['city_residence'];
        }

        $createdAddress = civicrm_api3('Address', 'create', $adres);

        return $createdAddress['values'];

    }

    private function _existsAddress($contact_id, $locationtype,  $data) {
        $adres = array();

        $params = array(
            'sequential' => 1,
            'location_type_id' => $locationtype,
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
    
    
    private function _addKlantMedewerkerAllFields(&$contacts) {
        $config = CRM_Basis_Config::singleton();

        foreach ($contacts as $arrayRowId => $contact) {
            if (isset($contact['id'])) {
                // tellers custom fields
                $contacts[$arrayRowId] = $config->addDaoData( $config->getKlantMedewerkerExpertsysteemTellersCustomGroup(), $contacts[$arrayRowId] );

            }
        }
    }
}