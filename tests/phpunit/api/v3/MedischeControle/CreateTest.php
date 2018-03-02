<?php

/**
 * MedischeControle.Create API Test Case
 * This is a generic test class implemented with PHPUnit.
 * @group headless
 */
class api_v3_MedischeControle_CreateTest extends CRM_Basis_Test{

  private $_params = array();

  public function setUp(){
    parent::setUp();
    /* deze velden worden afgedwongen door de api */
    $this->_params = array(
      'achternaam_contactpersoon' => 'Jensen',
      'mmc_controle_datum' => '2018010',
    );
  }

  /**
   * @expectedException \CiviCRM_API3_Exception
   * @expectedExceptionMessage Verplicht BTW nr of Klant Id of Klant external identifier if Klant naam
   */
  public function testValidateCreate() {
       civicrm_api3('MedischeControle', 'Create',$this->_params);
  }

  /**
   * Hier voegen we wel een BTW nummer toe maar het is te kort
   *
   * @expectedException \CiviCRM_API3_Exception
   * @expectedExceptionMessage Verplicht BTW nr of Klant Id of Klant external identifier if Klant naam
   */
  public function testValidateCreateMetBtwTeKort() {
    civicrm_api3('MedischeControle', 'Create',$this->_params+array('mf_btw_nummer' => '12'));
  }


  /**
   * De test wordt hier uitvoerd met een btw die lang genoeg is
   *
   * @expectedException \CiviCRM_API3_Exception
   * @expectedExceptionMessage Undefined index: medewerker_id
   */
  public function testValidateCreateMetBtw() {
    civicrm_api3('MedischeControle', 'Create',$this->_params+array(
      'mf_btw_nummer' => '1234567890',
      'medewerker_id' => 12));
  }

  /**
   * De test wordt hier uitvoerd met een btw die lang genoeg is
   *
   * @expectedException \CiviCRM_API3_Exception
   * @expectedExceptionMessage Undefined index: medewerker_id
   */
  public function testValidateCreateMetKlantId() {
    civicrm_api3('MedischeControle', 'Create',$this->_params+array(
      'klant_id' => '212',
        'medewerker_id' => 12
      ));
  }

  /**
   * De test wordt hier uitvoerd met een btw die lang genoeg is
   *
   * @expectedException \CiviCRM_API3_Exception
   * @expectedExceptionMessage Undefined index: medewerker_id
   */
  public function testValidateCreateMetKlantExternalIdentified() {
    civicrm_api3('MedischeControle', 'Create',$this->_params+array(
      'klant_external_identifier' => '1234567890',
      'medewerker_id' => 12
      ));
  }

  /**
   * De test wordt hier uitvoerd met een btw die lang genoeg is
   *
   * @expectedException \CiviCRM_API3_Exception
   * @expectedExceptionMessage Undefined index: medewerker_id
   */
  public function testValidateCreateMetKlantNaam() {
    civicrm_api3('MedischeControle', 'Create',$this->_params+array(
      'klant_naam' => 'Diverse Negosie',
      'medewerker_id' => 12 ));
  }

}
