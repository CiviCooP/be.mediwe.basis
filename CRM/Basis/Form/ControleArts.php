<?php

/**
 * Form controller class
 *
 * @see https://wiki.civicrm.org/confluence/display/CRMDOC/QuickForm+Reference
 */
class CRM_Basis_Form_ControleArts extends CRM_Core_Form {
  private $_sectorList = array();

  private $_languageData = array();
  private $_contactData = array();

  public function buildQuickForm() {

    $this->add('hidden', 'id', false, array(), FALSE);

    $this->add('text', 'legal_name', ts('Naam op factuur'), array(), TRUE);
    $this->add('text', 'organization_name', ts('Naam controlearts'), array(), TRUE);

    $this->add('text', 'supplemental_address_1', ts('Tweede lijn'), array(), FALSE);
    $this->add('text', 'street_address', ts('Adres (straat en huisnummer)'), array(), TRUE);
    $this->add('text', 'postal_code', ts('Postcode'), array(), TRUE);
    $this->add('text', 'city', ts('Gemeente'), array(), TRUE);

    $this->add('text', 'mcc_arts_riziv', ts('Riziv nummer '), array(), FALSE);

    $this->add('select', 'preferred_language', ts('Taal'), $this->_languageData, TRUE);
    
    $this->add('text', 'phone', ts('Telefoon'), array(), FALSE);
    $this->add('text', 'mobile', ts('GSM'), array(), FALSE);
    $this->add('text', 'email', ts('E-mail'), array(), FALSE);

    $this->addYesNo('mcc_arts_gebruikt_app', ts('Gebruikt applicatie'), TRUE, TRUE);

    $this->add('select', 'mcc_arts_bel_moment', ts('Belmoment'), $this->getBelMoment(), FALSE,
      array('id' => 'mcc_arts_bel_moment', 'multiple' => 'multiple', 'class' => 'crm-select2'));
    $this->add('select', 'mcc_arts_opdracht', ts('Opdrachten per'), $this->getOpdrachtPer(), FALSE,
      array('id' => 'mcc_arts_opdracht', 'multiple' => 'multiple', 'class' => 'crm-select2'));
    $this->addYesNo('mcc_arts_overzicht', ts('Wenst een overzicht'), TRUE, TRUE);

    $this->add('textarea', 'mcc_arts_opmerkingen', ts('Opmerkingen'), array(), FALSE);

    $this->add('text', 'leverancier_venice', ts('Nr Venice'), array(), FALSE);
    $this->add('text', 'leverancier_btw_nummer', ts('Ondernemingsnummer'), array(), FALSE);
    $this->add('text', 'leverancier_iban', ts('Rekening'), array(), FALSE);

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

        $this->getElement('mcc_arts_riziv')->setValue($this->_contactData[0]['mcc_arts_riziv']);

        $this->getElement('preferred_language')->setValue($this->_contactData[0]['preferred_language']);

        $this->getElement('phone')->setValue($this->_contactData[0]['phone']);
        $this->getElement('mobile')->setValue($this->_contactData[0]['mobile']);
        $this->getElement('email')->setValue($this->_contactData[0]['email']);

        $this->getElement('mcc_arts_gebruikt_app')->setValue($this->_contactData[0]['mcc_arts_gebruikt_app']);

        $this->getElement('mcc_arts_bel_moment')->setValue($this->_contactData[0]['mcc_arts_bel_moment']);
        $this->getElement('mcc_arts_opdracht')->setValue($this->_contactData[0]['mcc_arts_opdracht']);
        $this->getElement('mcc_arts_overzicht')->setValue($this->_contactData[0]['mcc_arts_overzicht']);

        $this->getElement('mcc_arts_opmerkingen')->setValue($this->_contactData[0]['mcc_arts_opmerkingen']);

        $this->getElement('leverancier_venice')->setValue($this->_contactData[0]['leverancier_venice']);
        $this->getElement('leverancier_btw_nummer')->setValue($this->_contactData[0]['leverancier_btw_nummer']);
        $this->getElement('leverancier_iban')->setValue($this->_contactData[0]['leverancier_iban']);


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
    $this->saveControleArts($this->_submitValues);
    parent::postProcess();
  }

  private function saveControleArts($formValues) {

    if (!$formValues['id']) {
        unset($formValues['id']);
    }

    civicrm_api3('ControleArts', 'create', $formValues);

  }


  private function setContactData($id) {
      $controlearts = new CRM_Basis_ControleArts();
      
      $this->_contactData = $controlearts->get(array ( 'id' => $id, ));

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
