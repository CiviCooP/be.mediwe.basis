<?php

/**
 * Form controller class
 *
 * @see https://wiki.civicrm.org/confluence/display/CRMDOC/QuickForm+Reference
 */
class CRM_Basis_Form_Ziektemelding extends CRM_Core_Form {

  private $_reasonData = array();
  private $_ziektemeldingData = array();

  public function buildQuickForm() {

    // De werkgever
    $this->add('text', 'employer_organization_name', ts('Werkgever '), array(), FALSE);
    $this->add('text', 'employer_customer_vat', ts('BTW nummer '), array(), FALSE);

    // De werknemer
    $this->add('text', 'employee_employee_national_nbr', ts('Rijksregisternummer '), array(), FALSE);
    $this->add('text', 'employee_employee_personnel_nbr', ts('Personeelsnummer'), array(), FALSE);

    $this->add('text', 'employee_display_name', ts('Naam werknemer'), array(), TRUE);

    $this->add('text', 'employee_partner', ts('Partner'), array(), FALSE);

    $this->add('text', 'employee_phone', ts('Telefoon'), array(), FALSE);
    $this->add('text', 'employee_mobile', ts('GSM'), array(), FALSE);

    $this->add('text', 'employee_level1', ts('Niveau 1'), array(), FALSE);
    $this->add('text', 'employee_code_level2', ts('Code Niveau 2'), array(), FALSE);
    $this->add('text', 'employee_level2', ts('Niveau 2'), array(), FALSE);
    $this->add('text', 'employee_level3', ts('Niveau 3'), array(), FALSE);

    $this->add('text', 'employee_function', ts('Functie'), array(), FALSE);

    $this->add( 'datepicker', 'employee_date_in',  ts('Datum in dienst'), array(), FALSE, array('time' => FALSE, 'date' => 'dd-mm-yy', 'minDate' => '1940-01-01'));
    $this->add( 'datepicker', 'employee_date_out',  ts('Datum uit dienst'), array(), FALSE, array('time' => FALSE, 'date' => 'dd-mm-yy', 'minDate' => '2010-01-01'));


    // Afwezigheidsgegevens
      $this->add('select', 'illness_reason', ts('Reden'), $this->_reasonData, TRUE);
      $this->add( 'datepicker', 'illness_date_begin',  ts('Begindatum'), array(), TRUE, array('time' => FALSE, 'date' => 'dd-mm-yy', 'minDate' => '2017-01-01'));
      $this->add( 'datepicker', 'illness_date_end',  ts('Einddatum'), array(), FALSE, array('time' => FALSE, 'date' => 'dd-mm-yy', 'minDate' => '2017-01-01'));
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

    if (isset($this->_ziektemeldingData[0])) {

         $employee_id = $this->_ziektemeldingData[0]['client_id'];
         $employee = $this->_ziektemeldingData[0]['contacts'][$employee_id];
          // set values to screen

        /*
          if  ($this->_contactData[0]['id'] > 0) {
              $this->add('hidden', 'id', ts('Id '), array(), FALSE);
              $this->getElement('id')->setValue($this->_contactData[0]['id']);
          }
        */

          //$this->getElement('employer_organization_name')->setValue($this->_contactData[0]['employer_organization_name']);
          //$this->getElement('employer_customer_vat')->setValue($this->_contactData[0]['employer_customer_vat']);

          //$this->getElement('employee_employee_national_nbr')->setValue($this->_contactData[0]['employee_employee_national_nbr']);
          //$this->getElement('employee_employee_personnel_nbr')->setValue($this->_contactData[0]['employee_employee_personnel_nbr']);
          $this->getElement('employee_display_name')->setValue($employee['display_name']);

          //$this->getElement('employee_partner')->setValue($this->_contactData[0]['employee_partner']);
          $this->getElement('employee_phone')->setValue($employee['phone']);
          //$this->getElement('employee_mobile')->setValue($this->_contactData[0]['employee_mobile']);
          //$this->getElement('employee_level1')->setValue($this->_contactData[0]['employee_level1']);
          //$this->getElement('employee_code_level2')->setValue($this->_contactData[0]['employee_code_level2']);
          //$this->getElement('employee_level2')->setValue($this->_contactData[0]['employee_level2']);
          //$this->getElement('employee_level3')->setValue($this->_contactData[0]['employee_level3']);

          //$this->getElement('employee_function')->setValue($this->_contactData[0]['employee_function']);
          //$this->getElement('employee_date_in')->setValue($this->_contactData[0]['employee_date_in']);
          //$this->getElement('employee_date_out')->setValue($this->_contactData[0]['employee_date_out']);

          //$this->getElement('employee_supplemental_address_1_residence')->setValue($this->_contactData[0]['employee_supplemental_address_1_residence']);
          //$this->getElement('employee_street_address_residence')->setValue($this->_contactData[0]['employee_street_address_residence']);
          //$this->getElement('employee_postal_code_residence')->setValue($this->_contactData[0]['employee_postal_code_residence']);
          //$this->getElement('employee_city_residence')->setValue($this->_contactData[0]['employee_city_residence']);

        $this->getElement('illness_reason')->setValue($this->_ziektemeldingData[0]['illness_reason']);
        $this->getElement('illness_date_begin')->setValue($this->_ziektemeldingData[0]['start_date']);
        $this->getElement('illness_date_end')->setValue($this->_ziektemeldingData[0]['end_date']);
        $this->getElement('illness_is_extension')->setValue($this->_ziektemeldingData[0]['illness_is_extension']);
        $this->getElement('illness_is_private_accident')->setValue($this->_ziektemeldingData[0]['illness_is_private_accident']);
        $this->getElement('illness_is_exit_allowed')->setValue($this->_ziektemeldingData[0]['illness_is_exit_allowed']);
        $this->getElement('illness_is_hospitalization')->setValue($this->_ziektemeldingData[0]['illness_is_hospitalization']);
        $this->getElement('illness_no_certificate')->setValue($this->_ziektemeldingData[0]['illness_no_certificate']);
    }


    parent::buildQuickForm();
  }

  public function preProcess() {

      $id =   CRM_Utils_Request::retrieve('id', 'Integer');
      if ($id) {
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

    civicrm_api3('Ziektemelding', 'create', $formValues);
  }


  private function _setReasonData() {

      $this->_reasonData = array(
            ''       => '(niet meegedeeld)',
            'ziekte' => 'Ziekte',
            'ao'     => 'Arbeidsongeval'
          );

  }

    private function _getZiektemeldingData($id) {

        $ziektemelding = new CRM_Basis_Ziektemelding();

        $this->_ziektemeldingData = $ziektemelding->get(array ( 'id' => $id, ));

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
