<?php

/**
 * Form controller class
 *
 * @see https://wiki.civicrm.org/confluence/display/CRMDOC/QuickForm+Reference
 */
class CRM_Basis_Form_Klant extends CRM_Core_Form {
  private $_sectorList = array();
  private $_contactData = array();
  private $_addressData = array();

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

    if (isset($this->_contactData['id'])) {
        // set values to screen
        $this->getElement('customer_procedure_id_sector')->setValue($this->_data($this->_contactData, 'customer_procedure_id_sector'));
        $this->getElement('organization_name')->setValue($this->_data($this->_contactData,'organization_name'));
        $this->getElement('customer_vat')->setValue($this->_data($this->_contactData,'customer_vat'));
        $this->getElement('customer_reference')->setValue($this->_data($this->_contactData,'customer_reference'));
        $this->getElement('customer_procedure_email_results')->setValue($this->_data($this->_contactData,'customer_procedure_email_results'));

        // adresgegevens
        $this->getElement('supplemental_address_1')->setValue($this->_data($this->_addressData,'supplemental_address_1'));
        $this->getElement('street_address')->setValue($this->_data($this->_addressData,'street_address'));
        $this->getElement('postal_code')->setValue($this->_data($this->_addressData,'postal_code'));
        $this->getElement('city')->setValue($this->_data($this->_addressData,'city'));
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

  private function _data($data_array, $element) {
      if (isset($data_array[$element])) {
          return $data_array[$element];
      }
      else {
          return false;
      }
  }

  private function saveKlant($formValues) {

      $adres = array();
      $config = CRM_Basis_Config::singleton();

      // klantgegevens
      if (isset($this->_contactData['id'])) {
          $formValues['id'] = $this->_contactData['id'];
      }

      try {
          $klant = civicrm_api3('Klant', 'create', $formValues);
      }
      catch (CiviCRM_API3_Exception $ex) {
          throw new API_Exception(ts('Could not create a Mediwe Klant in '.__METHOD__
              .', contact your system administrator! Error from API Klant create: '.$ex->getMessage()));
      }

      // Adresgegevens
      $adres['id'] = $this->_data($this->_addressData, 'id');
      $adres['contact_id'] = $klant['id'];
      $adres['location_type_id'] = $config->getKlantLocationType()['name'];
      $adres['street_address'] = $formValues['street_address'];
      $adres['supplemental_address_1'] = $formValues['supplemental_address_1'];
      $adres['postal_code'] = $formValues['postal_code'];
      $adres['city'] = $formValues['city'];

      try {
          civicrm_api3('Adres', 'create', $adres);
      }
      catch (CiviCRM_API3_Exception $ex) {
          throw new API_Exception(ts('Could not create a Mediwe adres in '.__METHOD__
              .', contact your system administrator! Error from API Adres create: '.$ex->getMessage()));
      }

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

      $config = CRM_Basis_Config::singleton();
      $locationtype = $config->getKlantLocationType()['name'];

      $this->_contactData = reset(civicrm_api3('Klant', 'get', array('id' => $id))['values']);
      $this->_addressData = reset(
          civicrm_api3('Adres', 'get', array (
            'contact_id' => $id,
            'location_type_id' => $locationtype,
          ))['values']
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
