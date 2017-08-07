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
      if (!isset($params['subject'])) {
          throw new Exception('Onderwerp van het dossier ontbreekt!');
      }

        if (isset($params['id'])) {
          $this->update($params);
        } else {
          // check if ziektemelding can not be found yet and only create if not
          if ($this->exists($params) === FALSE) {
            try {
              $createdCase = civicrm_api3('Case', 'create', $params);
              $ziektemelding = $createdCase['values'];
              return $ziektemelding;
            }
            catch (CiviCRM_API3_Exception $ex) {
              throw new API_Exception(ts('Could not create an ziektemelding in '.__METHOD__
                .', contact your system administrator! Error from API Case create: '.$ex->getMessage()));
            }

          } else {
            // todo maken activity type for DataOnderzoek of iets dergelijks zodat deze gevallen gesignaleerd kunnen worden
          }
    }
  }

  /**
   * Method to update an ziektemelding
   *
   * @param $params
   * @return array
   */
  public function update($params) {
    $ziektemelding = array();

    if ($this->exists($params)) {
        try {
            $ziektemelding = civicrm_api3('Case', 'create', $params);
        }
        catch (CiviCRM_API3_Exception $ex) {
            throw new API_Exception(ts('Could not create an ziektemelding in '.__METHOD__
                .', contact your system administrator! Error from API Case create: '.$ex->getMessage()));
        }

    }
    return $ziektemelding;
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
   * @return bool (if delete was succesfull or not)
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

}