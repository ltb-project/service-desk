<?php
/*
 * Delete account in LDAP directory
 */

$result = "";
$dn = "";
$comment = "";
$returnto = "welcome";
$prehook_message = "";
$prehook_return = 0;
$posthook_message = "";
$posthook_return = 0;

if (isset($_POST["returnto"]) and $_POST["returnto"]) {
    $returnto = $_POST["returnto"];
}

if (isset($_POST["dn"]) and $_POST["dn"]) {
    $dn = $_POST["dn"];
} else if (isset($_GET["dn"]) and $_GET["dn"]) {
    $dn = $_GET["dn"];
} else {
    $result = "dnrequired";
}

if (isset($_GET["returnto"]) and $_GET["returnto"]) {
    $returnto = $_GET["returnto"];
}

if (isset($_POST["comment"]) and $_POST["comment"]) {
    $comment = $_POST["comment"];
}

if ($result === "") {

    require_once("../conf/config.inc.php");
    require __DIR__ . '/../vendor/autoload.php';
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

            if ( isset($hook_login_attribute) ) {
                $hook_login = get_hook_login($dn, $ldapInstance, $hook_login_attribute);
            }

            list($prehook_return, $prehook_message) =
                  hook($hook_config['deleteAccount']['before'] ?? null, 'deleteAccount', $hook_login, array());

            if ( $prehook_return > 0 and !$hook['deleteAccount']['before']['ignoreError']) {
                $result = "hookerror";
            } else {
                if ( ldap_delete($ldap, $dn) ) {
                    $result = "deleteok";
                } else {
                    $result = "deletefailed";
                }
            }

            if ( $result === "deleteok" ) {
                list($posthook_return, $posthook_message) =
                      hook($hook_config['deleteAccount']['after'] ?? null, 'deleteAccount', $hook_login, array());
            }
        }
    }
}

if ($audit_log_file) {
    auditlog($audit_log_file, $dn, $audit_admin, "deleteentry", $result, $comment);
}

$location = 'index.php?page='.$returnto.'&dn='.urlencode($dn).'&deleteaccountresult='.$result;
if ( isset($prehook_return) and
     isset($hook_config['deleteAccount']['before']['displayError']) and
     $hook_config['deleteAccount']['before']['displayError'] and
     $prehook_return > 0 ) {
    $location .= '&prehookdeleteresult='.$prehook_message;
}
if ( isset($posthook_return) and
     isset($hook_config['deleteAccount']['after']['displayError']) and
     $hook_config['deleteAccount']['after']['displayError'] and
     $posthook_return > 0 ) {
    $location .= '&posthookdeleteresult='.$posthook_message;
}
header('Location: '.$location);
