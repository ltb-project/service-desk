<?php
/*
 * Search locked entries in LDAP directory
 */

require_once("../conf/config.inc.php");
require __DIR__ . '/../vendor/autoload.php';

[$ldap,$result,$nb_entries,$entries,$size_limit_reached] = $ldapInstance->search($ldap_user_filter, array('pwdpolicysubentry'), $attributes_map, $search_result_title, $search_result_sortby, $search_result_items, $ldap_scope);

if ( !empty($entries) )
{

    # Check if entry is still locked
    foreach($entries as $entry_key => $entry) {
        # Search active password policy
        $pwdPolicy = "";
        if (isset($entry['pwdpolicysubentry'][0])) {
            $pwdPolicy = $entry['pwdpolicysubentry'][0];
        } elseif (isset($ldap_default_ppolicy)) {
            $pwdPolicy = $ldap_default_ppolicy;
        }
        $lockoutDuration = $directory->getLockoutDuration($ldap, $entry['dn'], array('pwdPolicy' => $pwdPolicy, 'lockoutDuration' => $ldap_lockout_duration));
        $isLocked = $directory->isLocked($ldap, $entry['dn'], array('lockoutDuration'  => $lockoutDuration));

        if ( $isLocked === false ) {
            unset($entries[$entry_key]);
            $nb_entries--;
        }

    }

    $smarty->assign("page_title", "lockedaccounts");
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
        if ($use_unlockaccount) { $smarty->assign("display_unlock_button", true); }
    }
}

?>
