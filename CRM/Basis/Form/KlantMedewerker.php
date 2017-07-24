<?php

/**
 * Form controller class
 *
 * @see https://wiki.civicrm.org/confluence/display/CRMDOC/QuickForm+Reference
 */
class CRM_Basis_Form_KlantMedewerker extends CRM_Core_Form {
  private $_sectorList = array();
  private $_contactData = array();

  public function buildQuickForm() {

    $this->add('text', 'employee_national_nbr', ts('Rijksregisternummer '), array(), FALSE);
    $this->add('text', 'employee_personnel_nbr', ts('Personeelsnummer'), array(), FALSE);

    $this->add('text', 'name', ts('Naam werknemer'), array(), TRUE);
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

    $this->add( 'date', 'employee_date_in',
          ts('Datum in dienst'),
          CRM_Core_SelectValues::date('custom', 1, 0, $dateParts ) );
    $this->add( 'date', 'employee_date_out',
          ts('Datum uit dienst'),
          CRM_Core_SelectValues::date('custom', 1, 0, $dateParts ) );

    $this->addButtons(array(
      array('type' => 'next', 'name' => ts('Save'), 'isDefault' => true,),
      array('type' => 'cancel', 'name' => ts('Cancel')),
    ));

    // export form elements
    $this->assign('elementNames', $this->getRenderableElementNames());

    if (isset($this->_contactData[0])) {
          // set values to screen
          $this->getElement('employee_national_nbr')->setValue($this->_contactData[0]['employee_national_nbr']);
          $this->getElement('employee_personnel_nbr')->setValue($this->_contactData[0]['employee_personnel_nbr']);
          $this->getElement('name')->setValue($this->_contactData[0]['name']);
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
    }


    parent::buildQuickForm();
  }

  public function preProcess() {

      $id =   CRM_Utils_Request::retrieve('id', 'Integer');
      if ($id) {
          $this->setContactData($id);
      }

  }

  public function postProcess() {
    //CRM_Core_Error::debug('submitValues', $this->_submitValues);
    //exit();
    $this->saveKlantMedewerker($this->_submitValues);
    parent::postProcess();
  }

  private function saveKlantMedewerker($formValues) {
    civicrm_api3('KlantMedewerker', 'create', $formValues);
  }


  private function setContactData($id) {
      $medewerker = new CRM_Basis_KlantMedewerker();

      $this->_contactData = $medewerker->get(array ( 'id' => $id, ));

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
