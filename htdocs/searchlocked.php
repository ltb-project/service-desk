<?php
/*
 * Search locked entries in LDAP directory
 */

require_once("../conf/config.inc.php");
require __DIR__ . '/../vendor/autoload.php';
require_once("../lib/date.inc.php");



# Search filter
$ldap_filter = "(&".$ldap_user_filter."(pwdAccountLockedTime=*))";

# Search attributes
$attributes = array('pwdAccountLockedTime', 'pwdPolicySubentry');

[$ldap,$result,$nb_entries,$entries,$size_limit_reached]=\Ltb\LtbUtil::search($ldap_filter,$attributes);

if ( ! empty($entries) )
{
                # Register policies
                $pwdPolicies = array();

                # Check if entry is still locked
                foreach($entries as $entry_key => $entry) {

                    # Search active password policy
                    $pwdPolicy = "";
                    if (isset($entry['pwdpolicysubentry'][0])) {
                        $pwdPolicy = $entry['pwdpolicysubentry'][0];
                    } elseif (isset($ldap_default_ppolicy)) {
                        $pwdPolicy = $ldap_default_ppolicy;
                    }

                    $isLocked = false;
                    $ppolicy_entry = "";

                    if ($pwdPolicy) {
                        if (!isset($pwdPolicies[$pwdPolicy])){
                            $search_ppolicy = ldap_read($ldap, $pwdPolicy, "(objectClass=pwdPolicy)", array('pwdLockoutDuration'));

                            if ( $errno ) {
                                error_log("LDAP - PPolicy search error $errno  (".ldap_error($ldap).")");
                            } else {
                                $ppolicy_entry = ldap_get_entries($ldap, $search_ppolicy);
                                $pwdPolicies[$pwdPolicy]['pwdLockoutDuration'] = $ppolicy_entry[0]['pwdlockoutduration'][0];
                            }
                        }

                        # Lock
                        $pwdLockoutDuration = $pwdPolicies[$pwdPolicy]['pwdLockoutDuration'];
                        $pwdAccountLockedTime = $entry['pwdaccountlockedtime'][0];

                        if ( $pwdAccountLockedTime === "000001010000Z" ) {
                            $isLocked = true;
                        } else if (isset($pwdAccountLockedTime)) {
                            if (isset($pwdLockoutDuration) and ($pwdLockoutDuration > 0)) {
                                $lockDate = ldapDate2phpDate($pwdAccountLockedTime);
                                $unlockDate = date_add( $lockDate, new DateInterval('PT'.$pwdLockoutDuration.'S'));
                                if ( time() <= $unlockDate->getTimestamp() ) {
                                    $isLocked = true;
                                }
                            } else {
                                $isLocked = true;
                            }
                        }
                    }

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
