<?php

/**
 * Class to process Contribution in Mediwe
 *
 * @author Erik Hommel (CiviCooP) <erik.hommel@civicoop.org>
 * @date 14 Jul 2017
 * @license AGPL-3.0
 */
class CRM_Basis_Contribution {
  public static function searchColumns($objectName, &$headers, &$values) {
    foreach ($headers as $headerKey => $headerValue) {
      if (isset($headerValue['name'])) {
        if ($headerValue['name'] == 'Bedankje verzonden' || $headerValue['name'] == 'Relatiegeschenk') {
          unset($headers[$headerKey]);
        }
      }
    }
    $headers[] = array(
      'name' => 'Faktuurnummer',
      'sort' => 'faktuur_nummer',
      'direction' => 4,
    );
    foreach ($values as $valueKey => $valueValue) {
      unset($values[$valueKey]['thankyou_date']);
      unset($values[$valueKey]['product_name']);
      $values[$valueKey]['faktuur_nummer'] = civicrm_api3('Contribution', 'getvalue', array(
        'contribution_id' => $valueValue['contribution_id'],
        'return' => 'custom_4'
      ));
      $values[$valueKey]['action'] .=
        '<a href="http://localhost/mediwedev/index.php?q=civicrm/mediwe/printfaktuur&id='.$valueValue['contribution_id'].'" class="action-item crm-hover-button small-popup" title=\'Printen\'>Printen</a>';
    }
  }
}

