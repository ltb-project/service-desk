<?php
/*
 * Rename an entry
 */

$result = "";
$dn = "";
$entry = "";
$action = "displayform";
$result = "";

if (isset($_POST["dn"]) and $_POST["dn"]) {
    $dn = $_POST["dn"];
    $action = "renameentry";
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

            # Rename entry
            if ($action == "renameentry") {

                # Compute new RDN
                $new_rdn = "";
                foreach ($rename_items as $item) {
                    if (isset($_POST[$item]) and !empty($_POST[$item])) {
                        $value = $_POST[$item];
                        if (!empty($new_rdn)) { $new_rdn .= "+"; }
                        $new_rdn .= $attributes_map[$item]['attribute'] ."=". $value;
                    }
                }

                # Get current RDN and parent
                $dn_explode = ldap_explode_dn($dn, 0);
                $dn_parent = "";
                for ($i = 1; $i < $dn_explode['count']; $i++) {
                    $dn_parent .= $dn_explode[$i] . ",";
                }
                $parent = trim($dn_parent, ',');

                # Rename entry
                if (!ldap_rename($ldap, $dn, $new_rdn, $parent, true)) {
                    error_log("LDAP - rename failed for $dn");
                    $result = "renamefailed";
                    $action = "displayform";
                } else {
                    $errno = ldap_errno($ldap);
                    if ( $errno ) {
                        error_log("LDAP - rename error $errno (".ldap_error($ldap).") for $dn");
                        $result = "renamefailed";
                        $action = "displayform";
                    } else {
                        $result = "renameok";
                        $action = "displayentry";
                        $dn = $new_rdn ."," . $parent;
                    }
                }
            }

            # Display form
            if ($action == "displayform") {

                # Search attributes
                $attributes = array();
                foreach( $rename_items as $item ) {
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

                }
            }

        }}
}

if ($audit_log_file) {
    auditlog($audit_log_file, $dn, $audit_admin, "renameentry", $result, $comment);
}

if ( $action == "displayentry" ) {
    $location = 'index.php?page=display&dn='.$dn.'&renameresult='.$result;
    header('Location: '.$location);
}

$smarty->assign("entry", $entry[0]);
$smarty->assign("dn", $dn);
$smarty->assign("action", $action);

$smarty->assign("item_list", $item_list);

$smarty->assign("card_title", $display_title);
$smarty->assign("card_items", $rename_items);
$smarty->assign("show_undef", $display_show_undefined);

?>
