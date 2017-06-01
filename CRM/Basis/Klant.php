<?php

/**
 * Class to process Klant in Mediwe
 *
 * @author Erik Hommel (CiviCooP) <erik.hommel@civicoop.org>
 * @date 31 May 2017
 * @license AGPL-3.0
 */
class CRM_Basis_Klant {

  private $_klantId = NULL;

  /**
   * CRM_Basis_Klant constructor.
   */
  public function __construct() {
  }

  /**
   * Method to create a new klant
   *
   * @param $params
   * @return array
   */
  public function create($params) {
    $klant = array();
    return $klant;
  }

  /**
   * Method to update a klant
   *
   * @param $params
   * @return array
   */
  public function update($params) {
    $klant = array();
    return $klant;
  }

  /**
   * Method to check if a klant exists
   *
   * @param $params
   * @return bool
   */
  public function exists($params) {
    return TRUE;
  }

  /**
   * Method to get all klanten that meet the selection criteria based on incoming $params
   *
   * @param $params
   * @return array
   */
  public function get($params) {
    $config = CRM_Basis_Config::singleton();
    $klanten = array();
    // ensure that contact sub type is set
    $params['contact_sub_type'] = $config->getKlantContactSubType();
    try {
      $contacts = civicrm_api3('Contact', 'get', $params);
      $klanten = $contacts['values'];
    }
    catch (CiviCRM_API3_Exception $ex) {
    }
    return $klanten;
  }

  /**
   * Method to delete klant with id (set to is_deleted in CiviCRM)
   *
   * @param $klantId
   * @return bool (if delete was succesfull or not)
   */
  public function deleteWithId($klantId) {
    return TRUE;
  }

}