<?php
/**
 * Class for CaseType configuration
 *
 * @author Erik Hommel (CiviCooP) <erik.hommel@civicoop.org>
 * @date 3 Feb 2016
 * @license AGPL-3.0
 */
class CRM_Basis_ConfigItems_CaseType {

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
     * Method to create case type
     *
     * @param array $params
     * @return mixed
     * @throws Exception when error from API CaseType Create
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
            civicrm_api3('CaseType', 'Create', $this->_apiParams);
        } catch (CiviCRM_API3_Exception $ex) {
            throw new Exception('Could not create or update case type with name '.$this->_apiParams['name']
                .' in '.__METHOD__.', error from API CaseType Create: '.$ex->getMessage());
        }
    }


    /**
     * Method to get case sub type with name
     *
     * @param string $caseTypeName
     * @return array|bool
     * @access public
     * @static
     */
    public function getWithName($caseTypeName) {
        try {
            return civicrm_api3('CaseType', 'Getsingle', array('name' => $caseTypeName));
        } catch (CiviCRM_API3_Exception $ex) {
            return FALSE;
        }
    }


    /**
     * Method to uninstall case type
     *
     * @param $caseTypeName
     */
    public function uninstallCaseType($caseTypeName) {
        if (!empty($caseTypeName)) {
            // catch any errors and ignore (disabling can be done manually if problems)
            try {
                // get case type with name
                $caseTypeId = civicrm_api3('CaseType', 'getvalue', array('name' => $caseTypeName, 'return' => 'id'));
                civicrm_api3('CaseType', 'delete', array('id' => $caseTypeId,));
            } catch (CiviCRM_API3_Exception $ex) {
            }
        }
    }
}