<?php

/**
 * Class voor aanmaken van een Dagelijkse Bel activiteit
 *
 * @author Klaas Eikelboom (CiviCooP) <erik.hommel@civicoop.org>
 * @date 25 Januari 2017
 * @license AGPL-3.0
 */
class CRM_Basis_Acties_DagelijkseBelAfspraak {

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

  private function bestaandeBelAfspraak() {
    /**
     * ToDo dit gelt natuurlijk alleen voor activiteiten voor vandaag
     * of kijk naar afgehandelde status
     */
    $config = CRM_Basis_Config::singleton();
    return CRM_Core_DAO::singleValueQuery("
        SELECT activity_id FROM civicrm_activity_contact ac
        JOIN   civicrm_activity a ON (ac.activity_id = a.id AND a.activity_type_id=%1)
        WHERE  ac.record_type_id = %2 AND ac.contact_id = %3", [
      '1' => [$config->getBelAfspraakArtsActivityType()['value'], 'Integer'],
      '2' => [3, 'Integer'], // Activity Assignees
      '3' => [$this->_params['contact_id_a'], 'Integer'],
    ]);
  }


  /**
   * @throws \CiviCRM_API3_Exception
   */
  public function actie() {
    $config = CRM_Basis_Config::singleton();
    $belmoment = civicrm_api3('Contact', 'getvalue', [
        'id' => $this->_params['contact_id_a'],
        'return' => 'custom_' . $config->getArtsBelMomentCustomField('id'),
      ]
    );
    try
    {
      $naamOnderzochte = civicrm_api3('Contact', 'getvalue', [
        'id' => $this->_params['contact_id_b'],
        'return' => 'display_name',
      ]);
    }
    catch (CiviCRM_API3_Exception $ex) {
      $naamOnderzochte = 'Naam van de onderzochte werd niet gevonden';
    }

    $result = FALSE;

    foreach ($belmoment as $value) {
      try {
        $name = civicrm_api3('OptionValue', 'getvalue', [
          'option_group_id' => $config->getBelMomentOptionGroup('id'),
          'value' => $value,
          'return' => 'name',
        ]);
        if ($name == 'achteraf_bellen') {
          $result = TRUE;
        };
      }
      catch (CiviCRM_API3_Exception $ex) {
      }
    }
    if ($result) {
      $apiParams = [
        'activity_type_id' => $config->getBelAfspraakArtsActivityType()['value'],
        'subject' => 'Nabellen',
        'source_contact_id' => $config->getMediweTeamContactId(),
        'assignee_id' => $config->getMediweTeamContactId(),
        'target_id' => $this->_params['contact_id_a'],
      ];
      $activityId = $this->bestaandeBelAfspraak();
      if ($activityId) {
        try {
          $details = civicrm_api3('Activity', 'getvalue', [
            'id' => $activityId,
            'return' => 'details',
          ]);
        }
        catch (CiviCRM_API3_Exception $ex) {
          $details = '';
        }
        $apiParams['details'] = $details . "<br/>" . 'Medische controle voor ' . $naamOnderzochte;
        $apiParams['id'] = $activityId;
      }
      else {
        $apiParams['details'] = 'Medische controle voor ' . $naamOnderzochte;
      }
      $apiParams['status_id'] = 'Scheduled';
      $belafspraak = civicrm_api3('Activity', 'create', $apiParams);

    }
  }

}
