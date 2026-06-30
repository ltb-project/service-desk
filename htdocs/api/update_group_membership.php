<?php
$data = array();

$userdn = $_POST["userdn"];
$dn = $_POST["dn"];
$checked = $_POST["checked"];

error_log("USER DN ".$userdn." - DN ".$dn." CHECKED ".$checked);

if (!$use_groupmembership) {
    $data['error'] = "featurenoteanbled";
} else {

    # Connect to LDAP
    $ldap_connection = $ldapInstance->connect();

    $ldap = $ldap_connection[0];
    $result = $ldap_connection[1];

    if ($ldap) {

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
            if ($audit_log_file) {
                auditlog($audit_log_file, $userdn, $audit_admin, $audit_action, $result, $comment);
            }
        }
    } else {
        $data["error"] = "ldaperror";
    }
}

?>
