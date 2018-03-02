<?php
/**
 * Created by PhpStorm.
 * User: klaas
 * Date: 1-3-18
 * Time: 21:32
 */

use Civi\Test\HeadlessInterface;
use Civi\Test\HookInterface;
use Civi\Test\TransactionalInterface;

abstract class CRM_Basis_Test extends \PHPUnit_Framework_TestCase implements HeadlessInterface, HookInterface, TransactionalInterface {

  protected $_klantId;
  protected $_medewerkerId;

  public function setUpHeadless() {

    return \Civi\Test::headless()
      ->installMe(__DIR__)
      ->apply();
  }

  public function setUp() {

    $this->_klantId = civicrm_api3('Klant','Create',array(
      'organization_name' => 'Diverse Negosie',
      'external_identifier' => 'extid',
      'mf_btw_nummer' => '1234567890',
      'mf_venice' => '89'
    ))['id'];

    $this->_medewerkerId = civicrm_api3('KlantMedewerker','Create',array(
      'first_name' => 'Adele',
      'last_name' => 'Jensen',
      'medewerker_id' => 12,
      'klant_id' => $this->_klantId,
    ))['id'];
  }


  }