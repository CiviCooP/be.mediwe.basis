<?php

/**
 * Abstract class voor MediweOrganization
 *
 * @author  Erik Hommel (CiviCooP) <erik.hommel@civicoop.org>
 * @author  Klaas Eikelboom (CiviCooP) <klaas.eikelboom@civicoop.org>
 * @author  Christophe Deman <christophe.deman@mediwe.be>
 * @date    22 Feb 2018
 * @license AGPL-3.0
 */
abstract class CRM_Basis_MediweOrganization {

  /**
   * Method om BTW nummer te verwerken
   *
   * @param array $btwCustomField
   * @param int $btwCijfersCustomFieldId
   * @param int $entityId
   * @param array $data
   */
  protected function verwerkBtwNummer($btwCustomField, $btwCijfersCustomFieldId, $entityId, &$data) {
    foreach ($data as $dataRowId => $dataRow) {
      if (isset($dataRow['custom_field_id']) && $dataRow['custom_field_id'] == $btwCustomField['id']) {
        // alleen cijfers
        $btwCijfers = $this->btwNummerInCijfers($dataRow['value']);
        // sla geformatteerd veld op indien nodig
        if ($this->checkBtwFormatteren(CRM_Basis_Utils::getLandIdContact($entityId), $dataRow['value']) == TRUE) {
          $formattedBtw = $this->formatBtwBelgisch($btwCijfers);
          if ($formattedBtw) {
            $this->updateFormattedBtw($btwCustomField, $formattedBtw, $entityId);
          }
        }
        try {
          civicrm_api3('CustomValue', 'create', array(
            'entity_table' => 'civicrm_contact',
            'entity_id' => $entityId,
            'custom_' . $btwCijfersCustomFieldId => $btwCijfers,
          ));
        }
        catch (CiviCRM_API3_Exception $ex) {
          CRM_Core_Error::debug_log_message(ts('Fout bij het opslaan van het numeriek BTW nummer voor contact ')
            . $entityId . ts(' met BTW nummer ') . $dataRow['value'] . ts(' in ') . __METHOD__);
        }
      }
    }
  }

  /**
   * Method om geformatteerd BTW nummer op te slaan (met SQL omdat update via API Custom Value de custom loop
   * weer op zou roepen -> oneindige loop
   *
   * @param $customField
   * @param $formattedBtw
   * @param $entityId
   */
  private function updateFormattedBtw($customField, $formattedBtw, $entityId) {
    // gebruik custom_group_id van custom field om tabelnaam te halen
    $klantCustomGroupId = CRM_Basis_Config::singleton()->getKlantBoekhoudingCustomGroup('id');
    $leverancierCustomGroupId = CRM_Basis_Config::singleton()->getLeverancierCustomGroup('id');
    switch ($customField['custom_group_id']) {
      case $klantCustomGroupId:
        $tableName = CRM_Basis_Config::singleton()->getKlantBoekhoudingCustomGroup('table_name');
        break;

      case $leverancierCustomGroupId:
        $tableName = CRM_Basis_Config::singleton()->getLeverancierCustomGroup('table_name');
        break;
    }
    if ($tableName) {
      $query = 'UPDATE ' . $tableName . ' SET ' . $customField['column_name'] . ' = %1 WHERE entity_id = %2';
      CRM_Core_DAO::executeQuery($query, array(
        1 => array($formattedBtw, 'String'),
        2 => array($entityId, 'Integer'),
      ));
    }
  }

  /**
   * Method om btw nummer om te zetten in cijfers
   *
   * @param $btwNummer
   * @return string
   */
  public function btwNummerInCijfers($btwNummer) {
    $cijfers = array();
    for ($i = 0; $i < strlen($btwNummer); $i++) {
      if (is_numeric(substr($btwNummer, $i, 1))) {
        $cijfers[] = substr($btwNummer, $i, 1);
      }
    }
    return implode('', $cijfers);
  }

  /**
   * Method om btw nummer in belgisch formaat volgens instellingen op te maken
   *
   * @param string $btwCijfers
   * @return string
   */
  public function formatBtwBelgisch($btwCijfers) {
    $btwCijfers = substr($btwCijfers, 0, 10);
    // doe niets als cijfers minder dan 10
    if (strlen($btwCijfers) < 10) {
      return FALSE;
    }
    $btwDigits = array();
    // stop de format delen ertussen volgens het formaat in de instellingen
    $defaultBtwFormat = Civi::settings()->get('mediwe_belgisch_btw_formaat');
    $x = 0;
    for ($i = 0; $i < strlen($defaultBtwFormat); $i++) {
      if (!is_numeric(substr($defaultBtwFormat, $i, 1))) {
        $btwDigits[] = substr($defaultBtwFormat, $i, 1);
      }
      else {
        $btwDigits[] = substr($btwCijfers, $x, 1);
        $x++;
      }
    }
    return implode('', $btwDigits);
  }

  /**
   * Method om te kijken of er een belgisch BTW formatteer actie ondernomen moet worden
   *
   * @param $countryId
   * @param $btwNummer
   * @return bool
   */
  private function checkBtwFormatteren($countryId, $btwNummer) {
    // ja als btw nummer begint met BE of be
    if (strtolower(substr($btwNummer, 0, 2)) == 'be') {
      return TRUE;
    }
    if (empty($countryId)) {
      return FALSE;
    }
    // ja als land België
    try {
      $belgieId = civicrm_api3('Country', 'getvalue', array(
        'iso_code' => 'BE',
        'return' => 'id',
      ));
      if ($countryId == $belgieId) {
        return TRUE;
      }
    }
    catch (CiviCRM_API3_Exception $ex) {
      return FALSE;
    }
    return FALSE;
  }

}
