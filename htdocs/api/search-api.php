<?php
/*
 * Search entries in LDAP directory and returns a JSON structure
 */

require_once(__DIR__ . "/../../lib/date.inc.php");

$possible_actions = [ 'searchdisabled', 'searchexpired',
                      'searchidle', 'searchinvalid',
                      'searchlocked', 'search',
                      'searchwillexpire', 'display' ];
$action = "";
$targetDN = "";

# Get search parameters from request
$datatables_input = array(
    "columns" => null,
    "draw"    => null,
    "start"   => null,
    "length"  => null,
    "order"   => null,
    "search"  => null
);
foreach ($datatables_input as $key => $value) {
    if (isset($_REQUEST[$key]))
    {
        $datatables_input[$key] = $_REQUEST[$key];
    }
}
if ( isset($_REQUEST["action"]) && in_array($_REQUEST["action"], $possible_actions ) )
{
    $action = $_REQUEST["action"];
}
else
{
    error_log("Missing or invalid action: $action");
    exit(1);
}
if ( isset($_REQUEST["targetDN"]) )
{
    $targetDN = $_REQUEST["targetDN"];
}
$ldap_user_base = "";


# Prepare the LDAP request according to the action
switch ($action) {

    case "searchidle":
        # Compute idle date
        $dateIdle = new DateTime();
        date_sub( $dateIdle, new DateInterval('P'.$idledays.'D') );
        $dateIdleLdap = $directory->getLdapDate($dateIdle);
        # Search filter
        $ldap_filter = "(&".$ldap_user_filter."(|(!(".$ldap_lastauth_attribute."=*))(".$ldap_lastauth_attribute."<=".$dateIdleLdap.")))";
        $ldap_user_filter = $ldap_filter;
        break;

    case "searchinvalid":
        # Compute idle date
        $date= new DateTime();
        $dateLdap = $directory->getLdapDate($date);

        $ldap_filter = "(&". $ldap_user_filter . "(|";
        if ( isset($attributes_map['starttime']) ) {
            $ldap_filter .= "(" . $attributes_map['starttime']['attribute'] .">=". $dateLdap .")";
            $search_result_items[] = "starttime";
        }
        if ( isset($attributes_map['endtime']) ) {
            $ldap_filter .= "(" . $attributes_map['endtime']['attribute'] ."<=". $dateLdap .")";
            $search_result_items[] = "endtime";
        }
        $ldap_filter.= "))";
        $ldap_user_filter = $ldap_filter;
        break;

    case "search":
        $filter_escape_chars = "";
        if (!$search_use_substring_match) { $filter_escape_chars = "*"; }

        $search_query = ldap_escape($_REQUEST["search_query"], $filter_escape_chars, LDAP_ESCAPE_FILTER);

        # Search filter
        $ldap_filter = "(&".$ldap_user_filter."(|";
        foreach ($search_attributes as $attr) {
            $ldap_filter .= "($attr=";
            if ($search_use_substring_match) { $ldap_filter .= "*"; }
            $ldap_filter .= $search_query;
            if ($search_use_substring_match) { $ldap_filter .= "*"; }
            $ldap_filter .= ")";
        }
        $ldap_filter .= "))";
        $ldap_user_filter = $ldap_filter;
        break;

    case "display":
        $ldap_user_base = $ldapInstance->ldap_user_base;
        $ldapInstance->ldap_user_base = $targetDN;
        $search_result_items = array_merge($display_items, $display_password_items);
        $ldap_scope = "base";
        break;
}

# FILTERING
# If there is a search filter
if( !empty($datatables_input["search"]["value"]) )
{
    # Get the list of attributes to search
    foreach( $search_result_items as $item ) {
        $attributes[] = $attributes_map[$item]['attribute'];
    }
    $attributes[] = $attributes_map[$search_result_title]['attribute'];
    $attributes[] = $attributes_map[$search_result_sortby]['attribute'];

    # For each attribute, append the search filter with a new component
    $filter_components = "(|";
    foreach( $attributes as $attribute ) {
        $filter_components .= "($attribute=*".$datatables_input["search"]["value"]."*)";
    }
    $filter_components .= ")";

    # Include the new filter in the ldap_user_filter
    $ldap_user_filter = "(&". $ldap_user_filter . $filter_components . ")";
}

# LDAP request for searching users
[$ldap,$result,$nb_entries,$entries,$size_limit_reached] = $ldapInstance->search(
    $ldap_user_filter,
    $directory->getOperationalAttributes(),
    $attributes_map,
    $search_result_title,
    $search_result_sortby,
    $search_result_items,
    $ldap_scope
);

# Get password policies list + user's ppolicy assignment
list($passwordPolicies, $userPolicies) = $directory->getPwdPolicies(
                                             $ldap,
                                             $entries,
                                             $ldap_default_ppolicy
                                         );

# Filter the result according to the action
switch ($action) {

    case "searchdisabled":
        foreach($entries as $entry_key => $entry) {

            $isEnabled = $directory->isAccountEnabled($entry);

            if ( $isEnabled === true ) {
                unset($entries[$entry_key]);
                $nb_entries--;
            }

        }
        break;

    case "searchexpired":
        foreach($entries as $entry_key => $entry) {

            # Get password policy configuration
            $pwdPolicyConfiguration = $userPolicies[$entry['dn']];
            if (isset($ldap_lockout_duration) and $ldap_lockout_duration) { $pwdPolicyConfiguration['lockout_duration'] = $ldap_lockout_duration; }
            if (isset($ldap_password_max_age) and $ldap_password_max_age) { $pwdPolicyConfiguration['password_max_age'] = $ldap_password_max_age; }

            $isExpired = $directory->isPasswordExpired($entry, $pwdPolicyConfiguration);

            if ( $isExpired === false ) {
                unset($entries[$entry_key]);
                $nb_entries--;
            }

        }
        break;

    case "searchlocked":
        # Check if entry is still locked
        foreach($entries as $entry_key => $entry) {
            # Get password policy configuration
            $pwdPolicyConfiguration = $userPolicies[$entry['dn']];
            if (isset($ldap_lockout_duration) and $ldap_lockout_duration) { $pwdPolicyConfiguration['lockout_duration'] = $ldap_lockout_duration; }
            if (isset($ldap_password_max_age) and $ldap_password_max_age) { $pwdPolicyConfiguration['password_max_age'] = $ldap_password_max_age; }

            $isLocked = $directory->isLocked($entry, $pwdPolicyConfiguration);

            if ( $isLocked === false ) {
                unset($entries[$entry_key]);
                $nb_entries--;
            }
        }
        break;

    case "searchwillexpire":
        # Check if entry will soon expire
        foreach($entries as $entry_key => $entry) {

            # Get password policy configuration
            $pwdPolicyConfiguration = $userPolicies[$entry['dn']];
            if (isset($ldap_lockout_duration) and $ldap_lockout_duration) { $pwdPolicyConfiguration['lockout_duration'] = $ldap_lockout_duration; }
            if (isset($ldap_password_max_age) and $ldap_password_max_age) { $pwdPolicyConfiguration['password_max_age'] = $ldap_password_max_age; }

            $isWillExpire = false;
            $expirationDate = $directory->getPasswordExpirationDate($entry, $pwdPolicyConfiguration);

            if ($expirationDate) {
                $expirationDateClone = clone $expirationDate;
                $willExpireDate = date_sub( $expirationDateClone, new DateInterval('P'.$willexpiredays.'D'));
                $time = time();
                if ( $time >= $willExpireDate->getTimestamp() and $time < $expirationDate->getTimestamp() ) {
                    $isWillExpire = true;
                }
            }

            if ( $isWillExpire === false ) {
                unset($entries[$entry_key]);
                $nb_entries--;
            }
        }
        break;

    case "display":
        $ldapInstance->ldap_user_base = $ldap_user_base;
        break;
}


# Get columns labels
$columns = $search_result_items;

if( $action != "display" )
{
    # add search_result_title (cn) in front of the columns list
    if (! in_array($search_result_title, $columns)) array_unshift($columns, $search_result_title);
}


# Sort entries by the attribute provided
if(isset($datatables_input["order"]) &&
   isset($datatables_input["order"][0]) &&
   isset($datatables_input["order"][0]["column"]) &&
   $datatables_input["order"][0]["column"] > 0 )
{
    # use the order parameter provided in the request
    $column_sortby = $columns[($datatables_input["order"][0]["column"]-1)];
    $attribute_sortby = $attributes_map[$column_sortby]['attribute'];
    $attribute_type = $attributes_map[$column_sortby]['type'];

    $direction = isset($datatables_input["order"][0]["dir"]) ?
                 $datatables_input["order"][0]["dir"] :
                 "asc";

    # Sort entries
    $ldapInstance->sortEntries($entries, $attribute_sortby, $direction, $attribute_type);
}


# Only get a page of entries
$entries = array_slice( $entries,
                        intval($datatables_input["start"]),
                        intval($datatables_input["length"]) );


# Format data to send
$outputdata = array();

# Get attribute list from columns: attr => [ "type" => type, "column" => column ]
$attr_list = [];
foreach( $columns as $column ) {
    // Fill attribute list even for undefined items
    if (!$attributes_map[$column]['attribute']) {
        $attr_list[$column."_undefined"] = null;
    } else {
        $attr_list[$attributes_map[$column]['attribute']] =
            array(
                     "type" => $attributes_map[$column]['type'],
                     "column" => $column
            );
    }
}

# Always remove count parameter
unset($entries["count"]);

$i=0;
foreach ($entries as $entry)
{
    $outputdata[$i] = array();
    # Always push DN as first value of the entry
    array_push( $outputdata[$i], $entry["dn"] );
    foreach ($attr_list as $attr => $cont)
    {
        $type = $cont["type"];
        $col = $cont["column"];

        $values = [];
        if ( isset($entry[$attr]) && is_array($entry[$attr]))
        {
            foreach ($entry[$attr] as $j => $value) {
                if($j !== "count") {

                    # If this is a DN, we search for the corresponding cn
                    if( $type == "dn_link" || $type == "ppolicy_dn" )
                    {
                        $dn = $value;
                        $linked_attr = "cn";
                        if($type == "ppolicy_dn")
                        {
                            $linked_attr = $ldap_ppolicy_name_attribute;
                        }
                        # Get linked_attr of corresponding link
                        $linked_attr_res = $ldapInstance->get_attribute_values($dn, $linked_attr);
                        if( $linked_attr_res == false )
                        {
                            $linked_attr_vals = [];
                        }
                        else
                        {
                            $linked_attr_vals = [];
                            foreach ($linked_attr_res as $k => $linked_attr_val) {
                                if($k != "count") {
                                    array_push( $linked_attr_vals, $linked_attr_val );
                                }
                            }
                        }
                        array_push( $values, [ $dn, $linked_attr_vals ] );
                    }

                    # If this is a dynamic list, we get the corresponding values
                    elseif( $type == "list" )
                    {
                        if(isset($attributes_list) &&
                           isset($attributes_list[$col]) &&
                           isset($attributes_map[$col]["display"]) &&
                           $attributes_map[$col]["display"] == "value"
                          )
                        {
                            $list_filter = isset($attributes_list[$col]["filter"]) ?
                                           $attributes_list[$col]["filter"] :
                                           "(objectClass=*)";
                            $key_filter = isset($attributes_list[$col]["key"]) ?
                                          "(" . $attributes_list[$col]["key"] . "=" . $value . ")" :
                                          "(objectClass=*)";
                            $list_filter = "(&" . $list_filter . $key_filter . ")";
                            $list_attrs = isset($attributes_list[$col]["value"]) ?
                                          $attributes_list[$col]["value"] :
                                          ( isset($attributes_list[$col]["key"]) ?
                                            $attributes_list[$col]["key"] :
                                            "dummy" );

                            $entries_search = $ldapInstance->search_with_scope(
                                                                 "sub",
                                                                 $attributes_list[$col]["base"],
                                                                 $list_filter,
                                                                 array( $list_attrs )
                                                             );
                            $errno = ldap_errno($ldap);
                            if ( $errno ) {
                                error_log("LDAP - Search error $errno  (".ldap_error($ldap).")");
                                # Send original value
                                array_push( $values, $value );
                            }
                            $list_entries = ldap_get_entries($ldap, $entries_search);
                            if($list_entries['count'] == 1 ) {
                                $list_values = isset($list_entries[0][$list_attrs]) ?
                                               $list_entries[0][$list_attrs] :
                                               array();
                                if(isset($list_values["count"])) {
                                    unset($list_values["count"]);
                                }
                                $values = array_merge( $values, $list_values );
                            }
                            else
                            {
                                # Send original value
                                array_push( $values, $value );
                            }

                        }
                        else
                        {
                            # Send original value
                            array_push( $values, $value );
                        }
                    }

                    # If this is a static list, we get the corresponding values
                    elseif( $type == "static_list" )
                    {
                        if(isset($attributes_static_list) &&
                           isset($attributes_static_list[$col]) &&
                           isset($attributes_map[$col]["display"]) &&
                           $attributes_map[$col]["display"] == "value" &&
                           isset($attributes_static_list[$col][$value])
                          )
                        {
                            # Use value rather than key
                            array_push( $values, $attributes_static_list[$col][$value] );
                        }
                        else
                        {
                            # Send original value
                            array_push( $values, $value );
                        }
                    }

                    # If this is a standard list of values, just push it
                    else
                    {
                        array_push( $values, $value );
                    }
                }
            }
        }
        array_push( $outputdata[$i], $values );
    }
    $i++;
}

$error = "";
if($size_limit_reached)
{
    $error = "size_limit_reached";
}

$data = array(
    "draw" => $datatables_input["draw"],
    "recordsTotal" => $nb_entries,
    "recordsFiltered" => $nb_entries,
    "data" => $outputdata,
    "error" => $error
);

?>
