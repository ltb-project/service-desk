<thead>
    <tr>
        <th class="hidden-print" data-dt-order="disable">&nbsp;</th>
        {foreach $listing_columns as $item}<th{if $attributes_map.{$item}.dtorder} data-dt-order="{$attributes_map.{$item}.dtorder}"{/if}>{$msg_label_{$item}}</th>{/foreach}
    </tr>
</thead>
<tbody>
        {foreach $events as $event}
        <tr{if ! $listing_linkto|is_array} class="clickable" title="{$msg_displayentry}" {/if}>
            <th class="hidden-print">
                <a href="index.php?page=display&dn={$event.user_dn|escape:'url'}&search={$search}"
                    class="btn btn-info btn-sm{if $listing_linkto===false} hidden{/if}" role="button"
                    title="{$msg_displayentry}">
                    <i class="fa fa-fw fa-id-card"></i>
                </a>
            </th>
            {foreach $listing_columns as $column}
            <td>
            {if $column == "result" or $column == "action"}
            {$msg_{$event.$column}}
            {elseif $column == "user_dn"}
            <div class="display_dn_link" data-dn='{$event.user_dn}' ></div>
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
