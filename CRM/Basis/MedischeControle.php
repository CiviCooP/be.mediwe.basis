<?php

/**
 * Class to process MedischeControle in Mediwe
 *
 * @author Erik Hommel (CiviCooP) <erik.hommel@civicoop.org>
 * @author Klaas Eikelboom (CiviCooP) <klaas.eikelboom@civicoop.org>
 * @author Christophe Deman <christophe.deman@mediwe.be>
 * @date 22 Feb 2018
 * @license AGPL-3.0
 */
class CRM_Basis_MedischeControle {

  private $_todaysDate = NULL;

  /**
   * CRM_Basis_Klant constructor.
   */
  public function __construct() {
    $this->_todaysDate = new DateTime();
  }

  /**
   * Method om medische controle toe te voegen
   *
   * @param $params
   * @return array|bool
   */
  public function create($params) {
    // ensure mandatory data
    if (!isset($params['mmc_controle_datum'])) {
      CRM_Core_Error::createError(ts('Controledatum ziekte ontbreekt in ' .__METHOD__ ));
      return FALSE;
    }
    $params['case_type_id'] = CRM_Basis_Config::singleton()->getmedischeControleCaseType()['id'];
    if (isset($params['id'])) {
      return $this->update($params);
    } else {
      if ($this->exists($params)) {
        CRM_Core_Error::createError(ts('Probeert medische controle toe te voegen die al bestaat in ' . __METHOD__));
        return FALSE;
      }
      else {
        return $this->saveMedischeControle($params);
      }
    }
  }

  /**
   * Method om medische controle bij te werken
   *
   * @param $params
   * @return array|bool
   */
  public function update($params) {
    if (!$params['id']) {
      CRM_Core_Error::debug_log_message(ts('Poging medische controle dossier bij te werken zonder een ID mee te geven in ') > __METHOD__);
      return FALSE;
    }
    return $this->saveMedischeControle($params);
  }

  /**
   * Method om te checken of medische controle al bestaat
   *
   * @param $params
   * @return bool
   */
  public function exists($params) {
    if (isset($params['id'])) {
      return $params['id'];
    }
    else {
      if (isset($params['medewerker_id']) && isset($params['mcc_controle_datum'])) {
        $controleDatum = new DateTime($params['mcc_controle_datum']);
        $sql = "SELECT ca.id FROM civicrm_case ca INNER JOIN civicrm_case_contact cc ON ca.id = cc.case_id
        WHERE ca.case_type_id = %1 AND ca.is_deleted = %2 AND cc.contact_id = %3 AND ca.start_date = %4";
        return CRM_Core_DAO::singleValueQuery($sql, array(
          1 => array(CRM_Basis_Config::singleton()->getmedischeControleCaseType()['id'], 'Integer'),
          2 => array(0, 'Integer'),
          3 => array($params['medewerker_id'], 'Integer'),
          4 => array($controleDatum->format('Ymd'), 'String'),
        ));
      }
    }
    return FALSE;
  }

  /**
   * Method om medische controles op te halen
   *
   * @param $params
   * @return array
   */
  public function get($params) {
    $medischeControles = array();
    try {
      $medischeControles = civicrm_api3('Case', 'get', $params)['values'];
      // custom velden ophalen
    }
    catch (CiviCRM_API3_Exception $ex) {
    }
    return $medischeControles;
  }

  /**
   * Method om medische controle met id te verwijderen
   *
   * @param $medischeControleId
   * @return array|bool
   */
  public function deleteWithId($medischeControleId) {
    if (!isset($params['id'])) {
      CRM_Core_Error::debug_log_message(ts('Poging om medische controle te verwijderen zonder id in ') . __METHOD__);
      return FALSE;
    }
    try {
      civicrm_api3('Case', 'delete', array('id' => $medischeControleId));
    }
    catch (CiviCRM_API3_Exception $ex) {
      CRM_Core_Error::debug_log_message(ts('Fout bij verwijderen medische controle dossier met ID ') . $medischeControleId . ts(' in ') . __METHOD__);
      return FALSE;
    }
  }

  /**
   * Method om medische controle op te slaan
   *
   * @param $data
   * @return mixed
   * @throws API_Exception
   */
  private function saveMedischeControle($data) {
    $config = CRM_Basis_Config::singleton();
    $params = array();
    foreach ($data as $key => $value) {
      if ($value) {
        $params[$key] = $value;
      }
    }
    // maak eventueel ook een ziektemelding aan
    // haal of maak de klant
    // haal of maak de klantmedewerker
    // haal of maak de contactpersoon
    try {
      $params['subject'] = "Medische controle van " . $params['mmc_controle_datum'];
      $params['start_date'] = $params['mmc_controle_datum'];
      if (isset($params['end_date'])) {
        unset($params['end_date']);
      }
      $createdCase = civicrm_api3('Case', 'create', $params);
      // custom data voor medische controle opslaan
      return $createdCase['values'];
    }
    catch (CiviCRM_API3_Exception $ex) {
      throw new API_Exception(ts('Could not create a contact in ' . __METHOD__
         . ', contact your system administrator! Error from API Contact create: ' . $ex->getMessage()));
    }
  }
}