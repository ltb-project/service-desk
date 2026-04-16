<?php
/*
 * Search groups in LDAP directory and returns a JSON structure
 */

# Get search parameters from request
$datatables_input = array(
    "draw"   => null,
    "start"  => null,
    "length" => null,
    "order"  => null,
    "search" => null
);
foreach ($datatables_input as $key => $value) {
    if (isset($_REQUEST[$key])) {
        $datatables_input[$key] = $_REQUEST[$key];
    }
}

$userDn = isset($_REQUEST["dn"]) ? $_REQUEST["dn"] : "";

# Use group base instead of user base
$saved_base = $ldapInstance->ldap_user_base;
$ldapInstance->ldap_user_base = $ldap_group_base;

# Build LDAP filter, optionally including a DataTables search filter
$current_filter = $ldap_group_filter;
if (!empty($datatables_input["search"]["value"])) {
    $search_val = ldap_escape($datatables_input["search"]["value"], "", LDAP_ESCAPE_FILTER);
    $title_attr = $attributes_map[$group_result_title]['attribute'];
    $desc_attr   = $attributes_map['description']['attribute'];
    $current_filter = "(&" . $current_filter . "(|($title_attr=*$search_val*)($desc_attr=*$search_val*)))";
}

# Search groups - fetch member attribute as extra to enable membership check
[$ldap, $result, $nb_entries, $entries, $size_limit_reached] = $ldapInstance->search(
    $current_filter,
    array($ldap_group_member_attribute),
    $attributes_map,
    $group_result_title,
    $group_result_sortby,
    $group_result_items,
    "sub"
);

# Restore user base
$ldapInstance->ldap_user_base = $saved_base;

# Build ordered column list (title first, then result items)
$columns = $group_result_items;
if (!in_array($group_result_title, $columns)) {
    array_unshift($columns, $group_result_title);
}

# Sort entries by the column requested by DataTables
if (isset($datatables_input["order"][0]["column"]) &&
    $datatables_input["order"][0]["column"] > 0)
{
    $col_index = intval($datatables_input["order"][0]["column"]) - 1;
    if (isset($columns[$col_index])) {
        $column_sortby    = $columns[$col_index];
        $attribute_sortby = $attributes_map[$column_sortby]['attribute'];
        $attribute_type   = $attributes_map[$column_sortby]['type'];
        $direction        = $datatables_input["order"][0]["dir"] ?? "asc";
        if ($attribute_sortby) {
            $ldapInstance->sortEntries($entries, $attribute_sortby, $direction, $attribute_type);
        }
    }
}

# Paginate
unset($entries["count"]);
$entries = array_slice(
    $entries,
    intval($datatables_input["start"]),
    intval($datatables_input["length"])
);

# Format data for DataTables
$outputdata = array();
$i = 0;
foreach ($entries as $entry) {
    $outputdata[$i] = array();

    # First value is always the DN
    array_push($outputdata[$i], $entry["dn"]);

    # Standard columns from attributes_map
    foreach ($columns as $column) {
        $attr   = $attributes_map[$column]['attribute'];
        $values = array();
        if ($attr && isset($entry[$attr]) && is_array($entry[$attr])) {
            foreach ($entry[$attr] as $j => $value) {
                if ($j !== "count") {
                    array_push($values, $value);
                }
            }
        }
        array_push($outputdata[$i], $values);
    }

    # Compute ismember: check whether userDn is listed in the member attribute
    $isMember = false;
    if ($userDn &&
        isset($entry[$ldap_group_member_attribute]) &&
        is_array($entry[$ldap_group_member_attribute]))
    {
        foreach ($entry[$ldap_group_member_attribute] as $j => $member) {
            if ($j !== "count" && strtolower($member) === strtolower($userDn)) {
                $isMember = true;
                break;
            }
        }
    }
    array_push($outputdata[$i], array($isMember ? "TRUE" : "FALSE"));

    $i++;
}

$error = $size_limit_reached ? "size_limit_reached" : "";

$data = array(
    "draw"            => $datatables_input["draw"],
    "recordsTotal"    => $nb_entries,
    "recordsFiltered" => $nb_entries,
    "data"            => $outputdata,
    "error"           => $error
);

?>
