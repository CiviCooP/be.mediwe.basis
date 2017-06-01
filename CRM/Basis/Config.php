<?php

/**
 * Class for Mediwe Basis Configuration
 *
 * @author Erik Hommel (CiviCooP) <erik.hommel@civicoop.org>
 * @date 1 June 017
 * @license AGPL-3.0
 */
class CRM_Basis_Config {

  // property for singleton pattern (caching the config)
  static private $_singleton = NULL;

  // properties for specific contact sub types
  private $_klantContactSubType = NULL;
  private $_klantMedewerkerContactSubType = NULL;
  private $_controleArtsContactSubType = NULL;

  // properties for custom groups
  private $_klantDataCustomGroup = array();

  /**
   * CRM_Basis_Config constructor.
   */
  function __construct() {
    $this->setContactSubTypes();
    $this->setCustomGroups();
  }

  /**
   * Getter for klant contact sub type
   *
   * @return null
   */
  public function getKlantContactSubType() {
    return $this->_klantContactSubType;
  }

  /**
   * Getter for klant medewerker contact sub type
   *
   * @return mixed
   */
  public function getKlantMedewerkerContactSubType() {
    return $this->_klantMedewerkerContactSubType;
  }

  /**
   * Getter for controle arts contact sub type
   *
   * @return null
   */
  public function getControleArtsContactSubType() {
    return $this->_controleArtsContactSubType;
  }

  /**
   * Method to set the relevant contact sub type properties
   */
  private function setContactSubTypes() {
    try {
      $contactTypes = civicrm_api3('ContactType','get', array(
        'options' => array('limit' => 0)));
      foreach ($contactTypes['values'] as $contactType) {
        switch ($contactType['name']) {
          case 'mediwe_klant':
            $this->_klantContactSubType = $contactType['id'];
            break;
          case 'mediwe_klant_medewerker':
            $this->_klantMedewerkerContactSubType = $contactType['id'];
            break;
          case 'mediwe_controle_arts':
            $this->_controleArtsContactSubType = $contactType['id'];
            break;
        }
      }
    }
    catch (CiviCRM_API3_Exception $ex) {
    }
  }
  /**
   * Function to return singleton object
   *
   * @return object $_singleton
   * @access public
   * @static
   */
  public static function &singleton() {
    if (self::$_singleton === NULL) {
      self::$_singleton = new CRM_Basis_Config();
    }
    return self::$_singleton;
  }
}