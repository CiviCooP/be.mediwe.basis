<?php
/**
 * Page Ziektemelding to list all present claim leves
 *
 * @author Erik Hommel (CiviCooP) <erik.hommel@civicoop.org>
 * @date 31 Jan 2017
 * @license AGPL-3.0
 */

class CRM_Basis_Page_Ziektemelding extends CRM_Core_Page {

    /**
     * Standard run function created when generating page with Civix
     *
     * @access public
     */
    function run() {
        $this->setPageConfiguration();
        //$this->initializePager();
        $meldingen = $this->getZiektemeldingen();
        $this->assign('ziektemeldingen', $meldingen);
        parent::run();
    }

    /**
     * Function to get the claim levels
     *
     * @return array $meldingen
     * @access protected
     */
    protected function getZiektemeldingen() {
        $config = CRM_Basis_Config::singleton();
        $meldingen = array();

        /*
        list($offset, $limit) = $this->_pager->getOffsetAndRowCount();
        $query = "SELECT * FROM pum_claim_level LIMIT %1, %2";
        $queryParams[1] = array($offset, 'Integer');
        $queryParams[2] = array($limit, 'Integer');
        $dao = CRM_Core_DAO::executeQuery($query, $queryParams);
        while ($dao->fetch()) {
            $row = array();
            try {
                $row['level'] = civicrm_api3('OptionValue', 'getvalue', array(
                    'option_group_id' => $config->getZiektemeldingOptionGroup('id'),
                    'value' => $dao->level,
                    'return' => 'label'));
            } catch (CiviCRM_API3_Exception $ex) {}
            if ($dao->max_amount == 999999999.99) {
                $row['max_amount'] = 'no max';
            } else {
                $row['max_amount'] = $dao->max_amount;
            }
            $row['valid_types'] = $this->getZiektemeldingTypes($dao->id);
            $row['valid_main_activities'] = $this->getZiektemeldingMainActivities($dao->id);
            try {
                $row['authorizing_level'] = civicrm_api3('OptionValue', 'getvalue', array(
                    'option_group_id' => $config->getZiektemeldingOptionGroup('id'),
                    'value' => $dao->authorizing_level,
                    'return' => 'label'));
            } catch (CiviCRM_API3_Exception $ex) {}
            $row['actions'] = $this->setRowActions($dao->id);
            $meldingen[$dao->id] = $row;
        }
        */

        $obj_ziektemelding = new CRM_Basis_Ziektemelding();
        $params = array(
            
        );
        
        $meldingen = $obj_ziektemelding->get($params);
        foreach ($meldingen as $key => $ziekte) {
            $meldingen[$key]['actions'] = $this->setRowActions($ziekte['id']);
        }

        return $meldingen;
    }

    /**
     * Method to get all claim level types in a string field
     *
     * @param $claimLevelId
     * @return null
     * @access protected
     */
    /*
    protected function getZiektemeldingTypes($claimLevelId) {
        $config = CRM_Basis_Config::singleton();
        $result = NULL;
        $types = array();
        $claimLevelType = new CRM_Basis_DAO_ZiektemeldingType();
        $claimLevelType->claim_level_id = $claimLevelId;
        $claimLevelType->find();
        while ($claimLevelType->fetch()) {
            try {
                $types[] = civicrm_api3('OptionValue', 'getvalue', array(
                    'option_group_id' => $config->getClaimTypeOptionGroup('id'),
                    'value' => $claimLevelType->type_value,
                    'return' => 'label'
                ));
            } catch (CiviCRM_API3_Exception $ex) {}
        }
        if (!empty($types)) {
            $result = implode("; ", $types);
        }
        return $result;
    }
    */

    /**
     * Method to get all claim level main activities in a string field
     *
     * @param $claimLevelId
     * @return null
     * @access protected
     */
    /*
    protected function getZiektemeldingMainActivities($claimLevelId) {
        $result = NULL;
        $mainActivities = array();
        $claimLevelMain = new CRM_Basis_DAO_ZiektemeldingMain();
        $claimLevelMain->claim_level_id = $claimLevelId;
        $claimLevelMain->find();
        while ($claimLevelMain->fetch()) {
            try {
                $mainActivities[] = civicrm_api3('OptionValue', 'getvalue', array(
                    'option_group_id' => 'case_type',
                    'value' => $claimLevelMain->main_activity_type_id,
                    'return' => 'label'
                ));
            } catch (CiviCRM_API3_Exception $ex) {}
        }
        if (!empty($mainActivities)) {
            $result = implode("; ", $mainActivities);
        }
        return $result;
    }
    */

    /**
     * Function to set the row action urls and links for each row
     *
     * @param int $claimLevelId
     * @return array $actions
     * @access protected
     */
    protected function setRowActions($meldingId) {

        $actions = array();
        $editUrl = CRM_Utils_System::url('civicrm/basis/form/ziektemelding', 'action=update&id='.$meldingId, true);
        $deleteUrl = CRM_Utils_System::url('civicrm/basis/form/ziektemelding', 'action=delete&id='.$meldingId, true);
        //$contactsUrl = CRM_Utils_System::url('civicrm/pumexpenseclaims/page/claimlevelcontact', 'reset=1&id='.$meldingId, true);
        //$actions[] = '<a class="action-item" title="Contacts" href="'.$contactsUrl.'">Contacts</a>';
        $actions[] = '<a class="action-item" title="Edit" href="'.$editUrl.'">Edit</a>';
        $actions[] = '<a class="action-item" title="Delete" href="'.$deleteUrl.'">Delete</a>';

        //CRM_Core_Error::debug('actions', $actions);exit;
        return $actions;
    }

    /**
     * Function to set the page configuration
     *
     * @access protected
     */
    protected function setPageConfiguration() {
        CRM_Utils_System::setTitle(ts("Ziektemeldingen"));
        $this->assign('addUrl', CRM_Utils_System::url('civicrm/basis/form/ziektemelding', 'action=add', true));
        $session = CRM_Core_Session::singleton();
        $session->pushUserContext(CRM_Utils_System::url('civicrm/basis/form/ziektemelding', 'reset=1', true));
    }

    /**
     * Method to initialize pager
     *
     * @access protected
     */
    /*
    protected function initializePager() {
        $params           = array(
            'total' => CRM_Core_DAO::singleValueQuery("SELECT COUNT(*) FROM pum_claim_level"),
            'rowCount' => CRM_Utils_Pager::ROWCOUNT,
            'status' => ts('Expense Claim Levels %%StatusMessage%%'),
            'buttonBottom' => 'PagerBottomButton',
            'buttonTop' => 'PagerTopButton',
            'pageID' => $this->get(CRM_Utils_Pager::PAGE_ID),
        );
        $this->_pager = new CRM_Utils_Pager($params);
        $this->assign_by_ref('pager', $this->_pager);
    }
    */

}
