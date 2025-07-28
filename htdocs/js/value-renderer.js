/* renderTemplate:
   INPUT:
     template: template id to use
     values: dictionary of values to replace
   OUTPUT:
     string containing the template with modified values
*/
function renderTemplate(template, values) {
    var t = document.querySelector('#' + template);
    var clone = t.cloneNode(true);
    var content = clone.innerHTML;
    for (key in values) {
        content = content.replaceAll('{' + key + '}', values[key])
    }
    return content;
}


/* get_datatables_params:
   INPUT:
     datatables_params: structure storing the input params sent by the backend
   OUTPUT:
     messages: associative array containing all messages for selected language
     listing_linkto: array or string containing the attribute key(s) for linking
     search_result_show_undefined: boolean. When true, show a specific message when there is no value for the current attribute, during search of multiple entries
     display_show_undefined: boolean. When true, show a specific message when there is no value for the current attribute, during an entry display
     truncate_value_after: integer. max length after which the string is truncated
     search: string, parameter named "search" of the http query
     js_date_specifiers: string, format of the date as specified in https://tc39.es/ecma262/multipage/numbers-and-dates.html#sec-date-time-string-format
*/
function get_datatables_params(datatables_params)
{

    var messages = datatables_params["messages"];

    var listing_linkto = datatables_params["listing_linkto"];

    var search_result_show_undefined = datatables_params["search_result_show_undefined"];
    search_result_show_undefined = search_result_show_undefined ? true : false;

    var display_show_undefined = datatables_params["display_show_undefined"];
    display_show_undefined = display_show_undefined ? true : false;

    var truncate_value_after = datatables_params["truncate_value_after"];
    if(!truncate_value_after)
    {
        truncate_value_after = 0;
    }

    var search = datatables_params["search"];
    if(!search)
    {
        search = "";
    }

    var js_date_specifiers = datatables_params["js_date_specifiers"];
    if(!js_date_specifiers)
    {
        js_date_specifiers = "";
    }
    var unlock = datatables_params["unlock"];
    var enable = datatables_params["enable"];

    return [
             messages, listing_linkto, search_result_show_undefined,
             display_show_undefined, truncate_value_after, search,
             js_date_specifiers, unlock, enable
           ];
}


/* datatableTypeRenderer:
   INPUT:
     data: attribute value (javascript array or string)
     type: origin of datatables operation when performing the rendering: filter, display, type, sort, undefined, custom
     row: full data source of the row
     meta: meta information about the row: row index, col index, settings
     datatables_params: structure storing all parameters for rendering
*/

function datatableTypeRenderer(data, type, row, meta, datatables_params)
{
    var render = "";

    [messages, listing_linkto, search_result_show_undefined,
     display_show_undefined, truncate_value_after, search,
     js_date_specifiers, unlock, enable ] =
            get_datatables_params(datatables_params);

    var dn = row[0];
    // column: column name, ie key of $attributes_map associative array (firstname, fullname, identifier,...)
    var column = Object.keys(datatables_params["column_types"])[meta.col];
    // column_type: attribute type, ie value of "type" key in $attributes_map associative array (text, tel, dn_link,...)
    var column_type = datatables_params["column_types"][column];


    render += ldapTypeRenderer(dn, messages, listing_linkto, search, unlock, enable, column, column_type, data, search_result_show_undefined, truncate_value_after, js_date_specifiers);

    return render;
}


/* ldapTypeRenderer:
   INPUT:
     dn: DN of current attribute
     messages: associative array containing all messages for selected language
     listing_linkto: array or string containing the attribute key(s) for linking
     search: string, parameter named "search" of the http query
     unlock: associative array containing unlock config parameters
     enable: associative array containing enable config parameters
     column: attribute name: key of attributes_map config parameter (identifier, mail,...)
     column_type: attribute type: value of "type" property in attributes_map config parameter (text, date, dn_link,...)
     data: attribute value (javascript array or string)
     show_undef: boolean. When true, show a specific message when there is no value for the current attribute
     truncate_value_after: integer. max length after which the string is truncated
     js_date_specifiers: string, format of the date as specified in https://tc39.es/ecma262/multipage/numbers-and-dates.html#sec-date-time-string-format
   OUTPUT:
     render: the html code rendering the value according to the type
*/

function ldapTypeRenderer(dn, messages, listing_linkto, search, unlock, enable, column, column_type, data, show_undef, truncate_value_after, js_date_specifiers )
{
    var render = "";

    // empty value, return immediately the value
    if(!data || (Array.isArray(data) && !data.length) )
    {
        if(show_undef)
        {
            var values = {
              "message": messages["notdefined"]
            };
            render += renderTemplate("undefined_template", values);
        }
        else
        {
            render += "&nbsp;";
        }
    }
    // value not empty
    else
    {
        // force data to be an array, for treating it as multivalue
        if(!Array.isArray(data))
        {
            data = [data];
        }

        data.forEach((value) =>
        {
            switch(column_type)
            {
                case "dn":
                    render += ldapDNTypeRenderer(dn, messages, listing_linkto, search, unlock, enable);
                    break;
                case "text":
                    render += ldapTextTypeRenderer(value, truncate_value_after);
                    break;
                case "mailto":
                    render += ldapMailtoTypeRenderer(value, truncate_value_after, messages);
                    break;
                case "tel":
                    render += ldapTelTypeRenderer(value, messages, truncate_value_after);
                    break;
                case "boolean":
                    render += ldapBooleanTypeRenderer(value, messages, truncate_value_after);
                    break;
                case "date":
                    render += ldapDateTypeRenderer(value, js_date_specifiers);
                    break;
                case "ad_date":
                    render += ldapADDateTypeRenderer(value, js_date_specifiers);
                    break;
                case "static_list":
                    render += ldapListTypeRenderer(value, truncate_value_after);
                    break;
                case "list":
                    render += ldapListTypeRenderer(value, truncate_value_after);
                    break;
                case "bytes":
                    render += ldapBytesTypeRenderer(value, truncate_value_after);
                    break;
                case "timestamp":
                    render += ldapTimestampTypeRenderer(value, js_date_specifiers);
                    break;
                case "dn_link":
                    render += ldapDNLinkTypeRenderer(value, truncate_value_after, search);
                    break;
                case "ppolicy_dn":
                    render += ldapPPolicyDNTypeRenderer(value, truncate_value_after);
                    break;
                case "address":
                    render += ldapAddressTypeRenderer(value);
                    break;
            }
        });
    }

    // if we are processing column "linkto", add an html link <a>
    if(Array.isArray(listing_linkto) && listing_linkto.includes(column))
    {
        var values = {
          "dn": encodeURIComponent(dn),
          "search": encodeURIComponent(search),
          "message": messages["displayentry"],
          "value": render
        };
        render = renderTemplate("linkto_template", values);
    }

    return render;
}


// Duplicate the commentbox template and fill it for each entry
function comment_displayer(method, page, messages, dn, returnto, required)
{
    var render = "";

    var values = {
      "form_id": method,
      "action": "index.php?page="+page,
      "dn": dn,
      "returnto": returnto,
      "modal_id": "commentModal" + method + sha256(dn),
      "modal_title": messages[page],
      "textarea_id": "comment-" + method,
      "textarea_placeholder": messages["insert_comment"],
      "textarea_required": required ? "required" : "",
      "message_close": messages["close"],
      "message_submit": messages["submit"]
    };
    render = renderTemplate("commentbox", values);

    return render;
}

function unlock_displayer(dn, messages, search, unlock, page)
{
    var use_unlockaccount          = unlock["use_unlockaccount"];
    var use_unlockcomment          = unlock["use_unlockcomment"];
    var use_unlockcomment_required = unlock["use_unlockcomment_required"];

    var render = "";

    if(use_unlockaccount && page == "searchlocked")
    {
        if(use_unlockcomment)
        {

            var comment_form = comment_displayer(
                                   "unlock",
                                   "unlockaccount",
                                   messages,
                                   dn,
                                   page,
                                   use_unlockcomment_required
                               );
            var values = {
              "target": "#commentModalunlock" + sha256(dn),
              "message_comment_needed": messages["comment_needed"],
              "comment_form": comment_form
            };
            render = renderTemplate("unlock_with_comment", values);

        }
        else
        {
            var values = {
              "target": "index.php?page=unlockaccount&dn=" +
                        encodeURIComponent(dn) +
                        "&returnto=searchlocked",
              "title": messages["unlockaccount"],
            };
            render = renderTemplate("unlock_without_comment", values);
        }
    }
    return render;
}

function enable_displayer(dn, messages, search, enable, page)
{
    var use_enableaccount = enable["use_enableaccount"];
    var use_enablecomment = enable["use_enablecomment"];
    var use_enablecomment_required = enable["use_enablecomment_required"];

    var render = "";

    if(use_enableaccount && page == "searchdisabled")
    {
        if(use_enablecomment)
        {
            var comment_form = comment_displayer(
                                   "enable",
                                   "enableaccount",
                                   messages,
                                   dn,
                                   page,
                                   use_enablecomment_required
                               );
            var values = {
              "target": "#commentModalenable" + sha256(dn),
              "message_comment_needed": messages["comment_needed"],
              "comment_form": comment_form
            };
            render = renderTemplate("enable_with_comment", values);
        }
        else
        {
            var values = {
              "target": "index.php?page=enableaccount&dn=" +
                        encodeURIComponent(dn) +
                        "&returnto=searchdisabled",
              "title": messages["enableaccount"],
            };
            render = renderTemplate("enable_without_comment", values);
        }
    }
    return render;
}

// Renderer for special first column "DN"
// This column displays all the actions possible for the user:
// display, unlock,...
function ldapDNTypeRenderer(dn, messages, listing_linkto, search, unlock, enable)
{

    var render = "";

    var get_params = new URLSearchParams(document.location.search);
    var page = get_params.get("page");

    var values = {
      "dn": encodeURIComponent(dn),
      "search": encodeURIComponent(search),
      "hidden": listing_linkto == false ? ' hidden' : '',
      "message": messages["displayentry"]
    };
    render = renderTemplate("ldapDNTypeRenderer", values);

    render += unlock_displayer(dn, messages, search, unlock, page);
    render += enable_displayer(dn, messages, search, enable, page);

    return render;
}

function ldapTextTypeRenderer(value, truncate_value_after)
{
    var render = "";

    var values = {
      "value": truncate(value, truncate_value_after)
    };
    render = renderTemplate("ldapTextTypeRenderer", values);

    return render;
}

function ldapMailtoTypeRenderer(value, truncate_value_after, messages)
{
    var render = "";

    mail_hexa = value.split("")
                     .map(c => "%" + c.charCodeAt(0).toString(16).padStart(2, "0"))
                     .join("");
    var values = {
      "mailto": mail_hexa,
      "message": messages['tooltip_emailto'],
      "value": truncate(value, truncate_value_after)
    };
    render = renderTemplate("ldapMailtoTypeRenderer", values);

    return render;
}

function ldapTelTypeRenderer(value, messages, truncate_value_after)
{
    var render = "";

    var values = {
      "tel": value,
      "message": messages['tooltip_phoneto'],
      "value": truncate(value, truncate_value_after)
    };
    render = renderTemplate("ldapTelTypeRenderer", values);

    return render;
}

function ldapBooleanTypeRenderer(value, messages, truncate_value_after)
{

    var render = "";
    var bool = "";

    if( value == "TRUE" )
    {
        bool = truncate(messages['true'], truncate_value_after);
    }

    if( value == "FALSE" )
    {
        bool = truncate(messages['false'], truncate_value_after);
    }

    var values = {
      "value": bool
    };
    render = renderTemplate("ldapBooleanTypeRenderer", values);

    return render;
}

function ldapDateTypeRenderer(value, js_date_specifiers)
{
    var render = "";
    var date = ldap2date.parse(value);
    var val = dayjs(date).format(js_date_specifiers);

    var values = {
      "value": val
    };
    render = renderTemplate("ldapDateTypeRenderer", values);

    return render;
}


function ldapADDateTypeRenderer(value, js_date_specifiers)
{
    var render = "";

    // divide by 10 000 000 to get seconds
    winSecs = parseInt( value / 10000000 );
    // 1.1.1600 -> 1.1.1970 difference in seconds
    unixTimestamp = winSecs - 11644473600;
    // get js date object from unixtimestamp in ms
    date = new Date(unixTimestamp * 1000);
    val = dayjs(date).format(js_date_specifiers);

    var values = {
      "value": val
    };
    render = renderTemplate("ldapADDateTypeRenderer", values);

    return render;
}

function ldapListTypeRenderer(value, truncate_value_after)
{
    var render = "";

    var values = {
      "value": truncate(value, truncate_value_after)
    };
    render = renderTemplate("ldapListTypeRenderer", values);

    return render;
}

function ldapBytesTypeRenderer(value, truncate_value_after)
{
    var render = "";
    bytes = parseFloat(value);
    var result;

    var arBytes = [
                      {
                          "UNIT": "TB",
                          "VALUE": Math.pow(1024, 4)
                      },
                      {
                          "UNIT": "GB",
                          "VALUE": Math.pow(1024, 3)
                      },
                      {
                          "UNIT": "MB",
                          "VALUE": Math.pow(1024, 2)
                      },
                      {
                          "UNIT": "KB",
                          "VALUE": 1024
                      },
                      {
                          "UNIT": "B",
                          "VALUE": 1
                      }
                  ];

    for ( arItem of arBytes ) {
        if(bytes >= arItem["VALUE"])
        {
            result = bytes / arItem["VALUE"];
            result = String(Math.round(result * 100) / 100).replace(".",",") + " " + arItem["UNIT"];
            break;
        }
    }

    var values = {
      "value": result
    };
    render = renderTemplate("ldapBytesTypeRenderer", values);

    return render;
}

function ldapTimestampTypeRenderer(value, js_date_specifiers)
{
    var render = "";
    // timestamp is considered in seconds, converting to milliseconds
    var result = dayjs(value * 1000).format(js_date_specifiers);

    var values = {
      "value": result
    };
    render = renderTemplate("ldapTimestampTypeRenderer", values);

    return render;
}

function ldapDNLinkTypeRenderer(value, truncate_value_after, search)
{
    var render = "";
    var dn = "";
    var attr_values = [];
    var truncated_attr_values = [];
    var vals = "";
    if(Array.isArray(value))
    {
        if(value.length >= 1)
        {
            dn = value[0];
        }
        if(value.length >= 2)
        {
            attr_values = value[1];
        }
    }

    if(Array.isArray(attr_values))
    {
        for ( attr_value of attr_values )
        {
            truncated_attr_values.push(truncate(attr_value, truncate_value_after));
        }
        vals = truncated_attr_values.join(", ");
    }
    else
    {
        vals = truncate(attr_values, truncate_value_after);
    }

    var values = {
      "dn": encodeURIComponent(dn),
      "search": encodeURIComponent(search),
      "values": vals
    };
    render = renderTemplate("ldapDNLinkTypeRenderer", values);

    return render;
}

function ldapPPolicyDNTypeRenderer(value, truncate_value_after)
{
    var render = "";
    var dn = "";
    var linked_attr_vals = [];
    var truncated_attr_values = [];
    var vals = "";
    if(Array.isArray(value))
    {
        if(value.length >= 1)
        {
            dn = value[0];
        }
        if(value.length >= 2)
        {
            linked_attr_vals = value[1];
        }
    }

    if(Array.isArray(linked_attr_vals))
    {
        for ( attr_value of linked_attr_vals )
        {
            truncated_attr_values.push(truncate(attr_value, truncate_value_after));
        }
        vals = truncated_attr_values.join(", ");
    }
    else
    {
        vals = truncate(linked_attr_vals, truncate_value_after);
    }

    var values = {
      "values": vals
    };
    render = renderTemplate("ldapPPolicyDNTypeRenderer", values);

    return render;
}

function ldapAddressTypeRenderer(value)
{
    var render = "";
    var result = "";
    address_parts = value.split('$');
    for( address_part of address_parts )
    {
        result += address_part + '<br />';
    }

    var values = {
      "values": result
    };
    render = renderTemplate("ldapAddressTypeRenderer", values);

    return render;
}

function truncate(string, length)
{
    result = string;
    if(length && string.length > length)
    {
        result = string.substring(0, length) + "...";
    }

    return result;
}

function print_all_results(e, dt, node, config, cb, autoPrint)
{
    config.title = $(document).attr('title');
    config.header = true;
    config.exportOptions = {
        columns: ':not(.hidden-print)',
    };
    config.autoPrint = autoPrint;
    var table = new DataTable('table.dataTable');
    var pageLength = table.page.info().length;
    var pageNumber = table.page.info().page;

    var totalRows = table.page.info().recordsTotal;
    table.page.len(totalRows).draw(); // Draw table with all records

    table.on('draw', function() {
        var additionalParams = function() {
            // Once loaded, redraw at current page with page length
            table.off('draw');
            table.page.len(pageLength).page(pageNumber).draw('page');
        };
        // Draw table with all records
        DataTable.ext.buttons.print.action(e, dt, node, config, additionalParams);
    });
}

function updateEntriesCount(settings, datatables_params, page)
{
    var table = new DataTable('table.dataTable');
    var totalRows = table.page.info().recordsTotal;
    var messages = datatables_params["messages"];

    var msg = "";

    var title = "";
    var titleKey = "title_" + page;
    if( titleKey in messages)
    {
        title = messages[titleKey] + " ";
    }

    var nbEntriesMsg = "";
    if( totalRows == 0 )
    {
        msg = title + messages["noentriesfound"];
    }
    else if( totalRows == 1 )
    {
        nbEntriesMsg = messages["entryfound"];
        msg = title + totalRows + ' ' + nbEntriesMsg;
    }
    else
    {
        nbEntriesMsg = messages["entriesfound"];
        msg = title + totalRows + ' ' + nbEntriesMsg;
    }

    $('#entriesCount').html(msg);
}

function datatableManageError(datatables_params, error)
{
    var messages = datatables_params["messages"];
    if( error.match(/size_limit_reached/ ))
    {
        $('#size_limit_reached').html(messages["sizelimit"]);
        $('#size_limit_reached').show();
    }
    else
    {
        // display error to user
        alert(error);
    }
}

// Load comment box when available
function loadCommentBox(boxid, method, page, messages, dn, returnto, required)
{
    var commentbox = $("#" + boxid);

    if(commentbox.length)
    {
        commentbox.html(
            comment_displayer(method, page, messages, dn, returnto, required)
        );
    }
}

function renderUserAttributesList(datatables_params, targetDN, column, column_type, attribute, faclass, data)
{
    render = "";

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
        var values = {
          "faclass": faclass,
          "message": messages['label_' + column],
          "value": ldapTypeRenderer(
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
                                   )
        };
        render = renderTemplate("display_user_attributes_list", values);
    }
    else
    {
        // don't display anything
    }
    return render;
}

function renderStatusAttributesList(datatables_params, targetDN, column, column_type, attribute, faclass, data)
{
    render = "";

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
        var values = {
          "message": messages['label_' + column],
          "value": ldapTypeRenderer(
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
                                   )
        };
        render = renderTemplate("display_status_attributes_list", values);
    }
    else
    {
        // don't display anything
    }
    return render;
}
