{literal}
<script type="text/javascript">
$(document).ready(function(){

{/literal}
    var config_js = JSON.parse(atob("{$config_js}"));
{literal}

    $( ".display_dn_link" ).each(function( ) {

        var b64_json_dn = $( this ).attr("data-dn");
        var json_dn = atob(b64_json_dn);
        var dn = JSON.parse(json_dn);

        [messages, listing_linkto, search_result_show_undefined,
         display_show_undefined, truncate_value_after, search,
         js_date_specifiers, unlock, enable ] =
            get_config_js(config_js);

        render = "";
        render += ldapDnlinkTypeRenderer(dn, truncate_value_after, search);
        $( this ).html(render);

    });

});
</script>
{/literal}
