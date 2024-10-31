<?php
/*
 * Lock account in LDAP directory
 */

$result = "";
$dn = "";
$comment = "";
$returnto = "display";

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

if ($result === "") {

    require_once("../conf/config.inc.php");
    require __DIR__ . '/../vendor/autoload.php';

    # Connect to LDAP
    $ldap_connection = $ldapInstance->connect();

    $ldap = $ldap_connection[0];
    $result = $ldap_connection[1];

    if ($ldap)
    {
        # Get password policy configuration
        $pwdPolicyConfiguration = $directory->getPwdPolicyConfiguration($ldap, $dn, $ldap_default_ppolicy);
        if ($ldap_lockout_duration) { $pwdPolicyConfiguration['lockout_duration'] = $ldap_lockout_durantion; }
        if ($ldap_password_max_age) { $pwdPolicyConfiguration['password_max_age'] = $ldap_password_max_age; }

        # Apply the modification only the password can be locked
        if ($pwdPolicyConfiguration["lockout_enabled"]) {
            if ( $directory->lockAccount($ldap, $dn) ) {
                $result = "accountlocked";
            } else {
                $result = "ldaperror";
            }
        }
    }
}

if ($audit_log_file) {
    auditlog($audit_log_file, $dn, $audit_admin, "lockaccount", $result, $comment);
}

header('Location: index.php?page='.$returnto.'&dn='.$dn.'&lockaccountresult='.$result);
