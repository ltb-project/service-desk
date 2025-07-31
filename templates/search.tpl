<div id="entriesCount" class="alert alert-success">
</div>

<div id="size_limit_reached" class="alert alert-warning"><i class="fa fa-fw fa-exclamation-triangle"></i></div>

<table id="search-listing" class="table table-striped table-hover table-condensed dataTable">
    <thead>
        <tr>
            <th class="hidden-print" data-dt-order="disable">&nbsp;</th>
            {foreach $listing_columns as $item}<th{if $attributes_map.{$item}.dtorder} data-dt-order="{$attributes_map.{$item}.dtorder}"{/if}>{$msg_label_{$item}}</th>{/foreach}
        </tr>
    </thead>
    <tbody>
    </tbody>
</table>
