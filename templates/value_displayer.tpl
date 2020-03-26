{if $type eq 'text'}
    {$value|truncate:{$truncate_value_after}}<br />
{/if}

{if $type eq 'mailto'}
    {mailto address="{$value|escape:"html"}" encode="javascript" text="{$value|truncate:{$truncate_value_after}}" extra='class="link-email" title="'|cat:$msg_tooltip_emailto:'"'}<br />
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

{if $type eq 'list'}
    {$value|truncate:{$truncate_value_after}}<br />
{/if}
