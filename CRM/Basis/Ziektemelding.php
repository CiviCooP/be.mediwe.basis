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
    private $_ziektemeldingCaseTypeId = NULL;

  /**
   * CRM_Basis_Klant constructor.
   */
   public function __construct()
   {
     $config = CRM_Basis_Config::singleton();
     $ziektemeldingCaseType = $config->getZiektemeldingCaseType();
     $this->_ziektemeldingCaseTypeName = $ziektemeldingCaseType['name'];
     $this->_ziektemeldingCaseTypeId = $ziektemeldingCaseType['id'];
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

      // get the employer
      $params['employer_id'] = $this->_getEmployer($params)['id'];

      // get the employee
      $params['contact_id'] = $this->_getEmployee($params)['id'];


      // ensure mandatory data
      if (!isset($params['start_date'])) {
          throw new Exception('Begin datum ziekte ontbreekt!');
      }

      if (!isset($params['id'])) {
          // exists looks for overlapping periods for this employee
          $exists = $this->exists($params);

          if (!$exists) {
              return $this->_saveZiektemelding($params);
          } else {
              $params['id'] = $exists;
          }
      }

      return $this->update($params);


  }

  /**
   * Method to update an ziektemelding
   *
   * @param $params
   * @return array
   */
  public function update($params) {

      if (!isset($params['employer_id'])) {
          // get the employer
          $params['employer_id'] = $this->_getEmployer($params)['contact_id'];
      }

       try {
           $case =  $this->_saveZiektemelding($params);
           return $case;
        }
        catch (CiviCRM_API3_Exception $ex) {
            CRM_Core_Error::debug('function update params', $params);
            throw new API_Exception(ts('Could not create an ziektemelding in '.__METHOD__
                .', contact your system administrator! Error from API Case create: '.$ex->getMessage()));
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

      if (!isset($params['end_date'])) {
          $params['end_date'] = $params['start_date'];
      }

      if (isset($params['id'])) {
          return $params['id'];
      }
      else {
          $sql =    "
                    SELECT 
                      ca.*
                    FROM 
                      civicrm_case ca
                    INNER JOIN
                      civicrm_case_contact cc 
                    ON 
                      ca.id = cc.case_id 
                    WHERE
                      ca.case_type_id =  " . $this->_ziektemeldingCaseTypeId . " 
                    AND
                      cc.contact_id = " . $params['contact_id'] . "
                    AND (
                      (  ca.start_date >=  '" . $params['start_date'] . "' AND ca.start_date <=  '" . $params['end_date'] . "' )
                      OR
                      (  ca.end_date >=  '" . $params['start_date'] . "' AND ca.end_date <=  '" . $params['end_date'] . "' )
                      OR
                       (  ca.end_date >=  '" . $params['end_date'] . "' AND ca.start_date <=  '" . $params['start_date'] . "' )
                      )    
                ";
      }

      $dao = CRM_Core_DAO::executeQuery($sql);

      if ($dao->fetch()) {
          return $dao->id;
      }
      else {
          return false;
      }
  }

  /**
   * Method to get all ziektemeldinges that meet the selection criteria based on incoming $params
   *
   * @param $params
   * @return array
   */
  public function get($params) {
    $ziektemeldingen = array();

    try {

      $ziektemeldingen = civicrm_api3('Case', 'get', $params)['values'];
      $this->_addZiektemeldingAllFields($ziektemeldingen);
    }
    catch (CiviCRM_API3_Exception $ex) {
    }

    return $ziektemeldingen;
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

    private function _saveCustomFields($customGroup, $data, $case_id) {

        $id = 0;

        // get record
        $table = $customGroup['table_name'];
        $sql = "SELECT * FROM $table WHERE entity_id = $case_id";

        $dao = CRM_Core_DAO::executeQuery($sql);

        if ($dao->fetch()) {
            $id = $dao->id;
        }
        else {
            $sql = "INSERT INTO $table (entity_id) VALUES($case_id);";
            CRM_Core_DAO::executeQuery($sql);
            $sql = "SELECT * FROM $table WHERE entity_id = $case_id";
            $dao = CRM_Core_DAO::executeQuery($sql);
            if ($dao->fetch()) {
                $id = $dao->id;
            }
        }



        $sql = "UPDATE $table SET ";
        $count = 0;
        $customFields = $customGroup['custom_fields'];
        foreach ($customFields as $field) {
            $fieldName = $field['column_name'];

            if (isset($data[$field['name']])) {
                if (isset($data[$field['name']]) ) {
                    $count += 1;
                    $value = $data[$field['name']];
                    if ($value == 'null') {
                        $sql .= " $fieldName = NULL,";
                    }
                    else {
                        $sql .= " $fieldName = '" . $value . "',";
                    }
                }
            }
        }

        $sql = substr($sql, 0, -1);
        if ($count != 0 ) {
            $sql .= " WHERE id = $id;";
            CRM_Core_DAO::executeQuery($sql);
        }

    }

  private function _getEmployer($data) {

      $params_employer = array();

      foreach ($data as $key => $value) {
          if (substr($key, 0, 8) == "employer" && $value){
              $mykey = substr($key, 9);
              $params_employer[$mykey] = $value;
          }
      }

      $employer = civicrm_api3('Klant', 'Get', $params_employer);

      if ($employer['count'] == 0) {
          $employer = civicrm_api3('Klant', 'Create', $params_employer);
          return $employer['values'];
      }
      else {
          return $employer['values'][0];
      }


  }

    private function _getEmployee($data) {
    
        $params_employee = array();
    
        foreach ($data as $key => $value) {
            if (substr($key, 0, 8) == "employee" && $value ){
                $mykey = substr($key, 9);
                $params_employee[$mykey] = $value;
            }
        }

        $employee = civicrm_api3('KlantMedewerker', 'Get', $params_employee);

        if ($employee['count'] == 0) {
            $employee = civicrm_api3('KlantMedewerker', 'Create', $params_employee);
            return $employee['values'];
        }
        else {
            return $employee['values'][0];
        }


    }

    private function _addEmployerRelation($case_id, $data) {

        $config = CRM_Basis_Config::singleton();

        $params = array(
            'sequential' => 1,
            'contact_id_a' => $data['contact_id'],
            'contact_id_b' => $data['employer_id'],
            'relationship_type_id' => $config->getZiektemeldingRelationshipType()['id'],
            'case_id' => $case_id,
        );

        try {
            $result = civicrm_api3('Relationship', 'getsingle', array(
                'sequential' => 1,
                'relationship_type_id' => $config->getZiektemeldingRelationshipType()['id'],
                'case_id' => $case_id,
            ));
            $params['id'] = $result['id'];
        }
        catch (Exception $e) {
           $result = false;
        }

        $result = civicrm_api3('Relationship', 'create', $params );

        return $result;
    }
    
  private function _saveZiektemelding($data) {

      $config = CRM_Basis_Config::singleton();
      
      $params = array();

      foreach ($data as $key => $value) {
          if ($value) {
              $params[$key] = $value;
          }
      }

      if (!$params['id']) {
          unset($params['id']);
      }

      try {

          // save the illness
          $params['subject'] = "Ziektemelding periode vanaf " . $params['start_date'];
          $createdCase = civicrm_api3('Case', 'create', $params);

          // save custom data
          $this->_saveCustomFields($config->getZiektemeldingZiekteperiodeCustomGroup(), $data, $createdCase['id']);

          // add/update employer role in this case
          $params_relation = array();
          $params_relation['contact_id'] = $params['contact_id'];
          $params_relation['employer_id'] = $params['employer_id'];
          $this->_addEmployerRelation($createdCase['id'], $params_relation);

          // add employer and employee ids to case
          $createdCase['values'][$createdCase['id']]['employer_id'] = $params['employer_id'];
          $createdCase['values'][$createdCase['id']]['employee_id'] = $params['contact_id'];

          return $createdCase['values'][$createdCase['id']];
      }
      catch (CiviCRM_API3_Exception $ex) {
          throw new API_Exception(ts('Could not create a contact in '.__METHOD__
              .', contact your system administrator! Error from API Contact create: '.$ex->getMessage()));
      }
  }

    private function _addZiektemeldingAllFields(&$meldingen)
    {
        $config = CRM_Basis_Config::singleton();

        foreach ($meldingen as $arrayRowId => $ziektemelding) {

            if (isset($ziektemelding['id'])) {
                // ziekteperiode custom fields
                $meldingen[$arrayRowId] = $config->addDaoData($config->getZiektemeldingZiekteperiodeCustomGroup(), $meldingen[$arrayRowId]);

                // gegevens werknemer en diens werkgever
                $medewerker_id = $ziektemelding['client_id'][1];

                $employee = civicrm_api3('KlantMedewerker', 'Get', array('id' => $medewerker_id))['values'][0];
                foreach ($employee as $key => $value) {
                    $newkey = "employee_" . $key;
                    $meldingen[$arrayRowId][$newkey] = $value;
                }


            }
        }
    }

}