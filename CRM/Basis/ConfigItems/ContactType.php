<?php
/**
 * Class for ContactType configuration
 *
 * @author Erik Hommel (CiviCooP) <erik.hommel@civicoop.org>
 * @date 3 Feb 2016
 * @license AGPL-3.0
 */
class CRM_Basis_ConfigItems_ContactType {

  protected $_apiParams = array();

  /**
   * Method to validate params for create
   *
   * @param $params
   * @throws Exception when missing mandatory params
   */
  protected function validateCreateParams($params) {
    if (!isset($params['name']) || empty($params['name'])) {
      throw new Exception('Missing mandatory param name in '.__METHOD__);
    }
    $this->_apiParams = $params;
  }

  /**
   * Method to create contact type
   *
   * @param array $params
   * @return mixed
   * @throws Exception when error from API ContactType Create
   */
  public function create($params) {
    $this->validateCreateParams($params);
    $existing = $this->getWithName($this->_apiParams['name']);
    if (isset($existing['id'])) {
      $this->_apiParams['id'] = $existing['id'];
    }
    if (!isset($this->_apiParams['label']) || empty($this->_apiParams['label'])) {
      $this->_apiParams['label'] = CRM_Basis_Utils::buildLabelFromName($this->_apiParams['name']);
    }
    $this->_apiParams['is_active'] = 1;
    try {
      civicrm_api3('ContactType', 'Create', $this->_apiParams);
      $this->updateNavigationMenuUrl();
    } catch (CiviCRM_API3_Exception $ex) {
      throw new Exception('Could not create or update contact type with name '.$this->_apiParams['name']
        .' in '.__METHOD__.', error from API ContactType Create: '.$ex->getMessage());
    }
  }

  /**
   * Method to check if there is a navigation menu option for the contact type
   * and if so, update name and url
   *
   * @access private
   */
  private function updateNavigationMenuUrl() {
    // todo check if this is still applicable
    // check if there is a "New <label>" entry in the navigation table
    $query = "SELECT * FROM civicrm_navigation WHERE label = %1";
    $label = "New ".$this->_apiParams['label'];
    $dao = CRM_Core_DAO::executeQuery($query, array(1 => array($label, 'String')));
    $validParent = array("New Organization", "New Individual", "New Household");
    $newUrl = 'civicrm/contact/add&ct=Organization&cst='.$this->_apiParams['name'].'&reset=1';
    $newName = "New ".$this->_apiParams['name'];
    while ($dao->fetch()) {
      // parent should be either New Organization, New Individual or New Household
      if (isset($dao->parent_id)) {
        $parentQuery = "SELECT name FROM civicrm_navigation WHERE id = %1";
        $parentName = CRM_Core_DAO::singleValueQuery($parentQuery, array(1 => array($dao->parent_id, 'Integer')));
        if (in_array($parentName, $validParent)) {
          $update = "UPDATE civicrm_navigation SET url = %1, name = %2 WHERE id = %3";
          $params = array(
            1 => array($newUrl, 'String'),
            2 => array($newName, 'String'),
            3 => array($dao->id, 'Integer')
          );
          CRM_Core_DAO::executeQuery($update, $params);
        }
      }
    }
  }

  /**
   * Method to get contact sub type with name
   *
   * @param string $contactTypeName
   * @return array|bool
   * @access public
   * @static
   */
  public function getWithName($contactTypeName) {
    try {
      return civicrm_api3('ContactType', 'Getsingle', array('name' => $contactTypeName));
    } catch (CiviCRM_API3_Exception $ex) {
      return FALSE;
    }
  }

  /**
   * Method to disable contact type
   *
   * @param $contactTypeName
   */
  public function disableContactType($contactTypeName) {
    if (!empty($contactTypeName)) {
      // catch any errors and ignore (disabling can be done manually if problems)
      try {
        // get contact type with name
        $contactTypeId = civicrm_api3('ContactType', 'getvalue', array('name' => $contactTypeName, 'return' => 'id'));
        $sqlContactType = "UPDATE civicrm_contact_type SET is_active = %1 WHERE id = %2";
        CRM_Core_DAO::executeQuery($sqlContactType, array(
          1 => array(0, 'Integer'),
          2 => array($contactTypeId, 'Integer')));
      } catch (CiviCRM_API3_Exception $ex) {
      }
    }
  }

  /**
   * Method to enable contact type
   *
   * @param $contactTypeName
   */
  public function enableContactType($contactTypeName) {
    if (!empty($contactTypeName)) {
      // catch any errors and ignore (disabling can be done manually if problems)
      try {
        // get contact type with name
        $contactTypeId = civicrm_api3('ContactType', 'getvalue', array('name' => $contactTypeName, 'return' => 'id'));
        $sqlContactType = "UPDATE civicrm_contact_type SET is_active = %1 WHERE id = %2";
        CRM_Core_DAO::executeQuery($sqlContactType, array(
          1 => array(1, 'Integer'),
          2 => array($contactTypeId, 'Integer')));
      } catch (CiviCRM_API3_Exception $ex) {
      }
    }
  }

  /**
   * Method to uninstall contact type
   *
   * @param $contactTypeName
   */
  public function uninstallContactType($contactTypeName) {
    if (!empty($contactTypeName)) {
      // catch any errors and ignore (disabling can be done manually if problems)
      try {
        // get contact type with name
        $contactTypeId = civicrm_api3('ContactType', 'getvalue', array('name' => $contactTypeName, 'return' => 'id'));
        civicrm_api3('ContactType', 'delete', array('id' => $contactTypeId,));
      } catch (CiviCRM_API3_Exception $ex) {
      }
    }
  }
}