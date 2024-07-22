<?php
/*
 * Lock account in LDAP directory
 */

$result = "";
$dn = "";
$password = "";

if (isset($_POST["dn"]) and $_POST["dn"]) {
    $dn = $_POST["dn"];
} else {
    $result = "dnrequired";
}

if ($result === "") {

    require_once("../conf/config.inc.php");
    require __DIR__ . '/../vendor/autoload.php';

    # Connect to LDAP
    $ldap_connection = \Ltb\Ldap::connect($ldap_url, $ldap_starttls, $ldap_binddn, $ldap_bindpw, $ldap_network_timeout);

    $ldap = $ldap_connection[0];
    $result = $ldap_connection[1];


    # Consider pwdLockout = false by default
    $pwdLockout = false;

    # Search pwdLockout in associated ppolicy
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

        # Search pwdLockout attribute
        if ($pwdPolicy) {
                $search_ppolicy = ldap_read($ldap, $pwdPolicy, "(objectClass=pwdPolicy)", array('pwdlockout'));

                if ( $errno ) {
                    error_log("LDAP - PPolicy search error $errno  (".ldap_error($ldap).")");
                } else {
                    $ppolicy_entry = ldap_get_entries($ldap, $search_ppolicy);
                    $pwdLockout = strtolower($ppolicy_entry[0]['pwdlockout'][0]) == "true" ? true : false;
                    if( $pwdLockout == false )
                    {
                        error_log("No pwdLockout or pwdLockout=FALSE in associated ppolicy: ".$pwdPolicy.". Account locking disabled");
                    }
                }
        }
    }

    # apply the modification only if a password policy set with pwdLockout=TRUE is associated to the account
    if ($ldap and $pwdLockout == true) {
        $modification = ldap_mod_replace($ldap, $dn, array("pwdAccountLockedTime" => array("000001010000Z")));
        $errno = ldap_errno($ldap);
        if ( $errno ) {
            $result = "ldaperror";
        } else {
            $result = "accountlocked";
        }
    }
}

if ($audit_log_file) {
    auditlog($audit_log_file, $dn, $audit_admin, "lockaccount", $result, $_POST["comment"]);
}

header('Location: index.php?page=display&dn='.$dn.'&lockaccountresult='.$result);
