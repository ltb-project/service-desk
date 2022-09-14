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
$ldap_url = "ldap://localhost";
$ldap_starttls = false;
$ldap_binddn = "cn=manager,dc=example,dc=com";
$ldap_bindpw = "secret";
$ldap_base = "dc=example,dc=com";
$ldap_user_base = "ou=users,".$ldap_base;
$ldap_user_filter = "(objectClass=inetOrgPerson)";
$ldap_size_limit = 100;
#$ldap_default_ppolicy = "cn=default,ou=ppolicy,dc=example,dc=com";

# Site Authentication Configuration. These are pre-filled with examples, please modify to suit your needs.
$ldap_allowed_admin_users = array("Administrator");// UID's or SamAccountName(s) of users who are allowed to login and edit all accounts.
$ldap_allowed_admin_ous = array("OU=Managers,DC=example,DC=com");// Organizational Unit(s) of users who are allowed to login and edit all accounts.
$ldap_allowed_admin_groups = array("CN=Administrators,OU=Groups,DC=example,DC=com");// Security Group(s) of users who are allowed to login and edit all accounts.
$ldap_disallowed_ous = array("OU=Guests,DC=example,DC=com");// Organizational Units of users who are NOT allowed to log in at all.

# How display attributes
$attributes_map = array(
    'authtimestamp' => array( 'attribute' => 'authtimestamp', 'faclass' => 'lock', 'type' => 'date' ),
    'businesscategory' => array( 'attribute' => 'businesscategory', 'faclass' => 'briefcase', 'type' => 'text' ),
    'carlicense' => array( 'attribute' => 'carlicense', 'faclass' => 'car', 'type' => 'text' ),
    'created' => array( 'attribute' => 'createtimestamp', 'faclass' => 'clock-o', 'type' => 'date' ),
    'description' => array( 'attribute' => 'description', 'faclass' => 'info-circle', 'type' => 'text' ),
    'displayname' => array( 'attribute' => 'displayname', 'faclass' => 'user-circle', 'type' => 'text' ),
    'employeenumber' => array( 'attribute' => 'employeenumber', 'faclass' => 'hashtag', 'type' => 'text' ),
    'employeetype' => array( 'attribute' => 'employeetype', 'faclass' => 'id-badge', 'type' => 'text' ),
    'fax' => array( 'attribute' => 'facsimiletelephonenumber', 'faclass' => 'fax', 'type' => 'tel' ),
    'firstname' => array( 'attribute' => 'givenname', 'faclass' => 'user-o', 'type' => 'text' ),
    'fullname' => array( 'attribute' => 'cn', 'faclass' => 'user-circle', 'type' => 'text' ),
    'identifier' => array( 'attribute' => 'uid', 'faclass' => 'user-o', 'type' => 'text' ),
    'l' => array( 'attribute' => 'l', 'faclass' => 'globe', 'type' => 'text' ),
    'lastname' => array( 'attribute' => 'sn', 'faclass' => 'user-o', 'type' => 'text' ),
    'mail' => array( 'attribute' => 'mail', 'faclass' => 'envelope-o', 'type' => 'mailto' ),
    'mailquota' => array( 'attribute' => 'gosamailquota', 'faclass' => 'pie-chart', 'type' => 'bytes' ),
    'manager' => array( 'attribute' => 'manager', 'faclass' => 'user-circle-o', 'type' => 'dn_link' ),
    'mobile' => array( 'attribute' => 'mobile', 'faclass' => 'mobile', 'type' => 'tel' ),
    'modified' => array( 'attribute' => 'modifytimestamp', 'faclass' => 'clock-o', 'type' => 'date' ),
    'organization' => array( 'attribute' => 'o', 'faclass' => 'building', 'type' => 'text' ),
    'organizationalunit' => array( 'attribute' => 'ou', 'faclass' => 'building-o', 'type' => 'text' ),
    'pager' => array( 'attribute' => 'pager', 'faclass' => 'mobile', 'type' => 'tel' ),
    'phone' => array( 'attribute' => 'telephonenumber', 'faclass' => 'phone', 'type' => 'tel' ),
    'postaladdress' => array( 'attribute' => 'postaladdress', 'faclass' => 'map-marker', 'type' => 'text' ),
    'postalcode' => array( 'attribute' => 'postalcode', 'faclass' => 'globe', 'type' => 'text' ),
    'pwdaccountlockedtime' => array( 'attribute' => 'pwdaccountlockedtime', 'faclass' => 'lock', 'type' => 'date' ),
    'pwdchangedtime' => array( 'attribute' => 'pwdchangedtime', 'faclass' => 'lock', 'type' => 'date' ),
    'pwdfailuretime' => array( 'attribute' => 'pwdfailuretime', 'faclass' => 'lock', 'type' => 'date' ),
    'pwdreset' => array( 'attribute' => 'pwdreset', 'faclass' => 'lock', 'type' => 'boolean' ),
    'secretary' => array( 'attribute' => 'secretary', 'faclass' => 'user-circle-o', 'type' => 'dn_link' ),
    'state' => array( 'attribute' => 'st', 'faclass' => 'globe', 'type' => 'text' ),
    'street' => array( 'attribute' => 'street', 'faclass' => 'map-marker', 'type' => 'text' ),
    'title' => array( 'attribute' => 'title', 'faclass' => 'certificate', 'type' => 'text' ),
);

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
$datatables_auto_print = true;

$display_items = array('identifier', 'firstname', 'lastname', 'title', 'businesscategory', 'employeenumber', 'employeetype', 'mail', 'mailquota', 'phone', 'mobile', 'fax', 'postaladdress', 'street', 'postalcode', 'l', 'state', 'organizationalunit', 'organization');
$display_title = "fullname";
$display_show_undefined = false;
$display_password_items = array('pwdchangedtime', 'pwdreset', 'pwdaccountlockedtime', 'pwdfailuretime','pwdpolicysubentry', 'authtimestamp', 'created', 'modified');
$display_password_expiration_date = true;

# Features
$use_checkpassword = true;
$use_resetpassword = true;
$resetpassword_reset_default = true;
$use_unlockaccount = true;
$use_lockaccount = true;
$use_searchlocked = true;
$use_searchexpired = true;
$use_searchwillexpire = true;
$willexpiredays = 14;
$use_searchidle = true;
$idledays = 60;

# Language
$lang ="en";
$date_specifiers = "%Y-%m-%d %H:%M:%S (%Z)";

# Graphics
$logo = "images/ltb-logo.png";
$background_image = "images/unsplash-space.jpeg";
$custom_css = "";
$display_footer = true;
#$logout_link = "http://auth.example.com/logout";

# Debug mode
$debug = false;

## Post Hook
# Launch a posthook script after successful password change
#$posthook = "/usr/share/service-desk/posthook.sh";
# LDAP attribute used as login in posthook script
#$posthook_login = "uid";
# Display posthook error
#$display_posthook_error = true;
# Encode passwords sent to posthook script as base64. This will prevent alteration of the passwords if set to true.
# To read the actual password in the posthook script, use a base64_decode function/tool
#$posthook_password_encodebase64 = false;

# The name of an HTTP Header that may hold a reference to an extra config file to include.
#$header_name_extra_config="SSP-Extra-Config";

# Cache directory
#$smarty_compile_dir = "/var/cache/service-desk/templates_c";
#$smarty_cache_dir = "/var/cache/service-desk/cache";

# Smarty debug mode - will popup debug information on web interface
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
        $extraConfig = preg_replace("/[^a-zA-Z0-9-_]+/", "", filter_var($_SERVER[$extraConfigKey], FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH));
        if (strlen($extraConfig) > 0 && file_exists (__DIR__ . "/config.inc.".$extraConfig.".php")) {
            require  __DIR__ . "/config.inc.".$extraConfig.".php";
        }
    }
}

?>
