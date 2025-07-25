{literal}
<script type="text/javascript">
$(document).ready(function(){

{/literal}
    var datatables_params = JSON.parse(atob("{$datatables_params}"));
{literal}

    $( ".display_dn_link" ).each(function( ) {

        var b64_json_dn = $( this ).attr("data-dn");
        var json_dn = atob(b64_json_dn);
        var dn = JSON.parse(json_dn);

        [messages, listing_linkto, search_result_show_undefined,
         display_show_undefined, truncate_value_after, search,
         js_date_specifiers, unlock, enable ] =
            get_datatables_params(datatables_params);

        render = "";
        render += ldapDNLinkTypeRenderer(dn, truncate_value_after, search);
        $( this ).html(render);

    });

});
</script>
{/literal}
