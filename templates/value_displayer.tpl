{if $type eq 'text'}
    {$value|truncate:{$truncate_value_after}}<br />
{/if}

{if $type eq 'mailto'}
    {mailto address="{$value|escape:"html"}" encode="hex" text="{$value|truncate:{$truncate_value_after}}" extra='class="link-email" title="'|cat:$msg_tooltip_emailto:'"'}<br />
{/if}

{if $type eq 'tel'}
    <a href="tel:{$value}" rel="nofollow" class="link-phone" title="{$msg_tooltip_phoneto}">{$value|truncate:{$truncate_value_after}}</a><br />
{/if}

{if $type eq 'boolean'}
    {if $value=="TRUE"}{$msg_true|truncate:{$truncate_value_after}}<br />{/if}
    {if $value=="FALSE"}{$msg_false|truncate:{$truncate_value_after}}<br />{/if}
{/if}

{if $type eq 'date'}
    {convert_ldap_date($value)|date_format:{$date_specifiers}|truncate:{$truncate_value_after}}<br />
{/if}

{if $type eq 'ad_date'}
    {convert_ad_date($value)|date_format:{$date_specifiers}|truncate:{$truncate_value_after}}<br />
{/if}

{if $type eq 'static_list' or $type eq 'list'}
    {$value|truncate:{$truncate_value_after}}<br />
{/if}

{if $type eq 'bytes'}
    {convert_bytes($value)|truncate:{$truncate_value_after}}<br />
{/if}

{if $type eq 'timestamp'}
    {$value|date_format:{$date_specifiers}|truncate:{$truncate_value_after}}<br />
{/if}

{if $type eq 'dn_link'}
    {assign var="link" value="{{get_attribute dn="{$value}" attribute="cn" ldap_url="{$ldap_params.ldap_url}" ldap_starttls="{$ldap_params.ldap_starttls}" ldap_binddn="{$ldap_params.ldap_binddn}" ldap_bindpw="{$ldap_params.ldap_bindpw}" ldap_filter="{$ldap_params.ldap_user_filter}" ldap_network_timeout="{$ldap_params.ldap_network_timeout}"}|truncate:{$truncate_value_after}}"}
    {if $link}
    <a href="index.php?page=display&dn={$value|escape:'url'}&search={$search}">{$link}</a><br />
    {/if}
{/if}

{if $type eq 'ppolicy_dn'}
    {assign var="name" value="{{get_attribute dn="{$value}" attribute="{$ldap_params.ldap_ppolicy_name_attribute}" ldap_url="{$ldap_params.ldap_url}" ldap_starttls="{$ldap_params.ldap_starttls}" ldap_binddn="{$ldap_params.ldap_binddn}" ldap_bindpw="{$ldap_params.ldap_bindpw}" ldap_filter="{$ldap_params.ldap_ppolicy_filter}" ldap_network_timeout="{$ldap_params.ldap_network_timeout}"}|truncate:{$truncate_value_after}}"}
    {if $name}{$name}<br />{/if}
{/if}

{if $type eq 'address'}
    {foreach split_value($value,'$') as $fragment}
    {$fragment|truncate:{$truncate_value_after}}<br />
    {/foreach}
{/if}
