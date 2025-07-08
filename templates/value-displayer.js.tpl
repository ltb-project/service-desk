{literal}
<script type="text/javascript">
$(document).ready(function(){

{/literal}
    var datatables_params = JSON.parse(atob("{$datatables_params}"));
{literal}

    function display_ldap_value(element)
    {
        var render = "";

        var entry = JSON.parse(atob(element.attr("data-entry")));
        var attribute = element.attr("data-attribute");
        var column = element.attr("data-item");
        var column_type = element.attr("data-type");
        var dn = entry["dn"];
        var data = entry[attribute];

        [messages, listing_linkto, show_undef, truncate_value_after, search,
         js_date_specifiers, unlock, enable ] =
            get_datatables_params(datatables_params);

        // overload truncate_value_after to always display complete values
        truncate_value_after = 10000;

        render += ldapTypeRenderer(dn, messages, listing_linkto, search, unlock, enable, column, column_type, data, show_undef, truncate_value_after, js_date_specifiers);

        element.html(render);
    }

    $( ".value_displayer" ).each(function() {
        display_ldap_value( $( this ) );
    });

});
</script>
{/literal}
