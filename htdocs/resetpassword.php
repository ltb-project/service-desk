<?php
/*
 * Reset password in LDAP directory
 */

$result = "";
$dn = "";
$password = "";
$pwdreset = "";
$posthook_message = "";

if (isset($_POST["dn"]) and $_POST["dn"]) {
    $dn = $_POST["dn"];
} else {
    $result = "dnrequired";
}

if (isset($_POST["newpassword"]) and $_POST["newpassword"]) {
    $password = $_POST["newpassword"];
} else {
    $result = "passwordrequired";
}

if (isset($_POST["pwdreset"]) and $_POST["pwdreset"]) {
    $pwdreset = $_POST["pwdreset"];
}

if ($result === "") {

    require_once("../conf/config.inc.php");
    require_once("../lib/ldap.inc.php");
    require_once("../lib/posthook.inc.php");

    # Connect to LDAP
    $ldap_connection = wp_ldap_connect($ldap_url, $ldap_starttls, $ldap_binddn, $ldap_bindpw);

    $ldap = $ldap_connection[0];
    $result = $ldap_connection[1];

    if ($ldap) {
        $entry["userPassword"] = $password;
	if ( $pwdreset === "true" ) {
            $entry["pwdReset"] = "TRUE";
	}
	$modification = ldap_mod_replace($ldap, $dn, $entry);
	$errno = ldap_errno($ldap);
        if ( $errno ) {
            $result = "passwordrefused";
        } else {
            $result = "passwordchanged";
        }

	if ( $result === "passwordchanged" && isset($posthook) ) {

             $login_search = ldap_read($ldap, $dn, '(objectClass=*)', array($posthook_login));
             $login_entry = ldap_first_entry( $ldap, $login_search );
             $login_values = ldap_get_values( $ldap, $login_entry, $posthook_login );
             $login = $login_values[0];

             if ( !isset($login) ) {
                 $posthook_return = 255;
                 $posthook_message = "No login found, cannot execute posthook script";
             } else {
                 $command = posthook_command($posthook, $login, $password, $posthook_password_encodebase64);
		 exec($command, $posthook_output, $posthook_return);
	         $posthook_message = $posthook_output[0];
             }
        }
    }
}

$location = 'index.php?page=display&dn='.$dn.'&resetpasswordresult='.$result;
if ( isset($posthook_return) and $display_posthook_error and $posthook_return > 0 ) {
    $location .= '&posthookresult='.$posthook_message;
}

header('Location: '.$location);
