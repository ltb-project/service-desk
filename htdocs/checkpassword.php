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

    # DN match
    if ( !$ldapInstance->matchDn($dn, $dnAttribute, $ldap_user_filter, $ldap_user_base, $ldap_scope) ) {
        $result = "noentriesfound";
        error_log("LDAP - $dn not found using the configured search settings, reject request");
    } else {

        if ($use_checkpasswordhistory) {
            $password_history = $ldapInstance->get_attribute_values($dn, "pwdHistory");
            foreach ($password_history as $previous_password) {
                preg_match("/(?<={).*(?=})/", $previous_password, $algorithm);
                preg_match("/{(?<={).*/", $previous_password, $hash);
                if (\Ltb\Password::check_password($password, $hash[0], $algorithm[0])) {
                    $result = "passwordinhistory";
                }
            }
        }

        if (!$result) {
	    $bind = ldap_bind($ldap, $dn, $password);
	    $result = $bind ? "passwordok" : "passwordinvalid";
        }

    }
}

if ($audit_log_file) {
    auditlog($audit_log_file, $dn, $audit_admin, "checkpassword", $result, NULL);
}

header('Location: index.php?page=display&dn='.$dn.'&checkpasswordresult='.$result);
