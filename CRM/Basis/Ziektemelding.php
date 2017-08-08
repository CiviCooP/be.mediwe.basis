<?php

/**
 * Class to process Ziektemelding in Mediwe
 *
 * @author Erik Hommel (CiviCooP) <erik.hommel@civicoop.org>
 * @date 31 May 2017
 * @license AGPL-3.0
 */
class CRM_Basis_Ziektemelding {

    private $_ziektemeldingCaseTypeName = NULL;

  /**
   * CRM_Basis_Klant constructor.
   */
   public function __construct()
   {
     $config = CRM_Basis_Config::singleton();
     $ziektemeldingCaseType = $config->getZiektemeldingCaseType();
     $this->_ziektemeldingCaseTypeName = $ziektemeldingCaseType['name'];
   }

  /**
   * Method to create a new ziektemelding
   *
   * @param $params
   * @return array
   * @throws API_Exception when error from api Case Create
   */
  public function create($params) {

      // ensure contact_type and contact_sub_type are set correctly
      $params['case_type_id'] = $this->_ziektemeldingCaseTypeName;

      // ensure mandatory data
      if (!isset($params['contact_id'])) {
          throw new Exception('Medewerker identificatie ontbreekt!');
      }
      if (!isset($params['illness_date_begin'])) {
          throw new Exception('Begin datum ziekte ontbreekt!');
      }

      // if id is set, then update
      if ( isset($params['id']) || $this->exists($params)) {
          $this->update($params);
      } else {
          return $this->_saveZiektemelding($params);
      }
  }

  /**
   * Method to update an ziektemelding
   *
   * @param $params
   * @return array
   */
  public function update($params) {

    $exists = $this->exists($params);

    if ($exists) {
        try {
            $params['id'] = $exists['id'];
            return $this->_saveZiektemelding($params);
        }
        catch (CiviCRM_API3_Exception $ex) {
            throw new API_Exception(ts('Could not create an ziektemelding in '.__METHOD__
                .', contact your system administrator! Error from API Case create: '.$ex->getMessage()));
        }
    }
  }

  /**
   * Method to check if an ziektemelding exists
   *
   * @param $params
   * @return bool
   */
  public function exists($params) {
      $ziektemelding = array();

      try {
          $ziektemelding = civicrm_api3('Case', 'getsingle', $params);
      }
      catch (CiviCRM_API3_Exception $ex) {
          return false;
      }
      return true;
  }

  /**
   * Method to get all ziektemeldinges that meet the selection criteria based on incoming $params
   *
   * @param $params
   * @return array
   */
  public function get($params) {
    $ziektemeldingsen = array();
;
    try {
      $ziektemeldinges = civicrm_api3('Case', 'get', $params);
      $ziektemeldingsen = $ziektemeldinges['values'];
    }
    catch (CiviCRM_API3_Exception $ex) {
    }
    return $ziektemeldingsen;
  }


  /**
   * Method to delete an ziektemelding with id (set to is_deleted in CiviCRM)
   *
   * @param $ziektemeldingId
   * @return array
   */
  public function deleteWithId($ziektemeldingid) {
      $ziektemelding = array();

      $params['id'] = $ziektemeldingid;
      try {
          if ($this->exists($params)) {
              $ziektemelding = civicrm_api3('Case', 'delete', $params);
          }
      }
      catch (CiviCRM_API3_Exception $ex) {
          throw new API_Exception(ts('Could not create an ziektemelding in '.__METHOD__
              .', contact your system administrator! Error from API Case delete: '.$ex->getMessage()));
      }

      return $ziektemelding;
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

  private function _getEmployer($data) {

      $params_employer = array();

      foreach ($data as $key => $value) {
          if (substr($key, 0, 8) == "employer"){
              $params_employer[$key] = $value;
          }
      }
      $employer = civicrm_api3('Klant', 'Get', $params_employer);
      if (!$employer) {
          $employer = civicrm_api3('Klant', 'Create', $params_employer);
      }

      return $employer;
  }

    private function _getEmployee($data) {
    
        $params_employee = array();
    
        foreach ($data as $key => $value) {
            if (substr($key, 0, 8) == "employee"){
                $params_employee[$key] = $value;
            }
        }
        $employee = civicrm_api3('KlantMedewerker', 'Get', $params_employee);
        if (!$employee) {
            $employee = civicrm_api3('KlantMedewerker', 'Create', $params_employee);
        }
    
        return $employee;
    }

    private function _addEmployerRelation($case_id, $data) {

        $config = CRM_Basis_Config::singleton();

        $result = civicrm_api3('Relationship', 'create', array(
            'sequential' => 1,
            'contact_id_a' => $data['contact_id'],
            'contact_id_b' => $data['employer_id'],
            'relationship_type_id' => $config->getIsWerknemerVanRelationshipType(),
            'case_id' => $case_id,
        ));

        return $result;
    }
    
  private function _saveZiektemelding($data) {

      $config = CRM_Basis_Config::singleton();
      
      $params = array();
      $params_employer = array();
      $params_employee = array();
      

      foreach ($data as $key => $value) {
          $params[$key] = $value;
      }

      // rename ziektemelding custom fields for api  ($customFields, $data, &$params)
      $this->_addToParamsCustomFields($config->getZiektemeldingZiekteperiodeCustomGroup('custom_fields'), $data, $params);


      // get the employer
      $params['employer_id'] = $this->_getEmployer($data)['contact_id'];
      
      // get the employee
      $params['contact_id'] = $this->_getEmployee($data)['contact_id'];
      
      try {

          // save the illness
          $createdCase = civicrm_api3('Case', 'create', $params);

          // add/update employer role in this case
          $this->_addEmployerRelation($createdCase['values'][0]['id'], $data);


          return $createdCase['values'][0];
      }
      catch (CiviCRM_API3_Exception $ex) {
          throw new API_Exception(ts('Could not create a contact in '.__METHOD__
              .', contact your system administrator! Error from API Contact create: '.$ex->getMessage()));
      }
  }

}