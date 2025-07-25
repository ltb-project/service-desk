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

    // if we are processing column "linkto", add an html link <a>
    if(Array.isArray(listing_linkto) && listing_linkto.includes(column))
    {
        render += "<a href=\"index.php?page=display" +
                  "&dn=" + encodeURIComponent(dn) +
                  "&search=" + encodeURIComponent(search) +
                  "\" title=\"" + messages["displayentry"] + "\">";
    }

    // empty value, return immediately the value
    if(!data || (Array.isArray(data) && !data.length) )
    {
        if(show_undef)
        {
            render += "<i>" + messages["notdefined"] + "</i>";
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
        render += "</a>";
    }

    return render;
}


// Duplicate the commentbox template and fill it for each entry
function comment_displayer(method, page, messages, dn, returnto, required)
{
    var res = "";
    var commentbox = $("#commentbox").clone();

    commentbox.find("form").attr("id", method);
    commentbox.find("form").attr("action", "index.php?page="+page);
    commentbox.find("input[name='dn']").attr("value", dn);
    commentbox.find("input[name='returnto']").attr("value", returnto);
    commentbox.find("div[id='commentModalMethodHashedDN']").attr("id", "commentModal" + method + sha256(dn));
    commentbox.find("h1[id='CommentModal']").text(messages[page]);
    commentbox.find("textarea[name='comment']").attr("id", "comment-" + method);
    commentbox.find("textarea[name='comment']").attr("placeholder", messages["insert_comment"]);
    if(required)
    {
        commentbox.find("textarea[name='comment']").prop('required',true);
    }
    var button_close_content = commentbox.find("button[type='button']").html();
    button_close_content = button_close_content.replace("msg_close", messages["close"]);
    commentbox.find("button[type='button']").html(button_close_content);

    var button_submit_content = commentbox.find("button[type='submit']").html();
    button_submit_content = button_submit_content.replace("msg_submit", messages["submit"]);
    commentbox.find("button[type='submit']").html(button_submit_content);

    res += commentbox.html();

    return res;
}

function unlock_displayer(dn, messages, search, unlock, page)
{
    var use_unlockaccount          = unlock["use_unlockaccount"];
    var use_unlockcomment          = unlock["use_unlockcomment"];
    var use_unlockcomment_required = unlock["use_unlockcomment_required"];

    var res = "";

    if(use_unlockaccount && page == "searchlocked")
    {
        if(use_unlockcomment)
        {
            res += '<button type="button"' +
                   ' class="btn btn-success btn-sm"' +
                   ' data-bs-toggle="modal"' +
                   ' data-bs-target="#commentModalunlock' + sha256(dn) + '">';
            res += '<i class="fa fa-fw fa-unlock mr-3"></i>';
            res += '<i class="fa fa-fw fa-info-circle text-body-tertiary" title="' + messages["comment_needed"] + '"></i>';
            res += '</button>';
            res += '<div>';
            // Add comment form
            res += comment_displayer("unlock", "unlockaccount", messages, dn, page, use_unlockcomment_required);

            res += '</div>';
        }
        else
        {
            res += '<a href="index.php?page=unlockaccount&dn=' + encodeURIComponent(dn) + '&returnto=searchlocked"';
            res += ' class="btn btn-success btn-sm" role="button" title="' + messages["unlockaccount"] + '">';
            res += '<i class="fa fa-fw fa-unlock"></i>';
            res += '</a>';
        }
    }
    return res;
}

function enable_displayer(dn, messages, search, enable, page)
{
    var use_enableaccount = enable["use_enableaccount"];
    var use_enablecomment = enable["use_enablecomment"];
    var use_enablecomment_required = enable["use_enablecomment_required"];

    var res = "";

    if(use_enableaccount && page == "searchdisabled")
    {
        if(use_enablecomment)
        {
            res += '<button type="button"' +
                   ' class="btn btn-success btn-sm"' +
                   ' data-bs-toggle="modal"' +
                   ' data-bs-target="#commentModalenable' + sha256(dn) + '">';
            res += '<i class="fa fa-fw fa-user-check mr-3"></i>';
            res += '<i class="fa fa-fw fa-info-circle text-body-tertiary" title="' + messages["comment_needed"] + '"></i>';
            res += '</button>';
            res += '<div>';

            // Add comment form
            res += comment_displayer("enable", "enableaccount", messages, dn, page, use_enablecomment_required);

            res += '</div>';
        }
        else
        {
            res += '<a href="index.php?page=enableaccount&dn=' + encodeURIComponent(dn) + '&returnto=searchdisabled"';
            res += ' class="btn btn-success btn-sm" role="button" title="' + messages["enableaccount"] + '">';
            res += '<i class="fa fa-fw fa-user-check"></i>';
            res += '</a>';
        }
    }
    return res;
}

// Renderer for special first column "DN"
// This column displays all the actions possible for the user:
// display, unlock,...
function ldapDNTypeRenderer(dn, messages, listing_linkto, search, unlock, enable)
{

    var result = "";

    var get_params = new URLSearchParams(document.location.search);
    var page = get_params.get("page");

    result += '<a href="index.php?page=display&' +
              'dn=' + encodeURIComponent(dn) +
              '&search=' + encodeURIComponent(search) + '" ' +
              'class="btn btn-info btn-sm';

    result += listing_linkto == false ? ' hidden' : '';

    result += '" role="button"' +
              ' title="' + messages["displayentry"] + '">' +
              '<i class="fa fa-fw fa-id-card"></i>' +
              '</a>';

    result += unlock_displayer(dn, messages, search, unlock, page);
    result += enable_displayer(dn, messages, search, enable, page);

    return result;
}

function ldapTextTypeRenderer(value, truncate_value_after)
{
    text = truncate(value, truncate_value_after) + "<br />";
    return text;
}

function ldapMailtoTypeRenderer(value, truncate_value_after, messages)
{
    mail_hexa = value.split("")
                     .map(c => "%" + c.charCodeAt(0).toString(16).padStart(2, "0"))
                     .join("");

    mail = '<a href="mailto:' + mail_hexa + '" ' +
           'class="link-email" ' +
           'title="' + messages['tooltip_emailto'] + '">' +
           truncate(value, truncate_value_after) +
           '</a> <br />';

    return mail;
}

function ldapTelTypeRenderer(value, messages, truncate_value_after)
{
    tel = '<a href="tel:' + value + '" ' +
          'rel="nofollow" ' +
          'class="link-phone" ' +
          'title="' + messages['tooltip_phoneto'] + '">' +
          truncate(value, truncate_value_after) +
          '</a><br />';

    return tel;
}

function ldapBooleanTypeRenderer(value, messages, truncate_value_after)
{
    bool = "";

    if( value == "TRUE" )
    {
        bool = truncate(messages['true'], truncate_value_after) + '<br />';
    }

    if( value == "FALSE" )
    {
        bool = truncate(messages['false'], truncate_value_after) + '<br />';
    }

    return bool;
}

function ldapDateTypeRenderer(value, js_date_specifiers)
{
    date = ldap2date.parse(value);
    return dayjs(date).format(js_date_specifiers);
}


function ldapADDateTypeRenderer(value, js_date_specifiers)
{
    // divide by 10 000 000 to get seconds
    winSecs = parseInt( value / 10000000 );
    // 1.1.1600 -> 1.1.1970 difference in seconds
    unixTimestamp = winSecs - 11644473600;
    // get js date object from unixtimestamp in ms
    date = new Date(unixTimestamp * 1000);
    return dayjs(date).format(js_date_specifiers);
}

function ldapListTypeRenderer(value, truncate_value_after)
{
    text = truncate(value, truncate_value_after) + "<br />";
    return text;
}

function ldapBytesTypeRenderer(value, truncate_value_after)
{
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

    return result;
}

function ldapTimestampTypeRenderer(value, js_date_specifiers)
{
    // timestamp is considered in seconds, converting to milliseconds
    return dayjs(value * 1000).format(js_date_specifiers);
}

function ldapDNLinkTypeRenderer(value, truncate_value_after, search)
{
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

    var res = '<a href="index.php?page=display' +
              '&dn=' + encodeURIComponent(dn) +
              '&search=' + encodeURIComponent(search) + '">'
              + truncated_attr_values + '</a> <br />';
    return res;
}

function ldapPPolicyDNTypeRenderer(value, truncate_value_after)
{
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

    var res = vals + '<br />';
    return res;
}

function ldapAddressTypeRenderer(value)
{
    var result = "";
    address_parts = value.split('$');
    for( address_part of address_parts )
    {
        result += address_part + '<br />';
    }
    return result;
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

function updateEntriesCount(settings, datatables_params)
{
    var table = new DataTable('table.dataTable');
    var totalRows = table.page.info().recordsTotal;

    var messages = datatables_params["messages"];

    $('#entriesCount').html(totalRows + ' ' + messages['entriesfound'] );
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
