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
  public function create($params) {

    // ensure contact_type and contact_sub_type are set correctly
   $params['contact_type'] = 'Organization';
   $params['contact_sub_type'] = $this->_klantContactSubTypeName;

   if (isset($params['id'])) {
         return $this->update($params);
   }
   else {
        if ($this->exists($params) === FALSE) {
            return $this->_saveKlant($params);
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

    $exists = $this->exists($params);

    if ($this->exists($params)) {
        try {
            return $this->_saveKlant($params);
        }
        catch (CiviCRM_API3_Exception $ex) {
            throw new API_Exception(ts('Could not create a Mediwe Klant in '.__METHOD__
                .', contact your system administrator! Error from API Address create: '.$ex->getMessage()));
        }

        return $this->_saveKlant($params);
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
      $search_params['contact_sub_type'] = $this->_klantContactSubTypeName;

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

  public function getLocationType() {
      return $this->_klantLocationType;
  }

  /**
   * Method to add custom fields to an array of contacts
   *
   * @param $contacts
   */

  private function _saveKlant($params) {

      $config = CRM_Basis_Config::singleton();


      // rename klant custom fields for api  ($customFields, $data, &$params)
      $this->_addToParamsCustomFields($config->getKlantBoekhoudingCustomGroup('custom_fields'),  $params);
      $this->_addToParamsCustomFields($config->getKlantExpertsysteemCustomGroup('custom_fields'),  $params);
      $this->_addToParamsCustomFields($config->getKlantProcedureCustomGroup('custom_fields'),  $params);
      $this->_addToParamsCustomFields($config->getKlantOrganisatieCustomGroup('custom_fields'), $params);

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

  private function _addToParamsCustomFields($customFields, &$params) {

      foreach ($customFields as $field) {
          $fieldName = $field['name'];
          if (isset($params[$fieldName])) {
              $customFieldName = 'custom_' . $field['id'];
              $params[$customFieldName] = $params[$fieldName];
          }
      }

  }




  private function _addKlantAllFields(&$contacts) {
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
      $params['contact_sub_type'] = $this->_klantContactSubTypeName;
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

}