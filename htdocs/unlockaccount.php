<?php
/*
 * Unlock account in LDAP directory
 */

$result = "";
$dn = "";
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

            if ( isset($prehook['passwordUnlock']) ) {
               list($prehook_return, $prehook_message) =
                    hook($prehook, 'passwordUnlock', $ldapInstance, $dn, array());
            }

            if ( $prehook_return > 0 and !$prehook['passwordUnlock']['ignoreError']) {
                $result = "hookerror";
            } else {
                if ( $directory->unlockAccount($ldap, $dn) ) {
                    $result = "accountunlocked";
                } else {
                    $result = "ldaperror";
                }
            }

            if ( $result === "accountunlocked" && isset($posthook['passwordUnlock']) ) {
               list($posthook_return, $posthook_message) =
                    hook($posthook, 'passwordUnlock', $ldapInstance, $dn, array());
            }
        }
    }
}

if ($audit_log_file) {
    auditlog($audit_log_file, $dn, $audit_admin, "unlockaccount", $result, $comment);
}

$location = 'index.php?page='.$returnto.'&dn='.urlencode($dn).'&unlockaccountresult='.$result;
if ( isset($prehook_return) and $prehook['passwordUnlock']['displayError'] and $prehook_return > 0 ) {
    $location .= '&prehookunlockresult='.$prehook_message;
}
if ( isset($posthook_return) and $posthook['passwordUnlock']['displayError'] and $posthook_return > 0 ) {
    $location .= '&posthookunlockresult='.$posthook_message;
}
header('Location: '.$location);
