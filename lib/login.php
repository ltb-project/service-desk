<?php
/*
 * Authentication Handling
 */

// Declare volatile variables
$autherror = "";
$dn = "";
$displayname = "";
$memberOf = "";
$ou = "";
$username = "";
$password = "";
$search_query = "";
$entries = array();

// Sensitive authentication variables:
// These should be cleared each time login.php is called.
$isadmin = false;

// Verify that timezone is correct
date_default_timezone_set('America/Denver');

// Continue session variables
session_start();



// Logon was requested.
if(isset($_POST["username"]) and isset($_POST["password"])) {
    
    $username = strtolower($_POST["username"]);
    $password = $_POST["password"];
    
    // Input validations
    if(!empty($_POST["username"]) and !empty($_POST["password"])) {
        $autherror = logon($username, $password);// Do the login
        $_SESSION["authenticated"] = (!strcmp($autherror,"authsuccess")? true: false);// Log user out should any other condition fail
    }
    elseif (empty($_POST["username"])) {// Username field is empty
        $autherror = "usernamerequired";
    }
    elseif (empty($_POST["password"])) {// Password field is empty
        $autherror = "passwordrequired";
    }
    $smarty->assign('autherror',$autherror);// Pass any error code to Smarty

}




// Logoff if was requested.
if(isset($_POST["logoff"]) and $_POST["logoff"]){
    logoff();
}




function logon( $username, $password) {

    # Connect to LDAP
    require("../conf/config.inc.php");
    require_once("../lib/ldap.inc.php");

    # Connect to LDAP
    $ldap_connection = wp_ldap_connect($ldap_url, $ldap_starttls, $ldap_binddn, $ldap_bindpw);

    $ldap = $ldap_connection[0];
    $result = $ldap_connection[1];

    if ($ldap) {

        # Search filter
        $ldap_filter = "(&".$ldap_user_filter."(|";
        foreach ($search_attributes as $attr) {
            $ldap_filter .= "(".$attr."=".$username.")";
        }
        $ldap_filter .= "))";

        # Search attributes
        $attributes = array("dn", "cn", "surname", "givenname", "mail", "memberOf", "msds-parentdistname", "uid", "samaccountname");

        # Search for users
        $search = ldap_search($ldap, $ldap_user_base, $ldap_filter, $attributes, 0, $ldap_size_limit);
        $errno = ldap_errno($ldap);

        if ( $errno != 0 ) {// If there's an ldap error, stop here.

            $autherror = "LDAP Search error " . $errno . " (" . ldap_error($ldap) . ")";
            error_log("LDAP Search error $errno  (".ldap_error($ldap).")");

        } else {// Else get the entries
            
            $entries = ldap_get_entries($ldap, $search);
            // echo "Entries: "; print_r($entries); echo "<br>";

            if ( $entries['count'] === 1 ) {// Check for only one result

                $dn = $entries[0]['dn'];// Save distinguished name
                $ou = $entries[0]['msds-parentdistname'][0];// Save organizational unit
                $uid = ( !empty($entries[0]['samaccountname'][0]) ? $entries[0]['samaccountname'][0] : $entries[0]['uid'][0] );
                // echo "UID: $uid<br>";
                $displayname = $entries[0]['cn'][0];// Save display name
                $memberOf = $entries[0]['memberof'];// Save group memberships

                if ( !in_array(strtolower($ou), array_map('strtolower', $ldap_disallowed_ous)) ) {// Check if in allowed Organizational Unit
                    
                    $bind = ldap_bind($ldap, $dn, $password);// Bind to LDAP given $dn and $password

                    if ($bind) {// Log them in only if LDAP bind succeeds
                        $autherror = "authsuccess";
                    } else {
                        $autherror = "passwordrefused";
                    }
                } else {
                    $autherror = "usernotallowed";
                }

            } elseif ( $entries['count'] > 1 ) {
                $autherror = "notoneunique";// Too many entries returned
            } else {
                $autherror = "usernotfound";// User not found
            }
        }

        // Admin Checks
        $admincheck1 = ( isset($ldap_allowed_admin_users) and in_array(strtolower($uid), array_map('strtolower', $ldap_allowed_admin_users)) ? true : false );
        $admincheck2 = ( isset($ldap_allowed_admin_ous) and in_array(strtolower($ou), array_map('strtolower', $ldap_allowed_admin_ous)) ? true : false );
        $admincheck3 = ( isset($ldap_allowed_admin_groups) and array_in_array($memberOf, array_map('strtolower', $ldap_allowed_admin_groups)) ? true : false );
        $isadmin = (( $admincheck1 or $admincheck2 or $admincheck3 ) ? true : false );// If any above conditions are met, set the user to be an admin.
        
        // Update Session Variables
        $_SESSION["username"] = $username;
        $_SESSION["displayname"] = $displayname;
        $_SESSION["isadmin"] = $isadmin;
        $_SESSION["entry_dn"] = $dn;

    } else {
        $autherror = "LDAP connection error";
        error_log("LDAP connection error");
    }

    return $autherror;
	    
}// End logon()



function logoff() {
	// Unset all of the session variables.
	$_SESSION = array();

	// If it's desired to kill the session, also delete the session cookie.
	// Note: This will destroy the session, and not just the session data!
	if (ini_get("session.use_cookies")) {
		$params = session_get_cookie_params();
		setcookie(session_name(), '', time() - 42000,
			$params["path"], $params["domain"],
			$params["secure"], $params["httponly"]
		);
	}
	session_destroy();

}// End logoff()



// Multi-dimensional array comparison
function array_in_array($a, $b) {
    foreach ($a as $item) {
        if ( in_array( strtolower($item), $b) ) {
            return true;
        }
    }
    return false;
}// End in_array_r()



?>