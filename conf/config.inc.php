<?php
#==============================================================================
# LTB Service Desk
#
# Copyright (C) 2016 Clement OUDOT
# Copyright (C) 2016 LTB-project.org
#
# This program is free software; you can redistribute it and/or
# modify it under the terms of the GNU General Public License
# as published by the Free Software Foundation; either version 2
# of the License, or (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#
# GPL License: http://www.gnu.org/licenses/gpl.txt
#
#==============================================================================

#==============================================================================
# All the default values are kept here, you should not modify it but use
# config.inc.local.php file instead to override the settings from here.
#==============================================================================

# LDAP
$ldap_type = "openldap";
$ldap_url = "ldap://localhost";
$ldap_starttls = false;
$ldap_binddn = "cn=manager,dc=example,dc=com";
$ldap_bindpw = "secret";
$ldap_base = "dc=example,dc=com";
$ldap_user_base = "ou=users,".$ldap_base;
$ldap_scope = "sub"; # possible values: sub, one, base
$ldap_user_filter = "(objectClass=inetOrgPerson)";
$ldap_ppolicy_filter = "(objectClass=pwdPolicy)";
$ldap_ppolicy_name_attribute = "cn";
$ldap_size_limit = 100;
#$ldap_default_ppolicy = "cn=default,ou=ppolicy,dc=example,dc=com";
$ldap_lastauth_attribute = "authTimestamp";
#$ldap_network_timeout = 10;
$ldap_page_size = 0;

# Override LDAP password policy configuration
#$ldap_lockout_duration = 3600; # 1 hour
#$ldap_password_max_age = 7889400; # 3 months

# How display attributes
$attributes_map = array(
    'businesscategory' => array( 'attribute' => 'businesscategory', 'faclass' => 'briefcase', 'type' => 'text' ),
    'carlicense' => array( 'attribute' => 'carlicense', 'faclass' => 'car', 'type' => 'text' ),
    'created' => array( 'attribute' => 'createtimestamp', 'faclass' => 'clock-o', 'type' => 'date' ),
    'description' => array( 'attribute' => 'description', 'faclass' => 'info-circle', 'type' => 'text' ),
    'displayname' => array( 'attribute' => 'displayname', 'faclass' => 'user-circle', 'type' => 'text' ),
    'employeenumber' => array( 'attribute' => 'employeenumber', 'faclass' => 'hashtag', 'type' => 'text' ),
    'employeetype' => array( 'attribute' => 'employeetype', 'faclass' => 'id-badge', 'type' => 'text' ),
    'fax' => array( 'attribute' => 'facsimiletelephonenumber', 'faclass' => 'fax', 'type' => 'tel', 'dtorder' => 'disable' ),
    'firstname' => array( 'attribute' => 'givenname', 'faclass' => 'user-o', 'type' => 'text' , 'dtorder' => 'disable' ),
    'fullname' => array( 'attribute' => 'cn', 'faclass' => 'user-circle', 'type' => 'text' ),
    'l' => array( 'attribute' => 'l', 'faclass' => 'globe', 'type' => 'text' ),
    'lastname' => array( 'attribute' => 'sn', 'faclass' => 'user-o', 'type' => 'text' ),
    'mail' => array( 'attribute' => 'mail', 'faclass' => 'envelope-o', 'type' => 'mailto', 'multivalued' => true ),
    'mailquota' => array( 'attribute' => 'gosamailquota', 'faclass' => 'pie-chart', 'type' => 'bytes', 'dtorder' => 'disable' ),
    'manager' => array( 'attribute' => 'manager', 'faclass' => 'user-circle-o', 'type' => 'dn_link', 'dtorder' => 'disable' ),
    'mobile' => array( 'attribute' => 'mobile', 'faclass' => 'mobile', 'type' => 'tel', 'dtorder' => 'disable' ),
    'modified' => array( 'attribute' => 'modifytimestamp', 'faclass' => 'clock-o', 'type' => 'date' ),
    'organization' => array( 'attribute' => 'o', 'faclass' => 'building', 'type' => 'text' ),
    'organizationalunit' => array( 'attribute' => 'ou', 'faclass' => 'building-o', 'type' => 'text' ),
    'pager' => array( 'attribute' => 'pager', 'faclass' => 'mobile', 'type' => 'tel' , 'dtorder' => 'disable' ),
    'phone' => array( 'attribute' => 'telephonenumber', 'faclass' => 'phone', 'type' => 'tel', 'dtorder' => 'disable' ),
    'postaladdress' => array( 'attribute' => 'postaladdress', 'faclass' => 'map-marker', 'type' => 'address' ),
    'postalcode' => array( 'attribute' => 'postalcode', 'faclass' => 'globe', 'type' => 'text' ),
    'secretary' => array( 'attribute' => 'secretary', 'faclass' => 'user-circle-o', 'type' => 'dn_link' ),
    'state' => array( 'attribute' => 'st', 'faclass' => 'globe', 'type' => 'text' ),
    'street' => array( 'attribute' => 'street', 'faclass' => 'map-marker', 'type' => 'text' ),
    'title' => array( 'attribute' => 'title', 'faclass' => 'certificate', 'type' => 'text' ),
);

# Directory specific attributes
$openldap_attributes_map = array(
    'authtimestamp' => array( 'attribute' => 'authtimestamp', 'faclass' => 'lock', 'type' => 'date' ),
    'identifier' => array( 'attribute' => 'uid', 'faclass' => 'user-o', 'type' => 'text' ),
    'pwdaccountlockedtime' => array( 'attribute' => 'pwdaccountlockedtime', 'faclass' => 'lock', 'type' => 'date' ),
    'pwdchangedtime' => array( 'attribute' => 'pwdchangedtime', 'faclass' => 'lock', 'type' => 'date', 'sort' => 'descending' ),
    'pwdfailuretime' => array( 'attribute' => 'pwdfailuretime', 'faclass' => 'lock', 'type' => 'date', 'sort' => 'descending' ),
    'pwdlastsuccess' => array( 'attribute' => 'pwdlastsuccess', 'faclass' => 'lock', 'type' => 'date' ),
    'pwdpolicysubentry' => array( 'attribute' => 'pwdpolicysubentry', 'faclass' => 'lock', 'type' => 'ppolicy_dn' ),
    'pwdgraceusetime' => array( 'attribute' => 'pwdgraceusetime', 'faclass' => 'lock', 'type' => 'date' ),
    'starttime' => array( 'attribute' => 'pwdstarttime', 'faclass' => 'lock', 'type' => 'date' ),
    'endtime' => array( 'attribute' => 'pwdendtime', 'faclass' => 'lock', 'type' => 'date' ),
);
$activedirectory_attributes_map = array(
    'authtimestamp' => array( 'attribute' => 'lastlogon', 'faclass' => 'lock', 'type' => 'ad_date' ),
    'identifier' => array( 'attribute' => 'samaccountname', 'faclass' => 'user-o', 'type' => 'text' ),
    'pwdaccountlockedtime' => array( 'attribute' => 'lockouttime', 'faclass' => 'lock', 'type' => 'ad_date' ),
    'pwdchangedtime' => array( 'attribute' => 'pwdlastset', 'faclass' => 'lock', 'type' => 'ad_date' ),
    'pwdfailuretime' => array( 'attribute' => 'badpasswordtime', 'faclass' => 'lock', 'type' => 'ad_date' ),
    'endtime' => array( 'attribute' => 'accountexpires', 'faclass' => 'lock', 'type' => 'ad_date' ),
);

# List of values
// Static lists: key/values in configuration file
$attributes_static_list = array();
// $attributes_static_list['title'] = array( 'Mr' => 'Mister', 'Mrs' => 'Misses' );

// LDAP lists: key/values returned by an LDAP search
$attributes_list = array();
// $attributes_list['organizationalunit'] = array('base'=>'ou=services,dc=example,dc=com', 'filter'=>'(objectClass=organizationalUnit)', 'key'=>'description', 'value'=>'ou');

# Search
$search_attributes = array('uid', 'cn', 'mail');
$search_use_substring_match = true;
$search_result_items = array('identifier', 'mail', 'mobile');
$search_result_title = "fullname";
$search_result_sortby = "lastname";
$search_result_linkto = array("fullname");
$search_result_show_undefined = true;
$search_result_truncate_value_after = 20;

$datatables_page_length_choices = array(10, 25, 50, 100, -1);
$datatables_page_length_default = 10;
$datatables_print_all = true;
$datatables_print_page = true;
$datatables_auto_print = true;

$display_items = array('identifier', 'firstname', 'lastname', 'title', 'businesscategory', 'employeenumber', 'employeetype', 'mail', 'mailquota', 'phone', 'mobile', 'fax', 'postaladdress', 'street', 'postalcode', 'l', 'state', 'organizationalunit', 'organization', 'manager', 'secretary' );
$display_title = "fullname";
$display_show_undefined = false;
$display_password_items = array('pwdchangedtime', 'pwdfailuretime','pwdpolicysubentry', 'authtimestamp', 'pwdgraceusetime', 'starttime', 'endtime', 'created', 'modified');
$display_password_expiration_date = true;

# Features

$use_checkpassword = true;
$use_checkpasswordhistory = false;

$use_resetpassword = true;
$use_resetpassword_resetchoice = true;
$resetpassword_reset_default = true;

$show_lockstatus = true;
$use_unlockaccount = true;
$use_unlockcomment = false;
$use_unlockcomment_required = false;
$use_lockaccount = true;

$use_lockcomment = false;
$use_lockcomment_required = false;

$show_expirestatus = true;

$use_searchlocked = true;

$use_searchdisabled = true;

$use_searchexpired = true;

$use_searchwillexpire = true;
$willexpiredays = 14;

$use_searchidle = true;
$idledays = 60;

$use_enableaccount = false;
$use_disableaccount = false;
$show_enablestatus = false;

$use_enablecomment = false;
$use_enablecomment_required = false;
$use_disablecomment = false;
$use_disablecomment_required = false;

$show_validitystatus = true;
$use_updatestarttime = true;
$use_updateendtime = true;
$use_searchinvalid = true;

## Edition

$use_update = true;
$update_items = array('firstname', 'lastname', 'title', 'businesscategory', 'employeenumber', 'employeetype', 'mail', 'mailquota', 'phone', 'mobile', 'fax', 'postaladdress', 'street', 'postalcode', 'l', 'state', 'organizationalunit', 'organization', 'manager', 'secretary');

$use_rename = true;
$rename_items = array('identifier');

# Local password policy
# This is applied before directory password policy
# Minimal length
$pwd_min_length = 0;
# Maximal length
$pwd_max_length = 0;
# Minimal lower characters
$pwd_min_lower = 0;
# Minimal upper characters
$pwd_min_upper = 0;
# Minimal digit characters
$pwd_min_digit = 0;
# Minimal special characters
$pwd_min_special = 0;
# Definition of special characters
$pwd_special_chars = "^a-zA-Z0-9";
# Forbidden characters
#$pwd_forbidden_chars = "@%";
# Check that password is different than login
$pwd_diff_login = true;
# Forbidden words which must not appear in the password
$pwd_forbidden_words = array();
# Forbidden ldap fields
# Respective values of the user's entry must not appear in the password
# example: $pwd_forbidden_ldap_fields = array('cn', 'givenName', 'sn', 'mail');
$pwd_forbidden_ldap_fields = array();
# Complexity: number of different class of character required
$pwd_complexity = 0;
# use pwnedpasswords api v2 to securely check if the password has been on a leak
$use_pwnedpasswords = false;
# show password entropy bar (require php zxcvbn module)
$pwd_display_entropy = false;
# enforce password entropy check
$pwd_check_entropy = false;
# minimum entropy level required (when $pwd_check_entropy enabled)
$pwd_min_entropy = 3;
# Show policy constraints message:
# always
# never
# onerror
$pwd_show_policy = "never";
# Position of password policy constraints message:
# above - the form
# below - the form
$pwd_show_policy_pos = "above";

## Mail
# LDAP mail attribute
$mail_attributes = array( "mail", "gosaMailAlternateAddress", "proxyAddresses" );
# Get mail address directly from LDAP (only first mail entry)
# Who the email should come from
$mail_from = "admin@example.com";
$mail_from_name = "Service Desk";
$mail_signature = "";
# Notify users anytime their password is changed
$notify_on_change = false;
# Attribute containing user name - used in mail
$mail_username_attribute =  "cn";
# List of mail addresses of administrators to be notified of password changes
#$notify_admin_by_mail_list = array( 'a@example.com','b@example.com' 'c@example.com');
# HTTP header bearing mail of administrator to be notified of password changes
#$header_name_notify_admin_by_mail='ADMIN_MAIL';
# PHPMailer configuration (see https://github.com/PHPMailer/PHPMailer)
$mail_sendmailpath = '/usr/sbin/sendmail';
$mail_protocol = 'smtp';
$mail_smtp_debug = 0;
$mail_debug_format = 'error_log';
$mail_smtp_host = 'localhost';
$mail_smtp_auth = false;
$mail_smtp_user = '';
$mail_smtp_pass = '';
$mail_smtp_port = 25;
$mail_smtp_timeout = 30;
$mail_smtp_keepalive = false;
$mail_smtp_secure = 'tls';
$mail_smtp_autotls = true;
$mail_smtp_options = array();
$mail_contenttype = 'text/plain';
$mail_wordwrap = 0;
$mail_charset = 'utf-8';
$mail_priority = 3;

# Language
$lang = "en";
$allowed_lang = array();
$date_specifiers = "%Y-%m-%d %H:%M:%S (%Z)";
$date_timezone = "UTC";

# Graphics
$logo = "images/ltb-logo.png";
$background_image = "images/unsplash-space.jpeg";
$custom_css = "";
$display_footer = true;
#$logout_link = "http://auth.example.com/logout";
$fake_password_inputs = false;

# Audit
#$audit_log_file = "/var/log/service-desk/audit.log";
$use_showauditlog = false;
$audit_log_days = 5;
$audit_log_items = array('date','ip','user_dn','done_by','action','result','comment');
$audit_log_sortby = "date";
$audit_log_reverse = true;
$audit_log_truncate_value_after = 40;
#$header_name_audit_admin = "AUTH_USER";

# Debug mode
$debug = false;

### Prehooks

# Launch a prehook script before an action.
# Script should return with 0, else action will be aborted, unless error is ignored

# LDAP attribute used as login in posthook script
$prehook_login = "uid";

## Password reset

#$prehook = "/usr/share/service-desk/prehook.sh";

# Display prehook error
#$display_prehook_error = true;
# Encode passwords sent to prehook script as base64. This will prevent alteration of the passwords if set to true.
# To read the actual password in the prehook script, use a base64_decode function/tool
#$prehook_password_encodebase64 = false;
# Ignore prehook error. This will allow to change password even if prehook script fails.
#$ignore_prehook_error = true;

## Lock

#$prehook_lock = "/usr/share/service-desk/prehook_lock.sh";
#$display_prehook_lock_error = true;
#$ignore_prehook_lock_error = true;

## Unlock

#$prehook_unlock = "/usr/share/service-desk/prehook_unlock.sh";
#$display_prehook_unlock_error = true;
#$ignore_prehook_unlock_error = true;

## Enable

#$prehook_enable = "/usr/share/service-desk/prehook_enable.sh";
#$display_prehook_enable_error = true;
#$ignore_prehook_enable_error = true;

## Disable

#$prehook_disable = "/usr/share/service-desk/prehook_disable.sh";
#$display_prehook_disable_error = true;
#$ignore_prehook_disable_error = true;

## Update validity

#$prehook_updatevalidity = "/usr/share/service-desk/prehook_updatevalidity.sh";
#$display_prehook_updatevalidity_error = true;
#$ignore_prehook_updatevalidity_error = true;

### Posthooks

# The posthook is only launched if the action was successful

# LDAP attribute used as login in posthook script
$posthook_login = "uid";

## Password reset

#$posthook = "/usr/share/service-desk/posthook.sh";

# Display posthook error
#$display_posthook_error = true;
# Encode passwords sent to posthook script as base64. This will prevent alteration of the passwords if set to true.
# To read the actual password in the posthook script, use a base64_decode function/tool
#$posthook_password_encodebase64 = false;

## Lock

#$posthook_lock = "/usr/share/service-desk/posthook_lock.sh";
#$display_posthook_lock_error = true;

## Unlock

#$posthook_unlock = "/usr/share/service-desk/posthook_unlock.sh";
#$display_posthook_unlock_error = true;

## Enable

#$posthook_enable = "/usr/share/service-desk/posthook_enable.sh";
#$display_posthook_enable_error = true;

## Disable

#$posthook_disable = "/usr/share/service-desk/posthook_disable.sh";
#$display_posthook_disable_error = true;

## Update validity

#$posthook_updatevalidity = "/usr/share/service-desk/posthook_updatevalidity.sh";
#$display_posthook_updatevalidity_error = true;

# The name of an HTTP Header that may hold a reference to an extra config file to include.
#$header_name_extra_config="SSP-Extra-Config";

# Cache directory
#$smarty_compile_dir = "/var/cache/service-desk/templates_c";
#$smarty_cache_dir = "/var/cache/service-desk/cache";

# Smarty debug mode - will popup debug information on web interface
# and add many smarty debug messages in error logs
$smarty_debug = false;

# Allow to override current settings with local configuration
if (file_exists (dirname (__FILE__) . '/config.inc.local.php')) {
    include dirname (__FILE__) . '/config.inc.local.php';
}

# Smarty
if (!defined("SMARTY")) {
    define("SMARTY", "/usr/share/php/smarty3/Smarty.class.php");
}

# Allow to override current settings with an extra configuration file, whose reference is passed in HTTP_HEADER $header_name_extra_config
if (isset($header_name_extra_config)) {
    $extraConfigKey = "HTTP_".strtoupper(str_replace('-','_',$header_name_extra_config));
    if (array_key_exists($extraConfigKey, $_SERVER)) {
        $extraConfig = preg_replace("/[^a-zA-Z0-9-_]+/", "", htmlspecialchars($_SERVER[$extraConfigKey]));
        if (strlen($extraConfig) > 0 && file_exists (__DIR__ . "/config.inc.".$extraConfig.".php")) {
            require  __DIR__ . "/config.inc.".$extraConfig.".php";
        }
    }
}

# Get $notify_admin_by_mail from header $header_name_notify_admin_by_mail
if (isset($header_name_notify_admin_by_mail)) {
    # cgi header passing
    $cgi_admin_by_mail_var='HTTP_'.strtoupper(str_replace('-','_',$header_name_notify_admin_by_mail));
    if (array_key_exists($cgi_admin_by_mail_var, $_SERVER))
    {
        $notify_admin_by_mail=filter_var($_SERVER[$cgi_admin_by_mail_var], FILTER_VALIDATE_EMAIL);
    }
}

?>
