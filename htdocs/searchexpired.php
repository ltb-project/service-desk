<?php
/*
 * Search entries in LDAP directory
 */ 

$result = "";
$nb_entries = 0;
$entries = array();
$size_limit_reached = false;
$ldapExpirationDate="";

if ($result === "") {

    require_once("../conf/config.inc.php");
    require_once("../lib/ldap.inc.php");
    require_once("../lib/date.inc.php");

    # Connect to LDAP
    $ldap_connection = wp_ldap_connect($ldap_url, $ldap_starttls, $ldap_binddn, $ldap_bindpw);

    $ldap = $ldap_connection[0];
    $result = $ldap_connection[1];

    if ($ldap) {

        # Search filter
        $ldap_filter = "(&".$ldap_user_filter."(pwdChangedTime=*))";

        # Search attributes
        $attributes = array('pwdChangedTime', 'pwdPolicySubentry');
        foreach( $search_result_items as $item ) {
            $attributes[] = $attributes_map[$item]['attribute'];
        }
        $attributes[] = $attributes_map[$search_result_title]['attribute'];
        $attributes[] = $attributes_map[$search_result_sortby]['attribute'];

        # Search for users
        $search = ldap_search($ldap, $ldap_user_base, $ldap_filter, $attributes, 0, $ldap_size_limit);

        $errno = ldap_errno($ldap);

        if ( $errno == 4) {
            $size_limit_reached = true;
        }
        if ( $errno != 0 and $errno !=4 ) {
            $result = "ldaperror";
            error_log("LDAP - Search error $errno  (".ldap_error($ldap).")");
        } else {

            # Sort entries
            if (isset($search_result_sortby)) {
                $sortby = $attributes_map[$search_result_sortby]['attribute'];
                ldap_sort($ldap, $search, $sortby);
            }

            # Get search results
            $nb_entries = ldap_count_entries($ldap, $search);

            if ($nb_entries === 0) {
                $result = "noentriesfound";
            } else {
                $entries = ldap_get_entries($ldap, $search);
                unset($entries["count"]);

                # Check if entry is still locked
		foreach($entries as $entry_key => $entry) {

                    # Search active password policy
                    $pwdPolicy = "";
                    if (isset($entry['pwdpolicysubentry'][0])) {
                        $pwdPolicy = $entry['pwdpolicysubentry'][0];
                    } elseif (isset($ldap_default_ppolicy)) {
                        $pwdPolicy = $ldap_default_ppolicy;
                    }
   
                    $isExpired = false;
                    $ppolicy_entry = "";

                    if ($pwdPolicy) {
                        $search_ppolicy = ldap_read($ldap, $pwdPolicy, "(objectClass=pwdPolicy)", array('pwdMaxAge'));

                        if ( $errno ) {
                            error_log("LDAP - PPolicy search error $errno  (".ldap_error($ldap).")");
                        } else {
                            $ppolicy_entry = ldap_get_entries($ldap, $search_ppolicy);
                        }


                        # Expiration
                        $pwdMaxAge = $ppolicy_entry[0]['pwdmaxage'][0];
                        $pwdChangedTime = $entry['pwdchangedtime'][0];

                        if (isset($pwdChangedTime) and isset($pwdMaxAge) and ($pwdMaxAge > 0)) {
                            $changedDate = ldapDate2phpDate($pwdChangedTime);
                            $expirationDate = date_add( $changedDate, new DateInterval('PT'.$pwdMaxAge.'S'));
                            if ( time() >= $expirationDate->getTimestamp() ) {
                                $isExpired = true;
                            }
	                }

	            }

	            if ( $isExpired === false ) {
		        unset($entries[$entry_key]);
			$nb_entries--;
	            }

	        }

                $smarty->assign("page_title", "expiredaccounts");
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
        }
    }
}

?>
