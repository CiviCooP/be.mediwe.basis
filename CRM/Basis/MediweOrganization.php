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

  public function verwerkBtwNummerCustomField($btwCustomFieldId, $btwCijfersCustomFieldId, $entityId, $data) {
    foreach ($data as $dataRow) {
      if (isset($dataRow['custom_field_id']) && $dataRow['custom_field_id'] == $btwCustomFieldId) {
        $btwCijfers = $this->btwNummerInCijfers($dataRow['value']);
        try {
          civicrm_api3('CustomValue', 'create', array(
            'entity_table' => 'civicrm_contact',
            'entity_id' => $entityId,
            'custom_' . $btwCijfersCustomFieldId => $btwCijfers,
          ));
          return TRUE;
        }
        catch (CiviCRM_API3_Exception $ex) {
          CRM_Core_Error::debug_log_message(ts('Fout bij het opslaan van het numeriek BTW nummer voor contact ')
            . $entityId . ts(' met BTW nummer ').$dataRow['value'] . ts(' in ') . __METHOD__);
          return FALSE;
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
}
