<?php
/*
 * Display an entry
 */

$result = "";
$dn = "";
$entry = "";
$edit_link = "";
$checkpasswordresult= "";
$resetpasswordresult= "";
$accountunlockresult= "";
$accountlockresult= "";
$prehookresult= "";
$posthookresult= "";
$ldapExpirationDate="";
$canLockAccount="";
$isAccountEnabled = "";
$lockDate = "";

if (isset($_GET["dn"]) and $_GET["dn"]) {
    $dn = $_GET["dn"];
} elseif (isset($entry_dn)) {
    $dn = $entry_dn;
} else {
    $result = "dnrequired";
}

if (isset($_GET["checkpasswordresult"]) and $_GET["checkpasswordresult"]) {
    $checkpasswordresult = $_GET["checkpasswordresult"];
}

if (isset($_GET["resetpasswordresult"]) and $_GET["resetpasswordresult"]) {
    $resetpasswordresult = $_GET["resetpasswordresult"];
}

if (isset($_GET["accountunlockresult"]) and $_GET["accountunlockresult"]) {
    $accountunlockresult = $_GET["accountunlockresult"];
}

if (isset($_GET["accountlockresult"]) and $_GET["accountlockresult"]) {
    $accountlockresult = $_GET["accountlockresult"];
}

if (isset($_GET["prehookresult"]) and $_GET["prehookresult"]) {
    $prehookresult = $_GET["prehookresult"];
}

if (isset($_GET["posthookresult"]) and $_GET["posthookresult"]) {
    $posthookresult = $_GET["posthookresult"];
}

if ($result === "") {

    require_once("../conf/config.inc.php");
    require __DIR__ . '/../vendor/autoload.php';
    require_once("../lib/date.inc.php");

    # Connect to LDAP
    $ldap_connection = $ldapInstance->connect();

    $ldap = $ldap_connection[0];
    $result = $ldap_connection[1];

    if ($ldap) {

        # Search attributes
        $attributes = array();
        $search_items = array_merge($display_items, $display_password_items);
        foreach( $search_items as $item ) {
            $attributes[] = $attributes_map[$item]['attribute'];
        }
        $attributes[] = $attributes_map[$display_title]['attribute'];

        # Search entry
        $search = ldap_read($ldap, $dn, $ldap_user_filter, $attributes);
        $errno = ldap_errno($ldap);

        if ( $errno ) {
            $result = "ldaperror";
            error_log("LDAP - Search error $errno  (".ldap_error($ldap).")");
        } else {
            $entry = ldap_get_entries($ldap, $search);
        }

        # Sort attributes values
        foreach ($entry[0] as $attr => $values) {
            if ( is_array($values) && $values['count'] > 1 ) {
                asort($values);
            }
            if ( isset($values['count']) ) {
                unset($values['count']);
            }
            $entry[0][$attr] = $values;
        }

        # Get password policy configuration
        $pwdPolicyConfiguration = $directory->getPwdPolicyConfiguration($ldap, $dn, $ldap_default_ppolicy);
        if (isset($ldap_lockout_duration) and $ldap_lockout_duration) { $pwdPolicyConfiguration['lockout_duration'] = $ldap_lockout_duration; }
        if (isset($ldap_password_max_age) and $ldap_password_max_age) { $pwdPolicyConfiguration['password_max_age'] = $ldap_password_max_age; }

        if ($display_edit_link) {
            # Replace {dn} in URL
            $edit_link = str_replace("{dn}", urlencode($dn), $display_edit_link);
        }

        $lockDate = $directory->getLockDate($ldap, $dn);
        $unlockDate = $directory->getUnlockDate($ldap, $dn, $pwdPolicyConfiguration);
        $isLocked = $directory->isLocked($ldap, $dn, $pwdPolicyConfiguration);
        $canLockAccount = $pwdPolicyConfiguration["lockout_enabled"];

        $expirationDate = $directory->getPasswordExpirationDate($ldap, $dn, $pwdPolicyConfiguration);
        $isExpired = $directory->isPasswordExpired($ldap, $dn, $pwdPolicyConfiguration);

        $resetAtNextConnection = $directory->resetAtNextConnection($ldap, $dn);

        if ($show_enablestatus) {
            $isAccountEnabled = $directory->isAccountEnabled($ldap, $dn);
        }

    }
}

$smarty->assign("entry", $entry[0]);
$smarty->assign("dn", $dn);

$smarty->assign("card_title", $display_title);
$smarty->assign("card_items", $display_items);
$smarty->assign("password_items", $display_password_items);
$smarty->assign("show_undef", $display_show_undefined);

$smarty->assign("isLocked", $isLocked);
$smarty->assign("lockDate", $lockDate);
$smarty->assign("unlockDate", $unlockDate);
$smarty->assign("isExpired", $isExpired);
$smarty->assign("ldapExpirationDate", $expirationDate ? $expirationDate->getTimestamp(): NULL);
$smarty->assign("resetAtNextConnection", $resetAtNextConnection);

$smarty->assign("edit_link", $edit_link);

$smarty->assign("checkpasswordresult", $checkpasswordresult);
$smarty->assign("resetpasswordresult", $resetpasswordresult);
$smarty->assign("accountunlockresult", $accountunlockresult);
$smarty->assign("accountlockresult", $accountlockresult);
$smarty->assign("prehookresult", $prehookresult);
$smarty->assign("posthookresult", $posthookresult);
if ($canLockAccount == false) { $smarty->assign("use_lockaccount", $canLockAccount); }
$smarty->assign("isAccountEnabled", $isAccountEnabled);
if (isset($messages[$resetpasswordresult])) {
    $smarty->assign('msg_resetpasswordresult', $messages[$resetpasswordresult]);
} else {
    $smarty->assign('msg_resetpasswordresult','');
}

?>
