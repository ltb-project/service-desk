<?php
/*
 * Search entries expiring soon in LDAP directory
 */

require_once("../conf/config.inc.php");
require __DIR__ . '/../vendor/autoload.php';
require_once("../lib/date.inc.php");



$ldapExpirationDate="";

# Search filter
$ldap_filter = "(&".$ldap_user_filter."(pwdChangedTime=*))";

# Search attributes
$attributes = array('pwdChangedTime', 'pwdPolicySubentry');

[$ldap,$result,$nb_entries,$entries,$size_limit_reached]=$ldapInstance->search($ldap_filter, $attributes, $attributes_map, $search_result_title, $search_result_sortby, $search_result_items, $ldap_scope);

if ( ! empty($entries) )
{
                # Register policies
                $pwdPolicies = array();

                # Check if entry will soon expire
                foreach($entries as $entry_key => $entry) {

                    # Search active password policy
                    $pwdPolicy = "";
                    if (isset($entry['pwdpolicysubentry'][0])) {
                        $pwdPolicy = $entry['pwdpolicysubentry'][0];
                    } elseif (isset($ldap_default_ppolicy)) {
                        $pwdPolicy = $ldap_default_ppolicy;
                    }
   
                    $isWillExpire = false;
                    $ppolicy_entry = "";

                    if ($pwdPolicy) {
                        if (!isset($pwdPolicies[$pwdPolicy])){
                            $search_ppolicy = ldap_read($ldap, $pwdPolicy, "(objectClass=pwdPolicy)", array('pwdMaxAge'));

                            if ( $errno ) {
                                error_log("LDAP - PPolicy search error $errno  (".ldap_error($ldap).")");
                            } else {
                                $ppolicy_entry = ldap_get_entries($ldap, $search_ppolicy);
                                $pwdPolicies[$pwdPolicy]['pwdMaxAge'] = $ppolicy_entry[0]['pwdmaxage'][0];
                            }
                        }

                        # Expiration
                        $pwdMaxAge = $pwdPolicies[$pwdPolicy]['pwdMaxAge'];
                        $pwdChangedTime = $entry['pwdchangedtime'][0];

                        if (isset($pwdChangedTime) and isset($pwdMaxAge) and ($pwdMaxAge > 0)) {
                            $changedDate = ldapDate2phpDate($pwdChangedTime);
                            $expirationDate = date_add( $changedDate, new DateInterval('PT'.$pwdMaxAge.'S'));
                            $expirationDateClone = clone($expirationDate);
                            $willExpireDate = date_sub( $expirationDateClone, new DateInterval('P'.$willexpiredays.'D'));
                            $time = time();
                            if ( $time >= $willExpireDate->getTimestamp() and $time < $expirationDate->getTimestamp() ) {
                                $isWillExpire = true;
                            }
                        }

                    }

                    if ( $isWillExpire === false ) {
                        unset($entries[$entry_key]);
                        $nb_entries--;
                    }

                }

                $smarty->assign("page_title", "willexpireaccountstitle");
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
                }
}

?>
