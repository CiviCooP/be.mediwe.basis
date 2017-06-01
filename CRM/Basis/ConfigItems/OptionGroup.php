<?php
/**
 * Class for OptionGroup configuration
 *
 * @author Erik Hommel (CiviCooP) <erik.hommel@civicoop.org>
 * @date 31 May 2017
 * @license AGPL-3.0
 */
class CRM_Basis_ConfigItems_OptionGroup {

  protected $_apiParams = array();

  /**
   * CRM_Basis_ConfigItems_OptionGroup constructor.
   */
  public function __construct() {
    $this->_apiParams = array();
  }
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
   * Method to create or update option group
   *
   * @param $params
   * @return array
   * @throws Exception when error in API Option Group Create
   */
  public function create($params) {
    $this->validateCreateParams($params);
    $existing = $this->getWithName($this->_apiParams['name']);
    if (isset($existing['id'])) {
      $this->_apiParams['id'] = $existing['id'];
    }
    $this->_apiParams['is_active'] = 1;
    $this->_apiParams['is_reserved'] = 1;
    if (!isset($this->_apiParams['title'])) {
      $this->_apiParams['title'] = ucfirst($this->_apiParams['name']);
    }
    try {
      $optionGroup = civicrm_api3('OptionGroup', 'Create', $this->_apiParams);
      if (isset($params['option_values'])) {
        $this->processOptionValues($optionGroup['id'], $params['option_values']);
      }
    } catch (CiviCRM_API3_Exception $ex) {
      throw new Exception(ts('Could not create or update option_group with name'
        .$this->_apiParams['name'].' in '.__METHOD__.', error from API OptionGroup Create: ') . $ex->getMessage());
    }
  }

  /**
   * Method to process option values for option group
   *
   * @param int $optionGroupId
   * @param array $optionValueParams
   */
  protected function processOptionValues($optionGroupId, $optionValueParams) {
    foreach ($optionValueParams as $optionValueName => $params) {
      $params['option_group_id'] = $optionGroupId;
      $optionValue = new CRM_Basis_ConfigItems_OptionValue();
      $optionValue->create($params);
    }
  }

  /**
   * Function to get the option group with name
   *
   * @param string $name
   * @return array|boolean
   */
  public function getWithName($name) {
    $params = array('name' => $name);
    try {
      return civicrm_api3('OptionGroup', 'Getsingle', $params);
    } catch (CiviCRM_API3_Exception $ex) {
      return array();
    }
  }

  /**
   * Method to disable option group (and option values)
   *
   * @param $optionGroupName
   */
  public function disable($optionGroupName) {
    if (!empty($optionGroupName)) {
      // catch any errors and ignore (disabling can be done manually if problems)
      try {
        // get option group id with name
        $optionGroupId = civicrm_api3('OptionGroup', 'getvalue', array('name' => $optionGroupName, 'return' => 'id'));
        // disable all option values
        $sqlValues = "UPDATE civicrm_option_value SET is_active = %1 WHERE option_group_id = %2";
        CRM_Core_DAO::executeQuery($sqlValues, array(
          1 => array(0, 'Integer'),
          2 => array($optionGroupId, 'Integer')));
        // disable option group
        $sqlGroup = "UPDATE civicrm_option_group SET is_active = %1 WHERE id = %2";
        CRM_Core_DAO::executeQuery($sqlGroup, array(
          1 => array(0, 'Integer'),
          2 => array($optionGroupId, 'Integer')));
      } catch (CiviCRM_API3_Exception $ex) {
      }
    }
  }

  /**
   * Method to enable option group (and option values)
   *
   * @param $optionGroupName
   */
  public function enable($optionGroupName) {
    if (!empty($optionGroupName)) {
      // catch any errors and ignore (enabling can be done manually if problems)
      try {
        // get option group id with name
        $optionGroupId = civicrm_api3('OptionGroup', 'getvalue', array('name' => $optionGroupName, 'return' => 'id'));
        // enable all option values
        $sqlValues = "UPDATE civicrm_option_value SET is_active = %1 WHERE option_group_id = %2";
        CRM_Core_DAO::executeQuery($sqlValues, array(
          1 => array(1, 'Integer'),
          2 => array($optionGroupId, 'Integer')));
        // enable option group
        $sqlGroup = "UPDATE civicrm_option_group SET is_active = %1 WHERE id = %2";
        CRM_Core_DAO::executeQuery($sqlGroup, array(
          1 => array(1, 'Integer'),
          2 => array($optionGroupId, 'Integer')));
      } catch (CiviCRM_API3_Exception $ex) {
      }
    }
  }

  /**
   * Method to uninstall option group (and option values)
   *
   * @param $optionGroupName
   */
  public function uninstall($optionGroupName) {
    if (!empty($optionGroupName)) {
      // catch any errors and ignore (uninstalling can be done manually if problems)
      try {
        // get option group id with name
        $optionGroupId = civicrm_api3('OptionGroup', 'getvalue', array('name' => $optionGroupName, 'return' => 'id'));
        // delete all option values
        $optionValues = civicrm_api3('OptionValue', 'get', array('option_group_id' => $optionGroupId));
        foreach ($optionValues['values'] as $optionValue) {
          civicrm_api3('OptionValue', 'delete', array('id' => $optionValue['id']));
        }
        // delete option group
        civicrm_api3('OptionGroup', 'delete', array('id' => $optionGroupId));
      } catch (CiviCRM_API3_Exception $ex) {
      }
    }
  }
}