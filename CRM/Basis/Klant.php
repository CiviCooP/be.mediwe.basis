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
    $klantContactSubType = $config->getKlantContactSubType();
    $params['contact_sub_type'] = $klantContactSubType['name'];
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
        $sql = 'SELECT * FROM '.$config->getKlantGegevensCustomGroup('table_name').' WHERE entity_id = %1';
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
    return TRUE;
  }

}