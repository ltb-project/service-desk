{literal}
<script type="text/javascript">
$(document).ready(function(){

{/literal}
    var datatables_params = JSON.parse(atob("{$datatables_params}"));
{literal}

    // If we are on display page, call the API to get the attributes and display them
    display_dn_link = $( "#display_dn_link" );

    if(display_dn_link.length)
    {
        var json_dn = display_dn_link.attr("data-dn");
        var dn = JSON.parse(json_dn);

        [messages, listing_linkto, search_result_show_undefined,
         display_show_undefined, truncate_value_after, search,
         js_date_specifiers, unlock, enable ] =
            get_datatables_params(datatables_params);

        render = "";
        render += ldapDNLinkTypeRenderer(dn, truncate_value_after, search);
        display_dn_link.html(render);

    }

});
</script>
{/literal}
