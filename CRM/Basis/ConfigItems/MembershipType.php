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
      throw new Exception('Missing mandatory param name in ' . __METHOD__);
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
    // if financial type, retrieve financial type id
    if (isset($this->_apiParams['financial_type'])) {
      $financialTypeId = $this->getFinancialTypeIdWithName($this->_apiParams['financial_type']);
      if ($financialTypeId) {
        $this->_apiParams['financial_type_id'] = $financialTypeId;
        unset($this->_apiParams['financial_type']);
      }
    }
    // if relationshipt_type, retrieve relationship type id with names
    if (isset($this->_apiParams['relationship_type_name_a_b']) && isset($this->_apiParams['relationship_type_name_b_a'])) {
      $relationshipTypeId = $this->getRelationshipTypeIdWithNames($this->_apiParams['relationship_type_name_a_b'], $this->_apiParams['relationship_type_name_b_a']);
      if ($relationshipTypeId) {
        $this->_apiParams['relationship_type_id'] = $relationshipTypeId;
        unset($this->_apiParams['relationship_type_name_a_b'], $this->_apiParams['relationship_type_name_b_a']);
      }
    }
    $this->_apiParams['is_active'] = 1;
    try {
      civicrm_api3('MembershipType', 'Create', $this->_apiParams);
      //$this->updateNavigationMenuUrl();
    }
    catch (CiviCRM_API3_Exception $ex) {
      throw new Exception('Could not create or update membership type with name ' . $this->_apiParams['name']
        . ' in ' . __METHOD__ . ', error from API MembershipType Create: ' . $ex->getMessage());
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
    }
    catch (CiviCRM_API3_Exception $ex) {
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
      }
      catch (CiviCRM_API3_Exception $ex) {
      }
    }
  }

  /**
   * Method to enable membership type
   *
   * @param $membershipTypeName
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
      }
      catch (CiviCRM_API3_Exception $ex) {
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
        civicrm_api3('MembershipType', 'delete', array('id' => $membershipTypeId));
      }
      catch (CiviCRM_API3_Exception $ex) {
      }
    }
  }

  /**
   * Method to get the financial type id with name
   *
   * @param $name
   * @return bool|int
   */
  public function getFinancialTypeIdWithName($name) {
    $query = "SELECT id FROM civicrm_financial_type WHERE name = %1";
    $financialTypeId = CRM_Core_DAO::singleValueQuery($query, array(
        1 => array($name, 'String'),
    ));
    if ($financialTypeId) {
      return $financialTypeId;
    }
    return FALSE;
  }

  /**
   * Method to get the relationship type id with name_a_b and name_b_a
   *
   * @param string $nameAB
   * @param string $nameBA
   * @return bool|int
   */
  public function getRelationshipTypeIdWithNames($nameAB, $nameBA) {
    $query = "SELECT id FROM civicrm_relationship_type WHERE name_a_b = %1 AND name_b_a = %2";
    $relationshipTypeId = CRM_Core_DAO::singleValueQuery($query, array(
        1 => array($nameAB, 'String'),
        2 => array($nameBA, 'String'),
    ));
    if ($relationshipTypeId) {
      return $relationshipTypeId;
    }
    return FALSE;
  }

}
