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
    require_once("../lib/hook.inc.php");

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
                    $values = array();
                    $item_keys = preg_grep("/^$item(\d+)$/", array_keys($_POST));
                    foreach ($item_keys as $item_key) {
                        if (isset($_POST[$item_key]) and !empty($_POST[$item_key])) {
                            $value = $_POST[$item_key];
                            if ( $attributes_map[$item]['type'] == "date" ||  $attributes_map[$item]['type'] == "ad_date" ) {
                                $value = $directory->getLdapDate(new DateTime($_POST[$item_key]));
                            }
                            $values[] = $value;
                        }
                    }

                    $update_attributes[ $attributes_map[$item]['attribute'] ] = $values;
                }

                # Use macros
                foreach ($update_items_macros as $item => $macro) {
                    $value = preg_replace_callback('/%(\w+)%/',
                        function ($matches) use ($item, $update_attributes, $attributes_map) {
                            return $update_attributes[ $attributes_map[$matches[1]]['attribute'] ][0];
                        },
                        $macro);
                    error_log( "Use macro $macro for item $item: $value" );
                    $update_attributes[ $attributes_map[$item]['attribute'] ] = $value;
                }


                list($prehook_return, $prehook_message, $update_attributes) =
                      hook($prehook, 'updateAccount', "", array("dn" => $dn, "entry" => $update_attributes));

                if ( $prehook_return > 0 and !$prehook['updateAccount']['ignoreError']) {
                    $result = "hookerror";
                } else {
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

                if ( $result === "updateok" ) {
                    list($posthook_return, $posthook_message) =
                          hook($posthook, 'updateAccount', "", array("dn" => $dn, "entry" => $update_attributes));
                }

                if ($audit_log_file) {
                    auditlog($audit_log_file, $dn, $audit_admin, "updateentry", $result, $comment);
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

                    $entries = ldap_get_entries($ldap, $search);
                    $entry = $ldapInstance->sortEntry($entries[0], $attributes_map);

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

if ( $action == "displayentry" ) {
    $location = 'index.php?page=display&dn='.urlencode($dn).'&updateresult='.$result;
    if ( isset($prehook_return) and $prehook['updateAccount']['displayError'] and $prehook_return > 0 ) {
        $location .= '&prehookresult='.$prehook_message;
    }
    if ( isset($posthook_return) and $posthook['updateAccount']['displayError'] and $posthook_return > 0 ) {
        $location .= '&posthookresult='.$posthook_message;
    }
    header('Location: '.$location);
}

$smarty->assign("entry", $entry);
$smarty->assign("dn", $dn);
$smarty->assign("action", $action);

$smarty->assign("item_list", $item_list);

$smarty->assign("card_title", $display_title);
$smarty->assign("card_items", array_unique(array_merge($display_items, $update_items)));
$smarty->assign("update_items", $update_items);
$smarty->assign("show_undef", $display_show_undefined);

?>
