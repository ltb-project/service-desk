
<div id="entriesCount" class="alert alert-success">
    {$msg_title_search} {$nb_entries} {if $nb_entries==1}{$msg_entryfound}{else}{$msg_entriesfound}{/if}
</div>


{if {$size_limit_reached}}
<div class="alert alert-warning"><i class="fa fa-fw fa-exclamation-triangle"></i> {$msg_sizelimit}</div>
{/if}

<table id="search-listing" class="table table-striped table-hover table-condensed dataTable">
    {include 'listing_table.tpl' display="search"}
</table>
