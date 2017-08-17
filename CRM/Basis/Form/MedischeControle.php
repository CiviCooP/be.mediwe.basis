<?php

/**
 * Form controller class
 *
 * @see https://wiki.civicrm.org/confluence/display/CRMDOC/QuickForm+Reference
 */
class CRM_Basis_Form_MedischeControle extends CRM_Core_Form {

    private $_reasonData = array();
    private $_soortData = array();
    private $_criteriumData = array();
    private $_soortAdres = array();
    private $_medischeControleData = array();
    private $_minDate = false;
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


        // Controle gegevens
        $this->add('select', 'control_reason', ts('Reden'), $this->_reasonData, TRUE);
        $this->add( 'datepicker', 'illness_start_date',  ts('Begindatum'), array(), TRUE, array('time' => FALSE, 'date' => 'dd-mm-yy', 'minDate' => '2017-01-01'));
        $this->add( 'datepicker', 'illness_end_date',  ts('Einddatum'), array(), FALSE, array('time' => FALSE, 'date' => 'dd-mm-yy', 'minDate' => '2017-01-01'));

        $this->add('select', 'control_criterium', ts('Criterium voor controle'), $this->_criteriumData, FALSE);

        $this->add('select', 'control_type', ts('Type controle'), $this->_soortData, TRUE);
        $this->add( 'datepicker', 'control_date',  ts('ControleDatum'), array(), FALSE, array('time' => FALSE, 'date' => 'dd-mm-yy', 'minDate' => $this->_minDate));

        $this->add('select', 'visit_location_type', ts('Soort adres'), $this->_soortAdres, TRUE);
        $this->add('text', 'visit_supplemental_address_1', ts('Tweede lijn'), array(), FALSE);
        $this->add('text', 'visit_street_address', ts('Adres (straat en huisnummer)'), array(), TRUE);
        $this->add('text', 'visit_postal_code', ts('Postcode'), array(), TRUE);
        $this->add('text', 'visit_city', ts('Gemeente'), array(), TRUE);

        $this->add('textarea', 'control_job_description', ts('Job omschrijving'), array(), FALSE);
        $this->add('textarea', 'control_info_mediwe', ts('Info voor Mediwe'), array(), FALSE);
        $this->add('textarea', 'control_info_controlearts', ts('Info voor de controlearts'), array(), FALSE);

        $this->addYesNo('control_info_is_public', 'Mag deze info gedeeld worden?',  TRUE, FALSE);

        $this->add('text', 'control_name_requestor', ts('Aanvrager'), array(), FALSE);
        $this->add('text', 'control_name_contact', ts('Contactpersoon'), array(), FALSE);
        $this->add('text', 'control_phone_contact', ts('Telefoon contact'), array(), FALSE);
        $this->add('text', 'control_email_contact1', ts('Email bevestiging (1)'), array(), FALSE);
        $this->add('text', 'control_email_contact2', ts('Email bevestiging (2)'), array(), FALSE);
        $this->add('text', 'control_email_contact3', ts('Email bevestiging (3)'), array(), FALSE);
        $this->add('text', 'control_email_result1', ts('Email resultaat (1)'), array(), FALSE);
        $this->add('text', 'control_email_result2', ts('Email resultaat (2)'), array(), FALSE);
        $this->add('text', 'control_email_result3', ts('Email resultaat (3)'), array(), FALSE);

        $this->add('text', 'control_purchase_order', ts('PO nummer'), array(), FALSE);

        $this->addButtons(array(
            array('type' => 'next', 'name' => ts('Save'), 'isDefault' => true,),
            array('type' => 'cancel', 'name' => ts('Cancel')),
        ));

        // export form elements
        $this->assign('elementNames', $this->getRenderableElementNames());

        if ($this->_medischeControleData) {

            // keep id data
            $this->add('hidden', 'id', 'Id', array(), FALSE);
            $this->getElement('id')->setValue($this->_id);

            // set values to screen

            $this->getElement('employer_organization_name')->setValue($this->_medischeControleField('employee_employer_name'));
            $this->getElement('employer_customer_vat')->setValue($this->_medischeControleField('employee_employer_vat'));

            $this->getElement('employee_employee_national_nbr')->setValue($this->_medischeControleField('employee_employee_national_nbr'));
            $this->getElement('employee_employee_personnel_nbr')->setValue($this->_medischeControleField('employee_employee_personnel_nbr'));
            $this->getElement('employee_display_name')->setValue($this->_medischeControleField('employee_display_name'));

            $this->getElement('employee_employee_partner')->setValue($this->_medischeControleField('employee_employee_partner'));
            $this->getElement('employee_phone')->setValue($this->_medischeControleField('employee_phone'));
            $this->getElement('employee_mobile')->setValue($this->_medischeControleField('employee_mobile'));
            $this->getElement('employee_employee_level1')->setValue($this->_medischeControleField('employee_employee_level1'));
            $this->getElement('employee_employee_code_level2')->setValue($this->_medischeControleField('employee_employee_code_level2'));
            $this->getElement('employee_employee_level2')->setValue($this->_medischeControleField('employee_employee_level2'));
            $this->getElement('employee_employee_level3')->setValue($this->_medischeControleField('employee_employee_level3'));

            $this->getElement('employee_employee_function')->setValue($this->_medischeControleField('employee_employee_function'));
            $this->getElement('employee_employee_date_in')->setValue($this->_medischeControleField('employee_employee_date_in'));
            $this->getElement('employee_employee_date_out')->setValue($this->_medischeControleField('employee_employee_date_out'));

            $this->getElement('control_reason')->setValue($this->_medischeControleField('control_reason'));
            $this->getElement('illness_start_date')->setValue($this->_medischeControleField('illness_start_date'));
            $this->getElement('illness_end_date')->setValue($this->_medischeControleField('illness_end_date'));

            // type controle
            $this->getElement('control_type')->setValue($this->_medischeControleField('control_type'));
            $this->getElement('control_date')->setValue($this->_medischeControleField('control_date'));
            $this->getElement('control_criterium')->setValue($this->_medischeControleField('control_criterium'));

            $this->getElement('control_job_description')->setValue($this->_medischeControleField('control_job_description'));
            $this->getElement('control_info_mediwe')->setValue($this->_medischeControleField('control_info_mediwe'));
            $this->getElement('control_info_controlearts')->setValue($this->_medischeControleField('control_info_controlearts'));
            $this->getElement('control_info_is_public')->setValue($this->_medischeControleField('control_info_is_public'));
            $this->getElement('control_name_requestor')->setValue($this->_medischeControleField('control_name_requestor'));
            $this->getElement('control_name_contact')->setValue($this->_medischeControleField('control_name_contact'));
            $this->getElement('control_phone_contact')->setValue($this->_medischeControleField('control_phone_contact'));
            $this->getElement('control_email_contact1')->setValue($this->_medischeControleField('control_email_contact1'));
            $this->getElement('control_email_contact2')->setValue($this->_medischeControleField('control_email_contact2'));
            $this->getElement('control_email_contact3')->setValue($this->_medischeControleField('control_email_contact3'));
            $this->getElement('control_email_result1')->setValue($this->_medischeControleField('control_email_result1'));
            $this->getElement('control_email_result2')->setValue($this->_medischeControleField('control_email_result2'));
            $this->getElement('control_email_result3')->setValue($this->_medischeControleField('control_email_result3'));
            $this->getElement('control_purchase_order')->setValue($this->_medischeControleField('control_purchase_order'));

        }


        parent::buildQuickForm();
    }

    public function preProcess() {

        $config = CRM_Basis_Config::singleton();

        $id =   CRM_Utils_Request::retrieve('id', 'Integer');
        if ($id) {
            $this->_id = $id;
            $this->_getMedischeControleData($id);
        }

        $this->_reasonData = $config->getOptions($config->getZiekteMeldingRedenKortOptionGroup());
        $this->_soortData = $config->getOptions($config->getMedischeControleSoortOptionGroup());
        $this->_criteriumData = $config->getOptions($config->getMedischeControleCriteriumOptionGroup());

        $this->_soortAdres = array( ''  => '(niet meegedeeld)');
        $this->_soortAdres[$config->getKlantMedewerkerDomicilieLocationType()] = 'Domicilie';
        $this->_soortAdres[$config->getKlantMedewerkerVerblijfLocationType()] = 'Verblijf';

        $this->_minDate = $config->getMedischeControleMinimaleDatum();
        
    }

    public function postProcess() {

        //CRM_Core_Error::debug('submitValues', $this->_submitValues);
        //exit();



        $this->saveMedischeControle($this->_submitValues);



        parent::postProcess();
    }

    private function saveMedischeControle($formValues) {

        $medische_controle = array();
        $huisbezoek = array();

        $medische_controle = civicrm_api3('MedischeControle', 'create', $formValues);

        if (!isset($formValues['id'])) {
            $formValues['id'] = $medische_controle['id'];
        }

        $huisbezoek = civicrm_api3('Huisbezoek', 'create', $formValues);

    }

    private function _medischeControleField($key) {
        if (isset($this->_medischeControleData[$key])) {
            return $this->_medischeControleData[$key];
        }
        else {
            return '';
        }
    }


    private function _getMedischeControleData($id) {

        $medische_controle = new CRM_Basis_MedischeControle();

        $this->_medischeControleData = $medische_controle->get(array ( 'id' => $id, ))[$id];
        //CRM_Core_Error::debug('data', $this->_medischeControleData);exit;
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
