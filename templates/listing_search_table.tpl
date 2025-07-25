<thead>
    <tr>
        <th class="hidden-print" data-dt-order="disable">&nbsp;</th>
        {foreach $listing_columns as $item}<th{if $attributes_map.{$item}.dtorder} data-dt-order="{$attributes_map.{$item}.dtorder}"{/if}>{$msg_label_{$item}}</th>{/foreach}
    </tr>
</thead>
<tbody>
</tbody>
