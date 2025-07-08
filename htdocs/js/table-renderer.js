
/* get_datatables_params:
   INPUT:
     same as ldapTypeRenderer
   OUTPUT:
     column: column name, ie key of $attributes_map associative array (firstname, fullname, identifier,...)
     column_type: attribute type, ie value of "type" key in $attributes_map associative array (text, tel, dn_link,...)
     dn: DN of current attribute
     messages: associative array containing all messages for selected language
     listing_linkto: array or string containing the attribute key(s) for linking
     show_undef: boolean. When true, show a specific message when there is no value for the current attribute
     truncate_value_after: integer. max length after which the string is truncated
     search: string, parameter named "search" of the http query
     js_date_specifiers: string, format of the date as specified in https://tc39.es/ecma262/multipage/numbers-and-dates.html#sec-date-time-string-format
*/
function get_datatables_params(data, type, row, meta, datatables_params)
{

    var column = Object.keys(datatables_params["column_types"])[meta.col];

    var column_type = datatables_params["column_types"][column];

    var dn = row[0];

    var messages = datatables_params["messages"];

    var listing_linkto = datatables_params["listing_linkto"];

    var show_undef = datatables_params["show_undef"];
    show_undef = show_undef ? true : false;

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
             column, column_type, dn, messages, listing_linkto,
             show_undef, truncate_value_after, search, js_date_specifiers,
             unlock, enable
           ];
}


/* ldapTypeRenderer:
   INPUT:
     data: attribute value (javascript array or string)
     type: origin of datatables operation when performing the rendering: filter, display, type, sort, undefined, custom
     row: full data source of the row
     meta: meta information about the row: row index, col index, settings
     datatables_paras: structure storing all parameters for rendering
*/

function ldapTypeRenderer(data, type, row, meta, datatables_params)
{
    var render = "";

    [column, column_type, dn, messages, listing_linkto,
     show_undef, truncate_value_after, search, js_date_specifiers,
     unlock, enable ] =
            get_datatables_params(data, type, row, meta, datatables_params);

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

function rightRotate(value, amount) {
    return (value>>>amount) | (value<<(32 - amount));
};

function sha256(ascii) {

    var mathPow = Math.pow;
    var maxWord = mathPow(2, 32);
    var lengthProperty = 'length'
    var i, j; // Used as a counter across the whole file
    var result = ''

    var words = [];
    var asciiBitLength = ascii[lengthProperty]*8;

    //* caching results is optional - remove/add slash from front of this line to toggle
    // Initial hash value: first 32 bits of the fractional parts of the square roots of the first 8 primes
    // (we actually calculate the first 64, but extra values are just ignored)
    var hash = sha256.h = sha256.h || [];
    // Round constants: first 32 bits of the fractional parts of the cube roots of the first 64 primes
    var k = sha256.k = sha256.k || [];
    var primeCounter = k[lengthProperty];
    /*/
    var hash = [], k = [];
    var primeCounter = 0;
    //*/

    var isComposite = {};
    for (var candidate = 2; primeCounter < 64; candidate++) {
        if (!isComposite[candidate]) {
            for (i = 0; i < 313; i += candidate) {
                isComposite[i] = candidate;
            }
            hash[primeCounter] = (mathPow(candidate, .5)*maxWord)|0;
            k[primeCounter++] = (mathPow(candidate, 1/3)*maxWord)|0;
        }
    }

    ascii += '\x80' // Append Ƈ' bit (plus zero padding)
    while (ascii[lengthProperty]%64 - 56) ascii += '\x00' // More zero padding
    for (i = 0; i < ascii[lengthProperty]; i++) {
        j = ascii.charCodeAt(i);
        if (j>>8) return; // ASCII check: only accept characters in range 0-255
        words[i>>2] |= j << ((3 - i)%4)*8;
    }
    words[words[lengthProperty]] = ((asciiBitLength/maxWord)|0);
    words[words[lengthProperty]] = (asciiBitLength)

    // process each chunk
    for (j = 0; j < words[lengthProperty];) {
        var w = words.slice(j, j += 16); // The message is expanded into 64 words as part of the iteration
        var oldHash = hash;
        // This is now the undefinedworking hash", often labelled as variables a...g
        // (we have to truncate as well, otherwise extra entries at the end accumulate
        hash = hash.slice(0, 8);

        for (i = 0; i < 64; i++) {
            var i2 = i + j;
            // Expand the message into 64 words
            // Used below if
            var w15 = w[i - 15], w2 = w[i - 2];

            // Iterate
            var a = hash[0], e = hash[4];
            var temp1 = hash[7]
                + (rightRotate(e, 6) ^ rightRotate(e, 11) ^ rightRotate(e, 25)) // S1
                + ((e&hash[5])^((~e)&hash[6])) // ch
                + k[i]
                // Expand the message schedule if needed
                + (w[i] = (i < 16) ? w[i] : (
                        w[i - 16]
                        + (rightRotate(w15, 7) ^ rightRotate(w15, 18) ^ (w15>>>3)) // s0
                        + w[i - 7]
                        + (rightRotate(w2, 17) ^ rightRotate(w2, 19) ^ (w2>>>10)) // s1
                    )|0
                );
            // This is only used once, so *could* be moved below, but it only saves 4 bytes and makes things unreadble
            var temp2 = (rightRotate(a, 2) ^ rightRotate(a, 13) ^ rightRotate(a, 22)) // S0
                + ((a&hash[1])^(a&hash[2])^(hash[1]&hash[2])); // maj

            hash = [(temp1 + temp2)|0].concat(hash); // We don't bother trimming off the extra ones, they're harmless as long as we're truncating when we do the slice()
            hash[4] = (hash[4] + temp1)|0;
        }

        for (i = 0; i < 8; i++) {
            hash[i] = (hash[i] + oldHash[i])|0;
        }
    }

    for (i = 0; i < 8; i++) {
        for (j = 3; j + 1; j--) {
            var b = (hash[i]>>(j*8))&255;
            result += ((b < 16) ? 0 : '') + b.toString(16);
        }
    }
    return result;
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

// TODO: remove templates/value_displayer.tpl file and its call in display.tpl
