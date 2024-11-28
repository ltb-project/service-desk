<?php
/*
 * Update start time and end time in LDAP directory
 */

$result = "";
$dn = "";
$start_date = "";
$end_date = "";
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

if (isset($_POST["start_date"]) and $_POST["start_date"]) {
    $start_date = $_POST["start_date"];
}

if (isset($_POST["end_date"]) and $_POST["end_date"]) {
    $end_date = $_POST["end_date"];
}

if (!($use_updatestarttime or $use_updateendtime)) {
    $result = "actionforbidden";
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
        if ($use_updatestarttime and $start_date) {
            $startDate = new DateTime($start_date);
            $ldapStartDate = $directory->getLdapDate($startDate);
            $update = $ldapInstance->modify_attributes($dn, array( $attributes_map['starttime']['attribute'] => $ldapStartDate));
            if ( $update[0] == 0 ) {
                $result = "validiydatesupdated";
            } else {
                $result = "ldaperror";
            }
        }
        if ($use_updateendtime and $end_date) {
            $endDate = new DateTime($end_date);
            $ldapEndDate = $directory->getLdapDate($endDate);
            $update = $ldapInstance->modify_attributes($dn, array( $attributes_map['endtime']['attribute'] => $ldapEndDate));
            if ( $update[0] == 0 and $result !== "ldaperror" ) {
                $result = "validiydatesupdated";
            } else {
                $result = "ldaperror";
            }
       }
    }
}

if ($audit_log_file) {
    auditlog($audit_log_file, $dn, $audit_admin, "updatevaliditydates", $result, $comment);
}

header('Location: index.php?page='.$returnto.'&dn='.$dn.'&updatevaliditydatesresult='.$result);
