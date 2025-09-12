<?php
/*
 * Display an entry
 */

$result = "";
$dn = "";
$entry = "";
$checkpasswordresult= "";
$resetpasswordresult= "";
$unlockaccountresult= "";
$lockaccountresult= "";
$enableaccountresult= "";
$disableaccountresult= "";
$prehookresult= "";
$posthookresult= "";
$prehooklockresult= "";
$posthooklockresult= "";
$prehookunlockresult= "";
$posthookunlockresult= "";
$prehookenableresult= "";
$posthookenableresult= "";
$prehookdisableresult= "";
$posthookdisableresult= "";
$ldapExpirationDate="";
$canLockAccount="";
$isAccountEnabled = "";
$lockDate = "";
$isAccountValid = "";
$startDate = "";
$endDate = "";
$updatevaliditydatesresult = "";
$prehookupdatevalidityresult= "";
$posthookupdatevalidityresult= "";
$updateresult = "";
$renameresult = "";
$createresult = "";
$deleteresult = "";
$prehookdeleteresult= "";
$posthookdeleteresult= "";

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

if (isset($_GET["unlockaccountresult"]) and $_GET["unlockaccountresult"]) {
    $unlockaccountresult = $_GET["unlockaccountresult"];
}

if (isset($_GET["lockaccountresult"]) and $_GET["lockaccountresult"]) {
    $lockaccountresult = $_GET["lockaccountresult"];
}

if (isset($_GET["enableaccountresult"]) and $_GET["enableaccountresult"]) {
    $enableaccountresult = $_GET["enableaccountresult"];
}

if (isset($_GET["disableaccountresult"]) and $_GET["disableaccountresult"]) {
    $disableaccountresult = $_GET["disableaccountresult"];
}

if (isset($_GET["prehookresult"]) and $_GET["prehookresult"]) {
    $prehookresult = $_GET["prehookresult"];
}

if (isset($_GET["posthookresult"]) and $_GET["posthookresult"]) {
    $posthookresult = $_GET["posthookresult"];
}

if (isset($_GET["prehooklockresult"]) and $_GET["prehooklockresult"]) {
    $prehooklockresult = $_GET["prehooklockresult"];
}

if (isset($_GET["posthooklockresult"]) and $_GET["posthooklockresult"]) {
    $posthooklockresult = $_GET["posthooklockresult"];
}

if (isset($_GET["prehookunlockresult"]) and $_GET["prehookunlockresult"]) {
    $prehookunlockresult = $_GET["prehookunlockresult"];
}

if (isset($_GET["posthookunlockresult"]) and $_GET["posthookunlockresult"]) {
    $posthookunlockresult = $_GET["posthookunlockresult"];
}

if (isset($_GET["prehookenableresult"]) and $_GET["prehookenableresult"]) {
    $prehookenableresult = $_GET["prehookenableresult"];
}

if (isset($_GET["posthookenableresult"]) and $_GET["posthookenableresult"]) {
    $posthookenableresult = $_GET["posthookenableresult"];
}

if (isset($_GET["prehookdisableresult"]) and $_GET["prehookdisableresult"]) {
    $prehookdisableresult = $_GET["prehookdisableresult"];
}

if (isset($_GET["posthookdisableresult"]) and $_GET["posthookdisableresult"]) {
    $posthookdisableresult = $_GET["posthookdisableresult"];
}

if (isset($_GET["updatevaliditydatesresult"]) and $_GET["updatevaliditydatesresult"]) {
    $updatevaliditydatesresult = $_GET["updatevaliditydatesresult"];
}

if (isset($_GET["prehookupdatevalidityresult"]) and $_GET["prehookupdatevalidityresult"]) {
    $prehookupdatevalidityresult = $_GET["prehookupdatevalidityresult"];
}

if (isset($_GET["posthookupdatevalidityresult"]) and $_GET["posthookupdatevalidityresult"]) {
    $posthookupdatevalidityresult = $_GET["posthookupdatevalidityresult"];
}

if (isset($_GET["updateresult"]) and $_GET["updateresult"]) {
    $updateresult = $_GET["updateresult"];
}

if (isset($_GET["renameresult"]) and $_GET["renameresult"]) {
    $renameresult = $_GET["renameresult"];
}

if (isset($_GET["createresult"]) and $_GET["createresult"]) {
    $createresult = $_GET["createresult"];
}

if (isset($_GET["deleteresult"]) and $_GET["deleteresult"]) {
    $deleteresult = $_GET["deleteresult"];
}

if (isset($_GET["prehookdeleteresult"]) and $_GET["prehookdeleteresult"]) {
    $prehookdeleteresult = $_GET["prehookdeleteresult"];
}

if (isset($_GET["posthookdeleteresult"]) and $_GET["posthookdeleteresult"]) {
    $posthookdeleteresult = $_GET["posthookdeleteresult"];
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

        # DN match
        if ( !$ldapInstance->matchDn($dn, $dnAttribute, $ldap_user_filter, $ldap_user_base, $ldap_scope) ) {
            $result = "noentriesfound";
            error_log("LDAP - $dn not found using the configured search settings, reject request");
        } else {

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

        $entries = ldap_get_entries($ldap, $search);
        $entry = $ldapInstance->sortEntry($entries[0], $attributes_map);

        # Get password policy configuration
        $pwdPolicyConfiguration = $directory->getPwdPolicyConfiguration($ldap, $dn, $ldap_default_ppolicy);
        if (isset($ldap_lockout_duration) and $ldap_lockout_duration) { $pwdPolicyConfiguration['lockout_duration'] = $ldap_lockout_duration; }
        if (isset($ldap_password_max_age) and $ldap_password_max_age) { $pwdPolicyConfiguration['password_max_age'] = $ldap_password_max_age; }

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

        if ($show_validitystatus) {
            $isAccountValid = $directory->isAccountValid($ldap, $dn);
            $startDate = $directory->getStartDate($ldap, $dn);
            $endDate = $directory->getEndDate($ldap, $dn);
        }

    }}}
}

$smarty->assign("entry", $entry);
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

$smarty->assign("refresh_link", "index.php?page=display&dn=".urlencode($dn));
$smarty->assign("edit_link", "index.php?page=update&dn=".urlencode($dn));
$smarty->assign("rename_link", "index.php?page=rename&dn=".urlencode($dn));
$smarty->assign("delete_link", "index.php?page=delete&dn=".urlencode($dn));

$smarty->assign("checkpasswordresult", $checkpasswordresult);
$smarty->assign("resetpasswordresult", $resetpasswordresult);
$smarty->assign("unlockaccountresult", $unlockaccountresult);
$smarty->assign("lockaccountresult", $lockaccountresult);
$smarty->assign("enableaccountresult", $enableaccountresult);
$smarty->assign("disableaccountresult", $disableaccountresult);
$smarty->assign("prehookresult", $prehookresult);
$smarty->assign("posthookresult", $posthookresult);
$smarty->assign("prehooklockresult", $prehooklockresult);
$smarty->assign("posthooklockresult", $posthooklockresult);
$smarty->assign("prehookunlockresult", $prehookunlockresult);
$smarty->assign("posthookunlockresult", $posthookunlockresult);
if ($canLockAccount == false) { $smarty->assign("use_lockaccount", $canLockAccount); }
$smarty->assign("isAccountEnabled", $isAccountEnabled);
$smarty->assign("prehookenableresult", $prehookenableresult);
$smarty->assign("posthookenableresult", $posthookenableresult);
$smarty->assign("prehookdisableresult", $prehookdisableresult);
$smarty->assign("posthookdisableresult", $posthookdisableresult);
if (isset($messages[$resetpasswordresult])) {
    $smarty->assign('msg_resetpasswordresult', $messages[$resetpasswordresult]);
} else {
    $smarty->assign('msg_resetpasswordresult','');
}
$smarty->assign("isAccountValid", $isAccountValid);
$smarty->assign("startDate", $startDate);
$smarty->assign("endDate", $endDate);
$smarty->assign("updatevaliditydatesresult", $updatevaliditydatesresult);
$smarty->assign("prehookupdatevalidityresult", $prehookupdatevalidityresult);
$smarty->assign("posthookupdatevalidityresult", $posthookupdatevalidityresult);
$smarty->assign("updateresult", $updateresult);
$smarty->assign("renameresult", $renameresult);
$smarty->assign("createresult", $createresult);
$smarty->assign("deleteresult", $deleteresult);
$smarty->assign("prehookdeleteresult", $prehookdeleteresult);
$smarty->assign("posthookdeleteresult", $posthookdeleteresult);

?>
