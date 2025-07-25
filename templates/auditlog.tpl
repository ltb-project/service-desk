<div class="alert alert-warning">
    {if $nb_events==0}{$msg_noeventsfound}{elseif $nb_events==1}{$nb_events} {$msg_eventfound}{else}{$nb_events} {$msg_eventsfound}{/if}
</div>

<table id="search-listing" class="table table-striped table-hover table-condensed dataTable">
    {include 'listing_audit_table.tpl'}
</table>
