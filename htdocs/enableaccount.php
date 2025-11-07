<?php
/*
 * Enable account in LDAP directory
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

if (!$use_enableaccount) {
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

        if ( isset($hook_login_attribute) ) {
            $hook_login = get_hook_login($dn, $ldapInstance, $hook_login_attribute);
        }

        list($prehook_return, $prehook_message) =
            hook($prehook, 'accountEnable', $hook_login, array());

        if ( $prehook_return > 0 and !$prehook['accountEnable']['ignoreError']) {
            $result = "hookerror";
        } else {
            if ( $directory->enableAccount($ldap, $dn) ) {
                $result = "accountenabled";
            } else {
                $result = "ldaperror";
            }
        }

        if ( $result === "accountenabled" ) {
            list($posthook_return, $posthook_message) =
                hook($posthook, 'accountEnable', $hook_login, array());
        }
    }
}

if ($audit_log_file) {
    auditlog($audit_log_file, $dn, $audit_admin, "enableaccount", $result, $comment);
}

$location = 'index.php?page='.$returnto.'&dn='.urlencode($dn).'&enableaccountresult='.$result;
if ( isset($prehook_return) and $prehook['accountEnable']['displayError'] and $prehook_return > 0 ) {
    $location .= '&prehookenableresult='.$prehook_message;
}
if ( isset($posthook_return) and $posthook['accountEnable']['displayError'] and $posthook_return > 0 ) {
    $location .= '&posthookenableresult='.$posthook_message;
}
header('Location: '.$location);
