{literal}
<script type="text/javascript">
$(document).ready(function(){

{/literal}
    var config_js = JSON.parse(atob("{$config_js}"));
{literal}

    // If we are on display page, call the API to get the attributes and display them
    user_attributes = $( "#user_attributes" );

    if(user_attributes.length)
    {

        targetDN = user_attributes.attr("data-dn");
        $.post( "index.php",
                { action: "display",
                  start: "0",
                  length: "1",
                  search_query: "",
                  targetDN: targetDN,
                  apiendpoint: "search-api"
                }
              )
              .done(function( apiResponse ) {

                  var i = 0; // index of attribute
                             // attribute values are returned in this order:
                             // [ user_attributes, status_attributes ]

                  var apiResponseJSON = JSON.parse(apiResponse);

                  $( "#user_attributes tr" ).each(function( ) {

                      var render          = "";
                      var column          = $( this ).attr("data-item");
                      var column_type     = $( this ).attr("data-type");
                      var attribute       = $( this ).attr("data-attribute");
                      var faclass         = $( this ).attr("data-faclass");
                      var data            = apiResponseJSON["data"][0][(i+1)];

                      render += renderUserAttributesList(
                                    config_js,
                                    targetDN,
                                    column,
                                    column_type,
                                    attribute,
                                    faclass,
                                    data
                                );

                      $( this ).html(render);
                      i++;

                   });

                   $( "#status_attributes tr" ).each(function( ) {

                      var render = "";
                      var column          = $( this ).attr("data-item");
                      var column_type     = $( this ).attr("data-type");
                      var attribute       = $( this ).attr("data-attribute");
                      var faclass         = $( this ).attr("data-faclass");
                      var data            = apiResponseJSON["data"][0][(i+1)];

                      render += renderStatusAttributesList(
                                    config_js,
                                    targetDN,
                                    column,
                                    column_type,
                                    attribute,
                                    faclass,
                                    data
                                );

                      $( this ).html(render);
                      i++;

                   });

              });
    }

    // If we are on update page, directly get the values in the page and render them
    $( ".display_value" ).each(function( ) {

        var render = "";
        var column      = $( this ).attr("data-item");
        var column_type = $( this ).attr("data-type");
        var attribute   = $( this ).attr("data-attribute");
        var faclass     = $( this ).attr("data-faclass");
        var data        = $( this ).attr("data-value");
        var targetDN    = $( this ).attr("data-dn");

        // picking display_show_undefined as show_undef parameter
        var display_show_undefined = config_js["display_show_undefined"];
        display_show_undefined = display_show_undefined ? true : false;

        render += ldapTypeRenderer(
                                     config_js,
                                     targetDN,
                                     column,
                                     column_type,
                                     data,
                                     display_show_undefined
                                 );

        $( this ).html(render);

    });

});
</script>
{/literal}
