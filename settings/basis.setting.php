<?php
/**
 * Settings voor wat nog ingesteld moet worden
 *
 * @author Klaas Eikelboom (CiviCooP) <klaas.eikelboom@civicoop.org>
 * @date 26 Januari 2017
 * @license AGPL-3.0
 */

return [
  'mediwe_opdrachtemailarts_template_id' => [
    'add' => '4.7',
    'help_text' => 'Selecteer de email die naar de arts gaat',
    'is_domain' => 1,
    'is_contact' => 0,
    'group_name' => CRM_Admin_Form_Setting_Basis::MEDIWE_PREFERENCES_NAME,
    'group' => 'mediwe',
    'type' => 'String',
    'quick_form_type' => 'Element',
    'default' => NULL,
    'html_type' => 'select',
    'pseudoconstant' => [
      'callback' => 'CRM_Basis_Utils::messageTemplates',
    ],
    'title' => 'Opdracht Email Arts',
    'description' => 'Nadat de arts een zieke medewerker toegewezen is, krijgt hij deze email',
  ],
  'mediwe_location_type_id' => [
    'add' => '4.7',
    'help_text' => 'Standaard locatie type',
    'is_domain' => 1,
    'is_contact' => 0,
    'group_name' => CRM_Admin_Form_Setting_Basis::MEDIWE_PREFERENCES_NAME,
    'group' => 'mediwe',
    'type' => 'String',
    'quick_form_type' => 'Element',
    'default' => NULL,
    'html_type' => 'select',
    'pseudoconstant' => [
      'callback' => 'CRM_Basis_Utils::locationTypes',
    ],
    'title' => 'Standaard locatie type',
    'description' => 'Locatie type dat standaard binnen Mediwe gebruikt wordt',
  ],
];