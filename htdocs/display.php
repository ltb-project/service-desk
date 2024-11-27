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
$unlockaccountresult= "";
$lockaccountresult= "";
$enableaccountresult= "";
$disableaccountresult= "";
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

        $entry = ldap_get_entries($ldap, $search);

        # Sort attributes values
        foreach ($entry[0] as $attr => $values) {
            if ( is_array($values) && $values['count'] > 1 ) {

                # Find key in attributes_map
                $attributes_map_filter = array_filter($attributes_map, function($v) use(&$attr) {
                    return $v['attribute'] == "$attr";
                });
                if( count($attributes_map_filter) < 1 )
                {
                    $k = "";
                    error_log("WARN: no key found for attribute $attr in \$attributes_map");
                }
                elseif( count($attributes_map_filter) > 1 )
                {
                    $k = array_key_first($attributes_map_filter);
                    error_log("WARN: multiple keys found for attribute $attr in \$attributes_map, using first one: $k");
                }
                else
                {
                    $k = array_key_first($attributes_map_filter);
                }

                if(isset($attributes_map[$k]['sort']))
                {
                    if($attributes_map[$k]['sort'] == "descending" )
                    {
                        # descending sort
                        arsort($values);
                    }
                    else
                    {
                        # ascending sort
                        asort($values);
                    }
                }
                else
                {
                    # if 'sort' param unset: default to ascending sort
                    asort($values);
                }
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

    }}}
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
$smarty->assign("unlockaccountresult", $unlockaccountresult);
$smarty->assign("lockaccountresult", $lockaccountresult);
$smarty->assign("enableaccountresult", $enableaccountresult);
$smarty->assign("disableaccountresult", $disableaccountresult);
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
