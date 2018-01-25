<?php

/**
 * Class voor het versturen van de email naar de arts
 *
 * @author Klaas Eikelboom (CiviCooP) <erik.hommel@civicoop.org>
 * @date 25 Januari 2017
 * @license AGPL-3.0
 */
class CRM_Basis_Acties_OpdrachtEmailArts
{
    private $_params;

    /**
     * CRM_Basis_Acties_DagelijkseBelAfspraak constructor.
     * @param $_params
     */
    public function __construct($_params)
    {
        $this->_params = $_params;
    }

    /**
     * Controleer of aan de voorwaarde van de actie voldaan zijn.
     * - wil de gevraagde arts gebeld worden.
     *
     */
    public function controleer()
    {
        $config = CRM_Basis_Config::singleton();
        if (!isset($this->_params['case_id'])) {
            return FALSE;
        }
        if ($this->_params['relationship_type_id'] == $config->getControleArtsRelationshipTypeId()) {
            return TRUE;
        }
        return FALSE;
    }


    public function actie()
    {
       $apiParams['contact_id'] = $this->_params['contact_id_a'];
       $apiParams['template_id'] = 68;
       $apiParams['case_id'] = $this->_params['case_id'];

       civicrm_api3('Email','Send',$apiParams);

    }

}