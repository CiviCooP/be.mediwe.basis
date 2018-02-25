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
   * @param $btwCustomFieldId
   * @param $btwCijfersCustomFieldId
   * @param $entityId
   * @param $data
   */
  protected function verwerkBtwNummer($btwCustomFieldId, $btwCijfersCustomFieldId, $entityId, $data) {
    foreach ($data as $dataRow) {
      if (isset($dataRow['custom_field_id']) && $dataRow['custom_field_id'] == $btwCustomFieldId) {
        // alleen cijfers
        $btwCijfers = $this->btwNummerInCijfers($dataRow['value']);
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
        // vervolgens opmaken in Belgisch formaat indien nodig
        $entityCountryId = CRM_Basis_Utils::getLandIdContact($entityId);
        if ($this->checkBtwFormatteren($entityCountryId, $dataRow['value'])) {
          $formattedBtw = $this->formatBtwBelgisch($dataRow['value']);
          try {
            civicrm_api3('CustomValue', 'create', array(
              'entity_table' => 'civicrm_contact',
              'entity_id' => $entityId,
              'custom_' . $btwCustomFieldId => $formattedBtw,
            ));
          }
          catch (CiviCRM_API3_Exception $ex) {
            CRM_Core_Error::debug_log_message(ts('Fout bij het opslaan van het geformatteerd BTW nummer voor contact ')
              . $entityId . ts(' met BTW nummer ') . $dataRow['value'] . ts(' in ') . __METHOD__);
          }
        }
      }
    }
  }

  /**
   * Method om btw nummer om te zetten in cijfers
   *
   * @param $btwNummer
   * @return string
   */
  private function btwNummerInCijfers($btwNummer) {
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
   * @param string $inputBtw
   * @return string
   */
  public function formatBtwBelgisch($inputBtw) {
    // herleidt tot alleen cijfers en gebruik alleen eerste 10 cijfers
    $btwCijfers = substr($this->btwNummerInCijfers($inputBtw), 0, 10);
    // doe niets als cijfers minder dan 10
    if (strlen($btwCijfers) < 10) {
      return $inputBtw;
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
  protected function checkBtwFormatteren($countryId, $btwNummer) {
    // ja als btw nummer begint met BE of be
    if (strtolower(substr($btwNummer, 0, 2)) == 'be') {
      return TRUE;
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
