<?php
/**
 * Class for MembershipType configuration
 *
 * @author Erik Hommel (CiviCooP) <erik.hommel@civicoop.org>
 * @date 3 Feb 2016
 * @license AGPL-3.0
 */
class CRM_Basis_ConfigItems_MembershipType {

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
   * Method to create membership type
   *
   * @param array $params
   * @return mixed
   * @throws Exception when error from API MembershipType Create
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
      civicrm_api3('MembershipType', 'Create', $this->_apiParams);
      //$this->updateNavigationMenuUrl();
    } catch (CiviCRM_API3_Exception $ex) {
      CRM_Core_Error::debug('params', $this->_apiParams);
      throw new Exception('Could not create or update membership type with name '.$this->_apiParams['name']
        .' in '.__METHOD__.', error from API MembershipType Create: '.$ex->getMessage());
    }
  }

  /**
   * Method to check if there is a navigation menu option for the membership type
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
    $newUrl = 'civicrm/membership/add&ct=Organization&cst='.$this->_apiParams['name'].'&reset=1';
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
   * Method to get membership type with name
   *
   * @param string $membershipTypeName
   * @return array|bool
   * @access public
   * @static
   */
  public function getWithName($membershipTypeName) {
    try {
      return civicrm_api3('MembershipType', 'Getsingle', array('name' => $membershipTypeName));
    } catch (CiviCRM_API3_Exception $ex) {
      return FALSE;
    }
  }

  /**
   * Method to disable membership type
   *
   * @param $membershipTypeName
   */
  public function disableMembershipType($membershipTypeName) {
    if (!empty($membershipTypeName)) {
      // catch any errors and ignore (disabling can be done manually if problems)
      try {
        // get membership type with name
        $membershipTypeId = civicrm_api3('MembershipType', 'getvalue', array('name' => $membershipTypeName, 'return' => 'id'));
        $sqlMembershipType = "UPDATE civicrm_membership_type SET is_active = %1 WHERE id = %2";
        CRM_Core_DAO::executeQuery($sqlMembershipType, array(
          1 => array(0, 'Integer'),
          2 => array($membershipTypeId, 'Integer')));
      } catch (CiviCRM_API3_Exception $ex) {
      }
    }
  }

  /**
   * Method to enable membership type
   *
   *    @param $membershipTypeName
   */
  public function enableMembershipType($membershipTypeName) {
    if (!empty($membershipTypeName)) {
      // catch any errors and ignore (disabling can be done manually if problems)
      try {
        // get membership type with name
        $membershipTypeId = civicrm_api3('MembershipType', 'getvalue', array('name' => $membershipTypeName, 'return' => 'id'));
        $sqlMembershipType = "UPDATE civicrm_membership_type SET is_active = %1 WHERE id = %2";
        CRM_Core_DAO::executeQuery($sqlMembershipType, array(
          1 => array(1, 'Integer'),
          2 => array($membershipTypeId, 'Integer')));
      } catch (CiviCRM_API3_Exception $ex) {
      }
    }
  }

  /**
   * Method to uninstall membership type
   *
   * @param $membershipTypeName
   */
  public function uninstallMembershipType($membershipTypeName) {
    if (!empty($membershipTypeName)) {
      // catch any errors and ignore (disabling can be done manually if problems)
      try {
        // get membership type with name
        $membershipTypeId = civicrm_api3('MembershipType', 'getvalue', array('name' => $membershipTypeName, 'return' => 'id'));
        civicrm_api3('MembershipType', 'delete', array('id' => $membershipTypeId,));
      } catch (CiviCRM_API3_Exception $ex) {
      }
    }
  }
}