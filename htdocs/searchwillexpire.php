<?php
/*
 * Search entries expiring soon in LDAP directory
 */

require_once("../conf/config.inc.php");
require __DIR__ . '/../vendor/autoload.php';

$smarty->assign("page_title", "willexpireaccountstitle");
$columns = $search_result_items;
if (! in_array($search_result_title, $columns)) array_unshift($columns, $search_result_title);
$smarty->assign("listing_columns", $columns);

?>
