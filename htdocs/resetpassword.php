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

    # DN match
    if ( !$ldapInstance->matchDn($dn, $dnAttribute, $ldap_user_filter, $ldap_user_base, $ldap_scope) ) {
        $result = "noentriesfound";
        error_log("LDAP - $dn not found using the configured search settings, reject request");
    } else {
        if ( isset($prehook) || isset($posthook) ) {
            if ( isset($prehook_login) ) {
                $prehook_login_value = $ldapInstance->get_first_value($dn, "base", '(objectClass=*)', $prehook_login);
            }
            if ( isset($posthook_login) ) {
                $posthook_login_value = $ldapInstance->get_first_value($dn, "base", '(objectClass=*)', $posthook_login);
            }
        }

        # Get current entry first
        $entries_search = $ldapInstance->search_with_scope("base", $dn, '(objectClass=*)');
        $errno = ldap_errno($ldap);
        if ( $errno ) {
            $result = "ldaperror";
            error_log("LDAP - Search error $errno  (".ldap_error($ldap).")");
        }
        $entry_search = ldap_first_entry($ldap, $entries_search);
        $entry_array = ldap_get_attributes($ldap, $entry_search);
        # Get identifier attribute
        $identifiers = ldap_get_values( $ldap,
                                        $entry_search,
                                        $attributes_map['identifier']['attribute']
                                      );
        $identifier = $identifiers[0];
        if ( !isset($identifier) || $identifier == "" ) {
            $result = "ldaperror";
            error_log("LDAP - Unable to find identifier for LDAP entry ".
                      var_export($entry_array, true));
        }

        #==============================================================================
        # Check password strength
        #==============================================================================
        if( $result != "ldaperror" )
        {
            $result = \Ltb\Ppolicy::check_password_strength( $password,
                                                             "",
                                                             $pwd_policy_config,
                                                             $identifier,
                                                             $entry_array,
                                                             array()
                                                           );
        }

        if( $result === "")
        {
            if ( isset($prehook) ) {

                if ( !isset($prehook_login_value) ) {
                    $prehook_return = 255;
                    $prehook_message = "No login found, cannot execute prehook script";
                } else {
                    $command = password_hook_command($prehook, $prehook_login_value, $password, null, $prehook_password_encodebase64);
                    exec($command, $prehook_output, $prehook_return);
                    $prehook_message = $prehook_output[0];
                }
            }

            if ( $prehook_return > 0 and !$ignore_prehook_return) {
                $result = "passwordrefused";
            } else {
            $reset = ($pwdreset === "true") ? true : false;
            if ($directory->modifyPassword($ldap, $dn, $password, $reset)) {
                    $result = "passwordchanged";
                } else {
                    $result = "passwordrefused";
                }
            }
        }

        if ( $result === "passwordchanged" && isset($posthook) ) {

            if ( !isset($posthook_login_value) ) {
                $posthook_return = 255;
                $posthook_message = "No login found, cannot execute posthook script";
            } else {
                $command = password_hook_command($posthook, $posthook_login_value, $password, null, $posthook_password_encodebase64);
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
            $admin_mail_list = get_admin_mail_list($notify_admin_by_mail, $notify_admin_by_mail_list);
            $mailer->send_mail($admin_mail_list, $mail_from, $mail_from_name, $messages["changesubjectforadmin"], $messages["changemessageforadmin"].$mail_signature, $data);
        }

    }
}

if ($audit_log_file) {
    auditlog($audit_log_file, $dn, $audit_admin, "resetpassword", $result, NULL);
}

$location = 'index.php?page=display&dn='.$dn.'&resetpasswordresult='.$result;
if ( isset($prehook_return) and $display_prehook_error and $prehook_return > 0 ) {
    $location .= '&prehookresult='.$prehook_message;
}
if ( isset($posthook_return) and $display_posthook_error and $posthook_return > 0 ) {
    $location .= '&posthookresult='.$posthook_message;
}

header('Location: '.$location);
