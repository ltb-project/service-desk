<?php

require_once("../conf/config.inc.php");
require __DIR__ . '/../vendor/autoload.php';
require_once("../lib/date.inc.php");
require_once("../lib/audit.inc.php");

#TODO: Order based on newest to oldest
#TODO: Test comments and results to appear correctly

$entries = array();
[$entries,$nb_entries] = displayauditlog($audit_log_file, $audit_log_days);

if (!empty($entries)) {
    $smarty->assign("page_title", "auditlogtitle");
    $smarty->assign("nb_entries", $nb_entries);
    $smarty->assign("entries", $entries);
    $smarty->assign("listing_columns", $audit_log_items);
    $smarty->assign("truncate_value_after", $audit_log_truncate_value_after);
}
?>
