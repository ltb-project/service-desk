<?php
/*
 * Lock account in LDAP directory
 */

$result = "";
$dn = "";
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

if ($result === "") {

    require_once("../conf/config.inc.php");
    require __DIR__ . '/../vendor/autoload.php';
    require_once("../lib/hook.inc.php");

    # Connect to LDAP
    $ldap_connection = $ldapInstance->connect();

    $ldap = $ldap_connection[0];
    $result = $ldap_connection[1];

    if ($ldap) {
        # DN match
        if ( !$ldapInstance->matchDn($dn, $dnAttribute, $ldap_user_filter, $ldap_user_base, $ldap_scope) ) {
            $result = "noentriesfound";
            error_log("LDAP - $dn not found using the configured search settings, reject request");
        } else {

            if ( isset($hook_login_attribute) ) {
                $hook_login = get_hook_login($dn, $ldapInstance, $hook_login_attribute);
            }

            list($prehook_return, $prehook_message) =
                hook($hook_config['passwordLock']['before'] ?? null, 'passwordLock', $hook_login, array());

            if ( $prehook_return > 0 and !$hook['passwordLock']['before']['ignoreError']) {
                $result = "hookerror";
            } else {
                # Get password policy configuration
                $pwdPolicyConfiguration = $directory->getPwdPolicyConfiguration($ldap, $dn, $ldap_default_ppolicy);
                if (isset($ldap_lockout_duration) and $ldap_lockout_duration) { $pwdPolicyConfiguration['lockout_duration'] = $ldap_lockout_durantion; }
                if (isset($ldap_password_max_age) and $ldap_password_max_age) { $pwdPolicyConfiguration['password_max_age'] = $ldap_password_max_age; }

                # Apply the modification only if the password can be locked
                if ($pwdPolicyConfiguration["lockout_enabled"]) {
                    if ( $directory->lockAccount($ldap, $dn) ) {
                        $result = "accountlocked";
                    } else {
                        $result = "ldaperror";
                    }
                }
            }

            if ( $result === "accountlocked" ) {
                list($posthook_return, $posthook_message) =
                    hook($hook_config['passwordLock']['after'] ?? null, 'passwordLock', $hook_login, array());
            }
        }
    }
}

if ($audit_log_file) {
    auditlog($audit_log_file, $dn, $audit_admin, "lockaccount", $result, $comment);
}

$location = 'index.php?page='.$returnto.'&dn='.urlencode($dn).'&lockaccountresult='.$result;
if ( isset($prehook_return) and
     isset($hook_config['passwordLock']['before']['displayError']) and
     $hook_config['passwordLock']['before']['displayError'] and
     $prehook_return > 0 ) {
    $location .= '&prehooklockresult='.$prehook_message;
}
if ( isset($posthook_return) and
     isset($hook_config['passwordLock']['after']['displayError']) and
     $hook_config['passwordLock']['after']['displayError'] and
     $posthook_return > 0 ) {
    $location .= '&posthooklockresult='.$posthook_message;
}
header('Location: '.$location);
