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
$prehook_login_value = "";
$prehook_message = "";
$prehook_return = 0;
$posthook_login_value = "";
$posthook_message = "";
$posthook_return = 0;

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
    require_once("../lib/hook.inc.php");

    # Connect to LDAP
    $ldap_connection = $ldapInstance->connect();

    $ldap = $ldap_connection[0];
    $result = $ldap_connection[1];

    # DN match
    if ( !$ldapInstance->matchDn($dn, $dnAttribute, $ldap_user_filter, $ldap_user_base, $ldap_scope) ) {
        $result = "noentriesfound";
        error_log("LDAP - $dn not found using the configured search settings, reject request");
    } else {

        if ( isset($prehook_updatevalidity) || isset($posthook_updatevalidity) ) {
            if ( isset($prehook_login) ) {
                $prehook_login_value = $ldapInstance->get_first_value($dn, "base", '(objectClass=*)', $prehook_login);
            }
            if ( isset($posthook_login) ) {
                $posthook_login_value = $ldapInstance->get_first_value($dn, "base", '(objectClass=*)', $posthook_login);
            }
        }
        if ( isset($prehook_updatevalidity) ) {

            if ( !isset($prehook_login_value) ) {
                $prehook_return = 255;
                $prehook_message = "No login found, cannot execute prehook script";
            } else {
                    $command = validity_hook_command($prehook_updatevalidity, $prehook_login_value, $start_date, $end_date);
                exec($command, $prehook_output, $prehook_return);
                $prehook_message = $prehook_output[0];
            }
        }

        if ( $prehook_return > 0 and !$ignore_prehook_updatevalidity_error) {
            $result = "hookerror";
        } else {

            if ($use_updatestarttime) {
                $ldapStartDate = $start_date ? $directory->getLdapDate(new DateTime($start_date)) : array();
                $update = $ldapInstance->modify_attributes($dn, array( $attributes_map['starttime']['attribute'] => $ldapStartDate));
                if ( $update[0] == 0 ) {
                    $result = "validiydatesupdated";
                } else {
                    $result = "ldaperror";
                }
            }
            if ($use_updateendtime) {
                $ldapEndDate = $end_date ? $directory->getLdapDate(new DateTime($end_date)) : array();
                $update = $ldapInstance->modify_attributes($dn, array( $attributes_map['endtime']['attribute'] => $ldapEndDate));
                if ( $update[0] == 0 and $result !== "ldaperror" ) {
                    $result = "validiydatesupdated";
                } else {
                    $result = "ldaperror";
                }
            }
        }

        if ( $result === "validiydatesupdated" && isset($posthook_updatevalidity) ) {

            if ( !isset($posthook_login_value) ) {
                $posthook_return = 255;
                $posthook_message = "No login found, cannot execute posthook script";
            } else {
                $command = validiy_hook_command($posthook_updatevalidity, $posthook_login_value, $start_date, $end_date);
                exec($command, $posthook_output, $posthook_return);
                $posthook_message = $posthook_output[0];
            }
        }

    }
}

if ($audit_log_file) {
    auditlog($audit_log_file, $dn, $audit_admin, "updatevaliditydates", $result, $comment);
}

$location = 'index.php?page='.$returnto.'&dn='.urlencode($dn).'&updatevaliditydatesresult='.$result;
if ( isset($prehook_return) and $display_prehook_updatevalidity_error and $prehook_return > 0 ) {
    $location .= '&prehookupdatevalidityresult='.$prehook_message;
}
if ( isset($posthook_return) and $display_posthook_updatevalidity_error and $posthook_return > 0 ) {
    $location .= '&posthookupdatevalidityresult='.$posthook_message;
}
header('Location: '.$location);
