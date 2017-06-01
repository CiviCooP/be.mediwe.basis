<?php

/**
 * Class to process KlantMedewerker in Mediwe
 *
 * @author Erik Hommel (CiviCooP) <erik.hommel@civicoop.org>
 * @date 31 May 2017
 * @license AGPL-3.0
 */
class CRM_Basis_KlantMedewerker {

  private $_klantMedewerkerId = NULL;

  /**
   * CRM_Basis_KlantMedewerker constructor.
   */
  public function __construct() {
  }

  /**
   * Method to create a new klant medewerker
   *
   * @param $params
   * @return array
   */
  public function create($params) {
    $klantMedewerker = array();
    return $klantMedewerker;
  }

  /**
   * Method to update a klant medewerker
   *
   * @param $params
   * @return array
   */
  public function update($params) {
    $klantMedewerker = array();
    return $klantMedewerker;
  }

  /**
   * Method to check if a klant medewerker exists
   *
   * @param $params
   * @return bool
   */
  public function exists($params) {
    return TRUE;
  }

  /**
   * Method to get all klant medewerkers that meet the selection criteria based on incoming $params
   *
   * @param $params
   * @return array
   */
  public function get($params) {
    $klantMedewerkers = array();
    return $klantMedewerkers;
  }

  /**
   * Method to delete klant medewerker with id (set to is_deleted in CiviCRM)
   *
   * @param $klantMedewerkerId
   * @return bool (if delete was succesfull or not)
   */
  public function deleteWithId($klantMedewerkerId) {
    return TRUE;
  }

  /**
   * Method to delete all medewerkers from a klant with klantId (set to is_deleted in CiviCRM)
   *
   * @param $klantId
   * @return array
   */
  public function deleteWithKlantId($klantId) {
    return TRUE;
  }
}