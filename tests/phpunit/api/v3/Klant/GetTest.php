<?php
/**
 * Created by PhpStorm.
 * User: klaas
 * Date: 2-3-18
 * Time: 12:12
 *
 * @group headless
 */

class api_v3_Klant_GetTest extends CRM_Basis_Test {


  /**
   * controleert of de standaard klant goed is aangemaakt
   *
   * @throws \CiviCRM_API3_Exception
   */
  public function testDiverseNegosie() {
    $result = civicrm_api3('Klant','GetSingle',array(
      'id' => $this->_klantId,
    ));
    $this->assertEquals($result['organization_name'],'Diverse Negosie');
    $this->assertArrayHasKey('mf_venice', $result);
    $this->assertArrayHasKey('mf_btw_nummer', $result);
  }

  /**
   * kan er gezocht worden op venice nummer
   *
   * @throws \CiviCRM_API3_Exception
   */
  public function testZoekVeniceNummer() {
    $result = civicrm_api3('Klant','GetSingle',array(
      'mf_venice' => '89',
    ));
    $this->assertEquals($result['organization_name'],'Diverse Negosie');
  }

  /**
   * Een onbekend venice nummer mag niet gevonden worden. Dit wordt
   * afgevangen met exception annotaties
   *
   * @expectedException \CiviCRM_API3_Exception
   * @expectedExceptionMessage Expected one Klant but found 0
   */
  public function testZoekOnbekendVeniceNummer() {
    $result = civicrm_api3('Klant','GetSingle',array(
      'mf_venice' => '89onbekend',
    ));
  }

}