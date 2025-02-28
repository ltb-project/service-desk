<?php
/*
 * Search disabled entries in LDAP directory
 */

require_once("../conf/config.inc.php");
require __DIR__ . '/../vendor/autoload.php';

/*
[$ldap,$result,$nb_entries,$entries,$size_limit_reached] = $ldapInstance->search($ldap_user_filter, array(), $attributes_map, $search_result_title, $search_result_sortby, $search_result_items, $ldap_scope);

$ldapInstance->ldapSort($entries, $attributes_map['identifier']['attribute']);

if ( !empty($entries) )
{

    # Check if entry is still locked
    foreach($entries as $entry_key => $entry) {

        $isEnabled = $directory->isAccountEnabled($ldap, $entry['dn']);

        if ( $isEnabled === true ) {
            unset($entries[$entry_key]);
            $nb_entries--;
        }

    }

    $smarty->assign("page_title", "disabledaccounts");
    if ($nb_entries === 0) {
        $result = "noentriesfound";
    } else {
        $smarty->assign("nb_entries", $nb_entries);
        $smarty->assign("entries", $entries);
        $smarty->assign("size_limit_reached", $size_limit_reached);

        $columns = $search_result_items;
        if (! in_array($search_result_title, $columns)) array_unshift($columns, $search_result_title);
        $smarty->assign("listing_columns", $columns);
        $smarty->assign("listing_linkto",  isset($search_result_linkto) ? $search_result_linkto : array($search_result_title));
        $smarty->assign("listing_sortby",  array_search($search_result_sortby, $columns));
        $smarty->assign("show_undef", $search_result_show_undefined);
        $smarty->assign("truncate_value_after", $search_result_truncate_value_after);
        if ($use_enableaccount) { $smarty->assign("display_enable_button", true); }
    }
}
*/

$smarty->assign("nb_entries", 0);
$smarty->assign("entries", null);
$columns = $search_result_items;
if (! in_array($search_result_title, $columns)) array_unshift($columns, $search_result_title);
$smarty->assign("listing_columns", $columns);
$smarty->assign("listing_linkto",  isset($search_result_linkto) ? $search_result_linkto : array($search_result_title));
$smarty->assign("listing_sortby",  array_search($search_result_sortby, $columns));
$smarty->assign("show_undef", $search_result_show_undefined);
$smarty->assign("truncate_value_after", $search_result_truncate_value_after);
if ($use_enableaccount) { $smarty->assign("display_enable_button", true); }

?>
