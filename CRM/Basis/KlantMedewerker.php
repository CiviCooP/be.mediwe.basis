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

      // ensure contact_type and contact_sub_type are set correctly
      $params = array(
          'sequential' => 1,
          'contact_type' => 'Individual',
          'contact_sub_type' => $this->_klantMedewerkerContactSubTypeName,
      );

      if (isset($data['id']) && is_numeric($data['id'])) {
          $params['id'] = $data['id'];
      }
      else {
          $params['display_name'] = $data['display_name'];
          $params['street_address'] = $data['street_address'];
          $params['postal_code'] = $data['postal_code'];
      }

      // if id is set, then update
      if (isset($data['id']) || $this->exists($params)) {
          $this->update($data);
      } else {
          return $this->_saveKlantMedewerker($params, $data);
      }
  }

  /**
   * Method to update a klant medewerker
   *
   * @param $params
   * @return array
   */
  public function update($data) {

      // ensure contact_type and contact_sub_type are set correctly
      $params = array(
          'sequential' => 1,
          'contact_type' => 'Individual',
          'contact_sub_type' => $this->_klantMedewerkerContactSubTypeName,
      );

      if (isset($data['id'])) {
          $params['id'] = $data['id'];
      }
      else {
          $params['display_name'] = $data['display_name'];
          $params['street_address'] = $data['street_address'];
          $params['postal_code'] = $data['postal_code'];
      }

      $exists = $this->exists($params);

      if ($exists) {

          $params['id'] = $exists['contact_id'];
          $params['name'] = $data['display_name'];
          $params['display_name'] = $data['display_name'];

          return $this->_saveKlantMedewerker($params, $data);
      }
  }

  /**
   * Method to check if a klant medewerker exists
   *
   * @param $params
   * @return bool
   */
    public function exists($search_params) {
        $medewerker = array();

        // ensure that contact sub type is set
        $search_params['contact_sub_type'] = $this->_klantMedewerkerContactSubTypeName;

        try {
            $medewerker = civicrm_api3('Contact', 'getsingle', $search_params);
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

    private function _saveKlantMedewerker($params, $data) {

        $config = CRM_Basis_Config::singleton();

        foreach ($data as $key => $value) {
            $params[$key] = $value;
        }
        
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

            // create employer relationship
            if (isset($data['employer_name'])) {
                $this->_createEmployerRelationship($medewerker['id'], $data);
            }

            return $medewerker;
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


    private function _existsPhone($contact_id, $location_type, $phone_type) {
        $phone = array();

        $params = array(
            'sequential' => 1,
            'location_type_id' => $location_type,
            'phone_type_id' => $phone_type,
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

    private function _createEmployerRelationship($medewerkerId, $data) {
        $config = CRM_Basis_Config::singleton();

        $relationshipType = $config->getIsWerknemerVanRelationshipType();

        // look for the employer
        if (!isset($data['employer_id'])) {
            $data['employer_id']  = $this->_searchEmployer($data['employer_name'], $data['employer_vat']);
        }

        if ($data['employer_id']) {
            $params = array (
                'sequential' => 1,
                'relationship_type_id' => $relationshipType['id'],
                'contact_id_a' => $medewerkerId,
               // 'contact_id_b' => $data['employer_id'],
            );

            try {
                $exists = civicrm_api3( 'Relationship', 'getsingle', $params );
                $params['id'] = $exists['id'];
            }
            catch (CiviCRM_API3_Exception $ex) {
            }
            $params['contact_id_b'] = $data['employer_id'];
            civicrm_api3( 'Relationship', 'create', $params );

        }
    }

    private function _searchEmployer($name, $vat) {

        $config = CRM_Basis_Config::singleton();

        $vatField = 'custom_' . $config->getCustomerVatCustomField()['id'];

        $contactSubType = $config->getKlantContactSubType();
        $klantContactSubTypeName = $contactSubType['name'];

        $sql = "SELECT 
                  id 
                FROM 
                  civicrm_contact 
                WHERE 
                  organization_name LIKE '%" . $name . "%';";
        $dao = CRM_Core_DAO::executeQuery($sql);
        while ($dao->fetch()) {
            $ids[] = $dao->id;
        }

        if (strlen($vat) > 5) {
            // use the api to look for vat

            $params = array(
              'sequential' => 1,
                'contact_sub_type' => $klantContactSubTypeName,
              $vatField => $this->_formatVat($vat),
            );
            $contactList = civicrm_api3('Contact', 'get', $params);
            $contacts = $contactList['values'];
            foreach ($contacts as $contact) {
                if (in_array($contact['id'], $ids)) {
                    return $contact['id'];
                }
            }

        }

        if (count($ids) == 1) {
            return $ids[0];
        }

    }

    private function _formatVat($vat) {

      $string = $vat;

      if (is_numeric($string)) {
          $string = "BE" . $string;
      }

      $string = str_replace(" ", "", $string);
      $string = str_replace (".", "", $string);

      if (strlen($string) == 11) {
          $string = substr($string, 0, 2) . "0" . substr($string, 2, 999);
      }

      if (strlen($string) == 12) {
          $string = substr($string, 0, 2) . " " . substr($string, 2, 4) . "." . substr($string, 6, 3 ) . "." . substr($string, 9, 3);
      }
      else {
          $string = $vat;
      }

      return $string;
    }

    private function _getPhoneNumbers($contact_id) {

      $params = array(
        'sequential' => 1,
        'contact_id' => $contact_id
      );

      $phones = civicrm_api3('Phone', 'get', $params);

      return $phones['values'];
    }

    private function _getAddresses($contact_id) {

        $params = array(
            'sequential' => 1,
            'contact_id' => $contact_id
        );

        $addresses = civicrm_api3('Address', 'get', $params);

        return $addresses['values'];
    }

    private function _getEmployer($contact_id) {

        $config = CRM_Basis_Config::singleton();
        $invoicingtable = $config->getKlantBoekhoudingCustomGroup()['table_name'];
        $vatField = $config->getCustomerVatCustomField();
        $vatFieldName = $vatField['column_name'];

        $employer = array(
            'employer_id' => false,
            'employer_name' => '',
            'employer_vat' => ''
        );
        $sql = "SELECT 
                  c.id as employer_id, c.organization_name as employer_name, i.$vatFieldName as employer_vat 
                FROM 
                  civicrm_contact c
                INNER JOIN 
                  civicrm_relationship r
                ON  
                  r.contact_id_b = c.id
                AND 
                  r.relationship_type_id = 5 
                INNER JOIN
                  $invoicingtable i
                ON 
                  i.entity_id = c.id     
                WHERE 
                  r.contact_id_a = $contact_id;
                ";
        $dao = CRM_Core_DAO::executeQuery($sql);

        if ($dao->fetch()) {
            $employer = array(
                'employer_id' => $dao->employer_id,
                'employer_name' => $dao->employer_name,
                'employer_vat' => $dao->employer_vat
            );
        }

        return $employer;
    }

    private function _addKlantMedewerkerAllFields(&$contacts) {
        $config = CRM_Basis_Config::singleton();

        foreach ($contacts as $arrayRowId => $contact) {

            if (isset($contact['id'])) {
                // tellers custom fields
                $contacts[$arrayRowId] = $config->addDaoData( $config->getKlantMedewerkerExpertsysteemTellersCustomGroup(), $contacts[$arrayRowId] );
                // medewerker custom fields
                $contacts[$arrayRowId] = $config->addDaoData( $config->getKlantMedewerkerMedewerkerCustomGroup(), $contacts[$arrayRowId] );

                // telefoon nummers
                $phones = $this->_getPhoneNumbers($contact['id']);
                foreach ($phones as $phone) {
                    switch ($phone['phone_type_id']) {
                        case "2":
                            $contacts[$arrayRowId]['mobile'] = $phone['phone'];
                            break;
                    }
                }

                // adressen
                $adressen = $this->_getAddresses($contact['id']);
                //CRM_Core_Error::debug('adres', $adressen);exit;
                foreach ($adressen as $adres) {
                    switch ($adres['location_type_id']) {
                        case $config->getKlantMedewerkerVerblijfLocationType()['id']:
                            $contacts[$arrayRowId]['street_address_residence'] = $adres['street_address'];
                            $contacts[$arrayRowId]['supplemental_address_1_residence'] = $adres['supplemental_address_1'];
                            $contacts[$arrayRowId]['postal_code_residence'] = $adres['postal_code'];
                            $contacts[$arrayRowId]['city_residence'] = $adres['city'];
                            break;
                    }
                }

                // werkgever employer_name, employer_id
                $employer = $this->_getEmployer($contact['id']);
                $contacts[$arrayRowId]['employer_id'] = $employer['employer_id'];
                $contacts[$arrayRowId]['employer_name'] = $employer['employer_name'];
                $contacts[$arrayRowId]['employer_vat'] = $employer['employer_vat'];
            }
        }

    }
}

