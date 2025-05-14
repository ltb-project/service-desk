<?php
/*
 * Search idle entries in LDAP directory
 */

require_once("../conf/config.inc.php");
require __DIR__ . '/../vendor/autoload.php';
require_once("../lib/date.inc.php");

# Compute idle date
$dateIdle = new DateTime();
date_sub( $dateIdle, new DateInterval('P'.$idledays.'D') );
$dateIdleLdap = $directory->getLdapDate($dateIdle);

# Search filter
$ldap_filter = "(&".$ldap_user_filter."(|(!(".$ldap_lastauth_attribute."=*))(".$ldap_lastauth_attribute."<=".$dateIdleLdap.")))";

[$ldap,$result,$nb_entries,$entries,$size_limit_reached] = $ldapInstance->search($ldap_filter, array(), $attributes_map, $search_result_title, $search_result_sortby, $search_result_items, $ldap_scope);

if ( !empty($entries) )
{
    $smarty->assign("page_title", "idleaccountstitle");
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

?>
