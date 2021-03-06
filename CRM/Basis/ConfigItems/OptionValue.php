<?php
/**
 * Class for OptionValue configuration
 *
 * @author Erik Hommel (CiviCooP) <erik.hommel@civicoop.org>
 * @date 31 May 2017
 * @license AGPL-3.0
 */
class CRM_Basis_ConfigItems_OptionValue {

  protected $_apiParams = array();

  /**
   * CRM_Basis_ConfigItems_OptionValue constructor.
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
      throw new Exception(ts('Missing mandatory param name in class ' . __METHOD__));
    }
    if (!isset($params['option_group_id']) || empty($params['option_group_id'])) {
      throw new Exception(ts('Missing mandatory param option_group_id in ' . __METHOD__));
    }
    $this->_apiParams = $params;
  }

  /**
   * Method to create or update option value
   *
   * @param $params
   * @return array
   * @throws Exception when error in API Option Value Create
   */
  public function create($params) {
    $this->validateCreateParams($params);
    $existing = $this->getWithNameAndOptionGroupId($this->_apiParams['name'], $this->_apiParams['option_group_id']);
    if (isset($existing['id'])) {
      $this->_apiParams['id'] = $existing['id'];
    }
    if (!isset($this->_apiParams['is_active'])) {
      $this->_apiParams['is_active'] = 1;
    }
    if (!isset($this->_apiParams['is_reserved'])) {
      $this->_apiParams['is_reserved'] = 1;
    }
    if (!isset($this->_apiParams['label'])) {
      $this->_apiParams['label'] = ucfirst($this->_apiParams['name']);
    }
    // if component set, get component_id with name
    if (isset($this->_apiParams['component'])) {
      $this->_apiParams['component_id'] = $this->getComponentIdWithName($this->_apiParams['component']);
      unset($this->_apiParams['component']);
    }

    try {
      return civicrm_api3('OptionValue', 'Create', $this->_apiParams);
    }
    catch (CiviCRM_API3_Exception $ex) {
      throw new Exception(ts('Could not create or update option_value with name' . $this->_apiParams['name']
        . ' in option group with id ' . $this->_apiParams['option_group_id'] . ' in ' . __METHOD__
          . ', error from API OptionValue Create: ') . $ex->getMessage());
    }
  }

  /**
   * Method to get the option group with name
   *
   * @param string $name
   * @param int $optionGroupId
   * @return array|bool
   */
  public function getWithNameAndOptionGroupId($name, $optionGroupId) {
    $params = array('name' => $name, 'option_group_id' => $optionGroupId);
    try {
      return civicrm_api3('OptionValue', 'Getsingle', $params);
    }
    catch (CiviCRM_API3_Exception $ex) {
      return array();
    }
  }

  /**
   * Method to get the component_id with a component name
   *
   * @param $componentName
   * @return bool|null|string
   */
  public function getComponentIdWithName($componentName) {
    $query = "SELECT id FROM civicrm_component WHERE name = %1";
    $componentId = CRM_Core_DAO::singleValueQuery($query, array(
        1 => array($componentName, 'String'),
    ));
    if ($componentId) {
      return $componentId;
    }
    return FALSE;
  }

}
