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


/* get_config_js:
   INPUT:
     config_js: structure storing the input params sent by the backend
   OUTPUT:
     messages: associative array containing all messages for selected language
     listing_linkto: array or string containing the attribute key(s) for linking
     search_result_show_undefined: boolean. When true, show a specific message when there is no value for the current attribute, during search of multiple entries
     display_show_undefined: boolean. When true, show a specific message when there is no value for the current attribute, during an entry display
     truncate_value_after: integer. max length after which the string is truncated
     search: string, parameter named "search" of the http query
     js_date_specifiers: string, format of the date as specified in https://tc39.es/ecma262/multipage/numbers-and-dates.html#sec-date-time-string-format
     unlock: associative array containing unlock config parameters
     enable: associative array containing enable config parameters
*/
function get_config_js(config_js)
{

    var messages = config_js["messages"];

    var listing_linkto = config_js["listing_linkto"];

    var search_result_show_undefined = config_js["search_result_show_undefined"];
    search_result_show_undefined = search_result_show_undefined ? true : false;

    var display_show_undefined = config_js["display_show_undefined"];
    display_show_undefined = display_show_undefined ? true : false;

    var truncate_value_after = config_js["truncate_value_after"];
    if(!truncate_value_after)
    {
        truncate_value_after = 0;
    }

    var search = config_js["search"];
    if(!search)
    {
        search = "";
    }

    var js_date_specifiers = config_js["js_date_specifiers"];
    if(!js_date_specifiers)
    {
        js_date_specifiers = "";
    }
    var unlock = config_js["unlock"];
    var enable = config_js["enable"];

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
     config_js: structure storing all parameters for rendering
*/

function datatableTypeRenderer(data, type, row, meta, config_js)
{
    var render = "";

    var dn = row[0];
    // column: column name, ie key of $attributes_map associative array (firstname, fullname, identifier,...)
    var column = Object.keys(config_js["column_types"])[meta.col];
    // column_type: attribute type, ie value of "type" key in $attributes_map associative array (text, tel, dn_link,...)
    var column_type = config_js["column_types"][column];

    // picking search_result_show_undefined for show_undef parameter
    var search_result_show_undefined = config_js["search_result_show_undefined"];
    search_result_show_undefined = search_result_show_undefined ? true : false;


    render += ldapTypeRenderer(config_js, dn, column, column_type, data, search_result_show_undefined);

    return render;
}


/* ldapTypeRenderer:
   INPUT:
     config_js: structure storing the input params sent by the backend
     dn: DN of current attribute
     column: attribute name: key of attributes_map config parameter (identifier, mail,...)
     column_type: attribute type: value of "type" property in attributes_map config parameter (text, date, dn_link,...)
     data: attribute value (javascript array or string)
     show_undef: boolean. When true, show a specific message when there is no value for the current attribute
   OUTPUT:
     render: the html code rendering the value according to the type
*/

function ldapTypeRenderer(config_js, dn, column, column_type, data, show_undef)
{
    var render = "";

    var messages = config_js["messages"];
    var listing_linkto = config_js["listing_linkto"];
    var search = config_js["search"];
    if(!search)
    {
        search = "";
    }

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
                    render += ldapDNTypeRenderer(config_js, dn, value);
                    break;
                case "text":
                    render += ldapTextTypeRenderer(config_js, dn, value);
                    break;
                case "mailto":
                    render += ldapMailtoTypeRenderer(config_js, dn, value);
                    break;
                case "tel":
                    render += ldapTelTypeRenderer(config_js, dn, value);
                    break;
                case "boolean":
                    render += ldapBooleanTypeRenderer(config_js, dn, value);
                    break;
                case "date":
                    render += ldapDateTypeRenderer(config_js, dn, value);
                    break;
                case "ad_date":
                    render += ldapADDateTypeRenderer(config_js, dn, value);
                    break;
                case "static_list":
                    render += ldapListTypeRenderer(config_js, dn, value);
                    break;
                case "list":
                    render += ldapListTypeRenderer(config_js, dn, value);
                    break;
                case "bytes":
                    render += ldapBytesTypeRenderer(config_js, dn, value);
                    break;
                case "timestamp":
                    render += ldapTimestampTypeRenderer(config_js, dn, value);
                    break;
                case "dn_link":
                    render += ldapDNLinkTypeRenderer(config_js, dn, value);
                    break;
                case "ppolicy_dn":
                    render += ldapPPolicyDNTypeRenderer(config_js, dn, value);
                    break;
                case "address":
                    render += ldapAddressTypeRenderer(config_js, dn, value);
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
function ldapDNTypeRenderer(config_js, dn, value)
{

    var render = "";

    [messages, listing_linkto, search_result_show_undefined,
     display_show_undefined, truncate_value_after, search,
     js_date_specifiers, unlock, enable ] =
            get_config_js(config_js);

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

function ldapTextTypeRenderer(config_js, dn, value)
{
    var render = "";

    [messages, listing_linkto, search_result_show_undefined,
     display_show_undefined, truncate_value_after, search,
     js_date_specifiers, unlock, enable ] =
            get_config_js(config_js);

    var values = {
      "value": truncate(value, truncate_value_after)
    };
    render = renderTemplate("ldapTextTypeRenderer", values);

    return render;
}

function ldapMailtoTypeRenderer(config_js, dn, value)
{
    var render = "";

    [messages, listing_linkto, search_result_show_undefined,
     display_show_undefined, truncate_value_after, search,
     js_date_specifiers, unlock, enable ] =
            get_config_js(config_js);

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

function ldapTelTypeRenderer(config_js, dn, value)
{
    var render = "";

    [messages, listing_linkto, search_result_show_undefined,
     display_show_undefined, truncate_value_after, search,
     js_date_specifiers, unlock, enable ] =
            get_config_js(config_js);

    var values = {
      "tel": value,
      "message": messages['tooltip_phoneto'],
      "value": truncate(value, truncate_value_after)
    };
    render = renderTemplate("ldapTelTypeRenderer", values);

    return render;
}

function ldapBooleanTypeRenderer(config_js, dn, value)
{

    var render = "";
    var bool = "";

    [messages, listing_linkto, search_result_show_undefined,
     display_show_undefined, truncate_value_after, search,
     js_date_specifiers, unlock, enable ] =
            get_config_js(config_js);

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

function ldapDateTypeRenderer(config_js, dn, value)
{
    var render = "";

    [messages, listing_linkto, search_result_show_undefined,
     display_show_undefined, truncate_value_after, search,
     js_date_specifiers, unlock, enable ] =
            get_config_js(config_js);

    var date = ldap2date.parse(value);
    var val = dayjs(date).format(js_date_specifiers);

    var values = {
      "value": val
    };
    render = renderTemplate("ldapDateTypeRenderer", values);

    return render;
}


function ldapADDateTypeRenderer(config_js, dn, value)
{
    var render = "";

    [messages, listing_linkto, search_result_show_undefined,
     display_show_undefined, truncate_value_after, search,
     js_date_specifiers, unlock, enable ] =
            get_config_js(config_js);

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

function ldapListTypeRenderer(config_js, dn, value)
{
    var render = "";

    [messages, listing_linkto, search_result_show_undefined,
     display_show_undefined, truncate_value_after, search,
     js_date_specifiers, unlock, enable ] =
            get_config_js(config_js);

    var values = {
      "value": truncate(value, truncate_value_after)
    };
    render = renderTemplate("ldapListTypeRenderer", values);

    return render;
}

function ldapBytesTypeRenderer(config_js, dn, value)
{
    var render = "";
    var result;

    [messages, listing_linkto, search_result_show_undefined,
     display_show_undefined, truncate_value_after, search,
     js_date_specifiers, unlock, enable ] =
            get_config_js(config_js);

    bytes = parseFloat(value);

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

function ldapTimestampTypeRenderer(config_js, dn, value)
{
    var render = "";

    [messages, listing_linkto, search_result_show_undefined,
     display_show_undefined, truncate_value_after, search,
     js_date_specifiers, unlock, enable ] =
            get_config_js(config_js);

    // timestamp is considered in seconds, converting to milliseconds
    var result = dayjs(value * 1000).format(js_date_specifiers);

    var values = {
      "value": result
    };
    render = renderTemplate("ldapTimestampTypeRenderer", values);

    return render;
}

function ldapDNLinkTypeRenderer(config_js, dn, value)
{
    var render = "";

    [messages, listing_linkto, search_result_show_undefined,
     display_show_undefined, truncate_value_after, search,
     js_date_specifiers, unlock, enable ] =
            get_config_js(config_js);

    var dnlink = "";
    var attr_values = [];
    var truncated_attr_values = [];
    var vals = "";
    if(Array.isArray(value))
    {
        if(value.length >= 1)
        {
            dnlink = value[0];
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
      "dn": encodeURIComponent(dnlink),
      "search": encodeURIComponent(search),
      "values": vals
    };
    render = renderTemplate("ldapDNLinkTypeRenderer", values);

    return render;
}

function ldapPPolicyDNTypeRenderer(config_js, dn, value)
{
    var render = "";

    [messages, listing_linkto, search_result_show_undefined,
     display_show_undefined, truncate_value_after, search,
     js_date_specifiers, unlock, enable ] =
            get_config_js(config_js);

    var dnppolicy = "";
    var linked_attr_vals = [];
    var truncated_attr_values = [];
    var vals = "";
    if(Array.isArray(value))
    {
        if(value.length >= 1)
        {
            dnppolicy = value[0];
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

function ldapAddressTypeRenderer(config_js, dn, value)
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

function updateEntriesCount(settings, config_js, page)
{
    var table = new DataTable('table.dataTable');
    var totalRows = table.page.info().recordsTotal;
    var messages = config_js["messages"];

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

function datatableManageError(config_js, error)
{
    var messages = config_js["messages"];
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

function renderUserAttributesList(config_js, targetDN, column, column_type, attribute, faclass, data)
{
    render = "";

    // overload truncate_value_after to always display complete values
    truncate_value_after = 10000;
    // picking display_show_undefined as show_undef parameter
    var display_show_undefined = config_js["display_show_undefined"];
    display_show_undefined = display_show_undefined ? true : false;
    var messages = config_js["messages"];

    if( display_show_undefined || ( typeof data === 'string' && data ) || ( Array.isArray(data) && data.length != 0 ) )
    {
        // display value only if not empty
        // or if the conf says to show undefined values
        var values = {
          "faclass": faclass,
          "message": messages['label_' + column],
          "value": ldapTypeRenderer(
                                       config_js,
                                       targetDN,
                                       column,
                                       column_type,
                                       data,
                                       display_show_undefined
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

function renderStatusAttributesList(config_js, targetDN, column, column_type, attribute, faclass, data)
{
    render = "";

    // overload truncate_value_after to always display complete values
    truncate_value_after = 10000;
    // picking display_show_undefined as show_undef parameter
    var display_show_undefined = config_js["display_show_undefined"];
    display_show_undefined = display_show_undefined ? true : false;
    var messages = config_js["messages"];

    if( display_show_undefined || ( typeof data === 'string' && data ) || ( Array.isArray(data) && data.length != 0 ) )
    {
        // display value only if not empty
        // or if the conf says to show undefined values
        var values = {
          "message": messages['label_' + column],
          "value": ldapTypeRenderer(
                                       config_js,
                                       targetDN,
                                       column,
                                       column_type,
                                       data,
                                       display_show_undefined
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
