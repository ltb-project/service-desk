<?php
/*
 * Lock account in LDAP directory
 */

$result = "";
$dn = "";
$comment = "";

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

    $pwdPolicy = NULL;

    if ($ldap)
    {
        $search_ppolicysubentry = ldap_read($ldap, $dn, "(objectClass=*)", array('pwdpolicysubentry'));
        $user_entry = ldap_get_entries($ldap, $search_ppolicysubentry);

        # Search active password policy
        $pwdPolicy = "";
        if (isset($user_entry[0]['pwdpolicysubentry'][0])) {
            $pwdPolicy = $user_entry[0]['pwdpolicysubentry'][0];
        } elseif (isset($ldap_default_ppolicy)) {
            $pwdPolicy = $ldap_default_ppolicy;
        }
    }

    # Apply the modification only the password can be locked
    if ($ldap and $directory->canLockAccount($ldap, $dn, array('pwdPolicy' => $pwdPolicy))) {
        if ( $directory->lockAccount($ldap, $dn) ) {
            $result = "accountlocked";
        } else {
            $result = "ldaperror";
        }
    }
}

if ($audit_log_file) {
    auditlog($audit_log_file, $dn, $audit_admin, "lockaccount", $result, $comment);
}

header('Location: index.php?page=display&dn='.$dn.'&lockaccountresult='.$result);
