<?php

/**
 * Form controller class
 *
 * @see https://wiki.civicrm.org/confluence/display/CRMDOC/QuickForm+Reference
 */
class CRM_Basis_Form_Ziektemelding extends CRM_Core_Form {

    private $_reasonData = array();
    private $_ziektemeldingData = array();
    private $_employeeData = array();
    private $_employerData = array();
    private $_id = false;

    public function buildQuickForm() {

        // De werkgever
        $this->add('text', 'employer_organization_name', ts('Werkgever '), array(), FALSE);
        $this->add('text', 'employer_customer_vat', ts('BTW nummer '), array(), FALSE);

        // De werknemer
        $this->add('text', 'employee_employee_national_nbr', ts('Rijksregisternummer '), array(), FALSE);
        $this->add('text', 'employee_employee_personnel_nbr', ts('Personeelsnummer'), array(), FALSE);

        $this->add('text', 'employee_display_name', ts('Naam werknemer'), array(), TRUE);

        $this->add('text', 'employee_employee_partner', ts('Partner'), array(), FALSE);

        $this->add('text', 'employee_phone', ts('Telefoon'), array(), FALSE);
        $this->add('text', 'employee_mobile', ts('GSM'), array(), FALSE);

        $this->add('text', 'employee_employee_level1', ts('Niveau 1'), array(), FALSE);
        $this->add('text', 'employee_employee_code_level2', ts('Code Niveau 2'), array(), FALSE);
        $this->add('text', 'employee_employee_level2', ts('Niveau 2'), array(), FALSE);
        $this->add('text', 'employee_employee_level3', ts('Niveau 3'), array(), FALSE);

        $this->add('text', 'employee_employee_function', ts('Functie'), array(), FALSE);

        $this->add( 'datepicker', 'employee_employee_date_in',  ts('Datum in dienst'), array(), FALSE, array('time' => FALSE, 'date' => 'dd-mm-yy', 'minDate' => '1940-01-01'));
        $this->add( 'datepicker', 'employee_employee_date_out',  ts('Datum uit dienst'), array(), FALSE, array('time' => FALSE, 'date' => 'dd-mm-yy', 'minDate' => '2010-01-01'));


        // Afwezigheidsgegevens
        $this->add('select', 'illness_reason', ts('Reden'), $this->_reasonData, TRUE);
        $this->add( 'datepicker', 'start_date',  ts('Begindatum'), array(), TRUE, array('time' => FALSE, 'date' => 'dd-mm-yy', 'minDate' => '2017-01-01'));
        $this->add( 'datepicker', 'end_date',  ts('Einddatum'), array(), FALSE, array('time' => FALSE, 'date' => 'dd-mm-yy', 'minDate' => '2017-01-01'));
        $this->addYesNo('illness_is_extension', ts('Is verlening'), TRUE, FALSE);
        $this->addYesNo('illness_is_private_accident', ts('Is privé ongeval'), TRUE, FALSE);
        $this->addYesNo('illness_is_exit_allowed', ts('Mag het huis verlaten'), TRUE, FALSE);
        $this->addYesNo('illness_is_hospitalization', ts('Opname in het ziekenhuis'), TRUE, FALSE);
        $this->addYesNo('illness_no_certificate', ts('Ziekte zonder attest'), TRUE, FALSE);



        $this->addButtons(array(
            array('type' => 'next', 'name' => ts('Save'), 'isDefault' => true,),
            array('type' => 'cancel', 'name' => ts('Cancel')),
        ));

        // export form elements
        $this->assign('elementNames', $this->getRenderableElementNames());

        if ($this->_employerData) {
            $this->getElement('employer_organization_name')->setValue($this->_data($this->_employerData, 'organization_name'));
            $this->getElement('employer_customer_vat')->setValue($this->_data($this->_employerData,'customer_vat'));

            $this->add('hidden', 'employer_id');
            $this->getElement('employer_id')->setValue($this->_data($this->_employerData,'id'));
        }

        if ($this->_employeeData) {
            $this->getElement('employee_employee_national_nbr')->setValue($this->_data($this->_employeeData,'employee_national_nbr'));
            $this->getElement('employee_employee_personnel_nbr')->setValue($this->_data($this->_employeeData,'employee_personnel_nbr'));
            $this->getElement('employee_display_name')->setValue($this->_data($this->_employeeData,'display_name'));

            $this->getElement('employee_employee_partner')->setValue($this->_data($this->_employeeData,'employee_partner'));
            $this->getElement('employee_phone')->setValue($this->_data($this->_employeeData,'phone'));
            $this->getElement('employee_mobile')->setValue($this->_data($this->_employeeData,'mobile'));
            $this->getElement('employee_employee_level1')->setValue($this->_data($this->_employeeData,'employee_level1'));
            $this->getElement('employee_employee_code_level2')->setValue($this->_data($this->_employeeData,'employee_code_level2'));
            $this->getElement('employee_employee_level2')->setValue($this->_data($this->_employeeData,'employee_level2'));
            $this->getElement('employee_employee_level3')->setValue($this->_data($this->_employeeData,'employee_level3'));

            $this->getElement('employee_employee_function')->setValue($this->_data($this->_employeeData,'employee_function'));
            $this->getElement('employee_employee_date_in')->setValue($this->_data($this->_employeeData,'employee_date_in'));
            $this->getElement('employee_employee_date_out')->setValue($this->_data($this->_employeeData,'employee_date_out'));

            $this->add('hidden', 'employee_id');
            $this->getElement('employee_id')->setValue($this->_data($this->_employeeData,'id'));
        }

        if ($this->_ziektemeldingData) {

            // keep id data
            $this->add('hidden', 'id', 'Id', array(), FALSE);
            $this->getElement('id')->setValue($this->_data($this->_ziektemeldingData,'id'));

            // set values to screen
            $this->getElement('illness_reason')->setValue($this->_data($this->_ziektemeldingData,'illness_reason'));
            $this->getElement('start_date')->setValue($this->_data($this->_ziektemeldingData,'start_date'));
            $this->getElement('end_date')->setValue($this->_data($this->_ziektemeldingData,'end_date'));
            $this->getElement('illness_is_extension')->setValue($this->_data($this->_ziektemeldingData,'illness_is_extension'));
            $this->getElement('illness_is_private_accident')->setValue($this->_data($this->_ziektemeldingData,'illness_is_private_accident'));
            $this->getElement('illness_is_exit_allowed')->setValue($this->_data($this->_ziektemeldingData,'illness_is_exit_allowed'));
            $this->getElement('illness_is_hospitalization')->setValue($this->_data($this->_ziektemeldingData,'illness_is_hospitalization'));
            $this->getElement('illness_no_certificate')->setValue($this->_data($this->_ziektemeldingData,'illness_no_certificate'));
        }


        parent::buildQuickForm();
    }

    public function preProcess() {

        $id =   CRM_Utils_Request::retrieve('id', 'Integer');
        if ($id) {
            $this->_id = $id;
            $this->_getZiektemeldingData($id);
        }

        $this->_setReasonData();


    }

    public function postProcess() {
        //CRM_Core_Error::debug('submitValues', $this->_submitValues);
        //exit();

        $this->saveZiektemelding($this->_submitValues);
        parent::postProcess();
    }

    private function saveZiektemelding($formValues) {

        $employee = array();
        $config = CRM_Basis_Config::singleton();

        // save the employee data
        foreach ($formValues as $key => $value) {
            switch (substr($key, 0, 9)) {
                case 'employee_':
                    $newkey = substr($key, 9);
                    $employee[$newkey] = $value;
                    break;
                case 'employer_':
                    $newkey = substr($key, 9);
                    $employer[$newkey] = $value;
                    break;
            }
        }

        $employee_id = reset(
            civicrm_api3('KlantMedewerker', 'create', $employee)['values']
        );

        if ($employee['phone']) {
            $params = array (
                'contact_id' => $employee_id,
                'phone_type_id' => '1',
            );
            $telefoon = reset(
                civicrm_api3('Telefoon', 'get',
                    $params
                )['values']
            );
            if (isset($telefoon['id'])) {
                $params['id'] = $telefoon['id'];
                $params['location_type_id'] = $telefoon['location_type_id'];
            }

            $params['phone'] = $employee['phone'];
            civicrm_api3('Telefoon', 'create', $params);
        }

        if ($employee['mobile']) {
            $params = array (
                'contact_id' => $employee_id,
                'phone_type_id' => '2',
            );
            $telefoon = reset(
                civicrm_api3('Telefoon', 'get',
                    $params
                )['values']
            );
            if (isset($telefoon['id'])) {
                $params['id'] = $telefoon['id'];
                $params['location_type_id'] = $telefoon['location_type_id'];
            }

            $params['phone'] = $employee['mobile'];
            civicrm_api3('Telefoon', 'create', $params);
        }


        // save the ziektemelding
        $ziekte = reset(
                    civicrm_api3('Ziektemelding', 'create', $formValues)['values']
                    );

        // get the employer
        $werkgever = reset(
                            civicrm_api3('Klant', 'get', $employer)['values']
                        );

        // save the employer relationship
        if (isset($werkgever['id'])) {
            $params = array (
                'case_id' => $ziekte['id'],
                'relationship_type_id' => $config->getZiektemeldingRelationshipType()['id'],
            );
            $relation = reset(
                civicrm_api3('Relatie', 'get', $params)['values']
            );
            if (isset($relation)) {
                $params['id'] = $relation['id'];

            }
            $params['contact_id_a'] = $employee_id;
            $params['contact_id_b'] = $werkgever['id'];

            civicrm_api3('Relatie', 'create', $params);
        }






    }

    private function _data($array, $key) {
        if (isset($array[$key])) {
            return $array[$key];
        }
        else {
            return '';
        }
    }

    private function _setReasonData() {

        $this->_reasonData = array(
            ''       => '(niet meegedeeld)',
            'ziekte' => 'Ziekte',
            'ao'     => 'Arbeidsongeval'
        );

    }

    private function _getZiektemeldingData($id) {

        $config = CRM_Basis_Config::singleton();

        $melding = new CRM_Basis_Ziektemelding();
        $ziekte = $melding->get( array('id' => $id, ));
        $this->_ziektemeldingData = reset($ziekte);

        $contact_id = reset($this->_ziektemeldingData['contact_id']);
        $this->_employeeData = reset(
            civicrm_api3('KlantMedewerker', 'get' ,
                    array(
                        'id' => $contact_id,
                    )
                )['values']
        );

        foreach ($this->_ziektemeldingData['contacts'] as $contact) {
            if ($contact['relationship_type_id'] = $config->getZiektemeldingRelationshipType()['id']) {
                $this->_employerData = reset(
                    civicrm_api3('Klant', 'get' ,
                        array(
                            'id' => $contact['contact_id'],
                        )
                    )['values']
                );
            }
        }
    }

    /**
     * Get the fields/elements defined in this form.
     *
     * @return array (string)
     */
    public function getRenderableElementNames() {
        // The _elements list includes some items which should not be
        // auto-rendered in the loop -- such as "qfKey" and "buttons".  These
        // items don't have labels.  We'll identify renderable by filtering on
        // the 'label'.
        $elementNames = array();
        foreach ($this->_elements as $element) {
            /** @var HTML_QuickForm_Element $element */
            $label = $element->getLabel();
            if (!empty($label)) {
                $elementNames[] = $element->getName();
            }
        }
        return $elementNames;
    }

}
