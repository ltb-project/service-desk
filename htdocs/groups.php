<?php
/*
 * Manage groups
 */

$result = "";
$dn = "";
$entry = "";
$action = "display";
$result = "";

if (isset($_POST["dn"]) and $_POST["dn"]) {
    $dn = $_POST["dn"];
    $action = "updategroups";
} elseif (isset($_GET["dn"]) and $_GET["dn"]) {
    $dn = $_GET["dn"];
} elseif (isset($entry_dn)) {
    $dn = $entry_dn;
} else {
    $result = "dnrequired";
}

if ($result === "") {

    require_once("../conf/config.inc.php");
    require __DIR__ . '/../vendor/autoload.php';
    require_once("../lib/date.inc.php");
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

            # Update
            if ($action == "update") {

                list($prehook_return, $prehook_message, , $update_attributes) =
                      hook($hook_config['updateGroups']['before'] ?? null, 'updateGroups', "", array("dn" => $dn));

                if ( $prehook_return > 0 and !$hook_config['updateGroups']['before']['ignoreError']) {
                    $result = "hookerror";
                    $action = "display";
                } else {
                    # Update groups
                }

                if ( $result === "updateok" ) {
                    list($posthook_return, $posthook_message) =
                          hook($hook_config['updateGroups']['after'] ?? null, 'updateGroups', "", array("dn" => $dn));
                }

                if ($audit_log_file) {
                    auditlog($audit_log_file, $dn, $audit_admin, "updategroups", $result, $comment ?? "");
                }

            }

            # Display
            if ($action == "display") {
                # Set searchaction so datatables-search.js.tpl uses 'searchgroups'
                $searchaction = "searchgroups";

                # Build listing columns for table headers (fullname + group_result_items + ismember)
                $columns = $group_result_items;
                if (! in_array($group_result_title, $columns)) array_unshift($columns, $group_result_title);
                $columns[] = "ismember";
                $smarty->assign("listing_columns", $columns);

                # Override config_js for the groups DataTable
                $config_js["listing_linkto"] = false;
                $config_js["search_result_show_undefined"] = false;
                $config_js["attributes_map"] = array();
                $config_js["attributes_map"]["dn"] = array("type" => "dn");
                foreach ($columns as $column) {
                    $config_js["attributes_map"][$column] = array(
                        "type" => $column === "ismember" ? "checkbox" : $attributes_map[$column]["type"],
                    );
                }
                $smarty->assign("config_js", base64_encode(json_encode($config_js)));
            }

        }}
}

if ( $action == "displayentry" ) {
    $location = 'index.php?page=display&dn='.urlencode($dn).'&groupsresult='.$result;
    if ( isset($prehook_return) and
         isset($hook_config['updateGroups']['before']['displayError']) and
         $hook_config['updateGroups']['before']['displayError'] and
         $prehook_return > 0 ) {
        $location .= '&prehookgroupsresult='.$prehook_message;
    }
    if ( isset($posthook_return) and
         isset($hook_config['updateGroups']['after']['displayError']) and
         $hook_config['updateGroups']['after']['displayError'] and
         $posthook_return > 0 ) {
        $location .= '&posthookgroupsresult='.$posthook_message;
    }
    header('Location: '.$location);
}

$smarty->assign("dn", $dn);
$smarty->assign("action", $action);

$smarty->assign("show_undef", $display_show_undefined);

?>
