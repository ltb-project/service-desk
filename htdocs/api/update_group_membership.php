<?php
$data = array();

$userdn = $_POST["userdn"];
$dn = $_POST["dn"];
$checked = $_POST["checked"];

if (!$use_groupmembership) {
    $data['error'] = "featurenoteanbled";
} else {

    # Connect to LDAP
    $ldap_connection = $ldapInstance->connect();

    $ldap = $ldap_connection[0];
    $result = $ldap_connection[1];

    if ($ldap) {

        require_once("../lib/hook.inc.php");

        # User DN and Group DN match
        $user_dn_match = $ldapInstance->matchDn($userdn, $dnAttribute, $ldap_user_filter, $ldap_user_base, $ldap_scope);
        $group_dn_match = $ldapInstance->matchDn($dn, $dnAttribute, $ldap_group_filter, $ldap_group_base, $ldap_scope);
        if ( !$user_dn_match or !$group_dn_match ) {
            $data["error"] = "noentriesfound";
            error_log("LDAP - $dn or $userdn not found using the configured search settings, reject request");
        } else {

            $group_modify = false;
            $audit_action = "";
            $result = "";
            $infos = array();

            list($prehook_return, $prehook_message, $userdn, $infos) =
                hook($hook_config['updateGroupMembership']['before'] ?? null, 'updateGroupMembership', "", array("dn" => $userdn, "group_dn" => $dn, "checked" => $checked));

            if ( $prehook_return > 0 and !$hook['updateGroupMembership']['before']['ignoreError']) {
                $data["error"] = "hookerror";
                $result = "hookerror";
                error_log("Prehook failed for group membership update (group $dn and user $userdn)");
            } else {
                if (isset($infos['group_dn'])) { $dn = $infos['group_dn']; }
                if (isset($infos['checked'])) { $checked = $infos['checked']; }
                if ($checked == "true") {
                    $group_modify = ldap_mod_add($ldap, $dn, array( $ldap_group_member_attribute => $userdn ));
                    $audit_action = "addusertogroup";
                } else if ($checked == "false") {
                    $group_modify = ldap_mod_del($ldap, $dn, array( $ldap_group_member_attribute => $userdn ));
                    $audit_action = "removeuserfromgroup";
                }

                if (!$group_modify) {
                    $data["error"] = "groupupdateerror";
                    $result = "groupupdateerror";
                    error_log("Modification of group $dn for user $userdn failed");
                } else {
                    $result = "groupupdatesuccess";
                    error_log("Modification of group $dn for user $userdn succeeded");
                }
            }

            if ( $result === "groupupdatesuccess" ) {
                list($posthook_return, $posthook_message) =
                    hook($hook_config['updateGroupMembership']['after'] ?? null, 'updateGroupMembership', "", array("dn" => $userdn, "group_dn" => $dn, "checked" => $checked));
            }

            if ($audit_log_file) {
                auditlog($audit_log_file, $userdn, $audit_admin, $audit_action, $result, $comment);
            }
        }
    } else {
        $data["error"] = "ldaperror";
    }
}

?>
