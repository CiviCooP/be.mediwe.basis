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

    $this->add('hidden', 'id', ts('Id '), array(), FALSE);

    $this->add('text', 'organization_name', ts('Naam'), array(), TRUE);

    $this->add('text', 'supplemental_address_1', ts('Tweede lijn'), array(), FALSE);
    $this->add('text', 'street_address', ts('Adres (straat en huisnummer)'), array(), TRUE);
    $this->add('text', 'postal_code', ts('Postcode'), array(), TRUE);
    $this->add('text', 'city', ts('Gemeente'), array(), TRUE);

    $this->add('text', 'controlearts_vat', ts('BTW nummer'), array(), TRUE);
    $this->add('text', 'controlearts_riziv', ts('Riziv nummer '), array(), FALSE);

    $this->add('select', 'preferred_language', ts('Taal'), $this->_languageData, TRUE);
    
    $this->add('text', 'phone', ts('Telefoon'), array(), FALSE);
    $this->add('text', 'mobile', ts('GSM'), array(), FALSE);
    $this->add('text', 'email', ts('E-mail'), array(), FALSE);

    $this->addYesNo('arts_gebruikt_app', ts('Gebruikt applicatie'), TRUE, TRUE);

    $this->addYesNo('arts_bellen_vooraf', ts('Opbellen vooraf'), TRUE, TRUE);
    $this->addYesNo('arts_bellen_achteraf', ts('Opbellen achteraf'), TRUE, TRUE);
    $this->addYesNo('arts_opdracht_fax', ts('Opdrachten via Fax'), TRUE, TRUE);
    $this->addYesNo('arts_opdracht_mail', ts('Opdrachten via e-mail'), TRUE, TRUE);
    $this->addYesNo('arts_overzicht', ts('Wenst een overzicht'), TRUE, TRUE);

    $this->add('textarea', 'arts_opmerkingen', ts('Opmerkingen'), array(), FALSE);

    $this->add('text', 'supplier_venice', ts('Nr Venice'), array(), FALSE);
    $this->add('text', 'supplier_vat', ts('Ondernemingsnummer'), array(), FALSE);
    $this->add('text', 'supplier_account', ts('Rekening'), array(), FALSE);

    $this->addButtons(array(
      array('type' => 'next', 'name' => ts('Save'), 'isDefault' => true,),
      array('type' => 'cancel', 'name' => ts('Cancel')),
    ));

    // export form elements
    $this->assign('elementNames', $this->getRenderableElementNames());


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
    $this->saveControleArts($this->_submitValues);
    parent::postProcess();
  }

  private function saveControleArts($formValues) {
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
