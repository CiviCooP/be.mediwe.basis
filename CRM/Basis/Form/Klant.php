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
    $this->add('select', 'sector_id', ts('Sector'), $this->_sectorList, TRUE);
    $this->add('text', 'organization_name', ts('Naam organisatie'), array(), TRUE);
    $this->add('text', 'supplemental_address_1', ts('Tweede lijn'), array(), FALSE);
    $this->add('text', 'street_address', ts('Adres (straat en huisnummer)'), array(), TRUE);
    $this->add('text', 'postal_code', ts('Postcode'), array(), TRUE);
    $this->add('text', 'city', ts('Gemeente'), array(), TRUE);
    $this->add('text', 'customer_vat', ts('BTW nummer'), array(), TRUE);
    $this->add('text', 'customer_reference', ts('Eigen referentie'), array(), FALSE);
    $this->add('text', 'email_resultaten', ts('Emailadres voor resultaten'), array(), FALSE);
    $this->add('text', 'org_level1', ts('Omschrijving niveau 1'), array(), FALSE);
    $this->add('text', 'org_level2', ts('Omschrijving niveau 2'), array(), FALSE);
    $this->add('text', 'org_level3', ts('Omschrijving niveau 3'), array(), FALSE);
    $this->addButtons(array(
      array('type' => 'next', 'name' => ts('Save'), 'isDefault' => true,),
      array('type' => 'cancel', 'name' => ts('Cancel')),
    ));

    // export form elements
    $this->assign('elementNames', $this->getRenderableElementNames());
    parent::buildQuickForm();
  }

  public function preProcess() {
    $this->setSectorList();
    $this->setContactData(25);
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
