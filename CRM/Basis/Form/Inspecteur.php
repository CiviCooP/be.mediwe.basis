<?php

/**
 * Form controller class
 *
 * @see https://wiki.civicrm.org/confluence/display/CRMDOC/QuickForm+Reference
 */
class CRM_Basis_Form_Inspecteur extends CRM_Core_Form {
  private $_sectorList = array();

  private $_languageData = array();
  private $_contactData = array();

  public function buildQuickForm() {

    $this->add('hidden', 'id', false, array(), FALSE);

    $this->add('text', 'legal_name', ts('Naam op factuur'), array(), TRUE);
    $this->add('text', 'organization_name', ts('Naam inspecteur'), array(), TRUE);

    $this->add('text', 'supplemental_address_1', ts('Tweede lijn'), array(), FALSE);
    $this->add('text', 'street_address', ts('Adres (straat en huisnummer)'), array(), TRUE);
    $this->add('text', 'postal_code', ts('Postcode'), array(), TRUE);
    $this->add('text', 'city', ts('Gemeente'), array(), TRUE);

    $this->add('select', 'preferred_language', ts('Taal'), $this->_languageData, TRUE);
    
    $this->add('text', 'phone', ts('Telefoon'), array(), FALSE);
    $this->add('text', 'mobile', ts('GSM'), array(), FALSE);
    $this->add('text', 'email', ts('E-mail'), array(), FALSE);

    $this->add('text', 'supplier_venice', ts('Nr Venice'), array(), FALSE);
    $this->add('text', 'supplier_vat', ts('Ondernemingsnummer'), array(), FALSE);
    $this->add('text', 'supplier_account', ts('Rekening'), array(), FALSE);

    $this->addButtons(array(
      array('type' => 'next', 'name' => ts('Save'), 'isDefault' => true,),
      array('type' => 'cancel', 'name' => ts('Cancel')),
    ));

    // export form elements
    $this->assign('elementNames', $this->getRenderableElementNames());


    $this->getElement('id')->setValue(FALSE);

    if (isset($this->_contactData[0])) {
        if  ($this->_contactData[0]['id'] > 0) {
            $this->getElement('id')->setValue($this->_contactData[0]['id']);
        }

        $this->getElement('organization_name')->setValue($this->_contactData[0]['organization_name']);
        $this->getElement('legal_name')->setValue($this->_contactData[0]['legal_name']);

        $this->getElement('supplemental_address_1')->setValue($this->_contactData[0]['supplemental_address_1']);
        $this->getElement('street_address')->setValue($this->_contactData[0]['street_address']);
        $this->getElement('postal_code')->setValue($this->_contactData[0]['postal_code']);
        $this->getElement('city')->setValue($this->_contactData[0]['city']);

        $this->getElement('preferred_language')->setValue($this->_contactData[0]['preferred_language']);

        $this->getElement('phone')->setValue($this->_contactData[0]['phone']);
        $this->getElement('mobile')->setValue($this->_contactData[0]['mobile']);
        $this->getElement('email')->setValue($this->_contactData[0]['email']);

        $this->getElement('supplier_venice')->setValue($this->_contactData[0]['supplier_venice']);
        $this->getElement('supplier_vat')->setValue($this->_contactData[0]['supplier_vat']);
        $this->getElement('supplier_account')->setValue($this->_contactData[0]['supplier_account']);


    }


    parent::buildQuickForm();
  }

  public function preProcess() {

      $this->setLanguageData();
      
      $id =   CRM_Utils_Request::retrieve('id', 'Integer');
      if ($id) {
          $this->setContactData($id);
      }

  }

  public function postProcess() {
    //CRM_Core_Error::debug('submitValues', $this->_submitValues);
    //exit();
    $this->saveInspecteur($this->_submitValues);
    parent::postProcess();
  }

  private function saveInspecteur($formValues) {

    if (!$formValues['id']) {
        unset($formValues['id']);
    }

    civicrm_api3('Inspecteur', 'create', $formValues);

  }


  private function setContactData($id) {
      $inspecteur = new CRM_Basis_Inspecteur();
      
      $this->_contactData = $inspecteur->get(array ( 'id' => $id, ));

  }

    private function setLanguageData() {
        
        $this->_languageData = array(
                                  'nl_NL' => 'Nederlands',
                                  'fr_FR' => 'Frans',
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
