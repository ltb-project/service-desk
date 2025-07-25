<?php
/*
 * Collect audit logs to be displayed.
 */
require_once("../conf/config.inc.php");
require __DIR__ . '/../vendor/autoload.php';
require_once("../lib/date.inc.php");
require_once("../lib/audit.inc.php");

$events = array();
[$events,$nb_events] = displayauditlog($audit_log_file, $audit_log_days, $audit_log_sortby, $audit_log_reverse, $ldapInstance);

$smarty->assign("page_title", "auditlogtitle");
$smarty->assign("nb_events", $nb_events);
$smarty->assign("events", $events);
$smarty->assign("listing_columns", $audit_log_items);
$smarty->assign("truncate_value_after", $audit_log_truncate_value_after);

?>
