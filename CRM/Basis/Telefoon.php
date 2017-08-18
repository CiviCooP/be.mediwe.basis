<?php

/**
 * Class to process Phone in Mediwe
 *
 * @author Erik Hommel (CiviCooP) <erik.hommel@civicoop.org>
 * @date 31 May 2017
 * @license AGPL-3.0
 */
class CRM_Basis_Telefoon {


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
   * @throws API_Exception when error from api Phone Create
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
          $createdPhone = civicrm_api3('Phone', 'create', $params);
          $phone = $createdPhone['values'];
          return $phone;
        }
        catch (CiviCRM_API3_Exception $ex) {
          throw new API_Exception(ts('Could not create a phone number in '.__METHOD__
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
    $phone = array();

    if ($this->exists($params)) {
        try {
            $phone = civicrm_api3('Phone', 'create', $params);
        }
        catch (CiviCRM_API3_Exception $ex) {
            throw new API_Exception(ts('Could not create an address in '.__METHOD__
                .', contact your system administrator! Error from API Phone create: '.$ex->getMessage()));
        }

    }
    return $phone;
  }

  /**
   * Method to check if an address exists
   *
   * @param $params
   * @return bool
   */
  public function exists($params) {
      $phone = array();

      if (!isset($params['contact_id'])) {
          throw new Exception('Klant identificatie ontbreekt!');
      }
      if (!isset($params['location_type_id'])) {
          throw new Exception('Soort telefoon ontbreekt!');
      }
      if (!isset($params['phone_type_id'])) {
          throw new Exception('Type telefoon ontbreekt!');
      }
      
      try {
          $phone = civicrm_api3('Phone', 'getsingle', $params);
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
    $telefoons = array();
;
    try {
      $phones = civicrm_api3('Phone', 'get', $params);

        $telefoons = $phones['values'];
    }
    catch (CiviCRM_API3_Exception $ex) {
    }
    return $telefoons;
  }


  /**
   * Method to delete an address with id (set to is_deleted in CiviCRM)
   *
   * @param $addressId
   * @return bool (if delete was succesfull or not)
   */
  public function deleteWithId($phone_id) {
      $phone = array();

      $params['id'] = $phone_id;
      try {
          if ($this->exists($params)) {
              $phone = civicrm_api3('Phone', 'delete', $params);
          }
      }
      catch (CiviCRM_API3_Exception $ex) {
          throw new API_Exception(ts('Could not create a phone in '.__METHOD__
              .', contact your system administrator! Error from API Phone delete: '.$ex->getMessage()));
      }

      return $phone;
  }

}