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
$dateIdleLdap = string2ldapDate( $dateIdle->format('d/m/Y') );

# Search filter
$ldap_filter = "(&".$ldap_user_filter."(|(!(authTimestamp=*))(authTimestamp<=".$dateIdleLdap.")))";

# Search attributes
$attributes = array();

$result = "";
$nb_entries = 0;
$entries = array();
$size_limit_reached = false;

if ($result === "") {

    # Connect to LDAP
    $ldap_connection = \Ltb\Ldap::connect($ldap_url, $ldap_starttls, $ldap_binddn, $ldap_bindpw, $ldap_network_timeout);

    $ldap = $ldap_connection[0];
    $result = $ldap_connection[1];

    if ($ldap) {


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

            # Get search results
            $nb_entries = ldap_count_entries($ldap, $search);

            if ($nb_entries === 0) {
                $result = "noentriesfound";
            } else {
                $entries = ldap_get_entries($ldap, $search);

                # Sort entries
                if (isset($search_result_sortby)) {
                    $sortby = $attributes_map[$search_result_sortby]['attribute'];
                    \Ltb\Ldap::ldapSort($entries, $sortby);
                }

                unset($entries["count"]);
            }
        }
    }
}

if ( ! empty($entries) )
{
                $smarty->assign("page_title", "idleaccountstitle");
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

?>
