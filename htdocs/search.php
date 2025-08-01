<?php

/*
 * Search entries in LDAP directory
 */

require_once("../conf/config.inc.php");
require __DIR__ . '/../vendor/autoload.php';


switch ($searchaction) {
    case "searchdisabled":
        $smarty->assign("page_title", "disabledaccounts");
        break;
    case "searchexpired":
        $smarty->assign("page_title", "expiredaccounts");
        break;
    case "searchidle":
        $smarty->assign("page_title", "idleaccountstitle");
        break;
    case "searchinvalid":
        $smarty->assign("page_title", "invalidaccountstitle");
        break;
    case "searchlocked":
        $smarty->assign("page_title", "lockedaccounts");
        break;
    case "searchwillexpire":
        $smarty->assign("page_title", "willexpireaccountstitle");
        break;
    case "search":
        if (isset($_POST["search"]) and $_POST["search"]) {
            $result="";
            $smarty->assign("search_query", $_POST["search"]);

        } else {
            $result = "searchrequired";
        }
        break;
}

$columns = $search_result_items;
if (! in_array($search_result_title, $columns)) array_unshift($columns, $search_result_title);
$smarty->assign("listing_columns", $columns);

?>
