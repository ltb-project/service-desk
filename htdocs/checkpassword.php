<?php
/*
 * Check password in LDAP directory
 */

$result = "";
$dn = "";
$password = "";

if (isset($_POST["dn"]) and $_POST["dn"]) {
    $dn = $_POST["dn"];
} else {
    $result = "dnrequired";
}

if (isset($_POST["currentpassword"]) and $_POST["currentpassword"]) {
    $password = $_POST["currentpassword"];
} else {
    $result = "passwordrequired";
}



if ($result === "") {

    require_once("../conf/config.inc.php");
    require __DIR__ . '/../vendor/autoload.php';

    # Connect to LDAP
    $ldap_connection = $ldapInstance->connect();

    $ldap = $ldap_connection[0];
    $result = $ldap_connection[1];

    if( !$result ) {

        $bind = ldap_bind($ldap, $dn, $password);
        $result = $bind ? "passwordok" : "ldaperror";
    }

}

if ($audit_log_file) {
    auditlog($audit_log_file, $dn, $audit_admin, "checkpassword", $result);
}

header('Location: index.php?page=display&dn='.$dn.'&checkpasswordresult='.$result);
