{* Setting configuration form *}
<div class="crm-block crm-form-block">
    <div class="spacer"></div>
    <table class="form-layout-compressed">
        <tr>
            <td class="label">{$form.mediwe_opdrachtemailarts_template_id.label}</td>
            <td>{$form.mediwe_opdrachtemailarts_template_id.html}</td>
        </tr>
        <tr>
            <td class="label">{$form.mediwe_location_type_id.label}</td>
            <td>{$form.mediwe_location_type_id.html}</td>
        </tr>
        <tr>
            <td class="label">{$form.mediwe_belgisch_btw_formaat.label}</td>
            <td>{$form.mediwe_belgisch_btw_formaat.html}</td>
        </tr>
    </table>

    <div class="crm-submit-buttons">
        {include file="CRM/common/formButtons.tpl"}
    </div>
</div>