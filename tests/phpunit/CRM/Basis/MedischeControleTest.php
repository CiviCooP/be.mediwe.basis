<?php
/**
 * Class CRM_Basis_MedischeControleTest
 * @group headless
 *
 * @author Erik Hommel (CiviCooP) <erik.hommel@civicoop.org>
 * @author Klaas Eikelboom (CiviCooP) <klaas.eikelboom@civicoop.org>
 * @author Christophe Deman <christophe.deman@mediwe.be>
 * @date 1 Maart 2018
 * @license AGPL-3.0
 */

class CRM_Basis_MedischeControleTest extends CRM_Basis_Test {


  public function testHaalofMaakKlantMedewerker(){
    $medischeControle = new CRM_Basis_MedischeControle();

    $params['medewerker_id'] = $this->_medewerkerId;
    $contactId = $medischeControle->haalOfMaakKlantMedewerker($params);
    $this->assertEquals($this->_medewerkerId,$contactId);

    unset($params['medewerker_id']);
    $params['medewerker_external_identifier']='extmed';
    $contactId = $medischeControle->haalOfMaakKlantMedewerker($params);
    $this->assertEquals($this->_medewerkerId,$contactId);

    unset($params['medewerker_external_identifier']);
    $params['voornaam_medewerker'] = 'Bert';
    $params['achternaam_medewerker'] = 'Mutsears';
    $params['klant_id'] = $this->_klantId;
    $contactId = $medischeControle->haalOfMaakKlantMedewerker($params);
    $this->assertNotEquals($this->_medewerkerId,$contactId);
  }

  /**
   * Korte controle of het meegegeven medewerkers id inderdaad wel een
   * medewerkersid is is
   *
   * @expectedException \CiviCRM_API3_Exception
   * @expectedExceptionMessage Expected one KlantMedewerker but found 0
   */
  public function testHaalofMaakKlantMedewerkerVerkeerdMedewerkerId(){
    $medischeControle = new CRM_Basis_MedischeControle();

    $params['medewerker_id'] = $this->_klantId;
    $contactId = $medischeControle->haalOfMaakKlantMedewerker($params);
    $this->assertEquals($this->_medewerkerId,$contactId);
  }

  /**
   * Meest simple test, probeer het met de goede klant Id
   *
   * @throws \CiviCRM_API3_Exception
   */
  public function testHaalOfMaakKlantAlleenKlantId(){
    $medischeControle = new CRM_Basis_MedischeControle();
    $params['klant_id'] = $this->_klantId;
    $klantId = $medischeControle->haalOfMaakKlant($params);
    $this->assertEquals($this->_klantId,$klantId);
  }

  /**
   * Meest simple test, probeer het met de BTW nummer
   *
   * @throws \CiviCRM_API3_Exception
   */
  public function testHaalOfMaakKlantBtwNr(){
    $medischeControle = new CRM_Basis_MedischeControle();
    $params['mf_btw_nummer'] = '1234567890';
    $klantId = $medischeControle->haalOfMaakKlant($params);
    $this->assertEquals($this->_klantId,$klantId);
  }

  /**
   * Zoeken met een afwezig BTW nummer
   * Omdat hij hem niet kan vinden wordt er een nieuwe aangemaakt
   *
   * @throws \CiviCRM_API3_Exception
   */
  public function testHaalOfMaakKlantFoutBtwNr(){
    $medischeControle = new CRM_Basis_MedischeControle();
    $params['mf_btw_nummer'] = '22222222222';
    $params['klant_naam'] = 'Naam van een nieuwe klant';
    $klantId = $medischeControle->haalOfMaakKlant($params);
    // controleer of het een nieuwe is
    $this->assertNotEquals($this->_klantId,$klantId);
  }

  /**
   * Zoeken met een externe identifier
   *
   * @throws \CiviCRM_API3_Exception
   */
  public function testHaalOfMaakKlantExterneIdentifier(){
    $medischeControle = new CRM_Basis_MedischeControle();
    $params['klant_external_identifier'] = 'extklantid';
    $klantId = $medischeControle->haalOfMaakKlant($params);
    $this->assertEquals($this->_klantId,$klantId);
  }

  /**
   * Zoeken met een niet bestaande externe identifier
   *
   * @throws \CiviCRM_API3_Exception
   */
  public function testHaalOfMaakKlantNietBestaandeExterneIdentifier(){
    $medischeControle = new CRM_Basis_MedischeControle();
    $params['klant_external_identifier'] = 'extklantid_nb';
    $params['klant_naam'] = 'De onbekende klant';
    $klantId = $medischeControle->haalOfMaakKlant($params);
    // het moet wel een nieuwe klant zijn
    $this->assertNotEquals($this->_klantId,$klantId);

    // controleren of de klant goed aangemaakt is
    $klantNaam = civicrm_api3('Klant','getvalue',array(
      'id' => $klantId,
      'return' => 'organization_name',
    ));
    $this->assertEquals($klantNaam,'De onbekende klant');


  }

  /**
   * Is de procedure robuust tegen een fout klant id
   *
   * @expectedException \CiviCRM_API3_Exception
   * @expectedExceptionMessage Expected one Klant but found 0
   */

  public function testHaalOfMaakKlantVerkeerdeKlantId(){
    $medischeControle = new CRM_Basis_MedischeControle();
    $params['klant_id'] = $this->_medewerkerId;
    $medischeControle->haalOfMaakKlant($params);
  }

  /**
   * Maak een klant aan met alleen een naam
   *
   * @throws \CiviCRM_API3_Exception
   */
  public function testHaalOfMaakKlantMetAlleenEenNaam(){
    $medischeControle = new CRM_Basis_MedischeControle();
    $params['klant_naam'] = 'Org met alleen naam';
    $klantId = $medischeControle->haalOfMaakKlant($params);
    // het moet wel een nieuwe klant zijn
    $this->assertNotEquals($this->_klantId,$klantId);

    // controleren of de klant goed aangemaakt is
    $klantNaam = civicrm_api3('Klant','getvalue',array(
      'id' => $klantId,
      'return' => 'organization_name',
    ));
    $this->assertEquals($klantNaam,'Org met alleen naam');


  }

}