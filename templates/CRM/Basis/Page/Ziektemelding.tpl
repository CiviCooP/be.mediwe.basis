<div class="crm-content-block crm-block">
    <div id="help">
        {ts}De aanwezige ziektemelding{/ts}
    </div>
    <div class="action-link">
        <a class="button new-option" href="{$addUrl}">
            <span><div class="icon add-icon"></div>Nieuwe ziektemelding</span>
        </a>
    </div>
    {include file="CRM/common/pager.tpl" location="top"}
    {include file='CRM/common/jsortable.tpl'}
    <div id="claim_level-wrapper" class="dataTables_wrapper">
        <table id="claim_level-table" class="display">
            <thead>
            <tr>
                <th>{ts}Datum begin{/ts}</th>
                <th>{ts}Medewerker{/ts}</th>
                <th>{ts}Klant{/ts}</th>
                <th>{ts}Einddatum{/ts}</th>
                <th id="nosort"></th>
            </tr>
            </thead>
            <tbody>
            {assign var="rowClass" value="odd-row"}
            {assign var="rowCount" value=0}
            {foreach from=$ziektemeldingen key=id item=melding}
                {assign var="rowCount" value=$rowCount+1}
                <tr id="row{$id}" class="{cycle values="odd,even"}">
                    <td hidden="1">{$id}
                    <td>{$melding.start_date}</td>
                    <td>{$melding.employee_display_name}</td>
                    <td>{$melding.employee_current_employer}</td>
                    <td>{$melding.end_date}</td>
                    <td>
              <span>
                {foreach from=$melding.actions item=actionLink}
                    {$actionLink}
                {/foreach}
              </span>
                    </td>
                </tr>
                {if $rowClass eq "odd-row"}
                    {assign var="rowClass" value="even-row"}
                {else}
                    {assign var="rowClass" value="odd-row"}
                {/if}
            {/foreach}
            </tbody>
        </table>
    </div>
    {include file="CRM/common/pager.tpl" location="bottom"}
    <div class="action-link">
        <a class="button new-option" href="{$addUrl}">
            <span><div class="icon add-icon"></div>Nieuwe ziektemelding</span>
        </a>
    </div>
</div>