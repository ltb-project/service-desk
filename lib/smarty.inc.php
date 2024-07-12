<?php
# Smarty functions

require __DIR__ . '/../vendor/autoload.php';
require_once("date.inc.php");
require_once("filesize.inc.php");

function get_attribute($params) {

    $return = "";
    $dn = $params["dn"];
    $attribute = $params["attribute"];
    $ldap_url = $params["ldap_url"];
    $ldap_starttls = $params["ldap_starttls"];
    $ldap_binddn = $params["ldap_binddn"];
    $ldap_bindpw = $params["ldap_bindpw"];
    $ldap_filter = $params["ldap_filter"];

    $ldapInstance = new \Ltb\Ldap(
                                 $ldap_url,
                                 $ldap_starttls,
                                 isset($ldap_binddn) ? $ldap_binddn : null,
                                 isset($ldap_bindpw) ? $ldap_bindpw : null,
                                 isset($ldap_network_timeout) ? $ldap_network_timeout : null,
                                 $dn,
                                 null,
                                 isset($ldap_krb5ccname) ? $ldap_krb5ccname : null
                             );

    # Connect to LDAP
    $ldap_connection = $ldapInstance->connect();

    $ldap = $ldap_connection[0];
    $result = $ldap_connection[1];

    if ($ldap) {

        # Search entry
        $search = ldap_read($ldap, $dn, $ldap_filter, explode(",", $attribute));

        $errno = ldap_errno($ldap);

        if ( $errno ) {
            error_log("LDAP - Search error $errno  (".ldap_error($ldap).")");
        } else {
            $entry = ldap_get_entries($ldap, $search);

	    # Loop over attribute
	    foreach ( explode(",", $attribute) as $ldap_attribute ) {
                if ( isset ($entry[0][$ldap_attribute]) ) {
		     $return = $entry[0][$ldap_attribute][0];
		     break;
	        }
	    }
        }
    }

    return $return;
}

function convert_ldap_date($date) {

    return ldapDate2phpDate( $date );

}

function convert_bytes($bytes) {

    return FileSizeConvert( $bytes );

}

function split_value($value,$separator) {

    return explode( $separator, $value );

}
?>
