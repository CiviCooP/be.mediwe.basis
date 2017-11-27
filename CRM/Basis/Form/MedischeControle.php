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
        $this->add('text', 'mkm_rijksregister_nummer', ts('Rijksregisternummer '), array(), FALSE);
        $this->add('text', 'mkm_personeelsnummer', ts('Personeelsnummer'), array(), FALSE);

        $this->add('text', 'employee_display_name', ts('Naam werknemer'), array(), TRUE);

        $this->add('text', 'mkm_partner', ts('Partner'), array(), FALSE);

        $this->add('text', 'employee_phone', ts('Telefoon'), array(), FALSE);
        $this->add('text', 'employee_mobile', ts('GSM'), array(), FALSE);

        $this->add('text', 'mkm_niveau1', ts('Niveau 1'), array(), FALSE);
        $this->add('text', 'mkm_code_niveau2', ts('Code Niveau 2'), array(), FALSE);
        $this->add('text', 'mkm_niveau2', ts('Niveau 2'), array(), FALSE);
        $this->add('text', 'mkm_niveau3', ts('Niveau 3'), array(), FALSE);

        $this->add('text', 'mkm_functie', ts('Functie'), array(), FALSE);

        $this->add( 'datepicker', 'mkm_datum_in_dienst',  ts('Datum in dienst'), array(), FALSE, array('time' => FALSE, 'date' => 'dd-mm-yy', 'minDate' => '1940-01-01'));
        $this->add( 'datepicker', 'mkm_datum_uit_dienst',  ts('Datum uit dienst'), array(), FALSE, array('time' => FALSE, 'date' => 'dd-mm-yy', 'minDate' => '2010-01-01'));


        // Controle gegevens
        $this->add('select', 'mmc_reden_ziekte_kort', ts('Reden'), $this->_reasonData, TRUE);
        $this->add( 'datepicker', 'illness_start_date',  ts('Begindatum'), array(), TRUE, array('time' => FALSE, 'date' => 'dd-mm-yy', 'minDate' => '2017-01-01'));
        $this->add( 'datepicker', 'illness_end_date',  ts('Einddatum'), array(), FALSE, array('time' => FALSE, 'date' => 'dd-mm-yy', 'minDate' => '2017-01-01'));

        $this->add('select', 'mmc_controle_criterium', ts('Criterium voor controle'), $this->_criteriumData, FALSE);

        $this->add('select', 'mmc_type_controle', ts('Type controle'), $this->_soortData, TRUE);
        $this->add( 'datepicker', 'mmc_controle_datum',  ts('ControleDatum'), array(), FALSE, array('time' => FALSE, 'date' => 'dd-mm-yy', 'minDate' => $this->_minDate));

        $this->add('select', 'visit_location_type', ts('Soort adres'), $this->_soortAdres, TRUE);
        $this->add('text', 'visit_supplemental_address_1', ts('Tweede lijn'), array(), FALSE);
        $this->add('text', 'mh_huisbezoek_adres', ts('Adres (straat en huisnummer)'), array(), TRUE);
        $this->add('text', 'mh_huisbezoek_postcode', ts('Postcode'), array(), TRUE);
        $this->add('text', 'mh_huisbezoek_gemeente', ts('Gemeente'), array(), TRUE);

        $this->add('textarea', 'mmc_job_beschrijving', ts('Job omschrijving'), array(), FALSE);
        $this->add('textarea', 'mmc_opmerking_mediwe', ts('Info voor Mediwe'), array(), FALSE);
        $this->add('textarea', 'mmc_opmerking_controlearts', ts('Info voor de controlearts'), array(), FALSE);

        $this->addYesNo('mmc_info_delen_patient', 'Mag deze info gedeeld worden?',  TRUE, FALSE);

        $this->add('text', 'mmc_naam_aanvrager', ts('Aanvrager'), array(), FALSE);
        $this->add('text', 'mmc_naam_contactpersoon', ts('Contactpersoon'), array(), FALSE);
        $this->add('text', 'mmc_telefoon_contactpersoon', ts('Telefoon contact'), array(), FALSE);
        $this->add('text', 'mmc_email1_contactpersoon', ts('Email bevestiging (1)'), array(), FALSE);
        $this->add('text', 'mmc_email2_contactpersoon', ts('Email bevestiging (2)'), array(), FALSE);
        $this->add('text', 'mmc_email3_contactpersoon', ts('Email bevestiging (3)'), array(), FALSE);
        $this->add('text', 'mmc_result_email1', ts('Email resultaat (1)'), array(), FALSE);
        $this->add('text', 'mmc_result_email2', ts('Email resultaat (2)'), array(), FALSE);
        $this->add('text', 'mmc_result_email3', ts('Email resultaat (3)'), array(), FALSE);

        $this->add('text', 'mmc_po_nummer_klant', ts('PO nummer'), array(), FALSE);

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

            $this->getElement('mkm_rijksregister_nummer')->setValue($this->_medischeControleField('mkm_rijksregister_nummer'));
            $this->getElement('mkm_personeelsnummer')->setValue($this->_medischeControleField('mkm_personeelsnummer'));
            $this->getElement('employee_display_name')->setValue($this->_medischeControleField('employee_display_name'));

            $this->getElement('mkm_partner')->setValue($this->_medischeControleField('mkm_partner'));
            $this->getElement('employee_phone')->setValue($this->_medischeControleField('employee_phone'));
            $this->getElement('employee_mobile')->setValue($this->_medischeControleField('employee_mobile'));
            $this->getElement('mkm_niveau1')->setValue($this->_medischeControleField('mkm_niveau1'));
            $this->getElement('mkm_code_niveau2')->setValue($this->_medischeControleField('mkm_code_niveau2'));
            $this->getElement('mkm_niveau2')->setValue($this->_medischeControleField('mkm_niveau2'));
            $this->getElement('mkm_niveau3')->setValue($this->_medischeControleField('mkm_niveau3'));

            $this->getElement('mkm_functie')->setValue($this->_medischeControleField('mkm_functie'));
            $this->getElement('mkm_datum_in_dienst')->setValue($this->_medischeControleField('mkm_datum_in_dienst'));
            $this->getElement('mkm_datum_uit_dienst')->setValue($this->_medischeControleField('mkm_datum_uit_dienst'));

            $this->getElement('mmc_reden_ziekte_kort')->setValue($this->_medischeControleField('mmc_reden_ziekte_kort'));
            $this->getElement('illness_start_date')->setValue($this->_medischeControleField('illness_start_date'));
            $this->getElement('illness_end_date')->setValue($this->_medischeControleField('illness_end_date'));

            // type controle
            $this->getElement('mmc_type_controle')->setValue($this->_medischeControleField('mmc_type_controle'));
            $this->getElement('mmc_controle_datum')->setValue($this->_medischeControleField('mmc_controle_datum'));
            $this->getElement('mmc_controle_criterium')->setValue($this->_medischeControleField('mmc_controle_criterium'));

            $this->getElement('mmc_job_beschrijving')->setValue($this->_medischeControleField('mmc_job_beschrijving'));
            $this->getElement('mmc_opmerking_mediwe')->setValue($this->_medischeControleField('mmc_opmerking_mediwe'));
            $this->getElement('mmc_opmerking_controlearts')->setValue($this->_medischeControleField('control_info_controlearts'));
            $this->getElement('mmc_info_delen_patient')->setValue($this->_medischeControleField('mmc_info_delen_patient'));
            $this->getElement('mmc_naam_aanvrager')->setValue($this->_medischeControleField('mmc_naam_aanvrager'));
            $this->getElement('mmc_naam_contactpersoon')->setValue($this->_medischeControleField('mmc_naam_contactpersoon'));
            $this->getElement('mmc_telefoon_contactpersoon')->setValue($this->_medischeControleField('mmc_telefoon_contactpersoon'));
            $this->getElement('mmc_email1_contactpersoon')->setValue($this->_medischeControleField('mmc_email1_contactpersoon'));
            $this->getElement('mmc_email2_contactpersoon')->setValue($this->_medischeControleField('mmc_email2_contactpersoon'));
            $this->getElement('mmc_email3_contactpersoon')->setValue($this->_medischeControleField('mmc_email3_contactpersoon'));
            $this->getElement('mmc_result_email1')->setValue($this->_medischeControleField('mmc_result_email1'));
            $this->getElement('mmc_result_email2')->setValue($this->_medischeControleField('mmc_result_email2'));
            $this->getElement('mmc_result_email3')->setValue($this->_medischeControleField('mmc_result_email3'));
            $this->getElement('mmc_po_nummer_klant')->setValue($this->_medischeControleField('mmc_po_nummer_klant'));

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
        $return = array();

        $return = civicrm_api3('MedischeControle', 'create', $formValues)['values'];

        $formValues['case_id'] = reset($medische_controle)['id'];

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
