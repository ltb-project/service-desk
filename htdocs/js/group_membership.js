$(document).ready(function(){

    console.debug("JS membership loaded");
    var userdn = $("div#user-dn").data("user-dn");
    console.debug("USER DN "+userdn);

    $(document).on("change", 'input[data-component="membership"]', function (event) {
        console.debug("Change detected on membership switch button");
        var dn = $(this).data("dn");
        var checked = $(this).is(':checked');

        console.debug("CALL GROUP MEMBERSHIP WITH DN "+dn+" - USERDN "+userdn+" - CHECKED "+checked);
    });

});
