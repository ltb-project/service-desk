<?php
/*
 * Disable account in LDAP directory
 */

$result = "";
$dn = "";
$password = "";
$comment = "";
$returnto = "display";
$prehook_login_value = "";
$prehook_message = "";
$prehook_return = 0;
$posthook_login_value = "";
$posthook_message = ""; 
$posthook_return = 0;

if (isset($_POST["returnto"]) and $_POST["returnto"]) {
    $returnto = $_POST["returnto"];
}

if (isset($_POST["dn"]) and $_POST["dn"]) {
    $dn = $_POST["dn"];
} else {
    $result = "dnrequired";
}

if (isset($_POST["comment"]) and $_POST["comment"]) {
    $comment = $_POST["comment"];
}


if (!$use_disableaccount) {
    $result = "actionforbidden";
}

if ($result === "") {

    require_once("../conf/config.inc.php");
    require __DIR__ . '/../vendor/autoload.php';
    require_once("../lib/hook.inc.php");

    # Connect to LDAP
    $ldap_connection = $ldapInstance->connect();

    $ldap = $ldap_connection[0];
    $result = $ldap_connection[1];

    # DN match
    if ( !$ldapInstance->matchDn($dn, $dnAttribute, $ldap_user_filter, $ldap_user_base, $ldap_scope) ) {
        $result = "noentriesfound";
        error_log("LDAP - $dn not found using the configured search settings, reject request");
    } else {

        if ( isset($prehook_disable) || isset($posthook_disable) ) {
            if ( isset($prehook_login) ) {
                $prehook_login_value = $ldapInstance->get_first_value($dn, "base", '(objectClass=*)', $prehook_login);
            }
            if ( isset($posthook_login) ) {
                $posthook_login_value = $ldapInstance->get_first_value($dn, "base", '(objectClass=*)', $posthook_login);
            }
        }
        if ( isset($prehook_disable) ) {

            if ( !isset($prehook_login_value) ) {
                $prehook_return = 255;
                $prehook_message = "No login found, cannot execute prehook script";
            } else {
                $command = hook_command($prehook_disable, $prehook_login_value);
                exec($command, $prehook_output, $prehook_return);
                $prehook_message = $prehook_output[0];
            }
        }

        if ( $prehook_return > 0 and !$ignore_prehook_disable_error) {
            $result = "hookerror";
        } else {
            if ( $directory->disableAccount($ldap, $dn) ) {
                $result = "accountdisabled";
            } else {
                $result = "ldaperror";
            }
        }
        if ( $result === "accountdisabled" && isset($posthook_disable) ) {

            if ( !isset($posthook_login_value) ) {
                $posthook_return = 255;
                $posthook_message = "No login found, cannot execute posthook script";
            } else {
                $command = hook_command($posthook_disable, $posthook_login_value);
                exec($command, $posthook_output, $posthook_return);
                $posthook_message = $posthook_output[0];
            }
        }

    }
}

if ($audit_log_file) {
    auditlog($audit_log_file, $dn, $audit_admin, "disableaccount", $result, $comment);
}

$location = 'index.php?page='.$returnto.'&dn='.$dn.'&disableaccountresult='.$result;
if ( isset($prehook_return) and $display_prehook_disable_error and $prehook_return > 0 ) {
    $location .= '&prehookdisableresult='.$prehook_message;
}
if ( isset($posthook_return) and $display_posthook_disable_error and $posthook_return > 0 ) {
    $location .= '&posthookdisableresult='.$posthook_message;
}
header('Location: '.$location);
