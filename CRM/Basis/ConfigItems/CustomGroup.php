<?php
/**
 * Class for CustomGroup configuration
 *
 * @author Erik Hommel (CiviCooP) <erik.hommel@civicoop.org>
 * @date 31 May 2017
 * @license AGPL-3.0
 */
class CRM_Basis_ConfigItems_CustomGroup {

  protected $_apiParams = array();

  /**
   * CRM_Basis_ConfigItems_CustomGroup constructor.
   */
  public function __construct() {
    $this->_apiParams = array();
  }

  /**
   * Method to validate params for create
   *
   * @param $params
   * @throws Exception
   */
  private function validateCreateParams($params) {
    if (!isset($params['name']) || empty($params['name']) || !isset($params['extends']) ||
      empty($params['extends'])) {
      throw new Exception(ts('When trying to create a Custom Group name and extends are mandatory parameters
      and can not be empty in ' . __METHOD__));
    }
    $this->buildApiParams($params);
  }

  /**
   * Method to create custom group
   *
   * @param array $params
   * @return array
   * @throws Exception when error from API CustomGroup Create
   */
  public function create($params) {
    $this->validateCreateParams($params);
    $existing = $this->getWithName($this->_apiParams['name']);
    if (isset($existing['id'])) {
      $this->_apiParams['id'] = $existing['id'];
    }
    if (!isset($this->_apiParams['title']) || empty($this->_apiParams['title'])) {
      $this->_apiParams['title'] = CRM_Basis_Utils::buildLabelFromName($this->_apiParams['name']);
    }
    try {
      $customGroup = civicrm_api3('CustomGroup', 'Create', $this->_apiParams);
    }
    catch (CiviCRM_API3_Exception $ex) {
      throw new Exception(ts('Could not create or update custom group with name ' . $this->_apiParams['name']
        . ' to extend ' . $this->_apiParams['extends'] . ' in ' . __METHOD__ . ', error from API CustomGroup Create: ') .
        $ex->getMessage());
    }
    return $customGroup['values'][$customGroup['id']];
  }

  /**
   * Method to disable custom group (and custom fields)
   *
   * @param $customGroupName
   */
  public function disable($customGroupName) {
    if (!empty($customGroupName)) {
      // catch any errors and ignore (disabling can be done manually if problems)
      try {
        // get custom group id with name
        $customGroupId = civicrm_api3('CustomGroup', 'getvalue', array('name' => $customGroupName, 'return' => 'id'));
        // disable all custom fields
        $sqlFields = "UPDATE civicrm_custom_field SET is_active = %1 WHERE custom_group_id = %2";
        CRM_Core_DAO::executeQuery($sqlFields, array(
          1 => array(0, 'Integer'),
          2 => array($customGroupId, 'Integer')));
        // disable custom group
        $sqlGroup = "UPDATE civicrm_custom_group SET is_active = %1 WHERE id = %2";
        CRM_Core_DAO::executeQuery($sqlGroup, array(
          1 => array(0, 'Integer'),
          2 => array($customGroupId, 'Integer')));
      }
      catch (CiviCRM_API3_Exception $ex) {}
    }
  }

  /**
   * Method to uninstall custom group (and custom fields)
   *
   * @param $customGroupName
   */
  public function uninstall($customGroupName) {
    if (!empty($customGroupName)) {
      // catch any errors and ignore (uninstalling can be done manually if problems)
      try {
        // get custom group data with name
        $customGroup = civicrm_api3('CustomGroup', 'getsingle', array('name' => $customGroupName));
        // delete all custom fields
        $customFields = civicrm_api3('CustomField', 'get', array('custom_group_id' => $customGroup['id']));
        foreach ($customFields['values'] as $customField) {
          civicrm_api3('CustomField', 'delete', array('id' => $customField['id']));
        }
        // and delete custom group
        civicrm_api3('CustomGroup', 'delete', array('id' => $customGroup['id']));
      }
      catch (CiviCRM_API3_Exception $ex) {}
    }
  }

  /**
   * Method to enable custom group (and custom fields)
   *
   * @param $customGroupName
   */
  public function enable($customGroupName) {
    if (!empty($customGroupName)) {
      // catch any errors and ignore (enabling can be done manually if problems)
      try {
        // get custom group id with name
        $customGroupId = civicrm_api3('CustomGroup', 'getvalue', array('name' => $customGroupName, 'return' => 'id'));
        // enable all custom fields
        $sqlFields = "UPDATE civicrm_custom_field SET is_active = %1 WHERE custom_group_id = %2";
        CRM_Core_DAO::executeQuery($sqlFields, array(
          1 => array(1, 'Integer'),
          2 => array($customGroupId, 'Integer')));
        // enable custom group
        $sqlGroup = "UPDATE civicrm_custom_group SET is_active = %1 WHERE id = %2";
        CRM_Core_DAO::executeQuery($sqlGroup, array(
          1 => array(1, 'Integer'),
          2 => array($customGroupId, 'Integer')));
      }
      catch (CiviCRM_API3_Exception $ex) {
      }
    }
  }

  /**
   * Method to get custom group with name
   *
   * @param string $name
   * @return array|bool
   */
  public function getWithName($name) {
    try {
      return civicrm_api3('CustomGroup', 'Getsingle', array('name' => $name));
    }
    catch (CiviCRM_API3_Exception $ex) {
      return FALSE;
    }
  }

  /**
   * Method to get custom group table name with name
   *
   * @param string $name
   * @return array|bool
   */
  public function getTableNameWithName($name) {
    try {
      return civicrm_api3('CustomGroup', 'Getvalue', array('name' => $name, 'return' => 'table_name'));
    }
    catch (CiviCRM_API3_Exception $ex) {
      return FALSE;
    }
  }

  /**
   * Method to build api param list
   *
   * @param array $params
   */
  protected function buildApiParams($params) {
    $this->_apiParams = array();
    foreach ($params as $name => $value) {
      if ($name != 'fields') {
        $this->_apiParams[$name] = $value;
      }
    }
    // check for cases where one or more specific activity types are used
    switch ($this->_apiParams['extends']) {
      case "Activity":
        $this->setActivityTypeForCustomGroup();
        break;

      case "Case":
        $this->setCaseTypeForCustomGroup();
        break;

      case "Individual":
        $this->setContactTypeForCustomGroup();
        break;

      case "Membership":
        $this->setMembershipTypeForCustomGroup();
        break;

      case "Organization":
        $this->setContactTypeForCustomGroup();
        break;

      case "Household":
        $this->setContactTypeForCustomGroup();
        break;
    }
  }

  /**
   * Method to set the entity_column_value to specify which activity type to use
   */
  private function setActivityTypeForCustomGroup() {
    if (isset($this->_apiParams['extends_entity_column_value']) && !empty($this->_apiParams['extends_entity_column_value'])) {
      if (is_array($this->_apiParams['extends_entity_column_value'])) {
        foreach ($this->_apiParams['extends_entity_column_value'] as $extendsValue) {
          $activityType = new CRM_Basis_ConfigItems_ActivityType();
          $found = $activityType->getWithNameAndOptionGroupId($extendsValue, $activityType->getOptionGroupId());
          if (isset($found['value'])) {
            $this->_apiParams['extends_entity_column_value'][] = $found['value'];
          }
          unset ($activityType);
        }
      }
      else {
        $activityType = new CRM_Basis_ConfigItems_ActivityType();
        $found = $activityType->getWithNameAndOptionGroupId($this->_apiParams['extends_entity_column_value'], $activityType->getOptionGroupId());
        if (isset($found['value'])) {
          $this->_apiParams['extends_entity_column_value'] = $found['value'];
        }
      }
    }
  }

  /**
   * Method to set the entity_column_value to specify what contact type to use
   */
  private function setContactTypeForCustomGroup() {
    if (isset($this->_apiParams['extends_entity_column_value']) && !empty($this->_apiParams['extends_entity_column_value'])) {
      if (is_array($this->_apiParams['extends_entity_column_value'])) {
        foreach ($this->_apiParams['extends_entity_column_value'] as $extendsValue) {
          $contactType = new CRM_Basis_ConfigItems_ContactType();
          $found = $contactType->getWithName($extendsValue);
          if (isset($found['name'])) {
            if (!in_array($found['name'], $this->_apiParams['extends_entity_column_value'])) {
              $this->_apiParams['extends_entity_column_value'][] = $found['name'];
            }
          }
          unset ($contactType);
        }
      }
      else {
        $contactType = new CRM_Basis_ConfigItems_ContactType();
        $found = $contactType->getWithName($this->_apiParams['extends_entity_column_value']);
        if (isset($found['name'])) {
          $this->_apiParams['extends_entity_column_value'] = $found['name'];
        }
      }
    }
  }

  /**
   * Method to set the entity_column_value to specify what case type to use
   */
  private function setCaseTypeForCustomGroup() {
    if (isset($this->_apiParams['extends_entity_column_value']) && !empty($this->_apiParams['extends_entity_column_value'])) {
      if (is_array($this->_apiParams['extends_entity_column_value'])) {
        foreach ($this->_apiParams['extends_entity_column_value'] as $extendsValue) {
          $caseType = new CRM_Basis_ConfigItems_CaseType();
          $found = $caseType->getWithName($extendsValue);
          if (isset($found['name'])) {
            if (!in_array($found['name'], $this->_apiParams['extends_entity_column_value'])) {
              $this->_apiParams['extends_entity_column_value'][] = $found['name'];
            }
          }
          unset ($caseType);
        }
      }
      else {
        $caseType = new CRM_Basis_ConfigItems_CaseType();
        $found = $caseType->getWithName($this->_apiParams['extends_entity_column_value']);
        if (isset($found['name'])) {
          $this->_apiParams['extends_entity_column_value'] = $found['name'];
        }
      }
    }
  }

  /**
   * Method to set the entity_column_value to specify what membership type to use
   */
  private function setMembershipTypeForCustomGroup() {
    if (isset($this->_apiParams['extends_entity_column_value']) && !empty($this->_apiParams['extends_entity_column_value'])) {
      if (is_array($this->_apiParams['extends_entity_column_value'])) {
        foreach ($this->_apiParams['extends_entity_column_value'] as $extendsValue) {
          $membershipType = new CRM_Basis_ConfigItems_MembershipType();
          $found = $membershipType->getWithName($extendsValue);
          if (isset($found['name'])) {
            if (!in_array($found['name'], $this->_apiParams['extends_entity_column_value'])) {
              $this->_apiParams['extends_entity_column_value'][] = $found['name'];
            }
          }
          unset ($membershipType);
        }
      }
      else {
        $membershipType = new CRM_Basis_ConfigItems_MembershipType();
        $found = $membershipType->getWithName($this->_apiParams['extends_entity_column_value']);
        if (isset($found['name'])) {
          $this->_apiParams['extends_entity_column_value'] = $found['name'];
        }
      }
    }
  }

}
