<?php
/**
 * Class to process VakantiePeriode in Mediwe
 *
 * @author Erik Hommel (CiviCooP) <erik.hommel@civicoop.org>
 * @author Klaas Eikelboom (CiviCooP) <klaas.eikelboom@civicoop.org>
 * @author Christophe Deman <christophe.deman@mediwe.be>
 * @date 14 Feb 2018
 * @license AGPL-3.0
 */

class CRM_Basis_VakantiePeriode {

  public function __construct() {
  }

  /**
   * Method om vakantie periodes op te slaan
   *
   * @param int $contactId
   * @param $data
   */
  public function save($contactId, $data) {
    // todo nakijken wat er gebeurt als er al meerdere vakantieperiodes voor het contact staan en er meerdere toegevoegd worden
    if (isset($data['mvp_vakantie_van'])) {
      $holiday = array(
        'mvp_vakantie_van' => $data['mvp_vakantie_van'],
        'mvp_vakantie_tot' => $data['mvp_vakantie_tot'],
      );
      $oldPeriods = $this->getVakantiePeriodesCustomFields($contactId);
      foreach ($oldPeriods as $period) {
        if (substr($period['mvp_vakantie_van'], 0, 10) == substr($data['mvp_vakantie_van'], 0, 10)) {
          $holiday['id'] = $period['id'];
        }
      }
    }
    $config = CRM_Basis_Config::singleton();
    CRM_Basis_RepeatingCustomData::setRepeatingData(
      $config->getVakantieperiodeCustomGroup('custom_fields'),
      $contactId,
      $holiday,
      ['mvp_vakantie_van']
    );
  }


}