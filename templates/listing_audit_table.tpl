<thead>
    <tr>
        <th class="hidden-print" data-dt-order="disable">&nbsp;</th>
        {foreach $listing_columns as $item}<th{if $attributes_map.{$item}.dtorder} data-dt-order="{$attributes_map.{$item}.dtorder}"{/if}>{$msg_label_{$item}}</th>{/foreach}
    </tr>
</thead>
<tbody>
        {foreach $events as $event}
        <tr>
            <th class="hidden-print">
                {if $listing_linkto!==false && $event.user_dn_values}
                <a href="index.php?page=display&dn={$event.user_dn|escape:'url'}&search={$search}"
                    class="btn btn-outline-primary btn-sm" role="button"
                    title="{$msg_displayentry}">
                    <i class="fa fa-fw fa-id-card"></i>
                </a>
                {/if}
            </th>
            {foreach $listing_columns as $column}
            <td>
            {if $column == "result" or $column == "action"}
            {$msg_{$event.$column}}
            {elseif $column == "user_dn"}
            {if $event.user_dn_values}
            <div class="display_dn_link" data-dn='{$event.user_dn}' data-dn-values='{$event.user_dn_values}'></div>
            {else}
            <i title="{$event.user_dn}">{$event.user_dn|truncate:15}</i>
            {/if}
            {elseif $column == "date"}
            {$event.date|date_format:{$date_specifiers}}
            {else}
            {$event.$column}
            {/if}
        </td>
        {/foreach}
        </tr>
        {/foreach}
</tbody>
