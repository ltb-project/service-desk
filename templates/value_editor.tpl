<div class="{$type}">
{if $type eq 'text'}
    <input type="text" name="{$item}" class="form-control" value="{$value}" />

{else if $type eq 'mailto'}
    <input type="email" name="{$item}" class="form-control" value="{$value}" />

{else if $type eq 'tel'}
    <input type="tel" name="{$item}" class="form-control" value="{$value}" />

{else if $type eq 'boolean'}
    <div class="form-check form-switch">
        <input class="form-check-input" type="checkbox" role="switch" name="{$item}" {if $value eq 'TRUE'} checked{/if} value="TRUE" />
    </div>

{else if $type eq 'date'}
    <input type="date" class="form-control" name="{$item}" value="{convert_ldap_date($value)|date_format:"%Y-%m-%d"}"/>

{else if $type eq 'ad_date'}
    <input type="date" class="form-control" name="{$item}" value="{convert_ad_date($value)|date_format:"%Y-%m-%d"}"/>

{else if $type eq 'static_list' or $type eq 'list'}
    <select class="form-control" id="{$item}" name="{$item}">
        <option></option>
        {foreach $list as $lvalue}
        <option value="{$lvalue@key}"{if {$lvalue@key}=={$value}} selected{/if}>{$lvalue}</option>
        {/foreach}
    </select>

{else if $type eq 'bytes'}
    <input type="number" name="{$item}" class="form-control" value="{$value}" />

{else if $type eq 'dn_link'}
    <div class="dn_link_container">
    <input type="text" name="{$item}display" class="form-control" value="{get_attribute dn="{$value}" attribute="cn" ldap_url="{$ldap_params.ldap_url}" ldap_starttls="{$ldap_params.ldap_starttls}" ldap_binddn="{$ldap_params.ldap_binddn}" ldap_bindpw="{$ldap_params.ldap_bindpw}" ldap_filter="{$ldap_params.ldap_user_filter}" ldap_network_timeout="{$ldap_params.ldap_network_timeout}"}" />
    <input type="hidden" name="{$item}" value="{$value}" />
    <div class="z-3 list-group dn_link_suggestions"></div>
    </div>

{else}
    <input type="text" name={$item} class="form-control" value="{$value}" />

{/if}
</div>
