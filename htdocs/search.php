<?php
/*
 * Search entries in LDAP directory
 */

if (isset($_POST["search"]) and $_POST["search"]) {

    $result="";

    require_once("../conf/config.inc.php");
    require __DIR__ . '/../vendor/autoload.php';

    $columns = $search_result_items;
    if (! in_array($search_result_title, $columns)) array_unshift($columns, $search_result_title);
    $smarty->assign("listing_columns", $columns);
    $smarty->assign("search_query", $_POST["search"]);

} else {
    $result = "searchrequired";
}

?>
