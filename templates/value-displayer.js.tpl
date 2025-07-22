{literal}
<script type="text/javascript">
$(document).ready(function(){

{/literal}
    var datatables_params = JSON.parse(atob("{$datatables_params}"));
{literal}

    // If we are on display page, call the API to get the attributes and display them
    user_attributes = $( "#user_attributes" );

    if(user_attributes.length)
    {

        targetDN = user_attributes.attr("data-dn");
        $.post( "index.php?page=search-api",
                { action: "display",
                  start: "0",
                  length: "1",
                  search_query: "",
                  targetDN: targetDN
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
                                    datatables_params,
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
                                    datatables_params,
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

});
</script>
{/literal}
