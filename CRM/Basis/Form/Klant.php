<?php

/**
 * Form controller class
 *
 * @see https://wiki.civicrm.org/confluence/display/CRMDOC/QuickForm+Reference
 */
class CRM_Basis_Form_Klant extends CRM_Core_Form {
  private $_sectorList = array();
  private $_contactData = array();

  public function buildQuickForm() {
    $this->add('select', 'customer_procedure_id_sector', ts('Sector'), $this->_sectorList, TRUE);
    $this->add('text', 'organization_name', ts('Naam organisatie'), array(), TRUE);
    $this->add('text', 'supplemental_address_1', ts('Tweede lijn'), array(), FALSE);
    $this->add('text', 'street_address', ts('Adres (straat en huisnummer)'), array(), TRUE);
    $this->add('text', 'postal_code', ts('Postcode'), array(), TRUE);
    $this->add('text', 'city', ts('Gemeente'), array(), TRUE);
    $this->add('text', 'customer_vat', ts('BTW nummer'), array(), TRUE);
    $this->add('text', 'customer_reference', ts('Eigen referentie'), array(), FALSE);
    $this->add('text', 'customer_procedure_email_results', ts('Emailadres voor resultaten'), array(), FALSE);
    $this->add('text', 'org_level1', ts('Omschrijving niveau 1'), array(), FALSE);
    $this->add('text', 'org_level2', ts('Omschrijving niveau 2'), array(), FALSE);
    $this->add('text', 'org_level3', ts('Omschrijving niveau 3'), array(), FALSE);
    $this->addButtons(array(
      array('type' => 'next', 'name' => ts('Save'), 'isDefault' => true,),
      array('type' => 'cancel', 'name' => ts('Cancel')),
    ));

    // export form elements
    $this->assign('elementNames', $this->getRenderableElementNames());

    if (isset($this->_contactData[0])) {
        // set values to screen
        $this->getElement('customer_procedure_id_sector')->setValue($this->_contactData[0]['customer_procedure_id_sector']);
        $this->getElement('organization_name')->setValue($this->_contactData[0]['organization_name']);
        $this->getElement('supplemental_address_1')->setValue($this->_contactData[0]['supplemental_address_1']);
        $this->getElement('street_address')->setValue($this->_contactData[0]['street_address']);
        $this->getElement('postal_code')->setValue($this->_contactData[0]['postal_code']);
        $this->getElement('city')->setValue($this->_contactData[0]['city']);
        $this->getElement('customer_vat')->setValue($this->_contactData[0]['customer_vat']);
        $this->getElement('customer_reference')->setValue($this->_contactData[0]['customer_reference']);
        $this->getElement('customer_procedure_email_results')->setValue($this->_contactData[0]['customer_procedure_email_results']);
    }


    parent::buildQuickForm();
  }

  public function preProcess() {
    $this->setSectorList();

    $id =   CRM_Utils_Request::retrieve('id', 'Integer');
    if ($id) {
        $this->setContactData($id);
    }
  }

  public function postProcess() {
    //CRM_Core_Error::debug('submitValues', $this->_submitValues);
    //exit();
    $this->saveKlant($this->_submitValues);
    parent::postProcess();
  }

  private function saveKlant($formValues) {
    civicrm_api3('Klant', 'create', $formValues);
  }

  /**
   * Method to set the list of sectors
   */
  private function setSectorList() {
    try {
      $optionValues = civicrm_api3('OptionValue', 'get', array(
        'option_group_id' => 'sector',
        'is_active' => 1,
        'options' > array('limit' => 0),
      ));
      foreach ($optionValues['values'] as $optionValue) {
        $this->_sectorList[$optionValue['value']] = $optionValue['label'];
      }
    }
    catch (CiviCRM_API3_Exception $ex) {
    }
    asort($this->_sectorList);
  }

  private function setContactData($id) {
      $klant = new CRM_Basis_Klant();

      $this->_contactData = $klant->get(array ( 'id' => $id, ));

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
