<?php

require_once("../conf/config.inc.php");
require __DIR__ . '/../vendor/autoload.php';
require_once("../lib/date.inc.php");
require_once("../lib/audit.inc.php");

#TODO: Calculate getting logs for number of days
#TODO: Order based on newest to oldest
#TODO: Highlight user with Name and link rather than full CN
#TODO: Test comments and results to appear correctly

$entries = array();
[$entries,$nb_entries] = displayauditlog($audit_log_file);

if (!empty($entries)) {
    $smarty->assign("page_title", "auditlogtitle");
    $smarty->assign("nb_entries", $nb_entries);
    $smarty->assign("entries", $entries);
    $smarty->assign("listing_columns", $audit_log_items);
    $smarty->assign("truncate_value_after", $audit_log_truncate_value_after);
}
?>
