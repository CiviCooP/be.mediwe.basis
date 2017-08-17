<?php

/**
 * Class to process Email in Mediwe
 *
 * @author Erik Hommel (CiviCooP) <erik.hommel@civicoop.org>
 * @date 31 May 2017
 * @license AGPL-3.0
 */
class CRM_Basis_Mail {


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
   * @throws API_Exception when error from api Email Create
   */
  public function create($params) {
    //CRM_Core_Error::debug('params', $params);
    //exit();
    // if id is set, then update

    if (isset($params['id'])) {
      return $this->update($params);
    } else {
      // check if email can not be found yet and only create if not
      if ($this->exists($params) === FALSE) {
        try {
          $createdEmail = civicrm_api3('Email', 'create', $params);
          $email = $createdEmail['values'];
          return $email;
        }
        catch (CiviCRM_API3_Exception $ex) {
          throw new API_Exception(ts('Could not create a mail address in '.__METHOD__
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
    $email = array();

    if ($this->exists($params)) {
        try {
            $email = civicrm_api3('Email', 'create', $params);
        }
        catch (CiviCRM_API3_Exception $ex) {
            throw new API_Exception(ts('Could not create an address in '.__METHOD__
                .', contact your system administrator! Error from API Email create: '.$ex->getMessage()));
        }

    }
    return $email;
  }

  /**
   * Method to check if an address exists
   *
   * @param $params
   * @return bool
   */
  public function exists($params) {
      $email = array();

      if (!isset($params['contact_id'])) {
          throw new Exception('Klant identificatie ontbreekt!');
      }
      if (!isset($params['location_type_id'])) {
          throw new Exception('Soort email ontbreekt!');
      }

      try {
          $email = civicrm_api3('Email', 'getsingle', $params);
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
    $emailsen = array();
;
    try {
      $addresses = civicrm_api3('Email', 'get', $params);

        $emailsen = $addresses['values'];
    }
    catch (CiviCRM_API3_Exception $ex) {
    }
    return $emailsen;
  }


  /**
   * Method to delete an address with id (set to is_deleted in CiviCRM)
   *
   * @param $addressId
   * @return bool (if delete was succesfull or not)
   */
  public function deleteWithId($addressid) {
      $email = array();

      $params['id'] = $addressid;
      try {
          if ($this->exists($params)) {
              $email = civicrm_api3('Email', 'delete', $params);
          }
      }
      catch (CiviCRM_API3_Exception $ex) {
          throw new API_Exception(ts('Could not create an address in '.__METHOD__
              .', contact your system administrator! Error from API Email delete: '.$ex->getMessage()));
      }

      return $email;
  }

}