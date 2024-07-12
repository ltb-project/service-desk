<?php
/*
 * Reset password in LDAP directory
 */

require_once("../lib/mail.inc.php");

$result = "";
$dn = "";
$password = "";
$pwdreset = "";
$prehook_message = "";
$prehook_login_value = "";
$posthook_message = "";
$posthook_login_value = "";

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
    require __DIR__ . '/../vendor/autoload.php';
    require_once("../lib/hook.inc.php");

    # Connect to LDAP
    $ldap_connection = $ldapInstance->connect();

    $ldap = $ldap_connection[0];
    $result = $ldap_connection[1];

    if ($ldap) {

        if ( isset($prehook) || isset($posthook) ) {
            $login_search = ldap_read($ldap, $dn, '(objectClass=*)', array($prehook_login, $posthook_login));
            $login_entry = ldap_first_entry( $ldap, $login_search );
            if ( isset($prehook_login) ) {
                $prehook_login_values = ldap_get_values( $ldap, $login_entry, $prehook_login );
                $prehook_login_value = $prehook_login_values[0];
            }
            if ( isset($posthook_login) ) {
                $posthook_login_values = ldap_get_values( $ldap, $login_entry, $posthook_login );
                $posthook_login_value = $posthook_login_values[0];
            }
        }

        $entry["userPassword"] = $password;
        if ( $pwdreset === "true" ) {
            $entry["pwdReset"] = "TRUE";
        }

        if ( isset($prehook) ) {

            if ( !isset($prehook_login_value) ) {
                $prehook_return = 255;
                $prehook_message = "No login found, cannot execute prehook script";
            } else {
                $command = hook_command($prehook, $prehook_login_value, $password, null, $prehook_password_encodebase64);
                exec($command, $prehook_output, $prehook_return);
                $prehook_message = $prehook_output[0];
            }
        }

        if ( $prehook_return > 0 and !$ignore_prehook_return) {
            $result = "passwordrefused";
        } else {
            $modification = ldap_mod_replace($ldap, $dn, $entry);
            $errno = ldap_errno($ldap);
            if ( $errno ) {
                $result = "passwordrefused";
            } else {
                $result = "passwordchanged";
            }
        }

        if ( $result === "passwordchanged" && isset($posthook) ) {

            if ( !isset($posthook_login_value) ) {
                $posthook_return = 255;
                $posthook_message = "No login found, cannot execute posthook script";
            } else {
                $command = hook_command($posthook, $posthook_login_value, $password, null, $posthook_password_encodebase64);
                exec($command, $posthook_output, $posthook_return);
                $posthook_message = $posthook_output[0];
            }
        }

        #==============================================================================
        # Notify password change
        #==============================================================================
        if ($result === "passwordchanged") {

            if ($notify_on_change) {
                # Search for user
                $attributes = $mail_attributes;
                $attributes[] = $mail_username_attribute;
                $search = ldap_read($ldap, $dn, '(objectClass=*)', $attributes);
                $errno = ldap_errno($ldap);
                if ( $errno ) {
                    $result = "ldaperror";
                    error_log("LDAP - Search error $errno  (".ldap_error($ldap).")");
                } else {
                    # Get user DN
                    $entry = ldap_first_entry($ldap, $search);

                    $mail = \Ltb\AttributeValue::ldap_get_mail_for_notification($ldap, $entry, $mail_attributes);
                    $username_values = ldap_get_values( $ldap, $entry, $mail_username_attribute );
                    $username = $username_values[0];
                    if ($mail) {
                        $data = array( "name" => $username, "mail" => $mail, "password" => $newpassword);
                        if ( !$mailer->send_mail($mail, $mail_from, $mail_from_name, $messages["changesubject"], $messages["changemessage"].$mail_signature, $data) ) {
                            error_log("Error while sending change email to $mail (user $dn)");
                        }
                    }
                }
            }

            # Notify administrator if needed
            $data = array( "dn" => $dn );
            notify_admin_by_mail($mail_from, $mail_from_name, $messages["changesubjectforadmin"], $messages["changemessageforadmin"], $mail_signature, $data);
        }

    }
}

if ($audit_log_file) {
    auditlog($audit_log_file, $dn, $audit_admin, "resetpassword", $result);
}

$location = 'index.php?page=display&dn='.$dn.'&resetpasswordresult='.$result;
if ( isset($prehook_return) and $display_prehook_error and $prehook_return > 0 ) {
    $location .= '&prehookresult='.$prehook_message;
}
if ( isset($posthook_return) and $display_posthook_error and $posthook_return > 0 ) {
    $location .= '&posthookresult='.$posthook_message;
}

header('Location: '.$location);
