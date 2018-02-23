<?php
/**
 * Settings form for the Mediwe Basis
 *
 * @author Klaas Eikelboom (CiviCooP) <klaas.eikelboom@civicoop.org>
 * @date 25 Januari 2017
 * @license AGPL-3.0
 */

class CRM_Admin_Form_Setting_Basis extends CRM_Admin_Form_Setting
{
    const MEDIWE_PREFERENCES_NAME = 'Mediwe Preferences';

    protected $_settings =  array(
        'mediwe_opdrachtemailarts_template_id' => self::MEDIWE_PREFERENCES_NAME,
        'mediwe_location_type_id' => self::MEDIWE_PREFERENCES_NAME,
        'mediwe_belgisch_btw_formaat' => self::MEDIWE_PREFERENCES_NAME,
    );

    public function buildQuickForm()
    {
        CRM_Utils_System::setTitle(ts('Settings - Mediwe Basis'));
        parent::buildQuickForm();
    }

}