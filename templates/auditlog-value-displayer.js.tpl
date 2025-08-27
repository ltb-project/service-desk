{literal}
<script type="text/javascript">
$(document).ready(function(){

{/literal}
    var config_js = JSON.parse(atob("{$config_js}"));
{literal}

    $( ".display_dn_link" ).each(function( ) {

        var dn = $( this ).attr("data-dn");
        var b64_json_dn_values = $( this ).attr("data-dn-values");
        var json_dn_values = atob(b64_json_dn_values);
        var values = JSON.parse(json_dn_values);

        render = "";
        render += ldapDnlinkTypeRenderer(config_js, dn, values);
        $( this ).html(render);

    });

});
</script>
{/literal}
