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
  public function create($params) {

      // ensure contact_type and contact_sub_type are set correctly
      $params['contact_type'] = 'Individual';
      $params['contact_sub_type'] = $this->_klantMedewerkerContactSubTypeName;

      // if id is set, then update
      if (isset($data['id']) ) {
          $this->update($params);
      } else {
          return $this->_saveKlantMedewerker($params);
      }
  }

  /**
   * Method to update a klant medewerker
   *
   * @param $params
   * @return array
   */
  public function update($params) {

      // ensure contact_type and contact_sub_type are set correctly
      $params['contact_type'] = 'Individual';
      $params['contact_sub_type'] = $this->_klantMedewerkerContactSubTypeName;

      if ($this->exists($params)) {

          return $this->_saveKlantMedewerker($params);
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

        try {
            $medewerker = civicrm_api3('Contact', 'getsingle', $search_params);
            return true;
        }
        catch (CiviCRM_API3_Exception $ex) {
            return false;
        }
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
        $params['contact_type'] = 'Individual';
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
          throw new API_Exception(ts('Could not delete a contact in '.__METHOD__
              .', contact your system administrator! Error from API Contact delete: '.$ex->getMessage()));
      }

      return $medewerker;
  }

    private function _saveKlantMedewerker($params) {

        $config = CRM_Basis_Config::singleton();

        // rename klant custom fields for api  ($customFields, $data, &$params)
        $this->_addToParamsCustomFields($config->getKlantMedewerkerExpertsysteemTellersCustomGroup('custom_fields'),  $params);
        $this->_addToParamsCustomFields($config->getKlantMedewerkerMedewerkerCustomGroup('custom_fields'),  $params);

        try {

            $createdContact = civicrm_api3('Contact', 'create', $params);
            return reset($createdContact['values']);
        }
        catch (CiviCRM_API3_Exception $ex) {
            throw new API_Exception(ts('Could not create a contact in '.__METHOD__
                .', contact your system administrator! Error from API Contact create: '.$ex->getMessage()));
        }

    }

    private function _addToParamsCustomFields($customFields,  &$params) {

        foreach ($customFields as $field) {
            $fieldName = $field['name'];
            if (isset($params[$fieldName])) {
                $customFieldName = 'custom_' . $field['id'];
                $params[$customFieldName] = $params[$fieldName];
            }
        }
    }


    private function _addKlantMedewerkerAllFields(&$contacts) {
        $config = CRM_Basis_Config::singleton();

        foreach ($contacts as $arrayRowId => $contact) {

            if (isset($contact['id'])) {
                // tellers custom fields
                $contacts[$arrayRowId] = $config->addDaoData( $config->getKlantMedewerkerExpertsysteemTellersCustomGroup(), $contacts[$arrayRowId] );
                // medewerker custom fields
                $contacts[$arrayRowId] = $config->addDaoData( $config->getKlantMedewerkerMedewerkerCustomGroup(), $contacts[$arrayRowId] );

            }
        }

    }
}

