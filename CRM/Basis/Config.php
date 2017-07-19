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
  private $_klantLocationType = array();
  
  // properties for relationship types
  private $_isKlantViaRelationshipType = array();
  
  // properties for membership types
  private $_maandelijksMembershipType = array();
  private $_voorafbetaaldMembershipType = array();
  private $_controleartsMembershipType = array();
  private $_mijnMediweMembershipType = array();
  private $_zorgfondsMembershipType = array();
  private $_inspecteurMembershipType = array();


  // properties for custom groups
  private $_klantBoekhoudingCustomGroup = array();
  private $_klantExpertsysteemCustomGroup = array();
  private $_klantOrganisatieCustomGroup = array();
  private $_klantProcedureCustomGroup = array();

  private $_inspecteurLeverancierCustomGroup = array();
  private $_inspecteurWerkgebiedCustomGroup = array();

  private $_controleArtsLeverancierCustomGroup = array();
  private $_controleArtsVakantieperiodeCustomGroup = array();
  private $_controleArtsCommunicatieCustomGroup = array();
  private $_controleArtsWerkgebiedCustomGroup = array();

  private $_klantMedewerkerExpertsysteemTellersCustomGroup = array();

  private $_voorwaardenControleCustomGroup = array();
  private $_voorwaardenMijnMediweCustomGroup = array();
  private $_voorwaardenZorgfondsCustomGroup = array();


  /**
   * CRM_Basis_Config constructor.
   */
  function __construct() {
    $this->setContactSubTypes();
    $this->setRelationshipTypes();
    $this->setMembershipTypes();
    $this->setKlantLocationTypes();

    $this->setKlantCustomGroups();
    $this->setKlantMedewerkerCustomGroups();
    $this->setControleArtsCustomGroups();
    $this->setInspecteurCustomGroups();
    $this->setMembershipCustomGroups();
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
        $customField = $this->getCustomField($this->_controleArtsCommunicatieCustomGroup, 'arts_gebruikt_app');
        if (!empty($key) && isset($customField[$key])) {
            return $customField[$key];
        } else {
            return $customField;
        }
    }

    /**
     * Getter for bellen_vooraf custom field from communicatie custom group
     *
     * @param null $key
     * @return mixed|array
     */
    public function getArtsBellenVoorafCustomField($key = NULL) {
        $customField = $this->getCustomField($this->_controleArtsCommunicatieCustomGroup, 'arts_bellen_vooraf');
        if (!empty($key) && isset($customField[$key])) {
            return $customField[$key];
        } else {
            return $customField;
        }
    }

    /**
     * Getter for bellen_achteraf custom field from communicatie custom group
     *
     * @param null $key
     * @return mixed|array
     */
    public function getArtsBellenAchterafCustomField($key = NULL) {
        $customField = $this->getCustomField($this->_controleArtsCommunicatieCustomGroup, 'arts_bellen_achteraf');
        if (!empty($key) && isset($customField[$key])) {
            return $customField[$key];
        } else {
            return $customField;
        }
    }

    /**
     * Getter for opdracht_fax custom field from communicatie custom group
     *
     * @param null $key
     * @return mixed|array
     */
    public function getArtsOpdrachtPerFaxCustomField($key = NULL) {
        $customField = $this->getCustomField($this->_controleArtsCommunicatieCustomGroup, 'arts_opdracht_fax');
        if (!empty($key) && isset($customField[$key])) {
            return $customField[$key];
        } else {
            return $customField;
        }
    }

    /**
     * Getter for opdracht_mail custom field from communicatie custom group
     *
     * @param null $key
     * @return mixed|array
     */
    public function getArtsOpdrachtPerMailCustomField($key = NULL) {
        $customField = $this->getCustomField($this->_controleArtsCommunicatieCustomGroup, 'arts_opdracht_mail');
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
        $customField = $this->getCustomField($this->_controleArtsCommunicatieCustomGroup, 'arts_overzicht');
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
        $customField = $this->getCustomField($this->_controleArtsCommunicatieCustomGroup, 'arts_opmerkingen');
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
    public function getExpertSystemPeriodCustomField($key = NULL) {
        $customField = $this->getCustomField($this->_klantExpertsysteemCustomGroup, 'expert_system_period');
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
    public function getExpertSystemPopulationCustomField($key = NULL) {
        $customField = $this->getCustomField($this->_klantExpertsysteemCustomGroup, 'expert_system_population');
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
     * Getter for Ziekteperiodes custom field from expertsysteem custom group
     *
     * @param null $key
     * @return mixed|array
     */
    public function getExpertSystemPeriodsCustomField($key = NULL) {
        $customField = $this->getCustomField($this->_klantExpertsysteemCustomGroup, 'expert_system_periods');
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
     * Getter for Ziekte_op_maandag custom field from expertsysteemtellers custom group
     *
     * @param null $key
     * @return mixed|array
     */
    public function getTellerZiekteOpMaandagCustomField($key = NULL) {
        $customField = $this->getCustomField($this->_klantMedewerkerExpertsysteemTellersCustomGroup, 'periods_monday_count');
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
     * Getter for venice custom field from controleartsLeverancierCustomGroup custom group
     *
     * @param null $key
     * @return mixed|array
     */
    public function getArtsVeniceCustomField($key = NULL) {
        $customField = $this->getCustomField($this->_controleArtsLeverancierCustomGroup, 'supplier_venice');
        if (!empty($key) && isset($customField[$key])) {
            return $customField[$key];
        } else {
            return $customField;
        }
    }

    /**
     * Getter for vat custom field from controleartsLeverancierCustomGroup custom group
     *
     * @param null $key
     * @return mixed|array
     */
    public function getArtsVatCustomField($key = NULL) {
        $customField = $this->getCustomField($this->_controleArtsLeverancierCustomGroup, 'supplier_vat');
        if (!empty($key) && isset($customField[$key])) {
            return $customField[$key];
        } else {
            return $customField;
        }
    }

    /**
     * Getter for subject_to_vat custom field from controleartsLeverancierCustomGroup custom group
     *
     * @param null $key
     * @return mixed|array
     */
    public function getArtsSubjectToVatCustomField($key = NULL) {
        $customField = $this->getCustomField($this->_controleArtsLeverancierCustomGroup, 'supplier_subject_to_vat');
        if (!empty($key) && isset($customField[$key])) {
            return $customField[$key];
        } else {
            return $customField;
        }
    }

    /**
     * Getter for account custom field from controleartsLeverancierCustomGroup custom group
     *
     * @param null $key
     * @return mixed|array
     */
    public function getArtsAccountCustomField($key = NULL) {
        $customField = $this->getCustomField($this->_controleArtsLeverancierCustomGroup, 'supplier_account');
        if (!empty($key) && isset($customField[$key])) {
            return $customField[$key];
        } else {
            return $customField;
        }
    }

    /**
     * Getter for Eigen_referentie custom field from controleartsLeverancierCustomGroup custom group
     *
     * @param null $key
     * @return mixed|array
     */
    public function getArtsEigenReferentieCustomField($key = NULL) {
        $customField = $this->getCustomField($this->_controleArtsLeverancierCustomGroup, 'supplier_reference');
        if (!empty($key) && isset($customField[$key])) {
            return $customField[$key];
        } else {
            return $customField;
        }
    }

    /**
     * Getter for CSV_bestand_bij_factuur custom field from controleartsLeverancierCustomGroup custom group
     *
     * @param null $key
     * @return mixed|array
     */
    public function getArtsCsvBestandBijFactuurCustomField($key = NULL) {
        $customField = $this->getCustomField($this->_controleArtsLeverancierCustomGroup, 'supplier_detail_csv');
        if (!empty($key) && isset($customField[$key])) {
            return $customField[$key];
        } else {
            return $customField;
        }
    }


    /**
     * Getter for venice custom field from inspecteurLeverancierCustomGroup custom group
     *
     * @param null $key
     * @return mixed|array
     */
    public function getInspecteurVeniceCustomField($key = NULL) {
        $customField = $this->getCustomField($this->_inspecteurLeverancierCustomGroup, 'supplier_venice');
        if (!empty($key) && isset($customField[$key])) {
            return $customField[$key];
        } else {
            return $customField;
        }
    }

    /**
     * Getter for vat custom field from inspecteurLeverancierCustomGroup custom group
     *
     * @param null $key
     * @return mixed|array
     */
    public function getInspecteurVatCustomField($key = NULL) {
        $customField = $this->getCustomField($this->_inspecteurLeverancierCustomGroup, 'supplier_vat');
        if (!empty($key) && isset($customField[$key])) {
            return $customField[$key];
        } else {
            return $customField;
        }
    }

    /**
     * Getter for subject_to_vat custom field from inspecteurLeverancierCustomGroup custom group
     *
     * @param null $key
     * @return mixed|array
     */
    public function getInspecteurSubjectToVatCustomField($key = NULL) {
        $customField = $this->getCustomField($this->_inspecteurLeverancierCustomGroup, 'supplier_subject_to_vat');
        if (!empty($key) && isset($customField[$key])) {
            return $customField[$key];
        } else {
            return $customField;
        }
    }

    /**
     * Getter for account custom field from inspecteurLeverancierCustomGroup custom group
     *
     * @param null $key
     * @return mixed|array
     */
    public function getInspecteurAccountCustomField($key = NULL) {
        $customField = $this->getCustomField($this->_inspecteurLeverancierCustomGroup, 'supplier_account');
        if (!empty($key) && isset($customField[$key])) {
            return $customField[$key];
        } else {
            return $customField;
        }
    }

    /**
     * Getter for Eigen_referentie custom field from inspecteurLeverancierCustomGroup custom group
     *
     * @param null $key
     * @return mixed|array
     */
    public function getInspecteurEigenReferentieCustomField($key = NULL) {
        $customField = $this->getCustomField($this->_inspecteurLeverancierCustomGroup, 'supplier_reference');
        if (!empty($key) && isset($customField[$key])) {
            return $customField[$key];
        } else {
            return $customField;
        }
    }

    /**
     * Getter for CSV_bestand_bij_factuur custom field from inspecteurLeverancierCustomGroup custom group
     *
     * @param null $key
     * @return mixed|array
     */
    public function getInspecteurCsvBestandBijFactuurCustomField($key = NULL) {
        $customField = $this->getCustomField($this->_inspecteurLeverancierCustomGroup, 'supplier_detail_csv');
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
        $customField = $this->getCustomField($this->_klantOrganisatieCustomGroup, 'org_level1');
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
        $customField = $this->getCustomField($this->_klantOrganisatieCustomGroup, 'org_level2');
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
        $customField = $this->getCustomField($this->_klantOrganisatieCustomGroup, 'org_level3');
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
    public function getOrgApprovalEmailCustomField($key = NULL) {
        $customField = $this->getCustomField($this->_klantOrganisatieCustomGroup, 'org_approval_email');
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
    public function getOrgApprovalTitleCustomField($key = NULL) {
        $customField = $this->getCustomField($this->_klantOrganisatieCustomGroup, 'org_approval_title');
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
    public function getOrgShortPeriodCustomField($key = NULL) {
        $customField = $this->getCustomField($this->_klantOrganisatieCustomGroup, 'org_short_period');
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
    public function getOrgMonthsNewEmployeeCustomField($key = NULL) {
        $customField = $this->getCustomField($this->_klantOrganisatieCustomGroup, 'org_months_new_employee');
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
    public function getCustomerProcedureRemarksCustomField($key = NULL) {
        $customField = $this->getCustomField($this->_klantProcedureCustomGroup, 'customer_procedure_remarks');
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
    public function getCustomerProcedureFreeCustomField($key = NULL) {
        $customField = $this->getCustomField($this->_klantProcedureCustomGroup, 'customer_procedure_free');
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
    public function getCustomerProcedureSectorCustomField($key = NULL) {
        $customField = $this->getCustomField($this->_klantProcedureCustomGroup, 'customer_procedure_sector');
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
    public function getCustomerProcedureIdSectorCustomField($key = NULL) {
        $customField = $this->getCustomField($this->_klantProcedureCustomGroup, 'customer_procedure_id_sector');
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
    public function getCustomerProcedureFteCalculationTypeCustomField($key = NULL) {
        $customField = $this->getCustomField($this->_klantProcedureCustomGroup, 'customer_procedure_fte_calculation_type');
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
    public function getCustomerProcedureCaoNoExitFromCustomField($key = NULL) {
        $customField = $this->getCustomField($this->_klantProcedureCustomGroup, 'customer_procedure_cao_noexit_from');
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
    public function getCustomerProcedureCaoNoExitTillCustomField($key = NULL) {
        $customField = $this->getCustomField($this->_klantProcedureCustomGroup, 'customer_procedure_cao_noexit_till');
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
    public function getCustomerProcedureCustomField($key = NULL) {
        $customField = $this->getCustomField($this->_klantProcedureCustomGroup, 'customer_procedure');
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
    public function getCustomerProcedureVisionCustomField($key = NULL) {
        $customField = $this->getCustomField($this->_klantProcedureCustomGroup, 'customer_procedure_vision');
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
    public function getCustomerProcedureGoalCustomField($key = NULL) {
        $customField = $this->getCustomField($this->_klantProcedureCustomGroup, 'customer_procedure_goal');
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
    public function getCustomerProcedureUseSmsCustomField($key = NULL) {
        $customField = $this->getCustomField($this->_klantProcedureCustomGroup, 'customer_procedure_use_sms');
        if (!empty($key) && isset($customField[$key])) {
            return $customField[$key];
        } else {
            return $customField;
        }
    }

    /**
     * Getter for vakantie_van custom field from controleArtsVakantieperiode custom group
     *
     * @param null $key
     * @return mixed|array
     */
    public function getVakantieVanCustomField($key = NULL) {
        $customField = $this->getCustomField($this->_controleArtsVakantieperiodeCustomGroup, 'holiday_from');
        if (!empty($key) && isset($customField[$key])) {
            return $customField[$key];
        } else {
            return $customField;
        }
    }

    /**
     * Getter for vakantie_tot custom field from controleArtsVakantieperiode custom group
     *
     * @param null $key
     * @return mixed|array
     */
    public function getVakantieTotCustomField($key = NULL) {
        $customField = $this->getCustomField($this->_controleArtsVakantieperiodeCustomGroup, 'holiday_till');
        if (!empty($key) && isset($customField[$key])) {
            return $customField[$key];
        } else {
            return $customField;
        }
    }

    /**
     * Getter for postcode custom field from controleArtsWerkgebied custom group
     *
     * @param null $key
     * @return mixed|array
     */
    public function getArtsPostcodeCustomField($key = NULL) {
        $customField = $this->getCustomField($this->_controleArtsWerkgebiedCustomGroup, 'field_of_work_zip');
        if (!empty($key) && isset($customField[$key])) {
            return $customField[$key];
        } else {
            return $customField;
        }
    }

    /**
     * Getter for gemeente custom field from controleArtsWerkgebied custom group
     *
     * @param null $key
     * @return mixed|array
     */
    public function getArtsGemeenteCustomField($key = NULL) {
        $customField = $this->getCustomField($this->_controleArtsWerkgebiedCustomGroup, 'field_of_work_city');
        if (!empty($key) && isset($customField[$key])) {
            return $customField[$key];
        } else {
            return $customField;
        }
    }

    /**
     * Getter for prioriteit custom field from controleArtsWerkgebied custom group
     *
     * @param null $key
     * @return mixed|array
     */
    public function getArtsPrioriteitCustomField($key = NULL) {
        $customField = $this->getCustomField($this->_controleArtsWerkgebiedCustomGroup, 'field_of_work_prio');
        if (!empty($key) && isset($customField[$key])) {
            return $customField[$key];
        } else {
            return $customField;
        }
    }

    /**
     * Getter for postcode custom field from inspecteurWerkgebied custom group
     *
     * @param null $key
     * @return mixed|array
     */
    public function getInspecteurPostcodeCustomField($key = NULL) {
        $customField = $this->getCustomField($this->_inspecteurWerkgebiedCustomGroup, 'field_of_work_zip');
        if (!empty($key) && isset($customField[$key])) {
            return $customField[$key];
        } else {
            return $customField;
        }
    }

    /**
     * Getter for gemeente custom field from inspecteurWerkgebied custom group
     *
     * @param null $key
     * @return mixed|array
     */
    public function getInspecteurGemeenteCustomField($key = NULL) {
        $customField = $this->getCustomField($this->_inspecteurWerkgebiedCustomGroup, 'field_of_work_city');
        if (!empty($key) && isset($customField[$key])) {
            return $customField[$key];
        } else {
            return $customField;
        }
    }

    /**
     * Getter for prioriteit custom field from inspecteurWerkgebied custom group
     *
     * @param null $key
     * @return mixed|array
     */
    public function getInspecteurPrioriteitCustomField($key = NULL) {
        $customField = $this->getCustomField($this->_inspecteurWerkgebiedCustomGroup, 'field_of_work_prio');
        if (!empty($key) && isset($customField[$key])) {
            return $customField[$key];
        } else {
            return $customField;
        }
    }


    /**
   * Getter for controle arts vakantie periode custom group
   *
   * @param string $key
   * @return mixed|array
   */
  public function getControleArtsVakantieperiodeCustomGroup($key = NULL) {
    if (!empty($key) && isset($this->_controleArtsVakantieperiodeCustomGroup[$key])) {
      return $this->_controleArtsVakantieperiodeCustomGroup[$key];
    } else {
      return $this->_controleArtsVakantieperiodeCustomGroup;
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
      return $this->_klantExpertsysteemCustomGroup[$key];
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
     * Getter for klant location type
     *
     * @return null
     */
    public function getKlantLocationType() {
        return $this->_klantLocationType;
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


    /**
     * Method to set the relevant klant location type properties
     */
    private function setKlantLocationTypes() {
        try {
            $locationTypes = civicrm_api3('LocationType','get', array(
                'options' => array('limit' => 0)));

            foreach ($locationTypes['values'] as $locationTypeId => $locationType) {
                switch ($locationType['name']) {
                    case 'Billing':
                        $this->_klantLocationType = $locationType;
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
            $relaionshipTypes = civicrm_api3('RelationshipType','get', array(
                'options' => array('limit' => 0)));
            foreach ($relaionshipTypes['values'] as $relationshipTypeId => $relationshipType) {
                switch ($relationshipType['name_a_b']) {
                    case 'is_klant_via':
                        $this->_isKlantViaRelationshipType = $relationshipType;
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
   * Method to set the klant custom groups and custom fields
   */
  private function setKlantCustomGroups() {
    try {
      $customGroups = civicrm_api3('CustomGroup','get', array(
        'options' => array('limit' => 0)));
      foreach ($customGroups['values'] as $customGroupId => $customGroup) {
        $customFields = civicrm_api3('CustomField', 'get', array(
            'custom_group_id' => $customGroupId,
            'options' => array('limit' => 0)
        ));
        $customGroup['custom_fields'] = $customFields['values'];
        switch ($customGroup['name']) {
          case 'invoicing':
            $this->_klantBoekhoudingCustomGroup = $customGroup;
            break;
          case 'expert_system':
            $this->_klantExpertsysteemCustomGroup = $customGroup;
            break;
          case 'organization':
            $this->_klantOrganisatieCustomGroup = $customGroup;
            break;
          case 'customer_procedure':
            $this->_klantProcedureCustomGroup = $customGroup;
            break;
        }
      }
    }
    catch (CiviCRM_API3_Exception $ex) {
    }
  }

    /**
     * Method to set the controleArts custom groups and custom fields
     */
    private function setControleArtsCustomGroups() {
        try {
            $customGroups = civicrm_api3('CustomGroup','get', array(
                'options' => array('limit' => 0)));
            foreach ($customGroups['values'] as $customGroupId => $customGroup) {
                $customFields = civicrm_api3('CustomField', 'get', array(
                    'custom_group_id' => $customGroupId,
                    'options' => array('limit' => 0)
                ));
                $customGroup['custom_fields'] = $customFields['values'];
                switch ($customGroup['name']) {
                    case 'holiday':
                        $this->_controleArtsVakantieperiodeCustomGroup = $customGroup;
                        break;
                    case 'field_of_work':
                        $this->_controleArtsWerkgebiedCustomGroup = $customGroup;
                        break;
                    case 'communication':
                        $this->_controleArtsCommunicatieCustomGroup = $customGroup;
                        break;
                    case 'supplier':
                        $this->_controleArtsLeverancierCustomGroup = $customGroup;
                        break;
                }
            }
        }
        catch (CiviCRM_API3_Exception $ex) {
        }
    }

    /**
     * Method to set the klantmedewerker custom groups and custom fields
     */
    private function setKlantMedewerkerCustomGroups() {
        try {
            $customGroups = civicrm_api3('CustomGroup','get', array(
                'options' => array('limit' => 0)));
            foreach ($customGroups['values'] as $customGroupId => $customGroup) {
                $customFields = civicrm_api3('CustomField', 'get', array(
                    'custom_group_id' => $customGroupId,
                    'options' => array('limit' => 0)));
                $customGroup['custom_fields'] = $customFields['values'];
                switch ($customGroup['name']) {
                    case 'counters':
                        $this->_klantMedewerkerExpertSysteemTellersCustomGroup    = $customGroup;
                        break;
                }
            }
        }
        catch (CiviCRM_API3_Exception $ex) {
        }
    }

    /**
     * Method to set the inspecteur custom groups and custom fields
     */
    private function setInspecteurCustomGroups() {
        try {
            $customGroups = civicrm_api3('CustomGroup','get', array(
                'options' => array('limit' => 0)));
            foreach ($customGroups['values'] as $customGroupId => $customGroup) {
                $customFields = civicrm_api3('CustomField', 'get', array(
                    'custom_group_id' => $customGroupId,
                    'options' => array('limit' => 0)));
                $customGroup['custom_fields'] = $customFields['values'];
                switch ($customGroup['name']) {
                    case 'field_of_work':
                        $this->_inspecteurWerkgebiedCustomGroup = $customGroup;
                        break;
                    case 'supplier':
                        $this->_inspecteurLeverancierCustomGroup = $customGroup;
                        break;
                }
            }
        }
        catch (CiviCRM_API3_Exception $ex) {
        }
    }

    /**
     * Method to set the inspecteur custom groups and custom fields
     */
    private function setMembershipCustomGroups() {
        try {
            $customGroups = civicrm_api3('CustomGroup','get', array(
                'options' => array('limit' => 0)));
            foreach ($customGroups['values'] as $customGroupId => $customGroup) {
                $customFields = civicrm_api3('CustomField', 'get', array(
                    'custom_group_id' => $customGroupId,
                    'options' => array('limit' => 0)));
                $customGroup['custom_fields'] = $customFields['values'];
                switch ($customGroup['name']) {
                    case 'voorwaarden_controle':
                        $this->_voorwaardenControleCustomGroup = $customGroup;
                        break;
                    case 'voorwaarden_mijn_mediwe':
                        $this->_voorwaardenMijnMediweCustomGroup = $customGroup;
                        break;
                    case 'voorwaarden_zorgfonds':
                        $this->_voorwaardenZorgfondsCustomGroup = $customGroup;
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