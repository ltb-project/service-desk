<?php
/*
 * Update an entry
 */

$result = "";
$dn = "";
$entry = "";
$action = "displayform";
$result = "";

if (isset($_POST["dn"]) and $_POST["dn"]) {
    $dn = $_POST["dn"];
    $action = "updateentry";
} elseif (isset($_GET["dn"]) and $_GET["dn"]) {
    $dn = $_GET["dn"];
} elseif (isset($entry_dn)) {
    $dn = $entry_dn;
} else {
    $result = "dnrequired";
}

if ($result === "") {

    require_once("../conf/config.inc.php");
    require __DIR__ . '/../vendor/autoload.php';
    require_once("../lib/date.inc.php");

    # Connect to LDAP
    $ldap_connection = $ldapInstance->connect();

    $ldap = $ldap_connection[0];
    $result = $ldap_connection[1];

    if ($ldap) {

        # DN match
        if ( !$ldapInstance->matchDn($dn, $dnAttribute, $ldap_user_filter, $ldap_user_base, $ldap_scope) ) {
            $result = "noentriesfound";
            error_log("LDAP - $dn not found using the configured search settings, reject request");
        } else {

            # Update entry
            if ($action == "updateentry") {

                # Get all data
                $update_attributes = array();
                foreach ($update_items as $item) {
                    $value = array();
                    if (isset($_POST[$item]) and !empty($_POST[$item])) {
                        $value = array($_POST[$item]);

                        if ( $attributes_map[$item]['type'] == "date" ||  $attributes_map[$item]['type'] == "ad_date" ) {
                            $value = $directory->getLdapDate(new DateTime($_POST[$item]));
                        }
                    }

                    $update_attributes[ $attributes_map[$item]['attribute'] ] = $value;
                }

                # Update entry
                if (!ldap_mod_replace($ldap, $dn, $update_attributes)) {
                    error_log("LDAP - modify failed for $dn");
                    $result = "updatefailed";
                    $action = "displayform";
                } else {
                    $errno = ldap_errno($ldap);
                    if ( $errno ) {
                        error_log("LDAP - modify error $errno (".ldap_error($ldap).") for $dn");
                        $result = "updatefailed";
                        $action = "displayform";
                    } else {
                        $result = "updateok";
                        $action = "displayentry";
                    }
                }
            }

            # Display form
            if ($action == "displayform") {

                # Search attributes
                $attributes = array();
                $search_items = array_merge($display_items, $update_items);
                foreach( $search_items as $item ) {
                    $attributes[] = $attributes_map[$item]['attribute'];
                }
                $attributes[] = $attributes_map[$display_title]['attribute'];

                # Search entry
                $search = ldap_read($ldap, $dn, $ldap_user_filter, $attributes);
                $errno = ldap_errno($ldap);

                if ( $errno ) {
                    $result = "ldaperror";
                    error_log("LDAP - Search error $errno  (".ldap_error($ldap).")");
                } else {

                    $entry = ldap_get_entries($ldap, $search);

                    # Sort attributes values
                    foreach ($entry[0] as $attr => $values) {
                        if ( is_array($values) && $values['count'] > 1 ) {

                            # Find key in attributes_map
                            $attributes_map_filter = array_filter($attributes_map, function($v) use(&$attr) {
                                return $v['attribute'] == "$attr";
                            });
                            if( count($attributes_map_filter) < 1 )
                            {
                                $k = "";
                                error_log("WARN: no key found for attribute $attr in \$attributes_map");
                            }
                            elseif( count($attributes_map_filter) > 1 )
                            {
                                $k = array_key_first($attributes_map_filter);
                                error_log("WARN: multiple keys found for attribute $attr in \$attributes_map, using first one: $k");
                            }
                            else
                            {
                                $k = array_key_first($attributes_map_filter);
                            }

                            if(isset($attributes_map[$k]['sort']))
                            {
                                if($attributes_map[$k]['sort'] == "descending" )
                                {
                                    # descending sort
                                    arsort($values);
                                }
                                else
                                {
                                    # ascending sort
                                    asort($values);
                                }
                            }
                            else
                            {
                                # if 'sort' param unset: default to ascending sort
                                asort($values);
                            }
                        }
                        if ( isset($values['count']) ) {
                            unset($values['count']);
                        }
                        $entry[0][$attr] = $values;
                    }

                    # Compute lists
                    $item_list = array();

                    foreach ($update_items as $item) {
                        if ( $attributes_map[$item]["type"] === "static_list") {
                            $item_list[$item] = isset($attributes_static_list[$item]) ? $attributes_static_list[$item] : array();
                        }
                        if ( $attributes_map[$item]["type"] === "list") {
                            $item_list[$item] = $ldapInstance->get_list( $attributes_list[$item]["base"], $attributes_list[$item]["filter"], $attributes_list[$item]["key"], $attributes_list[$item]["value"]  );
                        }
                    }
                }
            }

        }}
}

if ($audit_log_file) {
    auditlog($audit_log_file, $dn, $audit_admin, "updateentry", $result, $comment);
}

if ( $action == "displayentry" ) {
    $location = 'index.php?page=display&dn='.$dn.'&updateresult='.$result;
    header('Location: '.$location);
}

$smarty->assign("entry", $entry[0]);
$smarty->assign("dn", $dn);
$smarty->assign("action", $action);

$smarty->assign("item_list", $item_list);

$smarty->assign("card_title", $display_title);
$smarty->assign("card_items", array_unique(array_merge($display_items, $update_items)));
$smarty->assign("update_items", $update_items);
$smarty->assign("show_undef", $display_show_undefined);

?>
