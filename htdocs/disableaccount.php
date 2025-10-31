<?php
/*
 * Disable account in LDAP directory
 */

$result = "";
$dn = "";
$password = "";
$comment = "";
$returnto = "display";
$prehook_message = "";
$prehook_return = 0;
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

        if ( isset($prehook['accountDisable']) ) {
            list($prehook_return, $prehook_message) =
                hook($prehook, 'accountDisable', $ldapInstance, $dn, array());
        }

        if ( $prehook_return > 0 and !$prehook['accountDisable']['ignoreError']) {
            $result = "hookerror";
        } else {
            if ( $directory->disableAccount($ldap, $dn) ) {
                $result = "accountdisabled";
            } else {
                $result = "ldaperror";
            }
        }
        if ( $result === "accountdisabled" && isset($posthook['accountDisable']) ) {

            list($posthook_return, $posthook_message) =
                hook($posthook, 'accountDisable', $ldapInstance, $dn, array());
            }
        }

    }
}

if ($audit_log_file) {
    auditlog($audit_log_file, $dn, $audit_admin, "disableaccount", $result, $comment);
}

$location = 'index.php?page='.$returnto.'&dn='.urlencode($dn).'&disableaccountresult='.$result;
if ( isset($prehook_return) and $prehook['accountDisable']['displayError'] and $prehook_return > 0 ) {
    $location .= '&prehookdisableresult='.$prehook_message;
}
if ( isset($posthook_return) and $posthook['accountDisable']['displayError'] and $posthook_return > 0 ) {
    $location .= '&posthookdisableresult='.$posthook_message;
}
header('Location: '.$location);
