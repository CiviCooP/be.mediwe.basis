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
     * Method to migrate a klant medewerker from the joomla application
     *
     * @param $params
     * @return array
     */
    public function migrate($params) {

        $config = CRM_Basis_Config::singleton();

        $domicilie = array();
        $verblijf = array();
        $phone = array();
        $mobile = array();
        $employer = array();

        $sql = " SELECT *  FROM mediwe_joomla.migratie_klantmedewerker; ";

        $dao = CRM_Core_DAO::executeQuery($sql);

        while ($dao->fetch()) {

            $params = (array)$dao;
            foreach ($params as $key => $value) {
                $newkey = $key;
                if (   substr($key, 0, 1 ) == "_" || $key == 'N' )  {
                    unset($params[$key]);
                }

                if (substr($key, 0, 9) == 'domicilie') {
                    $newkey = substr($key, 10);
                    $domicilie[$newkey] = $value;
                    unset($params[$key]);
                }

                if (substr($key, 0, 8 ) == 'verblijf') {
                    if ($value) {
                        $newkey = substr($key, 9);
                        $verblijf[$newkey] = $value;
                    }
                    unset($params[$key]);
                }
                
                if ($key == 'phone') {
                    $phone['phone'] = $value;
                    $phone['phone_type_id'] = '1';
                    $phone['location_type_id'] = $config->getKlantMedewerkerDomicilieLocationType();
                }
                if ($key == 'mobile') {
                    $mobile['phone'] = $value;
                    $mobile['phone_type_id'] = '2';
                    $mobile['location_type_id'] = $config->getKlantMedewerkerDomicilieLocationType();
                }
                if (substr($key, 0, 9) == 'employer_') {
                    $newkey = substr($key, 9);
                    $employer[$newkey] = $value;
                }

                if ($value == '1900-01-01 00:00:00') {
                    $params[$newkey] = false;
                }

            }

            // create the klantmedewerker
            $medewerker = $this->create($params);

            if (isset($medewerker['id'])) {
                $id = $medewerker['id'];
            }

            // create domicilie adres
            $domicilie['contact_id'] = $id;
            $domicilie['location_type_id'] = $config->getKlantMedewerkerDomicilieLocationType();
            $return = civicrm_api3('Adres', 'create', $domicilie);
            
            // create verblijf adres
            if (isset($verblijf['zip'])) {
                $verblijf['contact_id'] = $id;
                $verblijf['location_type_id'] = $config->getKlantMedewerkerVerblijfLocationType();
                $return = civicrm_api3('Adres', 'create', $verblijf);                
            }
            
            // create phone
            $phone['contact_id'] = $id;
            if ($phone['phone']) {
                $return = civicrm_api3('Telefoon', 'create', $phone);
            }
            else {
                unset($phone['phone']);
                $return = civicrm_api3('Telefoon', 'get', $phone);
                if ($return['count'] == 1) {
                    $return = civicrm_api3('Telefoon', 'delete', array('id' => $return['id']));
                }
            }
            
            // create mobile
            $mobile['contact_id'] = $id;
            if ($mobile['phone']) {
                $return = civicrm_api3('Telefoon', 'create', $mobile);
            }
            else {
                unset($mobile['phone']);
                $return = civicrm_api3('Telefoon', 'get', $mobile);
                if ($return['count'] == 1) {
                    $return = civicrm_api3('Telefoon', 'delete', array('id' => $return['id']));
                }
            }


            // add employer relationship
            $employer_id = reset(civicrm_api3('Klant', 'get', $employer )['values'])['contact_id'];

            if ($employer_id) {
                $employerRelation = array();
                $employerRelation['contact_id_a'] = $id;
                $employerRelation['relationship_type_id'] = $config->getIsWerknemerVanRelationshipType()['id'];

                // get the existing relation
                $return = reset(civicrm_api3('Relatie', 'get', $employerRelation)['values']);

                if (isset($return['id'])) {
                    $return['contact_id_b'] = $employer_id;
                    $employerRelation = $return;
                }
                else {
                    $employerRelation['contact_id_b'] = $employer_id;
                    $employerRelation['is_active'] = 1;
                }

                // create the relationship
                $return = civicrm_api3('Relationship', 'create', $employerRelation);
            }

        }
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

      $medewerker = $this->exists($params);

      if (isset($medewerker['contact_id'])) {
        $params['id'] = $medewerker['contact_id'];
      }

      // if id is set, then update
      if (isset($params['id']))  {
          return $this->update($params);
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

      if (isset($this->exists($params)['contact_id'])) {

          return $this->_saveKlantMedewerker($params);
      }
  }

  /**
   * Method to check if a klant medewerker exists
   *
   * @param $params
   * @return bool
   */
    public function exists($params) {
        $medewerker = array();

        $search_args = array();
        if (isset($params['contact_id'])) {
            $search_args['contact_id'] = $params['contact_id'];
        }
        elseif  (isset($params['external_identifier'])) {
            $search_args['external_identifier'] = $params['external_identifier'];
        }
        else {
            if (isset($params['employee_national_nbr'])) {
                $search_args['employee_national_nbr'] = $params['employee_national_nbr'];
            }
            if (isset($params['employee_personnel_nbr'])) {
                $search_args['employee_personnel_nbr'] = $params['employee_personnel_nbr'];
            }
            $search_args['display_name'] = $params['display_name'];
        }

        try {
            $medewerker = civicrm_api3('Contact', 'getsingle', $search_args);
            return $medewerker;
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
          if (isset($this->exists($params)['contact_id'])) {
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

