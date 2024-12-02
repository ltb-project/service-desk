<!--table class="table table-striped table-hover table-condensed dataTable"-->
<thead>
    <tr>
        <th class="hidden-print" data-dt-order="disable">&nbsp;</th>
        {foreach $listing_columns as $item}<th{if $attributes_map.{$item}.dtorder} data-dt-order="{$attributes_map.{$item}.dtorder}"{/if}>{$msg_label_{$item}}</th>{/foreach}
    </tr>
</thead>
<tbody>
    {foreach $entries as $entry}
    <tr{if ! $listing_linkto|is_array} class="clickable" title="{$msg_displayentry}" {/if}>
        <th class="hidden-print">
            <a href="index.php?page=display&dn={$entry.dn|escape:'url'}&search={$search}"
                class="btn btn-info btn-sm{if $listing_linkto===false} hidden{/if}" role="button"
                title="{$msg_displayentry}">
                <i class="fa fa-fw fa-id-card"></i>
            </a>
            {if $display_unlock_button}
                {if $use_unlockcomment}
                    <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#commentModalunlock{$entry.dn|sha256}">
                        <i class="fa fa-fw fa-unlock mr-3"></i>
                        <i class="fa fa-fw fa-info-circle text-body-tertiary" title="{$msg_comment_needed}"></i>
                    </button>
                    <div>
                        {include 'comment.tpl' method=unlock page=unlockaccount title=$msg_unlockaccount dn=$entry.dn returnto=$page required=$use_unlockcomment_required}
                    </div>
                {else}
                    <a href="index.php?page=unlockaccount&dn={$entry.dn|escape:'url'}&returnto=searchlocked"
                       class="btn btn-success btn-sm" role="button" title="{$msg_unlockaccount}">
                        <i class="fa fa-fw fa-unlock"></i>
                    </a>
                {/if}
            {/if}
            {if $display_enable_button}
                {if $use_enablecomment}
                    <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#commentModalenable{$entry.dn|sha256}">
                        <i class="fa fa-fw fa-user-check mr-3"></i>
                        <i class="fa fa-fw fa-info-circle text-body-tertiary" title="{$msg_comment_needed}"></i>
                    </button>
                    <div>
                        {include 'comment.tpl' method=enable page=enableaccount title=$msg_enableaccount dn=$entry.dn returnto=$page required=$use_enablecomment_required}
                    </div>
                {else}
                    <a href="index.php?page=enableaccount&dn={$entry.dn|escape:'url'}&returnto=searchdisabled"
                       class="btn btn-success btn-sm" role="button" title="{$msg_enableaccount}">
                        <i class="fa fa-fw fa-user-check"></i>
                    </a>
                {/if}
            {/if}
        </th>
        {foreach $listing_columns as $column}
        <td>
            {if $display == "search"}
            {$attribute=$attributes_map.{$column}.attribute}
            {if ({$entry.$attribute.0})}
            {if $listing_linkto|is_array && in_array($column, $listing_linkto)}
            <a href="index.php?page=display&dn={$entry.dn|escape:'url'}&search={$search}" title="{$msg_displayentry}">
                {/if}
                {foreach $entry.{$attribute} as $value}
                {if $value@index eq 0}{continue}{/if}
                {$type=$attributes_map.{$column}.type}
                {include 'value_displayer.tpl' value=$value type=$type}
                {/foreach}
                {if $listing_linkto|is_array && in_array($column, $listing_linkto)}
            </a>
            {/if}
            {else}
            {if $show_undef}<i>{$msg_notdefined}</i>{else}&nbsp;{/if}
            {/if}
            {/if}
        </td>
        {/foreach}
    </tr>
    {/foreach}
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
            {if $display == "audit"}
            {if $column == "result" or $column == "action"}
            {$msg_{$event.$column}}
            {elseif $column == "user_dn"}
            {include 'value_displayer.tpl' value={$event.user_dn} type="dn_link"}
            {else}
            {$event.$column}
            {/if}
            {/if}
        </td>
        {/foreach}
        </tr>
        {/foreach}
</tbody>
<!--/table-->
