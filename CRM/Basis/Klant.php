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
   private $_klantAdresLocationType = NULL;

  /**
   * CRM_Basis_Klant constructor.
   */
   public function __construct()
   {
     $config = CRM_Basis_Config::singleton();
     $contactSubType = $config->getKlantContactSubType();
     $locationType = $config->getKlantLocationType();

     $this->_klantContactSubTypeName = $contactSubType['name'];
     $this->_klantAdresLocationType = $locationType['name'];

   }

  /**
   * Method to create a new klant
   *
   * @param $params
   * @return array
   * @throws API_Exception when error from api Contact Create
   */
  public function create($params) {

      $config = CRM_Basis_Config::singleton();


    // if id is set, then update
    if (isset($params['id'])) {
      $this->update($params);
    } else {
      if (!empty($params['organization_name'])) {
        // ensure contact_type and contact_sub_type are set correctly
        $params['contact_type'] = 'Organization';
        $params['contact_sub_type'] = $this->_klantContactSubTypeName;
      }

      // rename klant custom fields for api
      $this->_renameCustomFields($config->getKlantBoekhoudingCustomGroup('custom_fields'), $params);
      $this->_renameCustomFields($config->getKlantExpertsysteemCustomGroup('custom_fields'), $params);
      $this->_renameCustomFields($config->getKlantProcedureCustomGroup('custom_fields'), $params);
      $this->_renameCustomFields($config->getKlantOrganisatieCustomGroup('custom_fields'), $params);

      if ($this->exists($params) === FALSE) {
        try {
          $createdContact = civicrm_api3('Contact', 'create', $params);
          $klant = $createdContact['values'];

          // process address fields
          $address = $this->_createAddress($klant['id'], $params);
CRM_Core_Error::debug('adres', $address);exit();
          return $klant;
        }
        catch (CiviCRM_API3_Exception $ex) {
          throw new API_Exception(ts('Could not create a contact in '.__METHOD__
            .', contact your system administrator! Error from API Contact create: '.$ex->getMessage()));
        }

      } else {
        // todo maken activity type for DataOnderzoek of iets dergelijks zodat deze gevallen gesignaleerd kunnen worden
          // check if klant can not be found yet and only create if not
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
    $klant = array();
    $params['contact_sub_type'] = $this->_klantContactSubTypeName;

    if ($this->exists($params)) {
        try {
            $klant = civicrm_api3('Contact', 'create', $params);
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
        $params = array();

        $params['location_type_id'] = $this->_klantAdresLocationType;
        $params['contact_id'] = $contact_id;

        try {
            $adres = civicrm_api3('Contact', 'getsingle', $params);
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
    try {
      $contacts = civicrm_api3('Contact', 'get', $params);
      $this->addKlantCustomFields($contacts['values']);
      $klanten = $contacts['values'];
    }
    catch (CiviCRM_API3_Exception $ex) {
    }
    return $klanten;
  }

  /**
   * Method to add custom fields to an array of contacts
   *
   * @param $contacts
   */

  private function _renameCustomFields($customFields, &$params) {

      foreach ($customFields as $field) {
          $fieldName = $field['name'];
          if (isset($params[$fieldName])) {
              $customFieldName = 'custom_' . $field['id'];
              $params[$customFieldName] = $params[$fieldName];
          }
      }

  }

  private function _createAddress($contact_id, $params) {

      $adres = $this->_existsAddress($contact_id,$params);

      if (!$adres) {
          $adres = array();
          $adres['location_type_id'] = $this->_klantAdresLocationType;
          $adres['contact_id'] = $contact_id;
      }

      $adres['street_address'] = $params['street_address'];
      $adres['supplemental_adress_1'] = $params['supplemental_adress_1'];
      $adres['postal_code'] = $params['postal_code'];
      $adres['city'] = $params['city'];

      $createdAddress = civicrm_api3('Contact', 'create', $adres);

      return $createdAddress['values'];

  }

  private function addKlantCustomFields(&$contacts) {
    $config = CRM_Basis_Config::singleton();

    foreach ($contacts as $arrayRowId => $contact) {
      if (isset($contact['id'])) {
            //  boekhouding

            $sql = 'SELECT * FROM '. $config->getKlantBoekhoudingCustomGroup('table_name') . ' WHERE entity_id = %1';
CRM_Core_error::debug('sql', $sql);
            $dao = CRM_Core_DAO::executeQuery($sql, array(
              1 => array($contact['id'], 'Integer',),
            ));
 CRM_Core_error::debug('dao', $dao);
            $found = false;
            while ($dao->fetch()) {
                $found = true;
                $contacts[$arrayRowId] = $this->placeKlantCustomFields($config->getKlantBoekhoudingCustomGroup['custom_fields'], $dao, $contact);
            }
            if (!$found) {
                $dao = array();
                $dao['entity_id'] = $contact['id'];
                $fields = $config->getKlantBoekhoudingCustomGroup('custom_fields');
                foreach ($fields as $field) {
                    $dao->$field['column_name'] = false;
                }
                $contacts[$arrayRowId] = $this->placeKlantCustomFields($config->getKlantBoekhoudingCustomGroup['custom_fields'], $dao, $contact);
            }
CRM_Core_error::debug('contacts', $contacts);
exit();

          // organisatie klant
          $sql = 'SELECT * FROM '.$config->getKlantOrganisatieCustomGroup('table_name').' WHERE entity_id = %1';
          $dao = CRM_Core_DAO::executeQuery($sql, array(
              1 => array($contact['id'], 'Integer',),
          ));
          while ($dao->fetch()) {
              $contacts[$arrayRowId] = $this->placeKlantCustomFields($dao, $contact);
          }

          // expert systeem
          $sql = 'SELECT * FROM '.$config->getKlantExpertsysteemCustomGroup('table_name').' WHERE entity_id = %1';
          $dao = CRM_Core_DAO::executeQuery($sql, array(
              1 => array($contact['id'], 'Integer',),
          ));
          while ($dao->fetch()) {
              $contacts[$arrayRowId] = $this->placeKlantCustomFields($dao, $contact);
          }

          // controleprocedure klant
          $sql = 'SELECT * FROM '.$config->getKlantProcedureCustomGroup('table_name').' WHERE entity_id = %1';
          $dao = CRM_Core_DAO::executeQuery($sql, array(
              1 => array($contact['id'], 'Integer',),
          ));
          while ($dao->fetch()) {
              $contacts[$arrayRowId] = $this->placeKlantCustomFields($dao, $contact);
          }
      }
    }
  }

  /**
   * Method to place the klant custom fields in the contact array based on the
   *
   * @param object $contactData (dao)
   * @param array $contactArray;
   * @return array
   */
  private function placeKlantCustomFields($fields, $contactData, $contactArray) {
    $config = CRM_Basis_Config::singleton();

    foreach ($fields as $customFieldId => $customField) {
      $propertyName = $customField['column_name'];
      if (isset($contactData->$propertyName)) {
        $contactArray[$propertyName] = $contactData->$propertyName;
      }
    }
    return $contactArray;
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