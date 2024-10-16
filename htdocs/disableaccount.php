<?php
/*
 * Disable account in LDAP directory
 */

$result = "";
$dn = "";
$password = "";
$comment = "";

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

    # Connect to LDAP
    $ldap_connection = $ldapInstance->connect();

    $ldap = $ldap_connection[0];
    $result = $ldap_connection[1];

    if ($ldap) {
        if ( $directory->disableAccount($ldap, $dn) ) {
            $result = "accountdisabled";
        } else {
            $result = "ldaperror";
        }
    }
}

if ($audit_log_file) {
    auditlog($audit_log_file, $dn, $audit_admin, "disableaccount", $result, $comment);
}

header('Location: index.php?page=display&dn='.$dn.'&disableaccountresult='.$result);
