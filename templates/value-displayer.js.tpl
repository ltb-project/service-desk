{literal}
<script type="text/javascript">
$(document).ready(function(){

{/literal}
    var datatables_params = JSON.parse(atob("{$datatables_params}"));
{literal}

    // For each LDAP entry, call the API to get the attributes and display them
    $( "#user_attributes" ).each(function() {

        targetDN = $( this ).attr("data-dn");
        $.post( "index.php?page=search-api",
                { action: "display",
                  start: "0",
                  length: "1",
                  search_query: "",
                  targetDN: targetDN
                }
              )
              .done(function( apiResponse ) {
                  $( "#user_attributes tr" ).each(function( i ) {

                      var render = "";
                      var column          = $( this ).attr("data-item");
                      var column_type     = $( this ).attr("data-type");
                      var attribute       = $( this ).attr("data-attribute");
                      var faclass         = $( this ).attr("data-faclass");
                      var apiResponseJSON = JSON.parse(apiResponse);
                      var data            = apiResponseJSON["data"][0][(i+1)];

                      [messages, listing_linkto, search_result_show_undefined,
                       display_show_undefined, truncate_value_after, search,
                       js_date_specifiers, unlock, enable ] =
                          get_datatables_params(datatables_params);

                      // overload truncate_value_after to always display complete values
                      truncate_value_after = 10000;
                      show_undef = display_show_undefined;

                      if( show_undef || ( typeof data === 'string' && data ) || ( Array.isArray(data) && data.length != 0 ) )
                      {
                          // display value only if not empty
                          // or if the conf says to show undefined values
                          render += '<th class="text-center">' + "\n";
                          render += '  <i class="fa fa-fw fa-' + faclass + '"></i>' + "\n";
                          render += '</th>' + "\n";
                          render += '<th class="d-none d-sm-table-cell">' + "\n";
                          render += messages['label_' + column] + "\n";
                          render += '</th>' + "\n";
                          render += '<td class="value_displayer">' + "\n";
                          render += ldapTypeRenderer(
                                                        targetDN,
                                                        messages,
                                                        listing_linkto,
                                                        search,
                                                        unlock,
                                                        enable,
                                                        column,
                                                        column_type,
                                                        data,
                                                        show_undef,
                                                        truncate_value_after,
                                                        js_date_specifiers
                                                    );
                          render += "\n";
                          render += '</td>' + "\n";
                      }
                      else
                      {
                          // don't display anything
                      }


                      $( this ).html(render);

                   });
              });
        // display_ldap_value( $( this ) );
    });

});
</script>
{/literal}
