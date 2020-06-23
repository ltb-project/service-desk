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
    require_once("../lib/ldap.inc.php");

    # Connect to LDAP
    $ldap_connection = wp_ldap_connect($ldap_url, $ldap_starttls, $ldap_binddn, $ldap_bindpw);

    $ldap = $ldap_connection[0];
    $result = $ldap_connection[1];

    if ($ldap) {
        $modification = ldap_mod_replace($ldap, $dn, array("pwdAccountLockedTime" => array("000001010000Z")));
        $errno = ldap_errno($ldap);
        if ( $errno ) {
            $result = "ldaperror";
        }
    }
}

header('Location: index.php?page=display&dn='.$dn.'&lockaccountresult='.$result);
