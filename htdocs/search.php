<?php
/*
 * Search entries in LDAP directory
 */

if (isset($_POST["search"]) and $_POST["search"]) {

    $result="";

    require_once("../conf/config.inc.php");
    require __DIR__ . '/../vendor/autoload.php';

    $filter_escape_chars = "";
    if (!$search_use_substring_match) { $filter_escape_chars = "*"; }

    $search_query = ldap_escape($_POST["search"], $filter_escape_chars, LDAP_ESCAPE_FILTER);

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

    # Search attributes
    $attributes = array();

    [$ldap,$result,$nb_entries,$entries,$size_limit_reached]=$ldapInstance->search($ldap_filter, $attributes, $attributes_map, $search_result_title, $search_result_sortby, $search_result_items, $ldap_scope);

    if ( ! empty($entries) )
    {
        if ($nb_entries === 1) {
                $entry_dn = $entries[0]["dn"];
                $page = "display";
                include("display.php");
        }
        else {
                $smarty->assign("nb_entries", $nb_entries);
                $smarty->assign("entries", $entries);
                $smarty->assign("size_limit_reached", $size_limit_reached);

                $columns = $search_result_items;
                if (! in_array($search_result_title, $columns)) array_unshift($columns, $search_result_title);
                $smarty->assign("listing_columns", $columns);
                $smarty->assign("listing_linkto",  isset($search_result_linkto) ? $search_result_linkto : array($search_result_title));
                $smarty->assign("js_date_specifiers", $js_date_specifiers );
                $smarty->assign("listing_sortby",  array_search($search_result_sortby, $columns));
                $smarty->assign("show_undef", $search_result_show_undefined);
                $smarty->assign("truncate_value_after", $search_result_truncate_value_after);
        }
    }
} else {
    $result = "searchrequired";
}

?>
