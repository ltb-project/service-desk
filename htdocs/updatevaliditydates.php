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
$prehook_message = "";
$prehook_return = 0;
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

        if ( isset($hook_login_attribute) ) {
            $hook_login = get_hook_login($dn, $ldapInstance, $hook_login_attribute);
        }

        list($prehook_return, $prehook_message) =
            hook($prehook, 'updateValidityDates', $hook_login,
                 array('start_date' => $start_date, 'end_date' => $end_date));

        if ( $prehook_return > 0 and !$prehook['updateValidityDates']['ignoreError']) {
            $result = "hookerror";
        } else {

            if ($use_updatestarttime) {
                $ldapStartDate = $start_date ? $directory->getLdapDate(new DateTime($start_date)) : array();
                $update = $ldapInstance->modify_attributes($dn, array( $attributes_map['starttime']['attribute'] => $ldapStartDate));
                if ( $update[0] == 0 ) {
                    $result = "validitydatesupdated";
                } else {
                    $result = "ldaperror";
                }
            }
            if ($use_updateendtime) {
                $ldapEndDate = $end_date ? $directory->getLdapDate(new DateTime($end_date)) : array();
                $update = $ldapInstance->modify_attributes($dn, array( $attributes_map['endtime']['attribute'] => $ldapEndDate));
                if ( $update[0] == 0 and $result !== "ldaperror" ) {
                    $result = "validitydatesupdated";
                } else {
                    $result = "ldaperror";
                }
            }
        }

        if ( $result === "validitydatesupdated" ) {

            list($posthook_return, $posthook_message) =
                hook($posthook, 'updateValidityDates', $hook_login,
                     array('start_date' => $start_date, 'end_date' => $end_date));
        }

    }
}

if ($audit_log_file) {
    auditlog($audit_log_file, $dn, $audit_admin, "updatevaliditydates", $result, $comment);
}

$location = 'index.php?page='.$returnto.'&dn='.urlencode($dn).'&updatevaliditydatesresult='.$result;
if ( isset($prehook_return) and $prehook['updateValidityDates']['displayError'] and $prehook_return > 0 ) {
    $location .= '&prehookupdatevalidityresult='.$prehook_message;
}
if ( isset($posthook_return) and $posthook['updateValidityDates']['displayError'] and $posthook_return > 0 ) {
    $location .= '&posthookupdatevalidityresult='.$posthook_message;
}
header('Location: '.$location);
