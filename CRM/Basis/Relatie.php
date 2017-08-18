<?php

/**
 * Class to process Relationship in Mediwe
 *
 * @author Erik Hommel (CiviCooP) <erik.hommel@civicoop.org>
 * @date 31 May 2017
 * @license AGPL-3.0
 */
class CRM_Basis_Relatie {


  /**
   * CRM_Basis_Klant constructor.
   */
   public function __construct()
   {
     $config = CRM_Basis_Config::singleton();
   }

  /**
   * Method to create a new address
   *
   * @param $params
   * @return array
   * @throws API_Exception when error from api Relationship Create
   */
  public function create($params) {
    //CRM_Core_Error::debug('params', $params);
    //exit();
    // if id is set, then update

    if (isset($params['id'])) {
      return $this->update($params);
    } else {
      // check if phone can not be found yet and only create if not
      if ($this->exists($params) === FALSE) {
        try {
          $createdRelationship = civicrm_api3('Relationship', 'create', $params);
          $relationship = $createdRelationship['values'];
          return $relationship;
        }
        catch (CiviCRM_API3_Exception $ex) {
          throw new API_Exception(ts('Could not create a relationship in '.__METHOD__
            .', contact your system administrator! Error from API Mail create: '.$ex->getMessage()));
        }

      } else {
        // todo maken activity type for DataOnderzoek of iets dergelijks zodat deze gevallen gesignaleerd kunnen worden
      }
    }
  }

  /**
   * Method to update an address
   *
   * @param $params
   * @return array
   */
  public function update($params) {
    $relationship = array();

    if ($this->exists($params)) {
        try {
            $relationship = civicrm_api3('Relationship', 'create', $params);
        }
        catch (CiviCRM_API3_Exception $ex) {
            throw new API_Exception(ts('Could not create a relationship in '.__METHOD__
                .', contact your system administrator! Error from API Relationship create: '.$ex->getMessage()));
        }

    }
    return $relationship;
  }

  /**
   * Method to check if an address exists
   *
   * @param $params
   * @return bool
   */
  public function exists($params) {
      $relationship = array();

      if (!isset($params['contact_id_a'])) {
          throw new Exception('Klant A identificatie ontbreekt!');
      }
      if (!isset($params['contact_id_b'])) {
          throw new Exception('Klant B identificatie ontbreekt!');
      }
      if (!isset($params['relationship_type_id'])) {
          throw new Exception('Soort relatie ontbreekt!');
      }

      
      try {
          $relationship = civicrm_api3('Relationship', 'get', $params);
      }
      catch (CiviCRM_API3_Exception $ex) {
          return false;
      }
      return true;
  }

  /**
   * Method to get all addresses that meet the selection criteria based on incoming $params
   *
   * @param $params
   * @return array
   */
  public function get($params) {
    $relaties = array();
;
    try {
      $relationships = civicrm_api3('Relationship', 'get', $params);

        $relaties = $relationships['values'];
    }
    catch (CiviCRM_API3_Exception $ex) {
    }
    return $relaties;
  }


  /**
   * Method to delete an address with id (set to is_deleted in CiviCRM)
   *
   * @param $addressId
   * @return bool (if delete was succesfull or not)
   */
  public function deleteWithId($relationship_id) {
      $relationship = array();

      $params['id'] = $relationship_id;
      try {
          if ($this->exists($params)) {
              $relationship = civicrm_api3('Relationship', 'delete', $params);
          }
      }
      catch (CiviCRM_API3_Exception $ex) {
          throw new API_Exception(ts('Could not create a phone in '.__METHOD__
              .', contact your system administrator! Error from API Relationship delete: '.$ex->getMessage()));
      }

      return $relationship;
  }

}