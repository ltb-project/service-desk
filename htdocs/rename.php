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

            # Rename entry
            if ($action == "renameentry") {

                # Compute new RDN
                $cur_dn = $dn;
                $new_rdn = "";
                foreach ($rename_items as $item) {
                    if (isset($_POST[$item]) and !empty($_POST[$item])) {
                        $value = $_POST[$item];
                        if (!empty($new_rdn)) { $new_rdn .= "+"; }
                        $new_rdn .= $attributes_map[$item]['attribute'] ."=". ldap_escape($value, "", LDAP_ESCAPE_DN);
                    }
                }

                # Get current RDN and parent
                $dn_explode = ldap_explode_dn($dn, 0);
                $dn_parent = "";
                for ($i = 1; $i < $dn_explode['count']; $i++) {
                    $dn_parent .= $dn_explode[$i] . ",";
                }
                $parent = trim($dn_parent, ',');

                if ( isset($hook_login_attribute) ) {
                    $hook_login = get_hook_login($dn, $ldapInstance, $hook_login_attribute);
                }

                list($prehook_return, $prehook_message) =
                      hook($hook_config['renameAccount']['before'] ?? null,
                           'renameAccount',
                           $hook_login,
                           array("dn" => $cur_dn, "new_rdn" => $new_rdn, "parent" => $parent));

                if ( $prehook_return > 0 and !$hook['renameAccount']['before']['ignoreError']) {
                    $result = "hookerror";
                    $action = "displayentry";
                } else {
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
                if ( $result === "renameok" ) {
                    list($posthook_return, $posthook_message) =
                          hook($hook_config['renameAccount']['after'] ?? null,
                               'renameAccount',
                               $hook_login,
                               array("dn" => $cur_dn, "new_rdn" => $new_rdn, "parent" => $parent));
                }

                if ($audit_log_file) {
                    auditlog($audit_log_file, $dn, $audit_admin, "renameentry", $result, $comment ?? "");
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
                    $entries = ldap_get_entries($ldap, $search);
                    $entry = $ldapInstance->sortEntry($entries[0], $attributes_map);
                }
            }

        }}
}

if ( $action == "displayentry" ) {
    $location = 'index.php?page=display&dn='.urlencode($dn).'&renameresult='.$result;

    if ( isset($prehook_return) and
         isset($hook_config['renameAccount']['before']['displayError']) and
         $hook_config['renameAccount']['before']['displayError'] and
         $prehook_return > 0 ) {
        $location .= '&prehookrenameresult='.$prehook_message;
    }
    if ( isset($posthook_return) and
         isset($hook_config['renameAccount']['after']['displayError']) and
         $hook_config['renameAccount']['after']['displayError'] and
         $posthook_return > 0 ) {
        $location .= '&posthookrenameresult='.$posthook_message;
    }

    header('Location: '.$location);
}

$smarty->assign("entry", $entry);
$smarty->assign("dn", $dn);
$smarty->assign("action", $action);

$smarty->assign("item_list", $item_list ?? null);

$smarty->assign("card_title", $display_title);
$smarty->assign("card_items", $rename_items);
$smarty->assign("show_undef", $display_show_undefined);

?>
