<?php

/**
 * Class to process Klant in Mediwe
 *
 * @author Erik Hommel (CiviCooP) <erik.hommel@civicoop.org>
 * @date 31 May 2017
 * @license AGPL-3.0
 */
class CRM_Basis_Klant {

   private $_klantContactSubTypeName = NULL;
   private $_klantAdresLocationType = NULL;

  /**
   * CRM_Basis_Klant constructor.
   */
   public function __construct()
   {
     $config = CRM_Basis_Config::singleton();
     $contactSubType = $config->getKlantContactSubType();
     $locationType = $config->getKlantLocationType();

     $this->_klantContactSubTypeName = $contactSubType['name'];
     $this->_klantAdresLocationType = $locationType['name'];

   }

  /**
   * Method to create a new klant
   *
   * @param $params
   * @return array
   * @throws API_Exception when error from api Contact Create
   */
  public function create($params) {

    // if id is set, then update
    if (isset($params['id'])) {
      $this->update($params);
    } else {
      if (!empty($params['organization_name'])) {
        // ensure contact_type and contact_sub_type are set correctly
        $params['contact_type'] = 'Organization';
        $params['contact_sub_type'] = $this->_klantContactSubTypeName;
      }
      // check if klant can not be found yet and only create if not
      if ($this->exists($params) === FALSE) {
        try {
          $createdContact = civicrm_api3('Contact', 'create', $params);
          $this->addKlantCustomFields($createdContact['values']);
          $klant = $createdContact['values'];
CRM_Core_Error::debug('params', $params);
CRM_Core_Error::debug('klant', $klant);
exit();
          return $klant;
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
   * Method to update a klant
   *
   * @param $params
   * @return array
   */
  public function update($params) {
    $klant = array();
    $params['contact_sub_type'] = $this->_klantContactSubTypeName;

    if ($this->exists($params)) {
        try {
            $klant = civicrm_api3('Contact', 'create', $params);
        }
        catch (CiviCRM_API3_Exception $ex) {
            throw new API_Exception(ts('Could not create a contact in '.__METHOD__
                .', contact your system administrator! Error from API Contact create: '.$ex->getMessage()));
        }

    }
    return $klant;
  }

  /**
   * Method to check if a klant exists
   *
   * @param $params
   * @return bool
   */
  public function exists($params) {
      $klant = array();
      $params['contact_sub_type'] = $this->_klantContactSubTypeName;

      CRM_Core_Error::debug('klant', $params);
      exit();

      // ensure that contact sub type is set
      try {
          $klant = civicrm_api3('Contact', 'getsingle', $params);
      }
      catch (CiviCRM_API3_Exception $ex) {
          return false;
      }
      CRM_Core_Error::debug('klant', $klant);
      exit();
      return true;
  }

  /**
   * Method to get all klanten that meet the selection criteria based on incoming $params
   *
   * @param $params
   * @return array
   */
  public function get($params) {
    $klanten = array();
    // ensure that contact sub type is set
    $params['contact_sub_type'] = $this->_klantContactSubTypeName;
    try {
      $contacts = civicrm_api3('Contact', 'get', $params);
      $this->addKlantCustomFields($contacts['values']);
      $klanten = $contacts['values'];
    }
    catch (CiviCRM_API3_Exception $ex) {
    }
    return $klanten;
  }

  /**
   * Method to add custom fields to an array of contacts
   *
   * @param $contacts
   */
  private function addKlantCustomFields(&$contacts) {
    $config = CRM_Basis_Config::singleton();
    foreach ($contacts as $arrayRowId => $contact) {
      if (isset($contact['contact_id'])) {
            //  boekhouding
            $sql = 'SELECT * FROM '.$config->getKlantGegevensCustomGroup('civicrm_value_boekhouding_1').' WHERE entity_id = %1';
            $dao = CRM_Core_DAO::executeQuery($sql, array(
              1 => array($contact['contact_id'], 'Integer',),
            ));
            while ($dao->fetch()) {
              $contacts[$arrayRowId] = $this->placeKlantCustomFields($dao, $contact);;
            }

          // organisatie klant
          $sql = 'SELECT * FROM '.$config->getKlantGegevensCustomGroup('civicrm_value_interne_organisatie_22').' WHERE entity_id = %1';
          $dao = CRM_Core_DAO::executeQuery($sql, array(
              1 => array($contact['contact_id'], 'Integer',),
          ));
          while ($dao->fetch()) {
              $contacts[$arrayRowId] = $this->placeKlantCustomFields($dao, $contact);;
          }

          // expert systeem
          $sql = 'SELECT * FROM '.$config->getKlantGegevensCustomGroup('civicrm_value_expertsysteem_17').' WHERE entity_id = %1';
          $dao = CRM_Core_DAO::executeQuery($sql, array(
              1 => array($contact['contact_id'], 'Integer',),
          ));
          while ($dao->fetch()) {
              $contacts[$arrayRowId] = $this->placeKlantCustomFields($dao, $contact);;
          }

          // contrleprocedure klant
          $sql = 'SELECT * FROM '.$config->getKlantGegevensCustomGroup('civicrm_value_controleprocedure_klant_16').' WHERE entity_id = %1';
          $dao = CRM_Core_DAO::executeQuery($sql, array(
              1 => array($contact['contact_id'], 'Integer',),
          ));
          while ($dao->fetch()) {
              $contacts[$arrayRowId] = $this->placeKlantCustomFields($dao, $contact);;
          }
      }
    }
  }

  /**
   * Method to place the klant custom fields in the contact array based on the
   *
   * @param object $contactData (dao)
   * @param array $contactArray;
   * @return array
   */
  private function placeKlantCustomFields($contactData, $contactArray) {
    $config = CRM_Basis_Config::singleton();
    $customFields = $config->getKlantCustomFields();
    foreach ($customFields as $customFieldId => $customField) {
      $propertyName = $customField['column_name'];
      if (isset($contactData->$propertyName)) {
        $contactArray[$propertyName] = $contactData->$propertyName;
      }
    }
    return $contactArray;
  }

  /**
   * Method to delete klant with id (set to is_deleted in CiviCRM)
   *
   * @param $klantId
   * @return bool (if delete was succesfull or not)
   */
  public function deleteWithId($klantId) {
      $klant = array();

      // ensure that contact sub type is set
      $params['contact_sub_type'] = $this->_klantContactSubTypeName;
      $params['contact_id'] = $klantId;
      try {
          if ($this->exists($params)) {
              $klant = civicrm_api3('Contact', 'delete', $params);
          }
      }
      catch (CiviCRM_API3_Exception $ex) {
          throw new API_Exception(ts('Could not create a contact in '.__METHOD__
              .', contact your system administrator! Error from API Contact delete: '.$ex->getMessage()));
      }

      return $klant;
  }

}