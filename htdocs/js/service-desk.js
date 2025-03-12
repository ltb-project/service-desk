$(document).ready(function(){
    $("input.fake-password").one("click", function(event) {
        $(this).removeAttr("placeholder");
        $(this).css("-webkit-text-security","circle");
    });

    $(".dn_link input").on("keyup", function(event) {
        // Minimal search characters
        if ( $(".dn_link input").val().length > 2 ) {
            // TODO Ajax call to get search results as JSON data
            // TODO Display results
        }
    });
});
