<?php

/**
 * Form controller class
 *
 * @see https://wiki.civicrm.org/confluence/display/CRMDOC/QuickForm+Reference
 */
class CRM_Basis_Form_KlantMedewerker extends CRM_Core_Form {

  private $_contactData = array();
  private $_domicilieAdres = array();
  private $_verblijfAdres = array();
  private $_telefoon = array();
  private $_mobile = array();
  private $_employerRelation = array();
  private $_employerData = array();


  public function buildQuickForm() {

    $this->add('hidden', 'organization_name');

    $this->add('text', 'employer_organization_name', ts('Werkgever '), array(), FALSE);
    $this->add('text', 'employer_customer_vat', ts('BTW nummer '), array(), FALSE);
      
    $this->add('text', 'employee_national_nbr', ts('Rijksregisternummer '), array(), FALSE);
    $this->add('text', 'employee_personnel_nbr', ts('Personeelsnummer'), array(), FALSE);

    $this->add('text', 'display_name', ts('Naam werknemer'), array(), TRUE);
    $this->add('text', 'domicilie_supplemental_address_1', ts('Tweede lijn'), array(), FALSE);
    $this->add('text', 'domicilie_street_address', ts('Adres (straat en huisnummer)'), array(), TRUE);
    $this->add('text', 'domicilie_postal_code', ts('Postcode'), array(), TRUE);
    $this->add('text', 'domicilie_city', ts('Gemeente'), array(), TRUE);

    $this->add('text', 'employee_partner', ts('Partner'), array(), FALSE);

    $this->add('text', 'phone', ts('Telefoon'), array(), FALSE);
    $this->add('text', 'mobile', ts('GSM'), array(), FALSE);

    $this->add('text', 'employee_level1', ts('Niveau 1'), array(), FALSE);
    $this->add('text', 'employee_code_level2', ts('Code Niveau 2'), array(), FALSE);
    $this->add('text', 'employee_level2', ts('Niveau 2'), array(), FALSE);
    $this->add('text', 'employee_level3', ts('Niveau 3'), array(), FALSE);

    $this->add('text', 'employee_function', ts('Functie'), array(), FALSE);

    $dateParts     = implode( CRM_Core_DAO::VALUE_SEPARATOR, array( 'Y', 'M' ) );

    $this->add( 'datepicker', 'employee_date_in',  ts('Datum in dienst'), array(), FALSE);
    $this->add( 'datepicker', 'employee_date_out',  ts('Datum uit dienst'), array(), FALSE);

    $this->add('text', 'verblijf_supplemental_address_1', ts('Tweede lijn (verblijfplaats)'), array(), FALSE);
    $this->add('text', 'verblijf_street_address', ts('Adres verblijf (straat en huisnummer)'), array(), FALSE);
    $this->add('text', 'verblijf_postal_code', ts('Postcode verblijf'), array(), FALSE);
    $this->add('text', 'verblijf_city', ts('Gemeente verblijf'), array(), FALSE);


    $this->addButtons(array(
      array('type' => 'next', 'name' => ts('Save'), 'isDefault' => true,),
      array('type' => 'cancel', 'name' => ts('Cancel')),
    ));

    // export form elements
    $this->assign('elementNames', $this->getRenderableElementNames());

      if ($this->_telefoon['id']) {
          $this->add('hidden', 'id_phone');
          $this->getElement('id_phone')->setValue($this->_telefoon['id']);
          $this->getElement('phone')->setValue($this->_data($this->_telefoon, 'phone'));
      }

      if ($this->_mobile['id']) {
          $this->add('hidden', 'id_mobile');
          $this->getElement('id_mobile')->setValue($this->_mobile['id']);
          $this->getElement('mobile')->setValue($this->_data($this->_mobile, 'phone'));
      }

      if ($this->_domicilieAdres['id']) {
          $this->add('hidden', 'domicilie_id');
          $this->getElement('domicilie_id')->setValue($this->_domicilieAdres['id']);
          $this->getElement('domicilie_supplemental_address_1')->setValue($this->_data($this->_domicilieAdres,'supplemental_address_1'));
          $this->getElement('domicilie_street_address')->setValue($this->_data($this->_domicilieAdres,'street_address'));
          $this->getElement('domicilie_postal_code')->setValue($this->_data($this->_domicilieAdres,'postal_code'));
          $this->getElement('domicilie_city')->setValue($this->_data($this->_domicilieAdres,'city'));
      }

      if ($this->_verblijfAdres['id']) {
          $this->add('hidden', 'verblijf_id');
          $this->getElement('verblijf_id')->setValue($this->_verblijfAdres['id']);
          $this->getElement('verblijf_supplemental_address_1')->setValue($this->_data($this->_verblijfAdres, 'supplemental_address_1'));
          $this->getElement('verblijf_street_address')->setValue($this->_data($this->_verblijfAdres, 'street_address'));
          $this->getElement('verblijf_postal_code')->setValue($this->_data($this->_verblijfAdres, 'postal_code'));
          $this->getElement('verblijf_city')->setValue($this->_data($this->_verblijfAdres, 'city'));
      }
      
    if (isset($this->_contactData['id'])) {
          // set values to screen

          $this->add('hidden', 'id');
          $this->getElement('id')->setValue($this->_contactData['id']);

          $this->add('hidden', 'original_employer_organization_name');
          $this->getElement('original_employer_organization_name')->setValue($this->_employerData['organization_name']);
          $this->add('hidden', 'original_employer_customer_vat');
          $this->getElement('original_employer_customer_vat')->setValue($this->_employerData['customer_vat']);
          $this->add('hidden', 'employer_id');
          $this->getElement('employer_id')->setValue($this->_employerData['id']);
        
          $this->getElement('employer_organization_name')->setValue($this->_data($this->_employerData, 'organization_name'));
          $this->getElement('employer_customer_vat')->setValue($this->_data($this->_employerData,'customer_vat'));

          $this->getElement('employee_national_nbr')->setValue($this->_data($this->_contactData,'employee_national_nbr'));
          $this->getElement('employee_personnel_nbr')->setValue($this->_data($this->_contactData,'employee_personnel_nbr'));
          $this->getElement('display_name')->setValue($this->_data($this->_contactData,'display_name'));


          $this->getElement('employee_partner')->setValue($this->_data($this->_contactData,'employee_partner'));

          $this->getElement('employee_level1')->setValue($this->_data($this->_contactData, 'employee_level1'));
          $this->getElement('employee_code_level2')->setValue($this->_data($this->_contactData, 'employee_code_level2'));
          $this->getElement('employee_level2')->setValue($this->_data($this->_contactData, 'employee_level2'));
          $this->getElement('employee_level3')->setValue($this->_data($this->_contactData, 'employee_level3'));

          $this->getElement('employee_function')->setValue($this->_data($this->_contactData, 'employee_function'));
          $this->getElement('employee_date_in')->setValue($this->_data($this->_contactData, 'employee_date_in'));
          $this->getElement('employee_date_out')->setValue($this->_data($this->_contactData, 'employee_date_out'));

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


      $_verblijfAdres = array();
      $_domicilieAdres = array();
      $_telefoon = array();
      $_mobile = array();
      
      $_employer = array();
      $_employerData = array();
      $_employerRelation = array();

    $config = CRM_Basis_Config::singleton();

    // neem de werknemer over in het hoofdscherm van civiCRM
    $formValues['organization_name'] = $formValues['employer_organization_name'];

    $id = reset(
        civicrm_api3('KlantMedewerker', 'create', $formValues)['values']
    );


    foreach ($formValues as $key => $value) {

        switch (substr($key, 0, 9)) {
            case "domicilie":
                $newkey = substr($key, 10);
                $_domicilieAdres[$newkey] = $value;
                break;
            case "verblijf_":
                $newkey = substr($key, 9);
                $_verblijfAdres[$newkey] = $value;
                break;
            case "phone":
                if (isset($formValues['id_phone'])) {
                    $_telefoon['id'] = $formValues['id_phone'];
                }
                $_telefoon['phone'] = $value;
                $_telefoon['phone_type_id'] = '1';
                $_telefoon['location_type_id'] = $config->getKlantMedewerkerDomicilieLocationType();
                $_telefoon['contact_id'] = $id;

                if ($value) {
                    $return = civicrm_api3('Telefoon', 'create', $_telefoon);
                }
                else {
                    if (isset($formValues['id_phone']) && $formValues['id_phone']) {
                        civicrm_api3('Telefoon', 'delete', array( 'id' => $formValues['id_phone']));
                    }
                }                
                break;
            case "mobile":
                if (isset($formValues['id_mobile'])) {
                    $_mobile['id'] = $formValues['id_mobile'];
                }
                $_mobile['phone'] = $value;
                $_mobile['phone_type_id'] = '2';
                $_mobile['location_type_id'] = $config->getKlantMedewerkerDomicilieLocationType();
                $_mobile['contact_id'] = $id;

                if ($value) {
                    $return = civicrm_api3('Telefoon', 'create', $_mobile);
                }
                else {
                    if (isset($formValues['id_mobile']) && $formValues['id_mobile']) {
                        civicrm_api3('Telefoon', 'delete', array( 'id' => $formValues['id_mobile']));
                    }
                }
                
                break;
            case "employer_":
                $newkey = substr($key, 9);
                $_employer[$newkey] = $value;
                break;
                
        }
    }

      $_domicilieAdres['location_type_id'] = $config->getKlantMedewerkerDomicilieLocationType();
      $_domicilieAdres['contact_id'] = $id;
      $_domicilieAdres['debug'] = '1';
      if (!$_domicilieAdres['id']) {
          unset($_domicilieAdres['id']);
      }
      $return = civicrm_api3('Address', 'create', $_domicilieAdres);


      $_verblijfAdres['location_type_id'] = $config->getKlantMedewerkerVerblijfLocationType();
      $_verblijfAdres['contact_id'] = $id;
      if (!$_verblijfAdres['id']) {
          unset($_verblijfAdres['id']);
      }

      $return = civicrm_api3('Adres', 'create', $_verblijfAdres);

      // relatie met de werkgever
       if ($_employer['organization_name'] != $formValues['original_employer_organization_name'] ||
          $_employer['customer_vat'] != $formValues['original_employer_customer_vat'] || !$_employer['id']) {
            
          // Dit is een nieuwe werkgever
          unset($_employer['id']);

          $_employerData = reset(
              civicrm_api3('Klant', 'get', $_employer )['values']
          );

          if (!isset($_employerData['id'])) {
              $_employerData = reset(
                  civicrm_api3('Klant', 'create',
                      $_employer
                  )['values']
              );
          }

          if (!isset($_employer['id']) || $_employerData['id'] != $_employer['id'] || !$_employer['id']) {

              $_employerRelation['contact_id_a'] = $id;
              $_employerRelation['relationship_type_id'] = $config->getIsWerknemerVanRelationshipType()['id'];

              // get the existing relation
              $return = reset(civicrm_api3('Relatie', 'get', $_employerRelation)['values']);
              if (isset($return['id'])) {
                  $_employerRelation['id'] = $return['id'];
              }
              $_employerRelation['contact_id_b'] = $_employerData['id'];
              $return = civicrm_api3('Relatie', 'create', $_employerRelation);
          }
      }
  }


  private function setContactData($id) {

      $config = CRM_Basis_Config::singleton();

      $domicilie_locationtype = $config->getKlantMedewerkerDomicilieLocationType();
      $verblijf_locationtype = $config->getKlantMedewerkerVerblijfLocationType();
      $relatietype = $config->getIsWerknemerVanRelationshipType()['name_a_b'];

      $this->_contactData = reset(
          civicrm_api3('KlantMedewerker', 'get',
              array(
                  'id' => $id
              )
          )['values']
      );

      $this->_employerRelation = reset(
          civicrm_api3('Relatie', 'get',
              array(
                  'contact_id_a' => $id,
                  'relation_type_id' => $relatietype,
              )
          )['values']
      );

      $this->_employerData = reset(
          civicrm_api3('Klant', 'get',
              array(
                  'id' => $this->_employerRelation['contact_id_b'],
              )
          )['values']
      );


      $this->_domicilieAdres = reset(
          civicrm_api3('Adres', 'get', array (
              'contact_id' => $id,
              'location_type_id' => $domicilie_locationtype,
          ))['values']
      );


      $this->_verblijfAdres = reset(
          civicrm_api3('Adres', 'get', array (
              'contact_id' => $id,
              'location_type_id' => $verblijf_locationtype,
          ))['values']
      );


      $this->_telefoon = reset(
          civicrm_api3('Telefoon', 'get',
                array (
                    'contact_id' => $id,
                    'phone_type_id' => '1',
                )
              )['values']
      );

      $this->_mobile = reset(
          civicrm_api3('Telefoon', 'get',
              array (
                  'contact_id' => $id,
                  'phone_type_id' => '2',
              )
          )['values']
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

    private function _data($data_array, $element) {
        if (isset($data_array[$element])) {
            return $data_array[$element];
        }
        else {
            return false;
        }
    }

}
