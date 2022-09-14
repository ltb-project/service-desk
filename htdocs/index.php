<?php

#==============================================================================
# Version
#==============================================================================
$version = 0.4;

#==============================================================================
# Configuration
#==============================================================================
require_once("../conf/config.inc.php");

#==============================================================================
# Language
#==============================================================================
require_once("../lib/detectbrowserlanguage.php");
# Available languages
$files = glob("../lang/*.php");
$languages = str_replace(".inc.php", "", $files);
$lang = detectLanguage($lang, $languages);
require_once("../lang/$lang.inc.php");
if (file_exists("../conf/$lang.inc.php")) {
    require_once("../conf/$lang.inc.php");
}

#==============================================================================
# Smarty
#==============================================================================
require_once(SMARTY);

$compile_dir = $smarty_compile_dir ? $smarty_compile_dir : "../templates_c/";
$cache_dir = $smarty_cache_dir ? $smarty_cache_dir : "../cache/";

$smarty = new Smarty();
$smarty->escape_html = true;
$smarty->setTemplateDir('../templates/');
$smarty->setCompileDir($compile_dir);
$smarty->setCacheDir($cache_dir);
$smarty->debugging = $smarty_debug;

error_reporting(0);
if ($debug) {
    error_reporting(E_ALL);
    # Set debug for LDAP
    ldap_set_option(NULL, LDAP_OPT_DEBUG_LEVEL, 7);
}

# Assign configuration variables
$smarty->assign('ldap_params',array('ldap_url' => $ldap_url, 'ldap_starttls' => $ldap_starttls, 'ldap_binddn' => $ldap_binddn, 'ldap_bindpw' => $ldap_bindpw, 'ldap_user_base' => $ldap_user_base, 'ldap_user_filter' => $ldap_user_filter));
$smarty->assign('logo',$logo);
$smarty->assign('background_image',$background_image);
$smarty->assign('custom_css',$custom_css);
$smarty->assign('attributes_map',$attributes_map);
$smarty->assign('date_specifiers',$date_specifiers);
if (is_array($datatables_page_length_choices)) $datatables_page_length_choices = implode(', ', $datatables_page_length_choices);
$smarty->assign('datatables_page_length_choices', $datatables_page_length_choices);
$smarty->assign('datatables_page_length_default', $datatables_page_length_default);
$smarty->assign('datatables_auto_print', $datatables_auto_print);
$smarty->assign('version',$version);
$smarty->assign('display_footer',$display_footer);
$smarty->assign('logout_link',$logout_link);
$smarty->assign('use_checkpassword',$use_checkpassword);
$smarty->assign('use_resetpassword',$use_resetpassword);
$smarty->assign('resetpassword_reset_default',$resetpassword_reset_default);
$smarty->assign('use_unlockaccount',$use_unlockaccount);
$smarty->assign('use_lockaccount',$use_lockaccount);
$smarty->assign('display_password_expiration_date',$display_password_expiration_date);
$smarty->assign('use_searchlocked',$use_searchlocked);
$smarty->assign('use_searchexpired',$use_searchexpired);
$smarty->assign('use_searchwillexpire',$use_searchwillexpire);
$smarty->assign('use_searchidle',$use_searchidle);

# Assign messages
$smarty->assign('lang',$lang);
foreach ($messages as $key => $message) {
    $smarty->assign('msg_'.$key,$message);
}

# Other assignations
$search = "";
if (isset($_REQUEST["search"]) and $_REQUEST["search"]) { $search = htmlentities($_REQUEST["search"]); }
$smarty->assign('search',$search);

# Register plugins
require_once("../lib/smarty.inc.php");
$smarty->registerPlugin("function", "get_attribute", "get_attribute");
$smarty->registerPlugin("function", "convert_ldap_date", "convert_ldap_date");
$smarty->registerPlugin("function", "convert_bytes", "convert_bytes");

#==============================================================================
# Authentication
#==============================================================================
require_once("../lib/login.php"); //Maintains session variables
$authenticated = $_SESSION["authenticated"];
$isadmin = $_SESSION['isadmin'];
$smarty->assign('authenticated',$_SESSION["authenticated"]);
$smarty->assign('isadmin',$_SESSION["isadmin"]);
$smarty->assign('displayname',$_SESSION["displayname"]);

#==============================================================================
# Route to page
#==============================================================================
$result = "";
$page = "login";// Default route to login page

if ( $authenticated ) { $page = "display"; }// If authenticated, route to display
if ( isset($_GET["page"]) and $_GET["page"] and !$authenticated) { $page = "login"; }// If not authenticated, route to login
if ( isset($_GET["page"])  and $_GET["page"] and $_GET["page"] != "login" and $authenticated) { $page = $_GET["page"]; }
if ( $page === "checkpassword" and (!$use_checkpassword or !$isadmin) ) { $page = "display"; }
if ( $page === "resetpassword" and (!$use_resetpassword or !$isadmin) ) { $page = "display"; }
if ( $page === "unlockaccount" and (!$use_unlockaccount or !$isadmin) ) { $page = "display"; }
if ( $page === "search" and !$isadmin ) { $page = "display"; }
if ( $page === "searchlocked" and (!$use_searchlocked or !$isadmin) ) { $page = "display"; }
if ( $page === "searchexpired" and (!$use_searchexpired or !$isadmin) ) { $page = "display"; }
if ( $page === "searchwillexpire" and (!$use_searchwillexpire or !$isadmin) ) { $page = "display"; }
if ( $page === "searchidle" and (!$use_searchidle or !$isadmin) ) { $page = "display"; }
if ( file_exists($page.".php") ) { require_once($page.".php"); }
$smarty->assign('page',$page);

if ($result) {
    $smarty->assign('error',$messages[$result]);
} else {
    $smarty->assign('error',"");
}

# Display
$smarty->display('index.tpl');

?>
