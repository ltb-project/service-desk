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

$compile_dir = isset($smarty_compile_dir) && $smarty_compile_dir ? $smarty_compile_dir : "../templates_c/" ;
$cache_dir = isset($smarty_cache_dir) && $smarty_cache_dir ? $smarty_cache_dir : "../cache/";

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
$smarty->assign("page_title", false);
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
$smarty->assign('logout_link',isset($logout_link) ? $logout_link : false);
$smarty->assign('use_checkpassword',$use_checkpassword);
$smarty->assign('use_resetpassword',$use_resetpassword);
$smarty->assign('use_resetpassword_resetchoice',$use_resetpassword_resetchoice);
$smarty->assign('resetpassword_reset_default',$resetpassword_reset_default);
$smarty->assign('use_unlockaccount',$use_unlockaccount);
$smarty->assign('use_lockaccount',$use_lockaccount);
$smarty->assign('display_password_expiration_date',$display_password_expiration_date);
$smarty->assign('use_searchlocked',$use_searchlocked);
$smarty->assign('use_searchexpired',$use_searchexpired);
$smarty->assign('use_searchwillexpire',$use_searchwillexpire);
$smarty->assign('use_searchidle',$use_searchidle);
$smarty->assign('fake_password_inputs',$fake_password_inputs);

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
# Route to page
#==============================================================================
$result = "";
$page = "welcome";
if (isset($_GET["page"]) and $_GET["page"]) { $page = $_GET["page"]; }
if ( $page === "checkpassword" and !$use_checkpassword ) { $page = "welcome"; }
if ( $page === "resetpassword" and !$use_resetpassword ) { $page = "welcome"; }
if ( $page === "unlockaccount" and !$use_unlockaccount ) { $page = "welcome"; }
if ( $page === "searchlocked" and !$use_searchlocked ) { $page = "welcome"; }
if ( $page === "searchexpired" and !$use_searchexpired ) { $page = "welcome"; }
if ( $page === "searchwillexpire" and !$use_searchwillexpire ) { $page = "welcome"; }
if ( $page === "searchidle" and !$use_searchidle ) { $page = "welcome"; }
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
