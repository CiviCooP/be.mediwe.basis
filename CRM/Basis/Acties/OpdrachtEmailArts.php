<?php

/**
 * Class voor het versturen van de email naar de arts
 *
 * @author Klaas Eikelboom (CiviCooP) <klaas.eikelboom@civicoop.org>
 * @date 25 Januari 2017
 * @license AGPL-3.0
 */
class CRM_Basis_Acties_OpdrachtEmailArts {

  private $_params;

  /**
   * CRM_Basis_Acties_DagelijkseBelAfspraak constructor.
   *
   * @param $_params
   */
  public function __construct($_params) {
    $this->_params = $_params;
  }

  /**
   * Controleer of aan de voorwaarde van de actie voldaan zijn.
   * - wil de gevraagde arts gebeld worden.
   *
   */
  public function controleer() {
    $config = CRM_Basis_Config::singleton();
    if (!isset($this->_params['case_id'])) {
      return FALSE;
    }
    if ($this->_params['relationship_type_id'] == $config->getControleArtsRelationshipTypeId()) {
      return TRUE;
    }
    return FALSE;
  }


  public function actie() {

    $smarty = CRM_Core_Smarty::singleton();
    $config = CRM_Basis_Config::singleton();

    $templateId = Civi::settings()->get('mediwe_opdrachtemailarts_template_id');

    if ($templateId) {
      $apiParams['contact_id'] = $this->_params['contact_id_a'];
      $apiParams['template_id'] = $templateId;
      $apiParams['case_id'] = $this->_params['case_id'];


      // De klazntmedewerker
      $klantmedewerker = civicrm_api3('KlantMedewerker', 'getsingle', array(
        'id' => $this->_params['contact_id_b'],
      ));
      if (isset($klantmedewerker['display_name'])) {
        $smarty->assign('klantmedewerker',$klantmedewerker);
      }


      // De controlearts
      $controleArts =  civicrm_api3('ControleArts', 'getsingle', array(
        'id' => $this->_params['contact_id_a'],
      ));
      if (isset($controleArts['display_name'])) {
        $smarty->assign('controleArts',$controleArts);
      }

      // De activiteit
      $activiteit = civicrm_api3('Activity', 'getsingle', array(
        'case_id' => $this->_params['case_id'],
        'activity_date_time' => date("Y-m-d"),
        'status_id' => "Scheduled",
      ));
      if (isset($activiteit['activity_date_time'])) {
         $smarty->assign('opdracht', $activiteit);
      }
      else {
        // nog geen mail versturen
        return;

      // de gekoppelde case van het type Ziektemelding
      }


      civicrm_api3('Email', 'Send', $apiParams);



    }
    else {
      Civi::log()
        ->error("Opdracht Arts Template id is nog niet ingesteld - de OpdrachtEmail wordt niet verstuurd");
    }

  }

}
