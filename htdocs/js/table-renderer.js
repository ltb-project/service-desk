/* ldapTypeRenderer:
     column: column name, ie key of $attributes_map associative array (firstname, fullname, identifier,...)
     type: attribute type, ie value of "type" key in $attributes_map associative array (text, tel, dn_link,...)
     data: attribute value (javascript array or string)
     dn: DN of current attribute
     messages: associative array containing all messages for selected language
     listing_linkto: array or string containing the attribute key(s) for linking
     show_undef: boolean. When true, show a specific message when there is no value for the current attribute
     truncate_value_after: integer. max length after which the string is truncated
     search: string, parameter named "search" of the http query
     js_date_specifiers: string, format of the date as specified in https://tc39.es/ecma262/multipage/numbers-and-dates.html#sec-date-time-string-format
*/
function ldapTypeRenderer(column, type, data, dn, messages, listing_linkto, show_undef, truncate_value_after, search, js_date_specifiers)
{
    var render = "";

    // if we are processing column "linkto", add an html link <a>
    if(Array.isArray(listing_linkto) && listing_linkto.includes(column))
    {
        // TODO: url escape of dn
        render += "<a href=\"index.php?page=display&dn=" + dn + "&search=" + search + "\" title=\"" + messages["displayentry"] + "\">";
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
            switch(type)
            {
                case "dn":
                    render += ldapDNTypeRenderer(column, type, value, dn, messages, listing_linkto, show_undef, truncate_value_after, search);
                    break;
                case "text":
                    render += ldapTextTypeRenderer(value, truncate_value_after);
                    break;
                case "mailto":
                    render += ldapMailtoTypeRenderer(value, messages);
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
                    render += ldapBytesTypeRenderer(column, type, value, dn, messages, listing_linkto, show_undef, truncate_value_after, search);
                    break;
                case "timestamp":
                    render += ldapTimestampTypeRenderer(column, type, value, dn, messages, listing_linkto, show_undef, truncate_value_after, search, js_date_specifiers);
                    break;
                case "dn_link":
                    render += ldapDNLinkTypeRenderer(value, search);
                    break;
                case "ppolicy_dn":
                    render += ldapPPolicyDNTypeRenderer(column, type, value, dn, messages, listing_linkto, show_undef, truncate_value_after, search);
                    break;
                case "address":
                    render += ldapAddressTypeRenderer(column, type, value, dn, messages, listing_linkto, show_undef, truncate_value_after, search);
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

// Renderer for special first column "DN"
function ldapDNTypeRenderer(column, type, value, dn, messages, listing_linkto, show_undef, truncate_value_after, search)
{
    return value;
}

function ldapTextTypeRenderer(value, truncate_value_after)
{
    text = truncate(value, truncate_value_after) + "<br />";
    return text;
}

function ldapMailtoTypeRenderer(value, messages)
{
    mail_hexa = value.split("")
                     .map(c => "%" + c.charCodeAt(0).toString(16).padStart(2, "0"))
                     .join("");

    mail = '<a href="mailto:' + mail_hexa + '" ' +
           'class="link-email" ' +
           'title="' + messages['tooltip_emailto'] + '">' +
           value +
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

function ldapBytesTypeRenderer(column, type, value, dn, messages, listing_linkto, show_undef, truncate_value_after, search)
{
    return value;
}

function ldapTimestampTypeRenderer(column, type, value, dn, messages, listing_linkto, show_undef, truncate_value_after, search, js_date_specifiers)
{
    return value;
}

function ldapDNLinkTypeRenderer(value, search)
{
    var dn = "";
    var cn = [];
    if(Array.isArray(value))
    {
        if(value.length >= 1)
        {
            dn = value[0];
        }
        if(value.length >= 2)
        {
            cn = value[1];
        }
    }

    if(Array.isArray(cn))
    {
        cn = cn.join(", ");
    }

    // TODO: url escape of dn
    var res = '<a href="index.php?page=display&dn=' + dn + '&search=' + search + '">' + cn + '</a> <br />';
    return res;
}

function ldapPPolicyDNTypeRenderer(column, type, value, dn, messages, listing_linkto, show_undef, truncate_value_after, search)
{
    return value;
}

function ldapAddressTypeRenderer(column, type, value, dn, messages, listing_linkto, show_undef, truncate_value_after, search)
{
    return value;
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

