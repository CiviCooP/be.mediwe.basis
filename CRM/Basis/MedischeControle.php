<?php

/**
 * Class to process MedischeControle in Mediwe
 *
 * @author Erik Hommel (CiviCooP) <erik.hommel@civicoop.org>
 * @date 31 May 2017
 * @license AGPL-3.0
 */
class CRM_Basis_MedischeControle {

    private $_medischeControleCaseTypeName = NULL;
    private $_medischeControleCaseTypeId = NULL;

  /**
   * CRM_Basis_Klant constructor.
   */
   public function __construct()
   {
     $config = CRM_Basis_Config::singleton();
     $medischeControleCaseType = $config->getMedischeControleCaseType();
     $this->_medischeControleCaseTypeName = $medischeControleCaseType['name'];
     $this->_medischeControleCaseTypeId = $medischeControleCaseType['id'];
   }

  /**
   * Method to create a new medischeControle
   *
   * @param $params
   * @return array
   * @throws API_Exception when error from api Case Create
   */
  public function create($params) {

      // ensure mandatory data
      if (!isset($params['control_date'])) {
          throw new Exception('Controledatum ziekte ontbreekt!');
      }

      // create/update ziektemelding
      $ziektemelding = new CRM_Basis_Ziektemelding();
      $melding = $ziektemelding->create($params);
      $params['mediwe_ziekte_id'] = $melding['id'];

      // ensure case type is set correctly
      $params['case_type_id'] = $this->_medischeControleCaseTypeName;

      // get the employee
      $params['contact_id'] = $melding['employee_id'];

      // get the employer
      $params['employer_id'] = $melding['employer_id'];

      if (!isset($params['id'])) {
          // exists looks for overlapping periods for this employee
          $exists = $this->exists($params);
          if (!$exists) {
              unset($params['id']);
              return $this->_saveMedischeControle($params);
          } else {
              $params['id'] = $exists->id;
          }
      }

      $this->update($params);
  }

  /**
   * Method to update an medischeControle
   *
   * @param $params
   * @return array
   */
  public function update($params) {

       try {
            return $this->_saveMedischeControle($params);
        }
        catch (CiviCRM_API3_Exception $ex) {
            CRM_Core_Error::debug('function update params', $params);
            throw new API_Exception(ts('Could not create an medischeControle in '.__METHOD__
                .', contact your system administrator! Error from API Case create: '.$ex->getMessage()));
        }
  }

  /**
   * Method to check if an medischeControle exists
   *
   * @param $params
   * @return bool
   */
  public function exists($params) {
      $medischeControle = array();

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
                      ca.case_type_id =  " . $this->_medischeControleCaseTypeId . " 
                    AND
                      ca.is_deleted = 0  
                    AND
                      cc.contact_id = " . $params['contact_id'] . "
                    AND 
                        ca.start_date =  '" . $params['control_date'] . "'      
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
   * Method to get all medischeControlees that meet the selection criteria based on incoming $params
   *
   * @param $params
   * @return array
   */
  public function get($params) {
    $medischeControles = array();

    try {

      $medischeControles = civicrm_api3('Case', 'get', $params)['values'];
      $this->_addMedischeControleAllFields($medischeControles);
    }
    catch (CiviCRM_API3_Exception $ex) {
    }

    return $medischeControles;
  }

  /**
   * Method to delete an medischeControle with id (set to is_deleted in CiviCRM)
   *
   * @param $medischeControleId
   * @return array
   */
  public function deleteWithId($medischeControleid) {
      $medischeControle = array();

      $params['id'] = $medischeControleid;
      try {
          if ($this->exists($params)) {
              $medischeControle = civicrm_api3('Case', 'delete', $params);
          }
      }
      catch (CiviCRM_API3_Exception $ex) {
          throw new API_Exception(ts('Could not create an medischeControle in '.__METHOD__
              .', contact your system administrator! Error from API Case delete: '.$ex->getMessage()));
      }

      return $medischeControle;
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
      }

      return $employer['values'][0];
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
        }

        return $employee['values'][0];
    }

    private function _addEmployerRelation($case_id, $data) {

        $config = CRM_Basis_Config::singleton();

        $params = array(
            'sequential' => 1,
            'contact_id_a' => $data['employer_id'],
            'contact_id_b' => $data['contact_id'],
            'relationship_type_id' => $config->getVraagtControleAanRelationshipType()['id'],
            'case_id' => $case_id,
        );

        try {
            $result = civicrm_api3('Relationship', 'getsingle', array(
                'sequential' => 1,
                'relationship_type_id' => $config->getVraagtControleAanRelationshipType()['id'],
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
    
  private function _saveMedischeControle($data) {

      $config = CRM_Basis_Config::singleton();
      
      $params = array();

      foreach ($data as $key => $value) {
          if ($value) {
              $params[$key] = $value;
          }
      }

      // check ziektemelding
      if (!isset($params['mediwe_ziekte_id'])) {

          $ziektemelding = new CRM_Basis_Ziektemelding();
          $melding = $ziektemelding->create($params);
          $params['mediwe_ziekte_id'] = $melding['id'];

          // ensure case type is set correctly
          $params['case_type_id'] = $this->_medischeControleCaseTypeName;

          // get the employee
          $params['contact_id'] = $melding['employee_id'];

          // get the employer
          $params['employer_id'] = $melding['employer_id'];

      }

      if (!$params['id']) {
          unset($params['id']);
      }

      try {

          // save the medical control

          $params['subject'] = "Medische controle van " . $params['control_date'];
          $params['start_date'] = $params['control_date'];
          if (isset($params['end_date'])) {
              unset($params['end_date']);
          }

          $createdCase = civicrm_api3('Case', 'create', $params);

          //  custom fields for api  ($customFields, $data, &$params)
          $this->_saveCustomFields($config->getMedischeControleCustomGroup(), $data, $createdCase['id']);

          // add/update employer role in this case
          $params_relation = array();
          $params_relation['contact_id'] = $params['contact_id'];
          $params_relation['employer_id'] = $params['employer_id'];
          $this->_addEmployerRelation($createdCase['id'], $params_relation);

          return $createdCase['values'];
      }
      catch (CiviCRM_API3_Exception $ex) {
          throw new API_Exception(ts('Could not create a contact in '.__METHOD__
              .', contact your system administrator! Error from API Contact create: '.$ex->getMessage()));
      }
  }

    private function _addMedischeControleAllFields(&$controles)
    {
        $config = CRM_Basis_Config::singleton();
        $ziektemelding = new CRM_Basis_Ziektemelding();

        foreach ($controles as $arrayRowId => $medischeControle) {

            if (isset($medischeControle['id'])) {
                // medische controle custom fields
                $controles[$arrayRowId] = $config->addDaoData($config->getMedischeControleCustomGroup(), $controles[$arrayRowId]);
                
                // gegevens ziektemelding
                $illness_id = $controles[$arrayRowId]['mediwe_ziekte_id'];
                $illness = $ziektemelding->get( array ( 'id' => $illness_id ))[$illness_id];

                foreach ($illness as $key => $value) {
                    if (substr($key, 0, 8) != 'employee') {
                        $newkey = "illness_" . $key;
                    }
                    else {
                        $newkey = $key;
                    }

                    $controles[$arrayRowId][$newkey] = $value;
                }
            }
        }
    }

}