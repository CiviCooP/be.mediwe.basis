<?php

/**
 * Class to process Ziektemelding in Mediwe
 *
 * @author Erik Hommel (CiviCooP) <erik.hommel@civicoop.org>
 * @author Klaas Eikelboom (CiviCooP) <klaas.eikelboom@civicoop.org>
 * @author Christophe Deman <christophe.deman@mediwe.be>
 * @date 15 Feb 2018
 * @license AGPL-3.0
 */
class CRM_Basis_Ziektemelding {

  private $_ziektemeldingCaseTypeName = NULL;
  private $_ziektemeldingCaseTypeId = NULL;

  /**
   * CRM_Basis_Klant constructor.
   */
  public function __construct() {
    $config = CRM_Basis_Config::singleton();
    $ziektemeldingCaseType = $config->getZiektemeldingCaseType();
    $this->_ziektemeldingCaseTypeName = $ziektemeldingCaseType['name'];
    $this->_ziektemeldingCaseTypeId = $ziektemeldingCaseType['id'];
  }

  /**
   * Method om een ziektemelding aan te maken of bij te werken ziektemelding
   *
   * @param $params
   * @return array
   * @throws API_Exception when error from api Case Create
   */
  public function create($params) {
    // ensure case_type is set correctly
    $params['case_type_id'] = $this->_ziektemeldingCaseTypeName;
    // todo wat gaan we doen met overlappende periodes?
    if (!isset($params['id'])) {
      return $this->saveZiektemelding($params);
    }
  }

  /**
   * Method om ziektemeldingen op te halen
   *
   * @param $params
   * @return array
   */
  public function get($params) {
    // ensure contact_type and contact_sub_type are set correctly
    $params['case_type_id'] = $this->_ziektemeldingCaseTypeName;
    $params['is_deleted'] = 0;
    $ziektemeldingen = array();
    try {
      // ensure the right case type is selected
      $ziektemeldingen = civicrm_api3('Case', 'get', $params)['values'];
      $this->addZiektemeldingAllFields($ziektemeldingen);
    }
    catch (CiviCRM_API3_Exception $ex) {
    }
    return $ziektemeldingen;
  }

  /**
   * Method om ziektemelding te verwijderen met id
   *
   * @param $ziektemeldingId
   * @return array
   * @throws API_Exception
   */
  public function deleteWithId($ziektemeldingId) {
    $params['id'] = $ziektemeldingId;
    try {
      return civicrm_api3('Case', 'delete', $params);
    }
    catch (CiviCRM_API3_Exception $ex) {
      throw new API_Exception(ts('Could not create an ziektemelding in ' . __METHOD__ .
        ', contact your system administrator! Error from API Case delete: ' . $ex->getMessage()));
    }
  }

  /**
   * Method om de custom velden bij ziektemelding op te slaan
   *
   * @param $customGroup
   * @param $data
   * @param $caseId
   */
  private function saveCustomFields($customGroup, $data, $caseId) {
    $id = 0;
    // get record
    $table = $customGroup['table_name'];
    $sql = "SELECT * FROM $table WHERE entity_id = %1";
    $dao = CRM_Core_DAO::executeQuery($sql, array(1 => array($caseId, 'Integer')));
    if ($dao->fetch()) {
      $id = $dao->id;
    }
    else {
      $sql = "INSERT INTO $table (entity_id) VALUES(%1)";
      CRM_Core_DAO::executeQuery($sql, array(1 => array($caseId, 'Integer')));
      $sql = "SELECT * FROM $table WHERE entity_id = %1";
      $dao = CRM_Core_DAO::executeQuery($sql, array(1 => array($caseId, 'Integer')));
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
        if (isset($data[$field['name']])) {
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
    if ($count != 0) {
      $sql .= " WHERE id = %1";
      CRM_Core_DAO::executeQuery($sql, array(1 => array($id, 'Integer')));
    }
  }

  /**
   * @param $caseId
   * @param $data
   * @return array|bool
   * @throws CiviCRM_API3_Exception
   */
  private function addEmployerRelation($caseId, $data) {
    $config = CRM_Basis_Config::singleton();
    $params = array(
      'contact_id_a' => $data['employer_id'],
      'contact_id_b' => $data['contact_id'],
      'relationship_type_id' => $config->getIsWerknemerVanRelationshipTypeId(),
      'case_id' => $caseId,
    );
    try {
      $existing = civicrm_api3('Relationship', 'getsingle', array(
        'relationship_type_id' => $config->getIsWerknemerVanRelationshipTypeId(),
        'case_id' => $caseId,
      ));
      $params['id'] = $existing['id'];
    }
    catch (CiviCRM_API3_Exception $ex) {
    }
    return civicrm_api3('Relationship', 'create', $params);
  }

  /**
   * Method om ziektemelding op te slaan
   * @param $data
   * @return mixed
   * @throws API_Exception
   */
  private function saveZiektemelding($data) {
    $config = CRM_Basis_Config::singleton();
    $params = array();
    foreach ($data as $key => $value) {
      if ($value) {
        $params[$key] = $value;
      }
    }
    if (isset($params['id']) && !$params['id']) {
      unset($params['id']);
    }
    try {
      // save the illness
      $params['subject'] = "Ziektemelding periode vanaf " . $params['start_date'];
      $createdCase = civicrm_api3('Case', 'create', $params);

      // save custom data
      $this->saveCustomFields($config->getZiektemeldingZiekteperiodeCustomGroup(), $data, $createdCase['id']);

      // add/update employer role in this case
      $params_relation = array();
      $params_relation['contact_id'] = $params['contact_id'];
      $params_relation['employer_id'] = $params['employer_id'];
      $this->addEmployerRelation($createdCase['id'], $params_relation);

      // add employer and employee ids to case
      $createdCase['values'][$createdCase['id']]['employer_id'] = $params['employer_id'];
      $createdCase['values'][$createdCase['id']]['employee_id'] = $params['contact_id'];
      return $createdCase['values'][$createdCase['id']];
    }
    catch (CiviCRM_API3_Exception $ex) {
      throw new API_Exception(ts('Could not create a contact in ' . __METHOD__
        . ', contact your system administrator! Error from API Contact create: ' . $ex->getMessage()));
    }
  }

  /**
   * Method om ziektemelding custom velden toe te voegen
   * @param $meldingen
   * @throws CiviCRM_API3_Exception
   */
  private function addZiektemeldingAllFields(&$meldingen) {
    $config = CRM_Basis_Config::singleton();
    foreach ($meldingen as $arrayRowId => $ziektemelding) {
      if (isset($ziektemelding['id'])) {
        // ziekteperiode custom fields
        $meldingen[$arrayRowId] = $config->addDaoData($config->getZiektemeldingZiekteperiodeCustomGroup(), $meldingen[$arrayRowId]);
        // gegevens werknemer en diens werkgever
        $medewerkerId = $ziektemelding['client_id'][1];
        $employee = civicrm_api3('KlantMedewerker', 'Get', array('id' => $medewerkerId))['values'][0];
        foreach ($employee as $key => $value) {
          $newkey = "employee_" . $key;
          $meldingen[$arrayRowId][$newkey] = $value;
        }
      }
    }
  }

}
