<?php
/**
 * Class for RelationshipType configuration
 *
 * @author Erik Hommel (CiviCooP) <erik.hommel@civicoop.org>
 * @date 3 Feb 2016
 * @license AGPL-3.0
 */
class CRM_Basis_ConfigItems_RelationshipType {

  protected $_apiParams = array();

  /**
   * Method to validate params for create
   *
   * @param $params
   * @throws Exception when missing mandatory params
   */
  protected function validateCreateParams($params) {
    if (!isset($params['name_a_b']) || empty($params['name_a_b'])) {
      throw new Exception('Missing mandatory param name in '.__METHOD__);
    }
    $this->_apiParams = $params;
  }

  /**
   * Method to create relationship type
   *
   * @param array $params
   * @return mixed
   * @throws Exception when error from API RelationshipType Create
   */
  public function create($params) {
    $this->validateCreateParams($params);
    $existing = $this->getWithName($this->_apiParams['name_a_b']);
    if (isset($existing['id'])) {
      $this->_apiParams['id'] = $existing['id'];
    }
    if (!isset($this->_apiParams['label_a_b']) || empty($this->_apiParams['label_a_b'])) {
      $this->_apiParams['label_a_b'] = CRM_Basis_Utils::buildLabelFromName($this->_apiParams['name_a_b']);
    }
    $this->_apiParams['is_active'] = 1;
    try {
      civicrm_api3('RelationshipType', 'Create', $this->_apiParams);
      //$this->updateNavigationMenuUrl();
    } catch (CiviCRM_API3_Exception $ex) {
      throw new Exception('Could not create or update relationship type with name '.$this->_apiParams['label_a_b']
        .' in '.__METHOD__.', error from API RelationshipType Create: '.$ex->getMessage());
    }
  }

  /**
   * Method to check if there is a navigation menu option for the relationship type
   * and if so, update name and url
   *
   * @access private
   */
  private function updateNavigationMenuUrl() {
    // todo check if this is still applicable
    // check if there is a "New <label>" entry in the navigation table
    $query = "SELECT * FROM civicrm_navigation WHERE label = %1";
    $label = "New ".$this->_apiParams['label_a_b'];
    $dao = CRM_Core_DAO::executeQuery($query, array(1 => array($label, 'String')));
    $validParent = array("New Organization", "New Individual", "New Household");
    $newUrl = 'civicrm/relationship/add&ct=Organization&cst='.$this->_apiParams['name_a_b'].'&reset=1';
    $newName = "New ".$this->_apiParams['name_a_b'];
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
   * Method to get relationship sub type with name
   *
   * @param string $relationshipTypeName
   * @return array|bool
   * @access public
   * @static
   */
  public function getWithName($relationshipTypeName) {
    try {
      return civicrm_api3('RelationshipType', 'Getsingle', array('name' => $relationshipTypeName));
    } catch (CiviCRM_API3_Exception $ex) {
      return FALSE;
    }
  }

  /**
   * Method to disable relationship type
   *
   * @param $relationshipTypeName
   */
  public function disableRelationshipType($relationshipTypeName) {
    if (!empty($relationshipTypeName)) {
      // catch any errors and ignore (disabling can be done manually if problems)
      try {
        // get relationship type with name
        $relationshipTypeId = civicrm_api3('RelationshipType', 'getvalue', array('name' => $relationshipTypeName, 'return' => 'id'));
        $sqlRelationshipType = "UPDATE civicrm_relationship_type SET is_active = %1 WHERE id = %2";
        CRM_Core_DAO::executeQuery($sqlRelationshipType, array(
          1 => array(0, 'Integer'),
          2 => array($relationshipTypeId, 'Integer')));
      } catch (CiviCRM_API3_Exception $ex) {
      }
    }
  }

  /**
   * Method to enable relationship type
   *
   * @param $relationshipTypeName
   */
  public function enableRelationshipType($relationshipTypeName) {
    if (!empty($relationshipTypeName)) {
      // catch any errors and ignore (disabling can be done manually if problems)
      try {
        // get relationship type with name
        $relationshipTypeId = civicrm_api3('RelationshipType', 'getvalue', array('name' => $relationshipTypeName, 'return' => 'id'));
        $sqlRelationshipType = "UPDATE civicrm_relationship_type SET is_active = %1 WHERE id = %2";
        CRM_Core_DAO::executeQuery($sqlRelationshipType, array(
          1 => array(1, 'Integer'),
          2 => array($relationshipTypeId, 'Integer')));
      } catch (CiviCRM_API3_Exception $ex) {
      }
    }
  }

  /**
   * Method to uninstall relationship type
   *
   * @param $relationshipTypeName
   */
  public function uninstallRelationshipType($relationshipTypeName) {
    if (!empty($relationshipTypeName)) {
      // catch any errors and ignore (disabling can be done manually if problems)
      try {
        // get relationship type with name
        $relationshipTypeId = civicrm_api3('RelationshipType', 'getvalue', array('name' => $relationshipTypeName, 'return' => 'id'));
        civicrm_api3('RelationshipType', 'delete', array('id' => $relationshipTypeId,));
      } catch (CiviCRM_API3_Exception $ex) {
      }
    }
  }
}