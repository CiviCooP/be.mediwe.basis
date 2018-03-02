<?php
/**
 * Class CRM_Basis_MedischeControleTest
 * @group headless
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
   * medewerks is
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

}