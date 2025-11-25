<?php

#==============================================================================
# Version
#==============================================================================
$version = "0.7.1";

#==============================================================================
# Configuration
#==============================================================================
require_once("../conf/config.inc.php");

#==============================================================================
# Includes
#==============================================================================
require_once("../vendor/autoload.php");

#==============================================================================
# Hooks
#==============================================================================
$hook_dir = __DIR__ . '/../hooks/';
if (is_dir($hook_dir)) {
    if( $dh = opendir($hook_dir) )
    {
        while( ($hook_file = readdir($dh)) !== false )
        {
            if( filetype( $hook_dir . $hook_file ) == "file" &&
                str_ends_with( $hook_file, '.php' )
              )
            {
                require_once($hook_dir . $hook_file);
            }
        }
        closedir($dh);
    }
}

#==============================================================================
# Language
#==============================================================================
# Available languages
$files = glob("../lang/*.php");
$languages = str_replace(".inc.php", "", $files);
$languages = str_replace("../lang/", "", $languages);
$lang = \Ltb\Language::detect_language($lang, $allowed_lang ? array_intersect($languages,$allowed_lang) : $languages);
require_once("../lang/$lang.inc.php");
if (file_exists("../conf/$lang.inc.php")) {
    require_once("../conf/$lang.inc.php");
}

#==============================================================================
# Email Config
#==============================================================================
$mailer = new \Ltb\Mail(
                           $mail_priority,
                           $mail_charset,
                           $mail_contenttype,
                           $mail_wordwrap,
                           $mail_sendmailpath,
                           $mail_protocol,
                           $mail_smtp_debug,
                           $mail_debug_format,
                           $mail_smtp_host,
                           $mail_smtp_port,
                           $mail_smtp_secure,
                           $mail_smtp_autotls,
                           $mail_smtp_auth,
                           $mail_smtp_user,
                           $mail_smtp_pass,
                           $mail_smtp_keepalive,
                           $mail_smtp_options,
                           $mail_smtp_timeout
                       );

#==============================================================================
# LDAP Config
#==============================================================================
$ldapInstance = new \Ltb\Ldap(
                                 $ldap_url,
                                 $ldap_starttls,
                                 isset($ldap_binddn) ? $ldap_binddn : null,
                                 isset($ldap_bindpw) ? $ldap_bindpw : null,
                                 isset($ldap_network_timeout) ? $ldap_network_timeout : null,
                                 $ldap_user_base,
                                 isset($ldap_size_limit) ? $ldap_size_limit : 0,
                                 isset($ldap_krb5ccname) ? $ldap_krb5ccname : null,
                                 isset($ldap_page_size) ? $ldap_page_size : 0
                             );

#==============================================================================
# Directory instance
#==============================================================================
$directory;

# Load specific directory settings
switch($ldap_type) {
  case "openldap":
    $openldap_attributes_map['authtimestamp']['attribute'] = strtolower($ldap_lastauth_attribute);
    $attributes_map = array_merge($attributes_map, $openldap_attributes_map);
    $directory = new \Ltb\Directory\OpenLDAP();
  break;
  case "activedirectory":
    $attributes_map = array_merge($attributes_map, $activedirectory_attributes_map);
    $directory = new \Ltb\Directory\ActiveDirectory();
    $ldap_lastauth_attribute = "lastLogon";
  break;
}

$dnAttribute = $directory->getDnAttribute();

#==============================================================================
# Other default values
#==============================================================================
if (!isset($pwd_forbidden_chars)) { $pwd_forbidden_chars = ""; }

# Password policy array
$pwd_policy_config = array(
    "pwd_show_policy"           => $pwd_show_policy,
    "pwd_min_length"            => $pwd_min_length,
    "pwd_max_length"            => $pwd_max_length,
    "pwd_min_lower"             => $pwd_min_lower,
    "pwd_min_upper"             => $pwd_min_upper,
    "pwd_min_digit"             => $pwd_min_digit,
    "pwd_min_special"           => $pwd_min_special,
    "pwd_special_chars"         => $pwd_special_chars,
    "pwd_no_reuse"              => false, # old password not available
    "pwd_forbidden_chars"       => $pwd_forbidden_chars,
    "pwd_diff_last_min_chars"   => 0, # old password not available
    "pwd_diff_login"            => $pwd_diff_login,
    "pwd_complexity"            => $pwd_complexity,
    "use_pwnedpasswords"        => $use_pwnedpasswords,
    "pwd_no_special_at_ends"    => $pwd_no_special_at_ends,
    "pwd_forbidden_words"       => $pwd_forbidden_words,
    "pwd_forbidden_ldap_fields" => $pwd_forbidden_ldap_fields,
    "pwd_display_entropy"       => $pwd_display_entropy,
    "pwd_check_entropy"         => $pwd_check_entropy,
    "pwd_min_entropy"           => $pwd_min_entropy
);

if (!isset($pwd_show_policy_pos)) { $pwd_show_policy_pos = "above"; }

#==============================================================================
# Smarty
#==============================================================================
require_once(SMARTY);

$compile_dir = isset($smarty_compile_dir) && $smarty_compile_dir ? $smarty_compile_dir : "../templates_c/" ;
$cache_dir = isset($smarty_cache_dir) && $smarty_cache_dir ? $smarty_cache_dir : "../cache/";
$tpl_dir = isset($custom_tpl_dir) ? array('../'.$custom_tpl_dir, '../templates/') : '../templates/';

$smarty = new Smarty();
$smarty->escape_html = true;
$smarty->setTemplateDir($tpl_dir);
$smarty->setCompileDir($compile_dir);
$smarty->setCacheDir($cache_dir);
$smarty->debugging = $smarty_debug;
function sha256($string)
{
    return hash("sha256",$string);
}
$smarty->registerPlugin("modifier","sha256", "sha256");
$smarty->registerPlugin("modifier","is_array", "is_array");
$smarty->registerPlugin("modifier","in_array", "in_array");
$smarty->registerPlugin("modifier", 'strstr', 'strstr');

if(isset($smarty_debug) && $smarty_debug == true )
{
    $smarty->error_reporting = E_ALL;
}
else
{
    # Do not report smarty stuff unless $smarty_debug == true
    $smarty->error_reporting = E_ALL & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED & ~E_WARNING;
}

# By default, only display error logs and not the other levels
error_reporting($debug_level);
if ($debug) {
    error_reporting(E_ALL);
    # Set debug for LDAP
    ldap_set_option(NULL, LDAP_OPT_DEBUG_LEVEL, 7);
}

# Assign configuration variables
$smarty->assign("page_title", false);
$smarty->assign('ldap_params',array('ldap_url' => $ldap_url, 'ldap_starttls' => $ldap_starttls, 'ldap_binddn' => $ldap_binddn, 'ldap_bindpw' => $ldap_bindpw, 'ldap_user_base' => $ldap_user_base, 'ldap_user_filter' => $ldap_user_filter, 'ldap_ppolicy_filter' => $ldap_ppolicy_filter, 'ldap_ppolicy_name_attribute' => $ldap_ppolicy_name_attribute, 'ldap_default_ppolicy' => $ldap_default_ppolicy));
$smarty->assign('logo',$logo);
$smarty->assign('background_image',$background_image);
$smarty->assign('favicon',$favicon);
$smarty->assign('custom_css',$custom_css);
$smarty->assign('attributes_map',$attributes_map);
$smarty->assign('date_specifiers',$date_specifiers);
if (is_array($datatables_page_length_choices)) {
    if ( $all = array_search('-1', $datatables_page_length_choices)) {
        $datatables_page_length_choices[$all] = '{"value":"-1","label":"'.$messages["pager_all"].'"}';
    }
    $datatables_page_length_choices = implode(', ', $datatables_page_length_choices);
}
$smarty->assign('datatables_page_length_choices', $datatables_page_length_choices);
$smarty->assign('datatables_page_length_default', $datatables_page_length_default);
$smarty->assign('datatables_print_all', $datatables_print_all);
$smarty->assign('datatables_print_page', $datatables_print_page);
$smarty->assign('datatables_auto_print', $datatables_auto_print);
$smarty->assign('version',$version);
$smarty->assign('display_footer',$display_footer);
$smarty->assign('logout_link',isset($logout_link) ? $logout_link : false);
$smarty->assign('use_checkpassword',$use_checkpassword);
$smarty->assign('use_checkpasswordhistory',$use_checkpasswordhistory);
$smarty->assign('use_resetpassword',$use_resetpassword);
$smarty->assign('use_resetpassword_resetchoice',$use_resetpassword_resetchoice);
$smarty->assign('resetpassword_reset_default',$resetpassword_reset_default);
$smarty->assign('show_lockstatus',$show_lockstatus);
$smarty->assign('use_unlockaccount',$use_unlockaccount);
$smarty->assign('use_unlockcomment',$use_unlockcomment);
$smarty->assign('use_unlockcomment_required',$use_unlockcomment_required);
$smarty->assign('use_lockaccount',$use_lockaccount);
$smarty->assign('use_lockcomment',$use_lockcomment);
$smarty->assign('use_lockcomment_required',$use_lockcomment_required);
$smarty->assign('show_expirestatus',$show_expirestatus);
$smarty->assign('display_password_expiration_date',$display_password_expiration_date);
$smarty->assign('use_searchlocked',$use_searchlocked);
$smarty->assign('use_searchdisabled',$use_searchdisabled);
$smarty->assign('use_searchexpired',$use_searchexpired);
$smarty->assign('use_searchwillexpire',$use_searchwillexpire);
$smarty->assign('use_searchidle',$use_searchidle);
$smarty->assign('use_showauditlog',$use_showauditlog);
$smarty->assign('fake_password_inputs',$fake_password_inputs);
$smarty->assign('use_enableaccount',$use_enableaccount);
$smarty->assign('use_disableaccount',$use_disableaccount);
$smarty->assign('show_enablestatus',$show_enablestatus);
$smarty->assign('use_enablecomment',$use_enablecomment);
$smarty->assign('use_enablecomment_required',$use_enablecomment_required);
$smarty->assign('use_disablecomment',$use_disablecomment);
$smarty->assign('use_disablecomment_required',$use_disablecomment_required);
$smarty->assign('show_validitystatus',$show_validitystatus);
$smarty->assign('use_updatestarttime',$attributes_map['starttime'] ? $use_updatestarttime : false);
$smarty->assign('use_updateendtime',$attributes_map['endtime'] ? $use_updateendtime : false);
$smarty->assign('use_searchinvalid',$use_searchinvalid);
$smarty->assign('use_rename',$use_rename);
$smarty->assign('use_update',$use_update);
$smarty->assign('use_create',$use_create);
$smarty->assign('use_delete',$use_delete);
$smarty->assign('dn_link_label_attributes',implode(",",$dn_link_label_attributes));
$smarty->assign('dn_link_search_min_chars',$dn_link_search_min_chars);

$config_js = [];
$config_js["messages"] = $messages;
$config_js["listing_linkto"] = isset($search_result_linkto) ? $search_result_linkto : array($search_result_title);
$config_js["display_show_undefined"] = $display_show_undefined;
$config_js["search_result_show_undefined"] = $search_result_show_undefined;
$config_js["truncate_value_after"] = $search_result_truncate_value_after;
$config_js["search"] = isset($search) ? $search : null;
$config_js["js_date_specifiers"] = $js_date_specifiers;
$config_js["unlock"] = [];
$config_js["unlock"]["use_unlockaccount"] = $use_unlockaccount;
$config_js["unlock"]["use_unlockcomment"] = $use_unlockcomment;
$config_js["unlock"]["use_unlockcomment_required"] = $use_unlockcomment_required;
$config_js["enable"] = [];
$config_js["enable"]["use_enableaccount"] = $use_enableaccount;
$config_js["enable"]["use_enablecomment"] = $use_enablecomment;
$config_js["enable"]["use_enablecomment_required"] = $use_enablecomment_required;
$config_js["attributes_map"] = [];
$config_js["attributes_map"]["dn"] = array("type" => "dn");
$columns = $search_result_items;
if (! in_array($search_result_title, $columns)) array_unshift($columns, $search_result_title);
foreach ($columns as $column )
{
    $config_js["attributes_map"][$column] = array(
        "type" => $attributes_map[$column]["type"],
    );
}
$smarty->assign("config_js", base64_encode(json_encode($config_js)));

# Assign custom template variables
foreach (get_defined_vars() as $key => $value) {
    if (preg_match('/^tpl_(.+)/', $key, $matches)) {
        $smarty->assign($matches[1], $value);
    }
}

# Assign messages
$smarty->assign('lang',$lang);
foreach ($messages as $key => $message) {
    $smarty->assign('msg_'.$key,$message);
}

# Other assignations
$search = "";
if (isset($_REQUEST["search"]) and $_REQUEST["search"] and is_string($_REQUEST["search"])) { $search = htmlentities($_REQUEST["search"]); }
$smarty->assign('search',$search);

# Register plugins
require_once("../lib/smarty.inc.php");
$smarty->registerPlugin("function", "get_attribute", "get_attribute");
$smarty->registerPlugin("function", "convert_ldap_date", "convert_ldap_date");
$smarty->registerPlugin("function", "convert_ad_date", "convert_ad_date");
$smarty->registerPlugin("function", "convert_bytes", "convert_bytes");
$smarty->registerPlugin("function", "split_value", "split_value");
$smarty->registerPlugin("function", "convert_ldap_date", "convert_ldap_date");
$smarty->registerPlugin("function", "convert_ad_date", "convert_ad_date");

# Set default timezone
if( isset($date_timezone) && !empty($date_timezone) )
{
    date_default_timezone_set($date_timezone);
}


#==============================================================================
# Load render templates
#==============================================================================
$templateDirectory = dirname(__FILE__) . "/../js-templates";
if(!is_dir($templateDirectory))
{
    error_log("Wrong template directory: $templateDirectory");
}
$templateFiles = array_diff(scandir($templateDirectory), array('..', '.'));
$templateFilesContent = "";
foreach ($templateFiles as $templateFile) {
    if(preg_match("/\.html$/",$templateFile))
    {
        $templateFilesContent .= file_get_contents(
                                     "$templateDirectory/$templateFile"
                                 ) . "\n";
    }
    else
    {
        error_log("Dropping template $templateFile as it does not end by .html");
    }
}
$smarty->assign('templateFilesContent',$templateFilesContent);

#==============================================================================
# Audit
#==============================================================================
if (isset($audit_log_file)) { require_once("../lib/audit.inc.php"); }

$audit_admin = "";
if (isset($header_name_audit_admin)) {
    $cgi_audit_admin='HTTP_'.strtoupper(str_replace('-','_',$header_name_audit_admin));
    if (array_key_exists($cgi_audit_admin, $_SERVER))
    {
        $audit_admin = $_SERVER[$cgi_audit_admin];
    } else {
        $audit_admin = "anonymous";
    }
} else {
    $audit_admin = "anonymous";
}

#==============================================================================
# Route to API
#==============================================================================
if (isset($_POST["apiendpoint"])) {
    $data = array();
    if (file_exists('api/'.$_POST["apiendpoint"].'.php')) {
        require_once('api/'.$_POST["apiendpoint"].'.php');
    }
    echo json_encode($data);
    exit(0);
}

#==============================================================================
# Route to page
#==============================================================================
$result = "";
$page = "welcome";
$searchaction = "";
if (isset($_GET["page"]) and $_GET["page"]) { $page = $_GET["page"]; }
if ( $page === "checkpassword" and !$use_checkpassword ) { $page = "welcome"; }
if ( $page === "resetpassword" and !$use_resetpassword ) { $page = "welcome"; }
if ( $page === "unlockaccount" and !$use_unlockaccount ) { $page = "welcome"; }
if ( $page === "enableaccount" and !$use_enableaccount ) { $page = "welcome"; }
if ( $page === "searchlocked" and !$use_searchlocked ) { $page = "welcome"; }
if ( $page === "searchdisabled" and !$use_searchdisabled ) { $page = "welcome"; }
if ( $page === "searchexpired" and !$use_searchexpired ) { $page = "welcome"; }
if ( $page === "searchwillexpire" and !$use_searchwillexpire ) { $page = "welcome"; }
if ( $page === "searchidle" and !$use_searchidle ) { $page = "welcome"; }
if ( $page === "auditlog" and !$use_showauditlog ) { $page = "welcome"; }
if ( $page === "updatevaliditydates" and !($use_updatestarttime or $use_updateendtime) ) { $page = "welcome"; }
if ( $page === "searchinvalid" and !$use_searchinvalid ) { $page = "welcome"; }
if ( $page === "update" and !$use_update ) { $page = "welcome"; }
if ( $page === "rename" and !$use_rename ) { $page = "welcome"; }
if ( $page === "create" and !$use_create ) { $page = "welcome"; }
if ( $page === "delete" and !$use_delete ) { $page = "welcome"; }
if ( preg_match("/^search.*$/",$page) )
{
    $searchaction = $page;
    $page = "search"; # Use generic page search
}
if ( file_exists($page.".php") ) { require_once($page.".php"); }
$smarty->assign('page',$page);
$smarty->assign('searchaction',$searchaction);

\Ltb\Ppolicy::smarty_assign_ppolicy($smarty, $pwd_show_policy_pos, $pwd_show_policy, $result, $pwd_policy_config);

if ($result) {
    $smarty->assign('error',$messages[$result]);
} else {
    $smarty->assign('error',"");
}

# Display
$smarty->display('index.tpl');

?>
