<?php
/**
 * @author Erik Hommel (CiviCooP) <erik.hommel@civicoop.org>
 * @author Klaas Eikelboom (CiviCooP) <klaas.eikelboom@civicoop.org>
 * @author Christophe Deman <christophe.deman@mediwe.be>
 * @date 22 Feb 2018
 * @license AGPL-3.0
 */

use Civi\Test\HeadlessInterface;
use Civi\Test\HookInterface;
use Civi\Test\TransactionalInterface;

abstract class CRM_Basis_Test extends \PHPUnit_Framework_TestCase implements HeadlessInterface, HookInterface, TransactionalInterface {

  protected $_klantId;
  protected $_medewerkerId;

  /**
   * @return \Civi\Test\CiviEnvBuilder
   * @throws \CRM_Extension_Exception_ParseException
   */
  public function setUpHeadless() {

    return \Civi\Test::headless()
      ->installMe(__DIR__)
      ->apply();
  }

  /**
   * @throws \CiviCRM_API3_Exception
   */
  public function setUp() {

    $this->_klantId = civicrm_api3('Klant','Create',array(
      'organization_name' => 'Diverse Negosie',
      'external_identifier' => 'extklantid',
      'mf_btw_nummer' => '1234567890',
      'mf_venice' => '89'
    ))['id'];

    $this->_medewerkerId = civicrm_api3('KlantMedewerker','Create',array(
      'first_name' => 'Adele',
      'last_name' => 'Jensen',
      'external_identifier' => 'extmed',
      'klant_id' => $this->_klantId,
    ))['id'];
  }


  }