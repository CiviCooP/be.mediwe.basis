<?php

require_once 'basis.civix.php';
use CRM_Basis_ExtensionUtil as E;

/**
 * Implements hook_civicrm_custom)(.
 * @param $op
 * @param $groupId
 * @param $entityId
 * @param $params
 */
function basis_civicrm_custom($op, $groupId, $entityId, &$params) {
  CRM_Basis_Klant::custom($op, $groupId, $entityId, $params);
}

/**
 * Implements hook_civicrm_searchColumns().
 *
 * @param $objectName
 * @param $headers
 * @param $values
 * @param $selector
 */
function basis_civicrm_searchColumns( $objectName, &$headers,  &$values, &$selector ) {
  if ($objectName == 'contribution') {
    CRM_Basis_Contribution::searchColumns($objectName, $headers, $values);
  }
}
function basis_civicrm_links($op, $objectName, $objectId, &$links, &$mask, &$values) {
  if ($objectName == 'Contribution') {
    $links[] = array(
      'name' => ts('Print'),
      'url' => 'civicrm/mediwe/printfaktuur',
      'title' => 'Print Faktuur',
      'qs' => 'reset=1&id=%%bid%%',
      'bit' => 'print',
    );
    $values['id'] = $objectId;
  }
}

/**
 * Implements hook_civicrm_config().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_config
 */
function basis_civicrm_config(&$config) {
  _basis_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_xmlMenu().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_xmlMenu
 */
function basis_civicrm_xmlMenu(&$files) {
  _basis_civix_civicrm_xmlMenu($files);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_install
 */
function basis_civicrm_install() {
  _basis_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_postInstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_postInstall
 */
function basis_civicrm_postInstall() {
  _basis_civix_civicrm_postInstall();
}

/**
 * Implements hook_civicrm_uninstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_uninstall
 */
function basis_civicrm_uninstall() {
  _basis_civix_civicrm_uninstall();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function basis_civicrm_enable() {
  _basis_civix_civicrm_enable();
}

/**
 * Implements hook_civicrm_disable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_disable
 */
function basis_civicrm_disable() {
  _basis_civix_civicrm_disable();
}

/**
 * Implements hook_civicrm_upgrade().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_upgrade
 */
function basis_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _basis_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implements hook_civicrm_managed().
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_managed
 */
function basis_civicrm_managed(&$entities) {
  _basis_civix_civicrm_managed($entities);
}

/**
 * Implements hook_civicrm_caseTypes().
 *
 * Generate a list of case-types.
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_caseTypes
 */
function basis_civicrm_caseTypes(&$caseTypes) {
  _basis_civix_civicrm_caseTypes($caseTypes);
}

/**
 * Implements hook_civicrm_angularModules().
 *
 * Generate a list of Angular modules.
 *
 * Note: This hook only runs in CiviCRM 4.5+. It may
 * use features only available in v4.6+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_angularModules
 */
function basis_civicrm_angularModules(&$angularModules) {
  _basis_civix_civicrm_angularModules($angularModules);
}

/**
 * Implements hook_civicrm_alterSettingsFolders().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_alterSettingsFolders
 */
function basis_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _basis_civix_civicrm_alterSettingsFolders($metaDataFolders);
}

// --- Functions below this ship commented out. Uncomment as required. ---

/**
 * Implements hook_civicrm_preProcess().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_preProcess
 *
function basis_civicrm_preProcess($formName, &$form) {

} //

/**
 * Implements hook_civicrm_navigationMenu().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_navigationMenu
 */

function basis_civicrm_navigationMenu(&$menu) {

  _basis_civix_insert_navigation_menu($menu, 'Administer', array(
    'label' => E::ts('Mediwe Settings'),
    'name' => 'settingbasis',
    'url' => 'civicrm/admin/setting/basis',
    'permission' => 'administer CiviCRM',
    'operator' => 'OR',
    'separator' => 0,
   ));
  _basis_civix_navigationMenu($menu);
}

/**
 * Implements hook_civicrm_post()
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_post/
 *
 * @param $op
 * @param $objectName
 * @param $objectId
 * @param $objectRef
 */

function basis_civicrm_post($op, $objectName, $objectId, &$objectRef){

    if($op =='create' && $objectName == 'Relationship'){
        $relationShip = $objectRef;

        $params = array(
            'case_id' => $relationShip->case_id,
            'relationship_type_id' => $relationShip->relationship_type_id,
            'contact_id_a' => $relationShip->contact_id_a,
            'contact_id_b' => $relationShip->contact_id_b
        );

        $dagelijkseBelAfspraak = new CRM_Basis_Acties_DagelijkseBelAfspraak($params);
        if($dagelijkseBelAfspraak->controleer())
        {
            $dagelijkseBelAfspraak->actie();
        }
        $opdrachtEmailArts = new CRM_Basis_Acties_OpdrachtEmailArts($params);
        if($opdrachtEmailArts->controleer())
        {
            $opdrachtEmailArts->actie();
        }

    }




}
