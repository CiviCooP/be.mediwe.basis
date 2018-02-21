<?php

/**
 * Class to process Huisbezoek in Mediwe
 *
 * @author Erik Hommel (CiviCooP) <erik.hommel@civicoop.org>
 * @date 31 May 2017
 * @license AGPL-3.0
 */
class CRM_Basis_Huisbezoek {

  private $_huisbezoekActivityTypeName = NULL;

  private $_huisbezoekActivityTypeId = NULL;

  /**
   * CRM_Basis_Klant constructor.
   */
  public function __construct() {
    $config = CRM_Basis_Config::singleton();
    $huisbezoekActivityType = $config->getHuisbezoekActivityType();
    $this->_huisbezoekActivityTypeName = $huisbezoekActivityType['name'];
    $this->_huisbezoekActivityTypeId = $huisbezoekActivityType['id'];
  }

  /**
   * Method to create a new huisbezoek
   *
   * @param $params
   *
   * @return array
   * @throws API_Exception when error from api Case Create
   */
  public function create($params) {

    // create/update huisbezoek
    /******************
     *  De link met het dossier Medische Controle wordt gelegd via de case_id
     *  De datum + uur van de controle in het veld activity_date_time
     *  Het adres van de controle in custom group "mediwe_home_visit"
     *  Het resultaat van de controle in custom group "mediwe_control_result"
     *
     *  De controlearts wordt toegekend aan de opdracht via zijn rol in de medische controle
     *  en komt hier niet voor.
     *
     *  case_id is in onze interface een verplicht veld
     *  datum_controle is ook een verplicht veld
     */

    // ensure mandatory data
    if (!isset($params['control_date'])) {
      throw new Exception('Controledatum huisbezoek ontbreekt!');
    }
    if (!isset($params['case_id'])) {
      throw new Exception('Verwijzing naar het dossier medische controle ontbreekt!');
    }

    // ensure activity type is set correctly
    $params['activity_type_id'] = $this->_huisbezoekActivityTypeName;

    // Standard velden
    $params['subject'] = "Huisbezoek op " . substr($params['date_control'], 0, 10);
    $params['activity_date_time'] = $params['date_control'];

    if (!isset($params['id'])) {
      // exists looks for this home visit
      $params_exists = [
        'subject' => $params['subject'],
        'activity_type_id' => $params['activity_type_id'],
        'case_id' => $params['case_id'],
      ];
      $exists = $this->exists($params_exists);
      if (!$exists) {
        unset($params['id']);
        return $this->_saveHuisbezoek($params);
      }
      else {
        $params['id'] = $exists['id'];
      }
    }

    $this->update($params);
  }

  /**
   * Method to update an huisbezoek
   *
   * @param $params
   *
   * @return array
   */
  public function update($params) {

    // ensure mandatory data
    if (!isset($params['control_date'])) {
      throw new Exception('Controledatum huisbezoek ontbreekt!');
    }
    if (!isset($params['case_id'])) {
      throw new Exception('Verwijzing naar het dossier medische controle ontbreekt!');
    }

    // ensure activity type is set correctly
    $params['activity_type_id'] = $this->_huisbezoekActivityTypeName;

    // Standard velden
    $params['subject'] = "Huisbezoek op " . substr($params['date_control'], 0, 10);
    $params['activity_date_time'] = $params['date_control'];

    try {
      return $this->_saveHuisbezoek($params);
    }
    catch (CiviCRM_API3_Exception $ex) {
      throw new API_Exception(ts('Could not create an huisbezoek in ' . __METHOD__
        . ', contact your system administrator! Error from API Case create: ' . $ex->getMessage()));
    }
  }

  /**
   * Method to check if an huisbezoek exists
   *
   * @param $params
   *
   * @return bool
   */
  public function exists($params) {
    $huisbezoek = [];

    if (isset($params['id'])) {
      return $params['id'];
    }
    else {
      try {
        $huisbezoek = civicrm_api3('Activity', 'get', $params)['values'][0];
      }
      catch (CiviCRM_API3_Exception $ex) {
        return FALSE;
      }

      return $huisbezoek;

    }
  }

  /**
   * Method to get all huisbezoekes that meet the selection criteria based on
   * incoming $params
   *
   * @param $params
   *
   * @return array
   */
  public function get($params) {
    $huisbezoeken = [];
    try {
      // ensure activity type is set correctly
      $params['activity_type_id'] = $this->_huisbezoekActivityTypeName;
      $huisbezoeken = civicrm_api3('Activity', 'get', $params)['values'];
      if ($huisbezoeken) {
        $this->getHuisbezoekCustomFields($huisbezoeken);
      }
    }
    catch (CiviCRM_API3_Exception $ex) {
    }
    return $huisbezoeken;
  }

  /**
   * Method om custom velden aan een huisbezoek toe te voegen
   *
   * @param $medewerkers
   */
  private function getHuisbezoekCustomFields(&$huisbezoeken) {
    $config = CRM_Basis_Config::singleton();
    foreach ($huisbezoeken as $rowId => $huisbezoek) {
      if (isset($huisbezoek['id'])) {
        $extra = CRM_Basis_Utils::addSingleDaoData($config->getMedischeControleHuisbezoekCustomGroup(), $huisbezoek['id']);
        $huisbezoeken[$rowId] = array_merge($huisbezoek, $extra);
      }
    }
  }

  /**
   * Method to delete an huisbezoek with id (set to is_deleted in CiviCRM)
   *
   * @param $huisbezoekId
   *
   * @return array
   */
  public function deleteWithId($huisbezoekid) {
    $huisbezoek = [];

    $params['id'] = $huisbezoekid;
    try {
      if ($this->exists($params)) {
        $huisbezoek = civicrm_api3('Activity', 'delete', $params);
      }
    }
    catch (CiviCRM_API3_Exception $ex) {
      throw new API_Exception(ts('Could not delete a huisbezoek in ' . __METHOD__
        . ', contact your system administrator! Error from API Case delete: ' . $ex->getMessage()));
    }

    return $huisbezoek;
  }

  private function _saveCustomFields($customGroup, $data, $activity_id) {

    $id = 0;

    // get record
    $table = $customGroup['table_name'];

    $sql = "SELECT * FROM $table WHERE entity_id = $activity_id";

    $dao = CRM_Core_DAO::executeQuery($sql);
    if ($dao->fetch()) {
      $id = $dao->id;
    }
    else {
      $sql = "INSERT INTO $table (entity_id) VALUES($activity_id);";
      CRM_Core_DAO::executeQuery($sql);
      $sql = "SELECT * FROM $table WHERE entity_id = $activity_id";
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
      $sql .= " WHERE id = $id;";
      CRM_Core_DAO::executeQuery($sql);
    }

  }


  private function _saveHuisbezoek($data) {

    $config = CRM_Basis_Config::singleton();

    $params = [];

    foreach ($data as $key => $value) {
      if ($value) {
        $params[$key] = $value;
      }
    }

    if (!$params['id']) {
      unset($params['id']);
    }

    try {

      // save the home visit
      $createdActivity = civicrm_api3('Activity', 'create', $params);

      //  custom fields for home visit
      $this->_saveCustomFields($config->getMedischeControleHuisbezoekCustomGroup(), $data, $createdActivity['id']);

      // custom fields for home visit results
      if (isset($data['control_result_step'])) {
        $this->_saveCustomFields($config->getMedischeControleResultaatCustomGroup(), $data, $createdActivity['id']);
      }

      return $createdActivity['values'];
    }
    catch (CiviCRM_API3_Exception $ex) {
      throw new API_Exception(ts('Could not create a contact in ' . __METHOD__
        . ', contact your system administrator! Error from API Activity create: ' . $ex->getMessage()));
    }
  }

}
