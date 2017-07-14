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
      // if id is set, then update
      if (isset($params['id'])) {
          $this->update($params);
      } else {
          if (!empty($params['name'])) {
              // ensure contact_type and contact_sub_type are set correctly
              $params['contact_type'] = 'Individual';
              $params['contact_sub_type'] = $this->_klantMedewerkerContactSubTypeName;
          }
          // check if klant can not be found yet and only create if not
          if ($this->exists($params) === FALSE) {
              try {
                  $createdContact = civicrm_api3('Contact', 'create', $params);
                  $this->addKlantCustomFields($createdContact['values']);
                  $medewerker = $createdContact['values'];
                  return $medewerker;
              }
              catch (CiviCRM_API3_Exception $ex) {
                  throw new API_Exception(ts('Could not create a contact in '.__METHOD__
                      .', contact your system administrator! Error from API Contact create: '.$ex->getMessage()));
              }

          } else {
              // todo maken activity type for DataOnderzoek of iets dergelijks zodat deze gevallen gesignaleerd kunnen worden
          }
      }
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