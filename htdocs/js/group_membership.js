$(document).ready(function(){

    console.debug("JS membership loaded");

    $('[data-component="membership"]').on("change", function (event) {
        console.debug("Change detected on membership switch button");
        var dn = $(this).data("dn");
        var groupdn = $(this).data("groupdn");
        var checked = $(this).checked;

        console.debug("CALL GROUP MEMBERSHIP WITH DN "+dn+" - GROUPDN "+groupdn+" - CHECKED "+checked);
    });

});
