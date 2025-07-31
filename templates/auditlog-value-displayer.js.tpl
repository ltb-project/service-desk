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

        render = "";
        render += ldapDnlinkTypeRenderer(config_js, dn, dn);
        $( this ).html(render);

    });

});
</script>
{/literal}
