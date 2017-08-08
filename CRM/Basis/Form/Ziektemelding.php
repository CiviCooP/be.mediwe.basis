<?php

/**
 * Form controller class
 *
 * @see https://wiki.civicrm.org/confluence/display/CRMDOC/QuickForm+Reference
 */
class CRM_Basis_Form_Ziektemelding extends CRM_Core_Form {

  private $_reasonData = array();

  public function buildQuickForm() {

    // De werkgever
    $this->add('text', 'employer_name', ts('Werkgever '), array(), FALSE);
    $this->add('text', 'employer_vat', ts('BTW nummer '), array(), FALSE);

    // De werknemer
    $this->add('text', 'employee_national_nbr', ts('Rijksregisternummer '), array(), FALSE);
    $this->add('text', 'employee_personnel_nbr', ts('Personeelsnummer'), array(), FALSE);

    $this->add('text', 'display_name', ts('Naam werknemer'), array(), TRUE);
    $this->add('text', 'supplemental_address_1', ts('Tweede lijn'), array(), FALSE);
    $this->add('text', 'street_address', ts('Adres (straat en huisnummer)'), array(), TRUE);
    $this->add('text', 'postal_code', ts('Postcode'), array(), TRUE);
    $this->add('text', 'city', ts('Gemeente'), array(), TRUE);

    $this->add('text', 'employee_partner', ts('Partner'), array(), FALSE);

    $this->add('text', 'phone', ts('Telefoon'), array(), FALSE);
    $this->add('text', 'mobile', ts('GSM'), array(), FALSE);

    $this->add('text', 'employee_level1', ts('Niveau 1'), array(), FALSE);
    $this->add('text', 'employee_code_level2', ts('Code Niveau 2'), array(), FALSE);
    $this->add('text', 'employee_level2', ts('Niveau 2'), array(), FALSE);
    $this->add('text', 'employee_level3', ts('Niveau 3'), array(), FALSE);

    $this->add('text', 'employee_function', ts('Functie'), array(), FALSE);

    $dateParts     = implode( CRM_Core_DAO::VALUE_SEPARATOR, array( 'Y', 'M' ) );

    $this->add( 'datepicker', 'employee_date_in',  ts('Datum in dienst'), array(), FALSE, array('time' => FALSE, 'date' => 'dd-mm-yy', 'minDate' => '1940-01-01'));
    $this->add( 'datepicker', 'employee_date_out',  ts('Datum uit dienst'), array(), FALSE, array('time' => FALSE, 'date' => 'dd-mm-yy', 'minDate' => '2010-01-01'));

    // Verblijfadres
    $this->add('text', 'supplemental_address_1_residence', ts('Tweede lijn (verblijfplaats)'), array(), FALSE);
    $this->add('text', 'street_address_residence', ts('Adres verblijf (straat en huisnummer)'), array(), FALSE);
    $this->add('text', 'postal_code_residence', ts('Postcode verblijf'), array(), FALSE);
    $this->add('text', 'city_residence', ts('Gemeente verblijf'), array(), FALSE);

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

    if (isset($this->_contactData[0])) {
          // set values to screen

          if  ($this->_contactData[0]['id'] > 0) {
              $this->add('hidden', 'id', ts('Id '), array(), FALSE);
              $this->getElement('id')->setValue($this->_contactData[0]['id']);
          }

          $this->getElement('employer_name')->setValue($this->_contactData[0]['employer_name']);
          $this->getElement('employer_vat')->setValue($this->_contactData[0]['employer_vat']);

          $this->getElement('employee_national_nbr')->setValue($this->_contactData[0]['employee_national_nbr']);
          $this->getElement('employee_personnel_nbr')->setValue($this->_contactData[0]['employee_personnel_nbr']);
          $this->getElement('display_name')->setValue($this->_contactData[0]['display_name']);
          $this->getElement('supplemental_address_1')->setValue($this->_contactData[0]['supplemental_address_1']);
          $this->getElement('street_address')->setValue($this->_contactData[0]['street_address']);
          $this->getElement('postal_code')->setValue($this->_contactData[0]['postal_code']);
          $this->getElement('city')->setValue($this->_contactData[0]['city']);

          $this->getElement('employee_partner')->setValue($this->_contactData[0]['employee_partner']);
          $this->getElement('phone')->setValue($this->_contactData[0]['phone']);
          $this->getElement('mobile')->setValue($this->_contactData[0]['mobile']);
          $this->getElement('employee_level1')->setValue($this->_contactData[0]['employee_level1']);
          $this->getElement('employee_code_level2')->setValue($this->_contactData[0]['employee_code_level2']);
          $this->getElement('employee_level2')->setValue($this->_contactData[0]['employee_level2']);
          $this->getElement('employee_level3')->setValue($this->_contactData[0]['employee_level3']);

          $this->getElement('employee_function')->setValue($this->_contactData[0]['employee_function']);
          $this->getElement('employee_date_in')->setValue($this->_contactData[0]['employee_date_in']);
          $this->getElement('employee_date_out')->setValue($this->_contactData[0]['employee_date_out']);

          $this->getElement('supplemental_address_1_residence')->setValue($this->_contactData[0]['supplemental_address_1_residence']);
          $this->getElement('street_address_residence')->setValue($this->_contactData[0]['street_address_residence']);
          $this->getElement('postal_code_residence')->setValue($this->_contactData[0]['postal_code_residence']);
          $this->getElement('city_residence')->setValue($this->_contactData[0]['city_residence']);
    }


    parent::buildQuickForm();
  }

  public function preProcess() {

      $id =   CRM_Utils_Request::retrieve('id', 'Integer');
      if ($id) {
          $this->setReasonData($id);
      }

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


  private function setReasonData() {


      $this->_reasonData = array(
            'ziekte' => 'Ziekte',
            'ao'     => 'Arbeidsongeval'
          );

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
