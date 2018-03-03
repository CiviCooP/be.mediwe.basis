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
   * @throws \CiviCRM_API3_Exception
   */
  public function testValidateCreateMetBtw() {
    civicrm_api3('MedischeControle', 'Create',$this->_params+array(
      'mf_btw_nummer' => '1234567890',
      'medewerker_id' => $this->_medewerkerId));
  }

  /**
   * De test wordt hier uitvoerd met een btw die lang genoeg is
   * @throws \CiviCRM_API3_Exception
   */
  public function testValidateCreateMetKlantId() {
    civicrm_api3('MedischeControle', 'Create',$this->_params+array(
        'klant_id' => $this->_klantId,
        'medewerker_id' => $this->_medewerkerId
      ));
  }

  /**
   * Voeg een externe klant id toe
   * @throws \CiviCRM_API3_Exception
   */
  public function testValidateCreateMetKlantExternalIdentified() {
    civicrm_api3('MedischeControle', 'Create',$this->_params+array(
      'klant_external_identifier' => 'extklantid',
      'medewerker_id' => $this->_medewerkerId
      ));
  }

  /**
   * Probeer het nu met klantnaam
   * @throws \CiviCRM_API3_Exception
   */
  public function testValidateCreateMetKlantNaam() {
    civicrm_api3('MedischeControle', 'Create',$this->_params+array(
      'klant_naam' => 'Diverse Negosie',
      'medewerker_id' => $this->_medewerkerId ));
  }

  /**
   * Probeer het nu met klantnaam
   * @expectedException \CiviCRM_API3_Exception
   * @expectedExceptionMessage Verplicht Medewerker Id of Medewerker external identifier of Voor en Achternaam
   */
  public function testValidateCreateMetKlantNaamZonderMedewerker() {
    civicrm_api3('MedischeControle', 'Create',$this->_params+array(
        'klant_naam' => 'Nieuwe Klant / Geen medewerker'));
  }

  /**
   * Probeer het nu met klantnaam en externe identifier medewerker
   * @throws \CiviCRM_API3_Exception
   */
  public function testValidateCreateMetKlantNaamMedewerkerExterneIdentifier() {
    civicrm_api3('MedischeControle', 'Create',$this->_params+array(
        'klant_naam' => 'Nieuwe Klant',
        'medewerker_external_identifier' => 'extmed'));
  }

  /**
   * Probeer het nu met klantnaam en alleen voornaam
   * @expectedException \CiviCRM_API3_Exception
   * @expectedExceptionMessage Verplicht Medewerker Id of Medewerker external identifier of Voor en Achternaam
   */
  public function testValidateCreateMetKlantNaamMedewerkerAlleenVoornaam() {
    civicrm_api3('MedischeControle', 'Create',$this->_params+array(
        'klant_naam' => 'Nieuwe Klant',
        'voornaam_medewerker' => 'Hennie'));
  }

  /**
   * Probeer het nu met klantnaam en alleen achternaam
   * @expectedException \CiviCRM_API3_Exception
   * @expectedExceptionMessage Verplicht Medewerker Id of Medewerker external identifier of Voor en Achternaam
   */
  public function testValidateCreateMetKlantNaamMedewerkerAlleenAchternaam() {
    civicrm_api3('MedischeControle', 'Create',$this->_params+array(
        'klant_naam' => 'Nieuwe Klant',
        'achternaam_medewerker' => 'Baytens'));
  }

  /**
   * Probeer het nu met klantnaam en alleen achternaam en voornaam
   * @throws \CiviCRM_API3_Exception
   */
  public function testValidateCreateMetKlantNaamMedewerkerVoorEnAchternaam() {
    civicrm_api3('MedischeControle', 'Create',$this->_params+array(
        'klant_naam' => 'Nieuwe Klant',
        'voornaam_medewerker' => 'Hennie',
        'achternaam_medewerker' => 'Baytens'));
  }

}
