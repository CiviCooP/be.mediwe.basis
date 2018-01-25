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
  private $_klantContactSubType = array();
  private $_klantMedewerkerContactSubType = array();
  private $_controleArtsContactSubType = array();
  private $_inspecteurContactSubType = array();

  // properties for address location types
  private $_klantLocationTypes = array();
  private $_klantMedewerkerLocationTypes = array();

  // properties for relationship types
  private $_isKlantViaRelationshipTypeId = NULL;
  private $_isWerknemerVanRelationshipTypeId = NULL;
  private $_ziektemeldingRelationshipTypeId = NULL;
  private $_vraagtControleAanRelationshipTypeId = NULL;
  private $_controleArtsRelationshipTypeId = NULL;


  // properties for membership types
  private $_maandelijksMembershipType = array();
  private $_voorafbetaaldMembershipType = array();
  private $_controleartsMembershipType = array();
  private $_mijnMediweMembershipType = array();
  private $_zorgfondsMembershipType = array();
  private $_inspecteurMembershipType = array();

  // properties for activity types
  private $_huisbezoekActivityType = array();
  private $_consultatieActivityType = array();
  private $_consultatieAoActivityType = array();
  private $_ziekteattestActivityType = array();
  private $_belAfspraakArtsActivityType = array();


    // properties for custom groups
  private $_klantBoekhoudingCustomGroup = array();
  private $_klantExpertsysteemCustomGroup = array();
  private $_klantOrganisatieCustomGroup = array();
  private $_klantProcedureCustomGroup = array();

  private $_leverancierCustomGroup = array();
  private $_voorwaardenArtsCustomGroup = array();
  private $_vakantiePeriodeCustomGroup = array();
  private $_communicatieCustomGroup = array();
  private $_werkgebiedCustomGroup = array();

  private $_klantMedewerkerExpertsysteemTellersCustomGroup = array();
  private $_klantMedewerkerMedewerkerCustomGroup = array();

  private $_voorwaardenControleCustomGroup = array();
  private $_voorwaardenMijnMediweCustomGroup = array();
  private $_voorwaardenZorgfondsCustomGroup = array();

  private $_ziektemeldingZiekteperiodeCustomGroup = array();
  private $_ziektemeldingZiekteAttestCustomGroup = array();
  private $_medischeControleCustomGroup = array();
  private $_medischeControleResultaatAoCustomGroup = array();
  private $_medischeControleResultaatCustomGroup = array();
  private $_medischeControleHuisbezoekCustomGroup = array();

  // properties for case types
  private $_ziektemeldingCaseType = array();
  private $_medischeControleCaseType = array();

  // properties for option groups
  private $_medischeControleSoortOptionGroup = array();
  private $_ziekteMeldingRedenKortOptionGroup = array();
  private $_ziekteMeldingRedenOptionGroup = array();
  private $_medischeControleCriteriumOptionGroup = array();
  private $_belMomentOptionGroup = array();
  private $_opdrachtWijzeOptionGroup = array();

  // properties voor migratie databases
  private $_joomlaDbName = NULL;
  private $_sourceCiviDbName = NULL;

  //mobiel telefoon type id
  private $_mobielPhoneTypeId = NULL;
  // contact id van het mediwe team
  private $_mediweTeamContactId = 1 ;


  /**
   * CRM_Basis_Config constructor.
   */
  function __construct() {


  $this->_joomlaDbName = "mediwe_joomla";
  $this->_sourceCiviDbName = "mediwe_old_civicrm";

    $this->setContactSubTypes();
    $this->setRelationshipTypes();
    $this->setActivityTypes();
    $this->setMembershipTypes();
    $this->setLocationTypes();
    $this->setOptionGroups();

    // set custom groups and custom fields voor controlearts/inspecteur
    $this->setCustomGroups('mediwe_communicatie_controlearts', '_communicatieCustomGroup');
    $this->setCustomGroups('mediwe_vakantie_periode', '_vakantiePeriodeCustomGroup');
    $this->setCustomGroups('mediwe_werkgebied', '_werkgebiedCustomGroup');
    $this->setCustomGroups('mediwe_leverancier', '_leverancierCustomGroup');
    $this->setCustomGroups('mediwe_voorwaarden_arts', '_voorwaardenArtsCustomGroup');

    // set custom groups and custom fields voor klanten
      $this->setCustomGroups('mediwe_facturatie', '_klantBoekhoudingCustomGroup');
      $this->setCustomGroups('mediwe_expertsysteem', '_klantExpertsysteemCustomGroup');
      $this->setCustomGroups('mediwe_interne_organisatie', '_klantOrganisatieCustomGroup');
      $this->setCustomGroups('mediwe_controle_procedure_klant', '_klantProcedureCustomGroup');

    $this->setCasesCustomGroups();
    $this->setMediweTeamContactId();
    $this->setCaseTypes();
      
    $this->_joomlaDbName = "mediwe_joomla";
    $this->_sourceCiviDbName = "mediwe_civicrm";
    try {
      $this->_mobielPhoneTypeId = civicrm_api3('OptionValue', 'getvalue', array(
        'option_group_id' => 'phone_type',
        'name' => 'Mobile',
        'return' => 'value',
      ));
    }
    catch (CiviCRM_API3_Exception $ex) {
    }
  }

  /**
   * Getter voor mobiel telefoon type id
   *
   * @return array|null
   */
  public function getMobielPhoneTypeId() {
    return $this->_mobielPhoneTypeId;
  }

  /**
   * Getter voor bel moment option group
   *
   * @param null $key
   * @return array|mixed
   */
  public function getBelMomentOptionGroup($key = NULL) {
    if (!empty($key) && isset($this->_belMomentOptionGroup[$key])) {
      return $this->_belMomentOptionGroup['id'];
    } else {
      return $this->_belMomentOptionGroup;
    }
  }

  /**
   * Getter voor opdracht wijze option group
   *
   * @param null $key
   * @return array|mixed
   */
  public function getOpdrachtWijzeOptionGroup($key = NULL) {
    if (!empty($key) && isset($this->_opdrachtWijzeOptionGroup[$key])) {
      return $this->_opdrachtWijzeOptionGroup['id'];
    } else {
      return $this->_opdrachtWijzeOptionGroup;
    }
  }

  /**
   * Getter for controle arts relationship type id
   *
   * @return null
   */
  public function getControleArtsRelationshipTypeId() {
    return $this->_controleArtsRelationshipTypeId;
  }

  /**
   * Getter for Joomla DB name
   *
   * @return null|string
   */
  public function getJoomlaDbName() {
    return $this->_joomlaDbName;
  }

  /**
   * Getter for Source CiviCRM DB name
   *
   * @return null|string
   */
  public function getSourceCiviDbName() {
    return $this->_sourceCiviDbName;
  }

  /**
   * Method to retrieve custom field from custom group
   */
  private function getCustomField($customGroup, $customFieldName) {
    if (!empty($customGroup) && !empty($customFieldName)) {
      foreach ($customGroup['custom_fields'] as $customFieldId => $customField) {
        if ($customField['name'] == $customFieldName) {
          return $customField;
        }
      }
    }
  }

  /**
   * Getter for venice custom field from boekhouding custom group
   *
   * @param null $key
   * @return mixed|array
   */
  public function getCustomerVeniceCustomField($key = NULL) {
    $customField = $this->getCustomField($this->_klantBoekhoudingCustomGroup, 'customer_venice');
    if (!empty($key) && isset($customField[$key])) {
      return $customField[$key];
    } else {
      return $customField;
    }
  }


    /**
     * Getter for CSV_bestand_bij_factuur custom field from boekhouding custom group
     *
     * @param null $key
     * @return mixed|array
     */
    public function getCustomerDetailCsvCustomField($key = NULL) {
        $customField = $this->getCustomField($this->_klantBoekhoudingCustomGroup, 'customer_detail_CSV');
        if (!empty($key) && isset($customField[$key])) {
            return $customField[$key];
        } else {
            return $customField;
        }
    }

    /**
     * Getter for Eigen_referentie custom field from boekhouding custom group
     *
     * @param null $key
     * @return mixed|array
     */
    public function getCustomerReferenceCustomField($key = NULL) {
        $customField = $this->getCustomField($this->_klantBoekhoudingCustomGroup, 'customer_reference');
        if (!empty($key) && isset($customField[$key])) {
            return $customField[$key];
        } else {
            return $customField;
        }
    }

    /**
     * Getter for account custom field from boekhouding custom group
     *
     * @param null $key
     * @return mixed|array
     */
    public function getCustomerAccountCustomField($key = NULL) {
        $customField = $this->getCustomField($this->_klantBoekhoudingCustomGroup, 'customer_account');
        if (!empty($key) && isset($customField[$key])) {
            return $customField[$key];
        } else {
            return $customField;
        }
    }

    /**
     * Getter for block_invoicing custom field from boekhouding custom group
     *
     * @param null $key
     * @return mixed|array
     */
    public function getCustomerBlockInvoicingCustomField($key = NULL) {
        $customField = $this->getCustomField($this->_klantBoekhoudingCustomGroup, 'customer_block_invoicing');
        if (!empty($key) && isset($customField[$key])) {
            return $customField[$key];
        } else {
            return $customField;
        }
    }

    /**
     * Getter for vat custom field from boekhouding custom group
     *
     * @param null $key
     * @return mixed|array
     */
    public function getCustomerVatCustomField($key = NULL) {
        $customField = $this->getCustomField($this->_klantBoekhoudingCustomGroup, 'customer_vat');
        if (!empty($key) && isset($customField[$key])) {
            return $customField[$key];
        } else {
            return $customField;
        }
    }
    
    /**
     * Getter for subject_to_vat custom field from boekhouding custom group
     *
     * @param null $key
     * @return mixed|array
     */
    public function getCustomerSubjectToVatCustomField($key = NULL) {
        $customField = $this->getCustomField($this->_klantBoekhoudingCustomGroup, 'customer_subject_to_vat');
        if (!empty($key) && isset($customField[$key])) {
            return $customField[$key];
        } else {
            return $customField;
        }
    }

    /**
     * Getter for gebruikt_app custom field from communicatie custom group
     *
     * @param null $key
     * @return mixed|array
     */
    public function getArtsGebruiktAppCustomField($key = NULL) {
        $customField = $this->getCustomField($this->_communicatieCustomGroup, 'mcc_arts_gebruikt_app');
        if (!empty($key) && isset($customField[$key])) {
            return $customField[$key];
        } else {
            return $customField;
        }
    }

    /**
     * Getter for belmoment custom field from communicatie custom group
     *
     * @param null $key
     * @return mixed|array
     */
    public function getArtsBelMomentCustomField($key = NULL) {
        $customField = $this->getCustomField($this->_communicatieCustomGroup, 'mcc_arts_bel_moment');
        if (!empty($key) && isset($customField[$key])) {
            return $customField[$key];
        } else {
            return $customField;
        }
    }

    /**
     * Getter for opdracht_per custom field from communicatie custom group
     *
     * @param null $key
     * @return mixed|array
     */
    public function getArtsOpdrachtPerCustomField($key = NULL) {
        $customField = $this->getCustomField($this->_communicatieCustomGroup, 'mcc_arts_opdracht');
        if (!empty($key) && isset($customField[$key])) {
            return $customField[$key];
        } else {
            return $customField;
        }
    }

    /**
     * Getter for overzicht custom field from communicatie custom group
     *
     * @param null $key
     * @return mixed|array
     */
    public function getArtsOverzichtCustomField($key = NULL) {
        $customField = $this->getCustomField($this->_communicatieCustomGroup, 'mcc_arts_overzicht');
        if (!empty($key) && isset($customField[$key])) {
            return $customField[$key];
        } else {
            return $customField;
        }
    }

    /**
     * Getter for opmerkingen custom field from communicatie custom group
     *
     * @param null $key
     * @return mixed|array
     */
    public function getArtsOpmerkingenCustomField($key = NULL) {
        $customField = $this->getCustomField($this->_communicatieCustomGroup, 'mcc_arts_opmerkingen');
        if (!empty($key) && isset($customField[$key])) {
            return $customField[$key];
        } else {
            return $customField;
        }
    }

  /**
   * Getter for arts percentage akkoord custom field
   *
   * @return mixed
   */
  public function getArtsPercentageAkkoordCustomField($key = NULL) {
    $customField = $this->getCustomField($this->_communicatieCustomGroup, 'mcc_arts_percentage_akkoord');
    if (!empty($key) && isset($customField[$key])) {
      return $customField[$key];
    } else {
      return $customField;
    }
  }

  /**
     * Getter for Periode custom field from expertsysteem custom group
     *
     * @param null $key
     * @return mixed|array
     */
    public function getExpertSysteemPeriodeCustomField($key = NULL) {
        $customField = $this->getCustomField($this->_klantExpertsysteemCustomGroup, 'mes_periode');
        if (!empty($key) && isset($customField[$key])) {
            return $customField[$key];
        } else {
            return $customField;
        }
    }

    /**
     * Getter for Populatie custom field from expertsysteem custom group
     *
     * @param null $key
     * @return mixed|array
     */
    public function getExpertSysteemPopulatieCustomField($key = NULL) {
        $customField = $this->getCustomField($this->_klantExpertsysteemCustomGroup, 'mes_populatie');
        if (!empty($key) && isset($customField[$key])) {
            return $customField[$key];
        } else {
            return $customField;
        }
    }

    /**
     * Getter for Actie custom field from expertsysteem custom group
     *
     * @param null $key
     * @return mixed|array
     */
    public function getExpertSystemActionCustomField($key = NULL) {
        $customField = $this->getCustomField($this->_klantExpertsysteemCustomGroup, 'expert_system_action');
        if (!empty($key) && isset($customField[$key])) {
            return $customField[$key];
        } else {
            return $customField;
        }
    }


    /**
     * Getter for Ziekte_op_maandag custom field from expertsysteem custom group
     *
     * @param null $key
     * @return mixed|array
     */
    public function getExpertSystemOnMondayCustomField($key = NULL) {
        $customField = $this->getCustomField($this->_klantExpertsysteemCustomGroup, 'expert_system_on_monday');
        if (!empty($key) && isset($customField[$key])) {
            return $customField[$key];
        } else {
            return $customField;
        }
    }

    /**
     * Getter for Ziektedagen custom field from expertsysteem custom group
     *
     * @param null $key
     * @return mixed|array
     */
    public function getExpertSystemDaysCustomField($key = NULL) {
        $customField = $this->getCustomField($this->_klantExpertsysteemCustomGroup, 'expert_system_days');
        if (!empty($key) && isset($customField[$key])) {
            return $customField[$key];
        } else {
            return $customField;
        }
    }

    /**
     * Getter for Bradford custom field from expertsysteem custom group
     *
     * @param null $key
     * @return mixed|array
     */
    public function getExpertSystemBradfordCustomField($key = NULL) {
        $customField = $this->getCustomField($this->_klantExpertsysteemCustomGroup, 'expert_system_bradford');
        if (!empty($key) && isset($customField[$key])) {
            return $customField[$key];
        } else {
            return $customField;
        }
    }

    /**
     * Getter for Ziekteperiodes_zonder_attest custom field from expertsysteem custom group
     *
     * @param null $key
     * @return mixed|array
     */
    public function getExpertSystemNoCertificateCustomField($key = NULL) {
        $customField = $this->getCustomField($this->_klantExpertsysteemCustomGroup, 'expert_system_no_certificate');
        if (!empty($key) && isset($customField[$key])) {
            return $customField[$key];
        } else {
            return $customField;
        }
    }

    /**
     * Getter for Random_frequentie custom field from expertsysteem custom group
     *
     * @param null $key
     * @return mixed|array
     */
    public function getExpertSystemRandomFrequencyCustomField($key = NULL) {
        $customField = $this->getCustomField($this->_klantExpertsysteemCustomGroup, 'expert_system_random_frequency');
        if (!empty($key) && isset($customField[$key])) {
            return $customField[$key];
        } else {
            return $customField;
        }
    }

    /**
     * Getter for Korte_ziekteperiodes custom field from expertsysteem custom group
     *
     * @param null $key
     * @return mixed|array
     */
    public function getExpertSystemShortPeriodsCustomField($key = NULL) {
        $customField = $this->getCustomField($this->_klantExpertsysteemCustomGroup, 'expert_system_short_periods');
        if (!empty($key) && isset($customField[$key])) {
            return $customField[$key];
        } else {
            return $customField;
        }
    }

    /**
     * Getter for Ziekteperiode_van custom field from expertsysteem custom group
     *
     * @param null $key
     * @return mixed|array
     */
    public function getExpertSystemPeriodFromCustomField($key = NULL) {
        $customField = $this->getCustomField($this->_klantExpertsysteemCustomGroup, 'expert_system_period_from');
        if (!empty($key) && isset($customField[$key])) {
            return $customField[$key];
        } else {
            return $customField;
        }
    }

    /**
     * Getter for Ziekteperiode_tot custom field from expertsysteem custom group
     *
     * @param null $key
     * @return mixed|array
     */
    public function getExpertSystemPeriodTillCustomField($key = NULL) {
        $customField = $this->getCustomField($this->_klantExpertsysteemCustomGroup, 'expert_system_period_till');
        if (!empty($key) && isset($customField[$key])) {
            return $customField[$key];
        } else {
            return $customField;
        }
    }

    /**
     * Getter for na_afgekeurde_ziekte custom field from expertsysteem custom group
     *
     * @param null $key
     * @return mixed|array
     */
    public function getExpertSystemAfterNegativeResultCustomField($key = NULL) {
        $customField = $this->getCustomField($this->_klantExpertsysteemCustomGroup, 'expert_system_after_negative_result');
        if (!empty($key) && isset($customField[$key])) {
            return $customField[$key];
        } else {
            return $customField;
        }
    }

    /**
     * Getter for na_verlenging_ziekte custom field from expertsysteem custom group
     *
     * @param null $key
     * @return mixed|array
     */
    public function getExpertSystemAfterExtensionCustomField($key = NULL) {
        $customField = $this->getCustomField($this->_klantExpertsysteemCustomGroup, 'expert_system_after_extension');
        if (!empty($key) && isset($customField[$key])) {
            return $customField[$key];
        } else {
            return $customField;
        }
    }

    /**
     * Getter for Periode custom field from expertsysteemtellers custom group
     *
     * @param null $key
     * @return mixed|array
     */
    public function getTellerPeriodeCustomField($key = NULL) {
        $customField = $this->getCustomField($this->_klantExpertsysteemCustomGroup, 'counter_period');
        if (!empty($key) && isset($customField[$key])) {
            return $customField[$key];
        } else {
            return $customField;
        }
    }

    /**
     * Getter for Ziekteperiodes custom field from expertsysteemtellers custom group
     *
     * @param null $key
     * @return mixed|array
     */
    public function getTellerZiekteperiodesCustomField($key = NULL) {
        $customField = $this->getCustomField($this->_klantExpertsysteemCustomGroup, 'periods_count');
        if (!empty($key) && isset($customField[$key])) {
            return $customField[$key];
        } else {
            return $customField;
        }
    }


    /**
     * Getter for rijksregisternummer custom field from medewerker custom group
     *
     * @param null $key
     * @return mixed|array
     */
    public function getMedewerkerRijksregisterNrCustomField($key = NULL) {
        $customField = $this->getCustomField($this->_klantMedewerkerMedewerkerCustomGroup, 'mkm_rijksregister_nummer');
        if (!empty($key) && isset($customField[$key])) {
            return $customField[$key];
        } else {
            return $customField;
        }
    }

    /**
     * Getter for personeelsnummer custom field from medewerker custom group
     *
     * @param null $key
     * @return mixed|array
     */
    public function getMedewerkerPersoneelsNummerCustomField($key = NULL) {
        $customField = $this->getCustomField($this->_klantMedewerkerMedewerkerCustomGroup, 'mkm_personeelsnummer');
        if (!empty($key) && isset($customField[$key])) {
            return $customField[$key];
        } else {
            return $customField;
        }
    }

    /**
     * Getter for mkm_partner custom field from medewerker custom group
     *
     * @param null $key
     * @return mixed|array
     */
    public function getMedewerkerPartnerCustomField($key = NULL) {
        $customField = $this->getCustomField($this->_klantMedewerkerMedewerkerCustomGroup, 'mkm_partner');
        if (!empty($key) && isset($customField[$key])) {
            return $customField[$key];
        } else {
            return $customField;
        }
    }

    /**
     * Getter for mkm_niveau1 custom field from medewerker custom group
     *
     * @param null $key
     * @return mixed|array
     */
    public function getMedewerkerNiveau1CustomField($key = NULL) {
        $customField = $this->getCustomField($this->_klantMedewerkerMedewerkerCustomGroup, 'mkm_niveau1');
        if (!empty($key) && isset($customField[$key])) {
            return $customField[$key];
        } else {
            return $customField;
        }
    }

    /**
     * Getter for employee_level2 custom field from medewerker custom group
     *
     * @param null $key
     * @return mixed|array
     */
    public function getMedewerkerNiveau2CustomField($key = NULL) {
        $customField = $this->getCustomField($this->_klantMedewerkerMedewerkerCustomGroup, 'mkm_niveau2');
        if (!empty($key) && isset($customField[$key])) {
            return $customField[$key];
        } else {
            return $customField;
        }
    }

    /**
     * Getter for mkm_niveau3 custom field from medewerker custom group
     *
     * @param null $key
     * @return mixed|array
     */
    public function getMedewerkerNiveau3CustomField($key = NULL) {
        $customField = $this->getCustomField($this->_klantMedewerkerMedewerkerCustomGroup, 'mkm_niveau3');
        if (!empty($key) && isset($customField[$key])) {
            return $customField[$key];
        } else {
            return $customField;
        }
    }

    /**
     * Getter for mkm_code_niveau2 custom field from medewerker custom group
     *
     * @param null $key
     * @return mixed|array
     */
    public function getMedewerkerCodeNiveau2CustomField($key = NULL) {
        $customField = $this->getCustomField($this->_klantMedewerkerMedewerkerCustomGroup, 'mkm_code_niveau2');
        if (!empty($key) && isset($customField[$key])) {
            return $customField[$key];
        } else {
            return $customField;
        }
    }

    /**
     * Getter for mkm_functie custom field from medewerker custom group
     *
     * @param null $key
     * @return mixed|array
     */
    public function getMedewerkerFunctieCustomField($key = NULL) {
        $customField = $this->getCustomField($this->_klantMedewerkerMedewerkerCustomGroup, 'mkm_functie');
        if (!empty($key) && isset($customField[$key])) {
            return $customField[$key];
        } else {
            return $customField;
        }
    }

    /**
     * Getter for mkm_statuut custom field from medewerker custom group
     *
     * @param null $key
     * @return mixed|array
     */
    public function getMedewerkerStatuutCustomField($key = NULL) {
        $customField = $this->getCustomField($this->_klantMedewerkerMedewerkerCustomGroup, 'mkm_statuut');
        if (!empty($key) && isset($customField[$key])) {
            return $customField[$key];
        } else {
            return $customField;
        }
    }

    /**
     * Getter for mkm_contract custom field from medewerker custom group
     *
     * @param null $key
     * @return mixed|array
     */
    public function getMedewerkerContractCustomField($key = NULL) {
        $customField = $this->getCustomField($this->_klantMedewerkerMedewerkerCustomGroup, 'mkm_contract');
        if (!empty($key) && isset($customField[$key])) {
            return $customField[$key];
        } else {
            return $customField;
        }
    }

    /**
     * Getter for mkm_contract_omschrijving custom field from medewerker custom group
     *
     * @param null $key
     * @return mixed|array
     */
    public function getMedewerkerContractOmschrijvingCustomField($key = NULL) {
        $customField = $this->getCustomField($this->_klantMedewerkerMedewerkerCustomGroup, 'mkm_contract_omschrijving');
        if (!empty($key) && isset($customField[$key])) {
            return $customField[$key];
        } else {
            return $customField;
        }
    }

    /**
     * Getter for mkm_ploegensysteem custom field from medewerker custom group
     *
     * @param null $key
     * @return mixed|array
     */
    public function getMedewerkerPloegenSysteemCustomField($key = NULL) {
        $customField = $this->getCustomField($this->_klantMedewerkerMedewerkerCustomGroup, 'mkm_ploegensysteem');
        if (!empty($key) && isset($customField[$key])) {
            return $customField[$key];
        } else {
            return $customField;
        }
    }

    /**
     * Getter for mkm_bezetting field from medewerker custom group
     *
     * @param null $key
     * @return mixed|array
     */
    public function getMedewerkerBezettingCustomField($key = NULL) {
        $customField = $this->getCustomField($this->_klantMedewerkerMedewerkerCustomGroup, 'mkm_bezetting');
        if (!empty($key) && isset($customField[$key])) {
            return $customField[$key];
        } else {
            return $customField;
        }
    }

    /**
     * Getter for mkm_kostenplaats custom field from medewerker custom group
     *
     * @param null $key
     * @return mixed|array
     */
    public function getMedewerkerKostenplaatsCustomField($key = NULL) {
        $customField = $this->getCustomField($this->_klantMedewerkerMedewerkerCustomGroup, 'mkm_kostenplaats');
        if (!empty($key) && isset($customField[$key])) {
            return $customField[$key];
        } else {
            return $customField;
        }
    }

    /**
     * Getter for mkm_datum_in_dienst custom field from medewerker custom group
     *
     * @param null $key
     * @return mixed|array
     */
    public function getMedewerkerDatumInDienstCustomField($key = NULL) {
        $customField = $this->getCustomField($this->_klantMedewerkerMedewerkerCustomGroup, 'mkm_datum_in_dienst');
        if (!empty($key) && isset($customField[$key])) {
            return $customField[$key];
        } else {
            return $customField;
        }
    }

    /**
     * Getter for mkm_datum_uit_dienst custom field from medewerker custom group
     *
     * @param null $key
     * @return mixed|array
     */
    public function getMedewerkerDatumUitDienstCustomField($key = NULL) {
        $customField = $this->getCustomField($this->_klantMedewerkerMedewerkerCustomGroup, 'mkm_datum_uit_dienst');
        if (!empty($key) && isset($customField[$key])) {
            return $customField[$key];
        } else {
            return $customField;
        }
    }

    /**
     * Getter for mkm_opmerkingen custom field from medewerker custom group
     *
     * @param null $key
     * @return mixed|array
     */
    public function getMedewerkerOpmerkingenCustomField($key = NULL) {
        $customField = $this->getCustomField($this->_klantMedewerkerMedewerkerCustomGroup, 'mkm_opmerkingen');
        if (!empty($key) && isset($customField[$key])) {
            return $customField[$key];
        } else {
            return $customField;
        }
    }

    /**
     * Getter for mkm_vrij_veld1 custom field from medewerker custom group
     *
     * @param null $key
     * @return mixed|array
     */
    public function getMedewerkerVrijVeld1CustomField($key = NULL) {
        $customField = $this->getCustomField($this->_klantMedewerkerMedewerkerCustomGroup, 'mkm_vrij_veld1');
        if (!empty($key) && isset($customField[$key])) {
            return $customField[$key];
        } else {
            return $customField;
        }
    }

    /**
     * Getter for mkm_is_controlevrij custom field from medewerker custom group
     *
     * @param null $key
     * @return mixed|array
     */
    public function getMedewerkerIsControlevrijCustomField($key = NULL) {
        $customField = $this->getCustomField($this->_klantMedewerkerMedewerkerCustomGroup, 'mkm_is_controlevrij');
        if (!empty($key) && isset($customField[$key])) {
            return $customField[$key];
        } else {
            return $customField;
        }
    }

    /**
     * Getter for mkm_controlevrij_tot custom field from medewerker custom group
     *
     * @param null $key
     * @return mixed|array
     */
    public function getMedewerkerIsControlevrijTotCustomField($key = NULL) {
        $customField = $this->getCustomField($this->_klantMedewerkerMedewerkerCustomGroup, 'mkm_controlevrij_tot');
        if (!empty($key) && isset($customField[$key])) {
            return $customField[$key];
        } else {
            return $customField;
        }
    }

    /**
     * Getter for mkm_steeds_aangewezen custom field from medewerker custom group
     *
     * @param null $key
     * @return mixed|array
     */
    public function getMedewerkerSteedsAangewezenCustomField($key = NULL) {
        $customField = $this->getCustomField($this->_klantMedewerkerMedewerkerCustomGroup, 'mkm_steeds_aangewezen');
        if (!empty($key) && isset($customField[$key])) {
            return $customField[$key];
        } else {
            return $customField;
        }
    }

    /**
     * Getter for mkm_aangewezen_tot custom field from medewerker custom group
     *
     * @param null $key
     * @return mixed|array
     */
    public function getMedewerkerAangewezenTotCustomField($key = NULL) {
        $customField = $this->getCustomField($this->_klantMedewerkerMedewerkerCustomGroup, 'mkm_aangewezen_tot');
        if (!empty($key) && isset($customField[$key])) {
            return $customField[$key];
        } else {
            return $customField;
        }
    }


    /**
     * Getter for Ziekte_op_maandag custom field from expertsysteemtellers custom group
     *
     * @param null $key
     * @return mixed|array
     */
    public function getTellerZiekteOpMaandagCustomField($key = NULL) {
        $customField = $this->getCustomField($this->_klantMedewerkerExpertsysteemTellersCustomGroup, 'mte_maandag_ziektes');
        if (!empty($key) && isset($customField[$key])) {
            return $customField[$key];
        } else {
            return $customField;
        }
    }

    /**
     * Getter for Ziektedagen custom field from expertsysteemtellers custom group
     *
     * @param null $key
     * @return mixed|array
     */
    public function getTellerZiektedagenCustomField($key = NULL) {
        $customField = $this->getCustomField($this->_klantExpertsysteemCustomGroup, 'days_count');
        if (!empty($key) && isset($customField[$key])) {
            return $customField[$key];
        } else {
            return $customField;
        }
    }

    /**
     * Getter for Bradford custom field from expertsysteemtellers custom group
     *
     * @param null $key
     * @return mixed|array
     */
    public function getTellerBradfordCustomField($key = NULL) {
        $customField = $this->getCustomField($this->_klantExpertsysteemCustomGroup, 'counters_bradford');
        if (!empty($key) && isset($customField[$key])) {
            return $customField[$key];
        } else {
            return $customField;
        }
    }

    /**
     * Getter for Ziekteperiodes_zonder_attest custom field from expertsysteemtellers custom group
     *
     * @param null $key
     * @return mixed|array
     */
    public function getTellerZiekteperiodesZonderAttestCustomField($key = NULL) {
        $customField = $this->getCustomField($this->_klantExpertsysteemCustomGroup, 'periods_no_certificate_count');
        if (!empty($key) && isset($customField[$key])) {
            return $customField[$key];
        } else {
            return $customField;
        }
    }



    /**
     * Getter for Korte_ziekteperiodes custom field from expertsysteemtellers custom group
     *
     * @param null $key
     * @return mixed|array
     */
    public function getTellerKorteZiekteperiodesCustomField($key = NULL) {
        $customField = $this->getCustomField($this->_klantExpertsysteemCustomGroup, 'short_periods_count');
        if (!empty($key) && isset($customField[$key])) {
            return $customField[$key];
        } else {
            return $customField;
        }
    }


    /**
     * Getter for Ziekteverzuim_percentage custom field from expertsysteemtellers custom group
     *
     * @param null $key
     * @return mixed|array
     */
    public function getTellerZiekteverzuimPercentageCustomField($key = NULL) {
        $customField = $this->getCustomField($this->_klantExpertsysteemCustomGroup, 'counters_percentage');
        if (!empty($key) && isset($customField[$key])) {
            return $customField[$key];
        } else {
            return $customField;
        }
    }


    /**
     * Getter for venice custom field from leverancier CustomGroup custom group
     *
     * @param null $key
     * @return mixed|array
     */
    public function getVeniceCustomField($key = NULL) {
        $customField = $this->getCustomField($this->_leverancierCustomGroup, 'ml_venice');
        if (!empty($key) && isset($customField[$key])) {
            return $customField[$key];
        } else {
            return $customField;
        }
    }

    /**
     * Getter for vat custom field from leverancierCustomGroup custom group
     *
     * @param null $key
     * @return mixed|array
     */
    public function getBtwCustomField($key = NULL) {
        $customField = $this->getCustomField($this->_leverancierCustomGroup, 'ml_btw_nummer');
        if (!empty($key) && isset($customField[$key])) {
            return $customField[$key];
        } else {
            return $customField;
        }
    }

    /**
     * Getter for subject_to_vat custom field from leverancierCustomGroup custom group
     *
     * @param null $key
     * @return mixed|array
     */
    public function getBtwPlichtigCustomField($key = NULL) {
        $customField = $this->getCustomField($this->_leverancierCustomGroup, 'ml_btw_plichtig');
        if (!empty($key) && isset($customField[$key])) {
            return $customField[$key];
        } else {
            return $customField;
        }
    }

    /**
     * Getter for account custom field from leverancierCustomGroup custom group
     *
     * @param null $key
     * @return mixed|array
     */
    public function getIbanCustomField($key = NULL) {
        $customField = $this->getCustomField($this->_leverancierCustomGroup, 'ml_iban');
        if (!empty($key) && isset($customField[$key])) {
            return $customField[$key];
        } else {
            return $customField;
        }
    }

    /**
     * Getter for Eigen_referentie custom field from leverancierCustomGroup custom group
     *
     * @param null $key
     * @return mixed|array
     */
    public function getBestelNummerCustomField($key = NULL) {
        $customField = $this->getCustomField($this->_leverancierCustomGroup, 'ml_bestelnummer');
        if (!empty($key) && isset($customField[$key])) {
            return $customField[$key];
        } else {
            return $customField;
        }
    }

    /**
     * Getter for CSV_bestand_bij_factuur custom field from leverancierCustomGroup custom group
     *
     * @param null $key
     * @return mixed|array
     */
    public function getCsvBestandBijFactuurCustomField($key = NULL) {
        $customField = $this->getCustomField($this->_leverancierCustomGroup, 'ml_csv_toevoegen');
        if (!empty($key) && isset($customField[$key])) {
            return $customField[$key];
        } else {
            return $customField;
        }
    }

    /**
     * Getter for level1 custom field from klantOrganisatie custom group
     *
     * @param null $key
     * @return mixed|array
     */
    public function getOrgLevel1CustomField($key = NULL) {
        $customField = $this->getCustomField($this->_klantOrganisatieCustomGroup, 'mio_niveau1');
        if (!empty($key) && isset($customField[$key])) {
            return $customField[$key];
        } else {
            return $customField;
        }
    }

    /**
     * Getter for level2 custom field from klantOrganisatie custom group
     *
     * @param null $key
     * @return mixed|array
     */
    public function getOrgLevel2CustomField($key = NULL) {
        $customField = $this->getCustomField($this->_klantOrganisatieCustomGroup, 'mio_niveau2');
        if (!empty($key) && isset($customField[$key])) {
            return $customField[$key];
        } else {
            return $customField;
        }
    }

    /**
     * Getter for level3 custom field from klantOrganisatie custom group
     *
     * @param null $key
     * @return mixed|array
     */
    public function getOrgLevel3CustomField($key = NULL) {
        $customField = $this->getCustomField($this->_klantOrganisatieCustomGroup, 'mio_niveau3');
        if (!empty($key) && isset($customField[$key])) {
            return $customField[$key];
        } else {
            return $customField;
        }
    }

    /**
     * Getter for email_goedkeuring_controle custom field from klantOrganisatie custom group
     *
     * @param null $key
     * @return mixed|array
     */
    public function getOrgEmailGoedkeuringCustomField($key = NULL) {
        $customField = $this->getCustomField($this->_klantOrganisatieCustomGroup, 'mio_email_goedkeuring');
        if (!empty($key) && isset($customField[$key])) {
            return $customField[$key];
        } else {
            return $customField;
        }
    }

    /**
     * Getter for aanspreking_goedkeuring_controle custom field from klantOrganisatie custom group
     *
     * @param null $key
     * @return mixed|array
     */
    public function getOrgAansprekingGoedkeuringCustomField($key = NULL) {
        $customField = $this->getCustomField($this->_klantOrganisatieCustomGroup, 'mio_aanspreking_goedkeuring');
        if (!empty($key) && isset($customField[$key])) {
            return $customField[$key];
        } else {
            return $customField;
        }
    }

    /**
     * Getter for korte_ziekteperiode custom field from klantOrganisatie custom group
     *
     * @param null $key
     * @return mixed|array
     */
    public function getOrgMaxKortCustomField($key = NULL) {
        $customField = $this->getCustomField($this->_klantOrganisatieCustomGroup, 'mio_max_dagen_kort');
        if (!empty($key) && isset($customField[$key])) {
            return $customField[$key];
        } else {
            return $customField;
        }
    }

    /**
     * Getter for maanden_nieuwe_medewerker custom field from klantOrganisatie custom group
     *
     * @param null $key
     * @return mixed|array
     */
    public function getOrgMaxMaandenNieuwCustomField($key = NULL) {
        $customField = $this->getCustomField($this->_klantOrganisatieCustomGroup, 'mio_max_maanden_nieuw');
        if (!empty($key) && isset($customField[$key])) {
            return $customField[$key];
        } else {
            return $customField;
        }
    }

    /**
     * Getter for remarks custom field from klantProcedure custom group
     *
     * @param null $key
     * @return mixed|array
     */
    public function getKlantProcedureOpmerkingenCustomField($key = NULL) {
        $customField = $this->getCustomField($this->_klantProcedureCustomGroup, 'mcpk_opmerkingen');
        if (!empty($key) && isset($customField[$key])) {
            return $customField[$key];
        } else {
            return $customField;
        }
    }


    /**
     * Getter for free custom field from klantProcedure custom group
     *
     * @param null $key
     * @return mixed|array
     */
    public function getKlantProcedureVrijVeldCustomField($key = NULL) {
        $customField = $this->getCustomField($this->_klantProcedureCustomGroup, 'mcpk_vrij_veld');
        if (!empty($key) && isset($customField[$key])) {
            return $customField[$key];
        } else {
            return $customField;
        }
    }

    /**
     * Getter for sector custom field from klantProcedure custom group
     *
     * @param null $key
     * @return mixed|array
     */
    public function getKlantProcedureSectorCustomField($key = NULL) {
        $customField = $this->getCustomField($this->_klantProcedureCustomGroup, 'mcpk_sector_omschrijving');
        if (!empty($key) && isset($customField[$key])) {
            return $customField[$key];
        } else {
            return $customField;
        }
    }

    /**
     * Getter for id_sector custom field from klantProcedure custom group
     *
     * @param null $key
     * @return mixed|array
     */
    public function getKlantProcedureSectorIdCustomField($key = NULL) {
        $customField = $this->getCustomField($this->_klantProcedureCustomGroup, 'mcpk_sector_id');
        if (!empty($key) && isset($customField[$key])) {
            return $customField[$key];
        } else {
            return $customField;
        }
    }

    /**
     * Getter for fte_calculation_type custom field from klantProcedure custom group
     *
     * @param null $key
     * @return mixed|array
     */
    public function getKlantProcedureFteBerekeningswijzeCustomField($key = NULL) {
        $customField = $this->getCustomField($this->_klantProcedureCustomGroup, 'mcpk_fte_berekeningswijze');
        if (!empty($key) && isset($customField[$key])) {
            return $customField[$key];
        } else {
            return $customField;
        }
    }

    /**
     * Getter for cao_noexit_from custom field from klantProcedure custom group
     *
     * @param null $key
     * @return mixed|array
     */
    public function getKlantProcedureHuisNietVerlatenVanCustomField($key = NULL) {
        $customField = $this->getCustomField($this->_klantProcedureCustomGroup, 'mcpk_huis_niet_verlaten_van');
        if (!empty($key) && isset($customField[$key])) {
            return $customField[$key];
        } else {
            return $customField;
        }
    }

    /**
     * Getter for cao_noexit_till custom field from klantProcedure custom group
     *
     * @param null $key
     * @return mixed|array
     */
    public function getKlantProcedureHuisNietVerlatenTotCustomField($key = NULL) {
        $customField = $this->getCustomField($this->_klantProcedureCustomGroup, 'mcpk_huis_niet_verlaten_tot');
        if (!empty($key) && isset($customField[$key])) {
            return $customField[$key];
        } else {
            return $customField;
        }
    }

    /**
     * Getter for procedure custom field from klantProcedure custom group
     *
     * @param null $key
     * @return mixed|array
     */
    public function getKlantProcedureCustomField($key = NULL) {
        $customField = $this->getCustomField($this->_klantProcedureCustomGroup, 'mcpk_controle_procedure');
        if (!empty($key) && isset($customField[$key])) {
            return $customField[$key];
        } else {
            return $customField;
        }
    }

    /**
     * Getter for vision custom field from klantProcedure custom group
     *
     * @param null $key
     * @return mixed|array
     */
    public function getKlantProcedureVisieCustomField($key = NULL) {
        $customField = $this->getCustomField($this->_klantProcedureCustomGroup, 'mcpk_verzuimbeleid_visie');
        if (!empty($key) && isset($customField[$key])) {
            return $customField[$key];
        } else {
            return $customField;
        }
    }

    /**
     * Getter for control_goal custom field from klantProcedure custom group
     *
     * @param null $key
     * @return mixed|array
     */
    public function getKlantCustomerProcedureDoelCustomField($key = NULL) {
        $customField = $this->getCustomField($this->_klantProcedureCustomGroup, 'mcpk_doel_controle');
        if (!empty($key) && isset($customField[$key])) {
            return $customField[$key];
        } else {
            return $customField;
        }
    }

    /**
     * Getter for sms_klantmedewerker custom field from klantProcedure custom group
     *
     * @param null $key
     * @return mixed|array
     */
    public function getKlantProcedureSmsMedwerkerCustomField($key = NULL) {
        $customField = $this->getCustomField($this->_klantProcedureCustomGroup, 'mcpk_sms_medewerker');
        if (!empty($key) && isset($customField[$key])) {
            return $customField[$key];
        } else {
            return $customField;
        }
    }

    /**
     * Getter for vakantie_van custom field from vakantieperiode custom group
     *
     * @param null $key
     * @return mixed|array
     */
    public function getVakantieVanCustomField($key = NULL) {
        $customField = $this->getCustomField($this->_vakantiePeriodeCustomGroup, 'mvp_vakantie_van');
        if (!empty($key) && isset($customField[$key])) {
            return $customField[$key];
        } else {
            return $customField;
        }
    }

    /**
     * Getter for vakantie_tot custom field from vakantieperiode custom group
     *
     * @param null $key
     * @return mixed|array
     */
    public function getVakantieTotCustomField($key = NULL) {
        $customField = $this->getCustomField($this->_vakantiePeriodeCustomGroup, 'mvp_vakantie_tot');
        if (!empty($key) && isset($customField[$key])) {
            return $customField[$key];
        } else {
            return $customField;
        }
    }

    /**
     * Getter for postcode custom field from werkgebied custom group
     *
     * @param null $key
     * @return mixed|array
     */
    public function getPostcodeCustomField($key = NULL) {
        $customField = $this->getCustomField($this->_werkgebiedCustomGroup, 'mw_postcode');
        if (!empty($key) && isset($customField[$key])) {
            return $customField[$key];
        } else {
            return $customField;
        }
    }

    /**
     * Getter for gemeente custom field from werkgebied custom group
     *
     * @param null $key
     * @return mixed|array
     */
    public function getGemeenteCustomField($key = NULL) {
        $customField = $this->getCustomField($this->_werkgebiedCustomGroup, 'mw_gemeente');
        if (!empty($key) && isset($customField[$key])) {
            return $customField[$key];
        } else {
            return $customField;
        }
    }

    /**
     * Getter for prioriteit custom field from werkgebied custom group
     *
     * @param null $key
     * @return mixed|array
     */
    public function getPrioriteitCustomField($key = NULL) {
        $customField = $this->getCustomField($this->_werkgebiedCustomGroup, 'mw_prioriteit');
        if (!empty($key) && isset($customField[$key])) {
            return $customField[$key];
        } else {
            return $customField;
        }
    }


    /**
     * Getter for vakantie periode custom group
     *
     * @param string $key
     * @return mixed|array
     */
    public function getVakantieperiodeCustomGroup($key = NULL) {
        if (!empty($key) && isset($this->_vakantiePeriodeCustomGroup[$key])) {
            return $this->_vakantiePeriodeCustomGroup[$key];
        } else {
            return $this->_vakantiePeriodeCustomGroup;
        }
    }

    /**
   * Getter for leverancier custom group
   *
   * @param string $key
   * @return mixed|array
   */
  public function getLeverancierCustomGroup($key = NULL) {
    if (!empty($key) && isset($this->_leverancierCustomGroup[$key])) {
      return $this->_leverancierCustomGroup[$key];
    } else {
      return $this->_leverancierCustomGroup;
}
}

    /**
     * Getter for werkgebied custom group
     *
     * @param string $key
     * @return mixed|array
     */
    public function getWerkgebiedCustomGroup($key = NULL) {
        if (!empty($key) && isset($this->_werkgebiedCustomGroup[$key])) {
            return $this->_werkgebiedCustomGroup[$key];
        } else {
            return $this->_werkgebiedCustomGroup;
        }
    }

    /**
     * Getter for communicatie custom group
     *
     * @param string $key
     * @return mixed|array
     */
    public function getCommunicatieCustomGroup($key = NULL) {
        if (!empty($key) && isset($this->_communicatieCustomGroup[$key])) {
            return $this->_communicatieCustomGroup[$key];
        } else {
            return $this->_communicatieCustomGroup;
        }
    }
    
  /**
   * Getter for klant boekhouding custom group
   *
   * @param string $key
   * @return mixed|array
   */
  public function getKlantBoekhoudingCustomGroup($key = NULL) {
    if (!empty($key) && isset($this->_klantBoekhoudingCustomGroup[$key])) {
      return $this->_klantBoekhoudingCustomGroup[$key];
    } else {
      return $this->_klantBoekhoudingCustomGroup;
    }
  }

  /**
   * Getter for klant expert systeem custom group
   *
   * @param string $key
   * @return mixed|array
   */
  public function getKlantExpertsysteemCustomGroup($key = NULL) {
    if (!empty($key) && isset($this->_klantExpertsysteemCustomGroup[$key])) {
      return $this->_klantExpertsysteemCustomGroup[$key];
    } else {
      return $this->_klantExpertsysteemCustomGroup;
    }
  }

  /**
   * Getter for klant organisatie custom group
   *
   * @param string $key
   * @return mixed|array
   */
  public function getKlantOrganisatieCustomGroup($key = NULL) {
    if (!empty($key) && isset($this->_klantExpertsysteemCustomGroup[$key])) {
      return $this->_klantOrganisatieCustomGroup[$key];
    } else {
      return $this->_klantOrganisatieCustomGroup;
    }
  }

  /**
   * Getter for klant procedure custom group
   *
   * @param string $key
   * @return mixed|array
   */
  public function getKlantProcedureCustomGroup($key = NULL) {

    if (!empty($key) && isset($this->_klantProcedureCustomGroup[$key])) {
      return $this->_klantProcedureCustomGroup[$key];
    } else {
      return $this->_klantProcedureCustomGroup;
    }
  }


    /**
     * Getter for klantmedewerker experst systeem tellers custom group
     *
     * @param string $key
     * @return mixed|array
     */
    public function getKlantMedewerkerExpertsysteemTellersCustomGroup($key = NULL) {
        if (!empty($key) && isset($this->_klantMedewerkerExpertsysteemTellersCustomGroup[$key])) {
            return $this->_klantMedewerkerExpertsysteemTellersCustomGroup[$key];
        } else {
            return $this->_klantMedewerkerExpertsysteemTellersCustomGroup;
        }
    }

    /**
     * Getter for klantmedewerker medewerker custom group
     *
     * @param string $key
     * @return mixed|array
     */
    public function getKlantMedewerkerMedewerkerCustomGroup($key = NULL) {
        if (!empty($key) && isset($this->_klantMedewerkerMedewerkerCustomGroup[$key])) {
            return $this->_klantMedewerkerMedewerkerCustomGroup[$key];
        } else {
            return $this->_klantMedewerkerMedewerkerCustomGroup;
        }
    }

    /**
     * Getter for ziekteperiode custom group
     *
     * @param string $key
     * @return mixed|array
     */
    public function getZiektemeldingZiekteperiodeCustomGroup($key = NULL) {
        if (!empty($key) && isset($this->_ziektemeldingZiekteperiodeCustomGroup[$key])) {
            return $this->_ziektemeldingZiekteperiodeCustomGroup[$key];
        } else {
            return $this->_ziektemeldingZiekteperiodeCustomGroup;
        }
    }

    /**
     * Getter for medische controle custom group
     *
     * @param string $key
     * @return mixed|array
     */
    public function getMedischeControleCustomGroup($key = NULL) {
        if (!empty($key) && isset($this->_medischeControleCustomGroup[$key])) {
            return $this->_medischeControleCustomGroup[$key];
        } else {
            return $this->_medischeControleCustomGroup;
        }
    }

    /**
     * Getter for medische controle huisbezoek custom group
     *
     * @param string $key
     * @return mixed|array
     */
    public function getMedischeControleHuisbezoekCustomGroup($key = NULL) {
        if (!empty($key) && isset($this->_medischeControleHuisbezoekCustomGroup[$key])) {
            return $this->_medischeControleHuisbezoekCustomGroup[$key];
        } else {
            return $this->_medischeControleHuisbezoekCustomGroup;
        }
    }

    /**
     * Getter for medische controle resultaat custom group
     *
     * @param string $key
     * @return mixed|array
     */
    public function getMedischeControleResultaatCustomGroup($key = NULL) {
        if (!empty($key) && isset($this->_medischeControleResultaatCustomGroup[$key])) {
            return $this->_medischeControleResultaatCustomGroup[$key];
        } else {
            return $this->_medischeControleResultaatCustomGroup;
        }
    }

    /**
     * Getter for medische controle resultaat custom group
     *
     * @param string $key
     * @return mixed|array
     */
    public function getMedischeControleResultaatAoCustomGroup($key = NULL) {
        if (!empty($key) && isset($this->_medischeControleResultaatAoCustomGroup[$key])) {
            return $this->_medischeControleResultaatAoCustomGroup[$key];
        } else {
            return $this->_medischeControleResultaatAoCustomGroup;
        }
    }

    /**
     * Getter for klant location type
     *
     * @return null
     */
    public function getklantLocationType() {
        return $this->_klantLocationTypes[0];
    }

    public function getKlantMedewerkerDomicilieLocationType() {
        return $this->_klantMedewerkerLocationTypes['domicilie'];
    }

    public function getKlantMedewerkerVerblijfLocationType() {
        return $this->_klantMedewerkerLocationTypes['verblijf'];
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
     * Getter for inspecteur contact sub type
     *
     * @return null
     */
    public function getInspecteurContactSubType() {
        return $this->_inspecteurContactSubType;
    }

    /**
     * Getter for is klant via relationship type
     *
     * @return null
     */
   public function getIsKlantViaRelationshipType() {
        return $this->_isKlantViaRelationshipType;
   }

    /**
     * Getter for is werknemer van relationship type
     *
     * @return null
     */
    public function getIsWerknemerVanRelationshipType() {
        return $this->_isWerknemerVanRelationshipType;
    }

    /**
     * Getter for ziektemelding relationship type
     *
     * @return null
     */
    public function getZiektemeldingRelationshipType() {
        return $this->_ziektemeldingRelationshipType;
    }

    /**
     * Getter for vraagt controle aan relationship type
     *
     * @return null
     */
    public function getVraagtControleAanRelationshipType() {
        return $this->_vraagtControleAanRelationshipType;
    }


    /**
     * Getter for ziekte attest activity type
     *
     * @return null
     */
    public function getZiekteattestActivityType() {
        return $this->_ziekteattestActivityType;
    }

    /**
     * Getter for consultatie AO activity type
     *
     * @return null
     */
    public function getConsultatieAoActivityType() {
        return $this->_consultatieAoActivityType;
    }

    /**
     * Getter for huisbezoek activity type
     *
     * @return null
     */
    public function getHuisbezoekActivityType() {
        return $this->_huisbezoekActivityType;
    }

    /**
     * Getter for consultatie activity type
     *
     * @return null
     */
    public function getConsultatieActivityType() {
        return $this->_consultatieAoActivityType;
    }

    /**
     *  Getter voor de belafspraak Arts activiteit
     *
     * @return array
     */
    public function getBelAfspraakArtsActivityType()
    {
        return $this->_belAfspraakArtsActivityType;
    }


    /**
     * Getter for ziektemelding  case type
     *
     * @return null
     */
    public function getZiektemeldingCaseType() {
        return $this->_ziektemeldingCaseType;
    }

    /**
     * Getter for medische controle case type
     *
     * @return null
     */
    public function getmedischeControleCaseType() {
        return $this->_medischeControleCaseType;
    }

    /**
     * Getter for maandelijks membership type
     *
     * @return null
     */
    public function getMaandelijksMembershipType() {
        return $this->_maandelijksMembershipType;
    }

    /**
     * Getter for voorafbetaald membership type
     *
     * @return null
     */
    public function getVoorafbetaaldMembershipType() {
        return $this->_voorafbetaaldMembershipType;
    }

    /**
     * Getter for mijn mediwe membership type
     *
     * @return null
     */
    public function getMijnMediweMembershipType() {
        return $this->_mijnMediweMembershipType;
    }

    /**
     * Getter for controlearts membership type
     *
     * @return null
     */
    public function getControleartsMembershipType() {
        return $this->_controleartsMembershipType;
    }

    /**
     * Getter for zorgfonds membership type
     *
     * @return null
     */
    public function getZorgfondsMembershipType() {
        return $this->_zorgfondsMembershipType;
    }

    /**
     * Getter for inspecteur membership type
     *
     * @return null
     */
    public function getInspecteurMembershipType() {
        return $this->_inspecteurMembershipType;
    }


    public function getOptions($optionGroup) {

        $list = array();

       foreach($optionGroup['option_values'] as $option) {
            $key = $option['value'];
            $value = $option['label'];
            $list[$key] = $value;
        }

        return $list;
    }

    /**
     * Method to set the relevant klant location type properties
     */
    private function setLocationTypes() {
        try {
            $locationTypes = civicrm_api3('LocationType','get', array(
                'options' => array('limit' => 0)));

            foreach ($locationTypes['values'] as $locationTypeId => $locationType) {
                switch ($locationType['name']) {
                    case 'Billing':
                        $this->_klantLocationTypes[] = $locationType;
                        break;
                    case 'Thuis':
                        $this->_klantMedewerkerLocationTypes['domicilie'] = $locationTypeId;
                        break;
                    case 'Andere':
                        $this->_klantMedewerkerLocationTypes['verblijf'] = $locationTypeId;
                        break;
                }
            }
        }
        catch (CiviCRM_API3_Exception $ex) {
        }
    }


    /**
   * Method to set the relevant contact sub type properties
   */
  private function setContactSubTypes() {
    try {
      $contactTypes = civicrm_api3('ContactType','get', array(
        'options' => array('limit' => 0)));
      foreach ($contactTypes['values'] as $contactTypeId => $contactType) {
        switch ($contactType['name']) {
          case 'mediwe_klant':
            $this->_klantContactSubType = $contactType;
            break;
          case 'mediwe_klant_medewerker':
            $this->_klantMedewerkerContactSubType = $contactType;
            break;
          case 'mediwe_controle_arts':
            $this->_controleArtsContactSubType = $contactType;
            break;
            case 'mediwe_inspecteur':
                $this->_inspecteurContactSubType = $contactType;
                break;
        }
      }
    }
    catch (CiviCRM_API3_Exception $ex) {
    }
  }


    /**
     * Method to set the relevant contact sub type properties
     */
    private function setCaseTypes() {
        try {
            $caseTypes = civicrm_api3('CaseType','get', array(
                'options' => array('limit' => 0)));
            foreach ($caseTypes['values'] as $caseTypeId => $caseType) {
                switch ($caseType['name']) {
                    case 'dossier_ziektemelding':
                        $this->_ziektemeldingCaseType = $caseType;
                        break;
                    case 'dossier_medische_controle':
                        $this->_medischeControleCaseType = $caseType;
                        break;
                }
            }
        }
        catch (CiviCRM_API3_Exception $ex) {
        }
    }


    /**
     * Method to set the relevant relationship type properties
     */
    private function setRelationshipTypes() {
        try {
            $relationshipTypes = civicrm_api3('RelationshipType','get', array(
                'options' => array('limit' => 0)));
            foreach ($relationshipTypes['values'] as $relationshipTypeId => $relationshipType) {
                switch ($relationshipType['name_a_b']) {
                    case 'is_klant_via':
                        $this->_isKlantViaRelationshipTypeId = $relationshipType['id'];
                        break;
                    case 'Employee of':
                        $this->_isWerknemerVanRelationshipTypeId = $relationshipType['id'];
                        break;
                    case 'ziektemelding':
                        $this->_ziektemeldingRelationshipTypeId = $relationshipType['id'];
                        break;
                    case 'vraagt_controle_aan':
                        $this->_vraagtControleAanRelationshipTypeId = $relationshipType['id'];
                        break;
                  case 'controlearts':
                    $this->_controleArtsRelationshipTypeId = $relationshipType['id'];
                    break;
                }
            }
        }
        catch (CiviCRM_API3_Exception $ex) {
        }
    }

    /**
     * Method to set the relevant relationship type properties
     */
    private function setActivityTypes() {
        try {
            $activityTypes = civicrm_api3('OptionValue', 'get', array(
                'option_group_id' => "activity_type",
                'options' => array('limit' => 0)));
            foreach ($activityTypes['values'] as $activityTypeId => $activityType) {
                switch ($activityType['name']) {
                    case "mediwe_ziekteattest":
                        $this->_ziekteattestActivityType = $activityType;
                        break;
                    case "mediwe_huisbezoek":
                        $this->_huisbezoekActivityType = $activityType;
                        break;
                    case "mediwe_convocatie":
                        $this->_consultatieActivityType = $activityType;
                        break;
                    case "mediwe_onderzoek_ao":
                        $this->_consultatieAoActivityType = $activityType;
                        break;
                    case "mediwe_belafspraak_arts":
                        $this->_belAfspraakArtsActivityType = $activityType;
                        break;
                }
            }
        }
        catch (CiviCRM_API3_Exception $ex) {
        }
    }
    
    
    /**
     * Method to set the relevant membership type properties
     */
    private function setMembershipTypes() {
        try {
            $relaionshipTypes = civicrm_api3('MembershipType','get', array(
                'options' => array('limit' => 0)));
            foreach ($relaionshipTypes['values'] as $membershipTypeId => $membershipType) {
                switch ($membershipType['name']) {
                    case 'Maandelijks':
                        $this->_maandelijksMembershipType = $membershipType;
                        break;
                    case 'Voorafbetaald':
                        $this->_voorafbetaaldMembershipType = $membershipType;
                        break;
                    case 'Mijn Mediwe':
                        $this->_mijnMediweMembershipType = $membershipType;
                        break;
                    case 'Controlearts':
                        $this->_controleartsMembershipType = $membershipType;
                        break;
                    case 'Zorgfonds':
                        $this->_zorgfondsMembershipType = $membershipType;
                        break;
                    case 'Inspecteur':
                        $this->_inspecteurMembershipType = $membershipType;
                        break; 
                }
            }
        }
        catch (CiviCRM_API3_Exception $ex) {
        }
    }  

    /**
     * Method to set the custom groups and all of its custom fields
     */
    private function setCustomGroups($customGroupName, $propertyName) {
      try {
        $customGroup = civicrm_api3('CustomGroup', 'getsingle', array(
          'name' => $customGroupName,
        ));
        $customFields = civicrm_api3('CustomField', 'get', array(
          'custom_group_id' => $customGroupName,
          'options' => array('limit' => 0),
        ));
        $customGroup['custom_fields'] = $customFields['values'];
        $this->$propertyName = $customGroup;
      }
      catch (CiviCRM_API3_Exception $ex) {
        CRM_Core_Error::createError('Unable to set the property '.$propertyName.' for custom group with name '
          .$customGroupName. ' in '.__METHOD__.', extension be.mediwe.basis will not function properly. Contact your system administrator');
      }
    }


    /**
     * Method to set the  option groups and option fields
     */
    private function setOptionGroups() {
        try {
            $optionGroups = civicrm_api3('OptionGroup','get', array(
                'options' => array('limit' => 0)));
            foreach ($optionGroups['values'] as $optionGroupId => $optionGroup) {
                switch ($optionGroup['name']) {
                    case 'mediwe_control_type':
                        $this->_medischeControleSoortOptionGroup = $optionGroup;
                        break;
                    case 'reason_illness_short':
                        $this->_ziekteMeldingRedenKortOptionGroup = $optionGroup;
                        break;
                    case 'reason_illness':
                        $this->_ziekteMeldingRedenOptionGroup = $optionGroup;
                        break;
                    case 'mediwe_control_criterium':
                        $this->_medischeControleCriteriumOptionGroup = $optionGroup;
                        break;
                  case 'mediwe_bellen_moment':
                    $this->_belMomentOptionGroup = $optionGroup;
                    break;
                  case 'mediwe_opdracht_wijze':
                    $this->_opdrachtWijzeOptionGroup = $optionGroup;
                    break;
                }
            }
        }
        catch (Exception $e) {

        }
    }

    /**
     * Method to get the soort controle option group and  option values
     */
    public function getMedischeControleSoortOptionGroup($key = NULL) {

        if (!empty($key) && isset($this->_medischeControleSoortOptionGroup[$key])) {
            return $this->_medischeControleSoortOptionGroup[$key];
        } else {
            return $this->_medischeControleSoortOptionGroup;
        }
    }

    /**
     * Method to get the criterium controle option group and  option values
     */
    public function getMedischeControleCriteriumOptionGroup($key = NULL) {

        if (!empty($key) && isset($this->_medischeControleCriteriumOptionGroup[$key])) {
                return $this->_medischeControleCriteriumOptionGroup[$key];
        } else {
            return $this->_medischeControleCriteriumOptionGroup;
        }
    }   
    
    /**
     * Method to get the reden ziekte (kort) option group and  option values
     */
    public function getZiekteMeldingRedenKortOptionGroup($key = NULL) {

        if (!empty($key) && isset($this->_ziekteMeldingRedenKortOptionGroup[$key])) {
                return $this->_ziekteMeldingRedenKortOptionGroup[$key];
        } else {
            return $this->_ziekteMeldingRedenKortOptionGroup;
        }
    }

    /**
     * Method to get the reden ziekte option group and  option values
     */
    public function getZiekteMeldingRedenOptionGroup($key = NULL) {
        if (!empty($key) && isset($this->_ziekteMeldingRedenOptionGroup[$key])) {
                return $this->_ziekteMeldingRedenOptionGroup[$key];
        } else {
            return $this->_ziekteMeldingRedenOptionGroup;
        }
    }

    /**
     * Method to set the inspecteur custom groups and custom fields
     */
    private function setCasesCustomGroups() {
        try {
            $customGroups = civicrm_api3('CustomGroup','get', array(
                'options' => array('limit' => 0)));
            foreach ($customGroups['values'] as $customGroupId => $customGroup) {
                $customFields = civicrm_api3('CustomField', 'get', array(
                    'custom_group_id' => $customGroupId,
                    'options' => array('limit' => 0)));
                $customGroup['custom_fields'] = $customFields['values'];
                switch ($customGroup['name']) {
                    case 'mediwe_illness':
                        $this->_ziektemeldingZiekteperiodeCustomGroup = $customGroup;
                        break;
                    case 'mediwe_medisch_attest':
                        $this->_ziektemeldingZiekteAttestCustomGroup = $customGroup;
                        break;
                    case 'mediwe_medische_controle':
                        $this->_medischeControleCustomGroup = $customGroup;
                        break;
                    case 'mediwe_huisbezoek':
                        $this->_medischeControleHuisbezoekCustomGroup = $customGroup;
                        break;
                    case 'mediwe_controle_ao_resultaat':
                        $this->_medischeControleResultaatAoCustomGroup = $customGroup;
                        break;
                    case 'mediwe_controle_resultaat':
                        $this->_medischeControleResultaatCustomGroup = $customGroup;
                        break;
                }
            }
        }
        catch (CiviCRM_API3_Exception $ex) {
        }
    }

    /**
     * Method plece the MediWe team id in the custom Config
     *
     */

    public function setMediweTeamContactId()
    {
        try {
            $this->_mediweTeamContactId = civicrm_api3('Domain', 'getvalue', array(
                'return' => "contact_id",
                'id' => 1,
            ));;
        } catch (CiviCRM_API3_Exception $ex) {
            $this->_mediweTeamContactId = 1;
        }
    }
    
    /**
     * Method to place the custom fields in the entity array based on the
     *
     * @param object $customGroup
     * @param array $entity;
     * @return array
     */
    public function addDaoData($customGroup, $entity) {
        $table_name = $customGroup['table_name'];

        $fields = $customGroup['custom_fields'];
        $sql = 'SELECT * FROM '. $table_name . ' WHERE entity_id = %1';
        $dao = CRM_Core_DAO::executeQuery($sql, array(
            1 => array($entity['id'], 'Integer',),
        ));

        if (!$dao->fetch()) {
            $dao = array();
            $dao['entity_id'] = $entity['id'];
            foreach ($fields as $field) {
                $columnName = $field['column_name'];
                $dao[$columnName] = false;
            }
        }
        return $this->_placeEntityCustomFields($fields, $dao, $entity);
    }

    /**
     * Method to place the  custom fields in the entity array based on the
     *
     * @param object $daoData (dao)
     * @param array $entityArray;
     * @return array
     */
    private function _placeEntityCustomFields($fields, $daoData, $entityArray) {
        $config = CRM_Basis_Config::singleton();

        foreach ($fields as $customFieldId => $customField) {
            $columnName = $customField['column_name'];
            $fieldName = $customField['name'];
            if (isset($daoData->$columnName)) {
                $entityArray[$fieldName] = $daoData->$columnName;
            }
        }

        return $entityArray;
    }

    public function getMedischeControleMinimaleDatum() {
        if (date('G') < 11) {
            return date('Y-m-d');
        }
        else {
            $d = new DateTime('+1day');
            return $d->format('Y-m-d');
        }
    }

    /**
     * @return int
     */
    public function getMediweTeamContactId()
    {
        return $this->_mediweTeamContactId;
    }

  /**
   * Function to return singleton object
   *
   * @return CRM_Basis_Config
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