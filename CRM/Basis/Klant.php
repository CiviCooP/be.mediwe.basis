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
   * CRM_Basis_Klant constructor.
   */
   public function __construct()
   {
     $config = CRM_Basis_Config::singleton();
     $contactSubType = $config->getKlantContactSubType();
     $this->_klantContactSubTypeName = $contactSubType['name'];

     $locationType = $config->getKlantLocationType();
     $this->_klantLocationType = $locationType['name'];
   }

  /**
   * Method to create a new klant
   *
   * @param $params
   * @return array
   * @throws API_Exception when error from api Contact Create
   */
  public function create($data) {

    $config = CRM_Basis_Config::singleton();

    // ensure contact_type and contact_sub_type are set correctly
    $params = array(
        'sequential' => 1,
        'contact_type' => 'Organization',
        'contact_sub_type' => $this->_klantContactSubTypeName,
        'organization_name' => $data['organization_name'],
    );

    // if id is set, then update
    if (isset($data['id']) || $this->exists($params)) {
      $this->update($data);
    } else {

      // rename klant custom fields for api  ($customFields, $data, &$params)
      $this->_addToParamsCustomFields($config->getKlantBoekhoudingCustomGroup('custom_fields'), $data, $params);
      $this->_addToParamsCustomFields($config->getKlantExpertsysteemCustomGroup('custom_fields'), $data, $params);
      $this->_addToParamsCustomFields($config->getKlantProcedureCustomGroup('custom_fields'), $data, $params);
      $this->_addToParamsCustomFields($config->getKlantOrganisatieCustomGroup('custom_fields'), $data, $params);

        try {

          $createdContact = civicrm_api3('Contact', 'create', $params);
          $klant = $createdContact['values'][0];

          // process address fields
          $address = $this->_createAddress($klant['id'], $data);

          // process email field
          if (isset($data['email'])) {
              $email = $this->_createEmail($klant['id'], $data['email']);
          }




          return $klant;
        }
        catch (CiviCRM_API3_Exception $ex) {
          throw new API_Exception(ts('Could not create a contact in '.__METHOD__
            .', contact your system administrator! Error from API Contact create: '.$ex->getMessage()));
        }

    }

  }

  /**
   * Method to update a klant
   *
   * @param $params
   * @return array
   */
  public function update($data) {

      $config = CRM_Basis_Config::singleton();
      $klant = array();

      // ensure contact_type and contact_sub_type are set correctly
      $params = array(
          'sequential' => 1,
          'contact_type' => 'Organization',
          'contact_sub_type' => $this->_klantContactSubTypeName,
          'organization_name' => $data['organization_name'],
      );

      if (isset($data['id'])) {
          $params['id'] = $data['id'];
      }

    $exists = $this->exists($params);

    if ($exists) {

        $params['id'] = $exists['contact_id'];

        // rename klant custom fields for api  ($customFields, $data, &$params)
        $this->_addToParamsCustomFields($config->getKlantBoekhoudingCustomGroup('custom_fields'), $data, $params);
        $this->_addToParamsCustomFields($config->getKlantExpertsysteemCustomGroup('custom_fields'), $data, $params);
        $this->_addToParamsCustomFields($config->getKlantProcedureCustomGroup('custom_fields'), $data, $params);
        $this->_addToParamsCustomFields($config->getKlantOrganisatieCustomGroup('custom_fields'), $data, $params);

        try {

            $updatedContact = civicrm_api3('Contact', 'create', $params);
            $klant = $updatedContact['values'][0];

            // process address fields
            $address = $this->_createAddress($klant['id'], $data);

            // process email field
            if (isset($data['email'])) {
                $email = $this->_createEmail($klant['id'], $data['email']);
            }
        }
        catch (CiviCRM_API3_Exception $ex) {
            throw new API_Exception(ts('Could not create a contact in '.__METHOD__
                .', contact your system administrator! Error from API Contact create: '.$ex->getMessage()));
        }

    }
    return $klant;
  }

  /**
   * Method to check if a klant exists
   *
   * @param $params
   * @return bool
   */
  public function exists($search_params) {
      $klant = array();
      $params = array();

      // ensure that contact sub type is set
      $params['contact_sub_type'] = $this->_klantContactSubTypeName;

      // take over search params
      if (isset($search_params['organization_name'])) {
          $params['organization_name'] = $search_params['organization_name'];
      }
      if (isset($search_params['external_id'])) {
          $params['external_id'] = $search_params['external_id'];
      }

      try {
          $klant = civicrm_api3('Contact', 'getsingle', $params);
      }
      catch (CiviCRM_API3_Exception $ex) {
          return false;
      }

      return $klant;
  }

    private function _existsAddress($contact_id, $search_params) {
        $adres = array();

        $params = array(
            'sequential' => 1,
            'location_type_id' => $this->_klantAdresLocationType,
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
  /**
   * Method to get all klanten that meet the selection criteria based on incoming $params
   *
   * @param $params
   * @return array
   */
  public function get($params) {
    $klanten = array();
    // ensure that contact sub type is set
    $params['contact_sub_type'] = $this->_klantContactSubTypeName;
    $params['sequential'] = 1;

    try {

      $contacts = civicrm_api3('Contact', 'get', $params);
      $klanten = $contacts['values'];

      $this->_addKlantAllFields($klanten);

      return $klanten;
    }
    catch (CiviCRM_API3_Exception $ex) {
    }

  }

  public function getByName($organization_name) {
      $params = array (
          'sequential' => 1,
          'organization_name' => $organization_name,
          'contact_sub_type' => $this->_klantContactSubTypeName,
      );

      return $this->get($params);
  }

  /**
   * Method to add custom fields to an array of contacts
   *
   * @param $contacts
   */

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

      $adres = $this->_existsAddress($contact_id,$data);

      if (!$adres) {
          $adres = array(
              'sequential' => 1,
              'location_type_id' => $this->_klantLocationType,
              'contact_id' => $contact_id,
          );
      }

      if ($adres['location_type_id'] == 'Billing') {
          $adres['is_billing'] = 1;
      }

      $adres['street_address'] = $data['street_address'];
      $adres['supplemental_address_1'] = $data['supplemental_address_1'];
      $adres['postal_code'] = $data['postal_code'];
      $adres['city'] = $data['city'];

      $createdAddress = civicrm_api3('Address', 'create', $adres);

      return $createdAddress['values'];

  }

  private function _addAddressData(&$contact) {

      $address = array();
      $params = array (
          'sequential' => 1,
          'location_type_id' => $this->_klantAdresLocationType,
          'contact_id' => $contact['id'],
      );

      try {
          $address = civicrm_api3('Address', 'getsingle', $params);
      }
      catch (CiviCRM_API3_Exception $ex) {

      }

      // add relevant address data to contact array
      foreach ($address as $key => $value) {
          switch ($key) {
              case 'id':
                    break;
              default:
                    $contact[$key] = $value;
                    break;
          }
      }
  }


  private function _addKlantAllFields(&$contacts) {
    $config = CRM_Basis_Config::singleton();

    foreach ($contacts as $arrayRowId => $contact) {
      if (isset($contact['id'])) {
          // boekhouding custom fields
          $contacts[$arrayRowId] = $config->addDaoData( $config->getKlantBoekhoudingCustomGroup(), $contact );

          // organisatie klant custom fields
          $contacts[$arrayRowId] = $config->addDaoData( $config->getKlantOrganisatieCustomGroup(), $contact );

          // expert systeem custom fields
          $contacts[$arrayRowId] = $config->addDaoData( $config->getKlantExpertsysteemCustomGroup(), $contact );

          // controleprocedure klant custom fields
          $contacts[$arrayRowId] = $config->addDaoData( $config->getKlantProcedureCustomGroup(), $contact );

          // klant address fields
          //$contacts[$arrayRowId] = $this->_addAddressData($contact);

      }
    }
  }

    private function _existsEmail($contact_id) {
        $email = array();

        $params = array(
            'sequential' => 1,
            'location_type_id' => $this->_klantLocationType,
            'contact_id' => $contact_id,
        );

        try {
            $email = civicrm_api3('Address', 'getsingle', $params);
        }
        catch (CiviCRM_API3_Exception $ex) {
            return false;
        }

        return $email;
    }

    private function _createEmail($contact_id,  $emailaddress) {

        $email = $this->_existsEmail($contact_id);

        if (!$email) {
            $email = array(
                'sequential' => 1,
                'contact_id' => $contact_id,
                'location_type_id' => $this->_klantLocationType,
            );
        }

        $adres['email'] = $emailaddress;

        $createdAddress = civicrm_api3('Email', 'create', $email);

        return $createdAddress['values'];

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
      $params['contact_sub_type'] = $this->_klantContactSubTypeName;
      $params['contact_id'] = $klantId;
      try {
          if ($this->exists($params)) {
              $klant = civicrm_api3('Contact', 'delete', $params);
          }
      }
      catch (CiviCRM_API3_Exception $ex) {
          throw new API_Exception(ts('Could not create a contact in '.__METHOD__
              .', contact your system administrator! Error from API Contact delete: '.$ex->getMessage()));
      }

      return $klant;
  }

}