<?php
/*
 * Create an entry
 */

$result = "";
$dn = "";
$entry = "";
$action = "displayform";
$result = "";

if (isset($_POST["action"]) and $_POST["action"]) {
    $action = $_POST["action"];
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

            # Create entry
            if ($action == "createentry") {

                # Get all data
                $create_attributes = array();
                foreach ($create_items as $item) {
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

                    if (!empty($values)) {
                        $create_attributes[ $attributes_map[$item]['attribute'] ] = $values;
                    }
                }

                $create_attributes['objectclass'] = $create_objectclass;

                # Use macros
                foreach ($create_items_macros as $item => $macro) {
                    $value = preg_replace_callback('/%(\w+)%/',
                        function ($matches) use ($item, $create_attributes, $attributes_map) {
                            return $create_attributes[ $attributes_map[$matches[1]]['attribute'] ][0];
                        },
                        $macro);
                    error_log( "Use macro $macro for item $item: $value" );
                    $create_attributes[ $attributes_map[$item]['attribute'] ] = $value;
                }

                # Build DN
                $dn = "";

                foreach ($create_dn_items as $dn_item) {
                    $attribute = $attributes_map[$dn_item]['attribute'];
                    if ($dn) { $dn .= "+"; }
                    $dn .= $attribute . "=" . ldap_escape($create_attributes[$attribute][0], "", LDAP_ESCAPE_DN);
                }

                $dn .= "," . $create_base;


                list($prehook_return, $prehook_message, $create_attributes) =
                      hook($hook_config['createAccount']['before'] ?? null, 'createAccount', "", array("dn" => $dn, "entry" => $create_attributes));

                if ( $prehook_return > 0 and !$hook['createAccount']['before']['ignoreError']) {
                    $result = "hookerror";
                } else {
                    # Create entry
                    if (!ldap_add($ldap, $dn, $create_attributes)) {
                        error_log("LDAP - modify failed for $dn");
                        $result = "createfailed";
                        $action = "displayform";
                    } else {
                        $errno = ldap_errno($ldap);
                        if ( $errno ) {
                            error_log("LDAP - create error $errno (".ldap_error($ldap).") for $dn");
                            $result = "createfailed";
                            $action = "displayform";
                        } else {
                            $result = "createok";
                            $action = "displayentry";
                        }
                    }
                }

                if ( $result === "createok" ) {
                    list($posthook_return, $posthook_message) =
                          hook($hook_config['createAccount']['after'] ?? null, 'createAccount', "", array("dn" => $dn, "entry" => $create_attributes));
                }


                if ($audit_log_file) {
                    auditlog($audit_log_file, $dn, $audit_admin, "createentry", $result, $comment ?? "" );
                }

            }

            # Display form
            if ($action == "displayform") {

                # Compute lists
                $item_list = array();

                foreach ($create_items as $item) {
                    if ( $attributes_map[$item]["type"] === "static_list") {
                        $item_list[$item] = isset($attributes_static_list[$item]) ? $attributes_static_list[$item] : array();
                    }
                    if ( $attributes_map[$item]["type"] === "list") {
                        $item_list[$item] = $ldapInstance->get_list( $attributes_list[$item]["base"], $attributes_list[$item]["filter"], $attributes_list[$item]["key"], $attributes_list[$item]["value"]  );
                    }
                }

            }
    }
}

if ( $action == "displayentry" ) {
    $location = 'index.php?page=display&dn='.urlencode($dn).'&createresult='.$result;
    if ( isset($prehook_return) and
         isset($hook_config['createAccount']['before']['displayError']) and
         $hook_config['createAccount']['before']['displayError'] and
         $prehook_return > 0 ) {
        $location .= '&prehookcreateresult='.$prehook_message;
    }
    if ( isset($posthook_return) and
         isset($hook_config['createAccount']['after']['displayError']) and
         $hook_config['createAccount']['after']['displayError'] and
         $posthook_return > 0 ) {
        $location .= '&posthookcreateresult='.$posthook_message;
    }
    header('Location: '.$location);
}

$smarty->assign("entry", $entry);
$smarty->assign("action", $action);

$smarty->assign("item_list", $item_list ?? null);

$smarty->assign("create_items", $create_items);
$smarty->assign("show_undef", $display_show_undefined);

?>
