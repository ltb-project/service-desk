#=================================================
# Specification file for Service Desk
#
# Install LTB project Service Desk
#
# GPL License
#
# Copyright (C) 2020 Clement OUDOT
# Copyright (C) 2020 Worteks
# Copyright (C) 2020 LTB-project
#=================================================

%global sd_destdir   %{_datadir}/%{name}
%global sd_cachedir  %{_localstatedir}/cache/%{name}
%define sd_realname  ltb-project-%{name}

Name:      service-desk
Version:   0.6
Release:   1%{?dist}
Summary:   LDAP Tool Box Service Desk web interface
URL:       https://ltb-project.org/
License:   GPL-3.0-only

BuildArch: noarch

Source0:   https://ltb-project.org/archives/%{sd_realname}-%{version}.tar.gz
Source1:   service-desk-apache.conf

%{?fedora:BuildRequires: phpunit9}
Requires:  coreutils
Requires:  php(language) >= 7.3
Requires:  php-ldap
Requires:  php-Smarty
Requires:  php-fpm

Provides:  bundled(js-bootstrap) = v5.3.2
Provides:  bundled(js-jquery) = v3.7.1
Provides:  bundled(js-datatables.net-datatables.net) = 2.1.2
Provides:  bundled(js-datatables.net-datatables.net-bs5) = 2.0.8
Provides:  bundled(js-datatables.net-datatables.net-buttons) = 3.1.0
Provides:  bundled(js-datatables.net-datatables.net-buttons-bs5) = 3.0.2
Provides:  bundled(fontawesome-fonts) = 6.5.2
Provides:  bundled(php-ltb-project-ltb-common) = 0.3.0
Provides:  bundled(php-bjeavons-zxcvbn-php) = 1.3.1
Provides:  bundled(php-guzzlehttp-guzzle) = 7.8.1
Provides:  bundled(php-guzzlehttp-promises) = 2.0.2
Provides:  bundled(php-guzzlehttp-psr7) = 2.6.2
Provides:  bundled(php-mxrxdxn-pwned-passwords) = 2.1.0
Provides:  bundled(php-phpmailer) = 6.9.1
Provides:  bundled(php-psr-http-client) = 1.0.3
Provides:  bundled(php-psr-http-factory) = 1.0.2
Provides:  bundled(php-psr-http-message) = 2.0
Provides:  bundled(php-ralouphie-getallheaders) = 3.0.3
Provides:  bundled(php-symfony-deprecation-contracts) = 3.4.0
Provides:  bundled(php-symfony-finder) = 7.0.0
Provides:  bundled(php-symfony-polyfill) = v1.31.0
Provides:  bundled(php-symfony-deprecation-contracts) = v2.5.3
Provides:  bundled(php-symfony-var-exporter) = v5.4.40
Provides:  bundled(php-psr-container) = 1.1.2
Provides:  bundled(php-symfony-service-contracts) = v2.5.3
Provides:  bundled(php-psr-cache) = 1.0.1
Provides:  bundled(php-symfony-cache-contracts) = v2.5.3
Provides:  bundled(php-psr-log) = 1.1.4
Provides:  bundled(php-symfony-cache) = v5.4.42
Provides:  bundled(php-predis-predis) = v2.2.2


%description
Service Desk is a PHP application that allows administrators to check, unlock
and reset user passwords in an LDAP directory.
Service Desk is provided by LDAP Tool Box project: https://ltb-project.org


%prep
%setup -q -n %{sd_realname}-%{version}
# Clean hidden files in bundled php libs
find . \
  \( -name .gitignore -o -name .travis.yml -o -name .pullapprove.yml \) \
  -delete


%install
# Create directories
mkdir -p %{buildroot}/%{sd_destdir}
mkdir -p %{buildroot}/%{sd_destdir}/conf
mkdir -p %{buildroot}/%{sd_destdir}/htdocs
mkdir -p %{buildroot}/%{sd_destdir}/lang
mkdir -p %{buildroot}/%{sd_destdir}/lib
mkdir -p %{buildroot}/%{sd_destdir}/templates
mkdir -p %{buildroot}/%{sd_destdir}/vendor
mkdir -p %{buildroot}/%{sd_cachedir}/cache
mkdir -p %{buildroot}/%{sd_cachedir}/templates_c

# Copy files
## Program
install -p -m 644 htdocs/*.php   %{buildroot}/%{sd_destdir}/htdocs
cp -a             htdocs/css     %{buildroot}/%{sd_destdir}/htdocs
cp -a             htdocs/images  %{buildroot}/%{sd_destdir}/htdocs
cp -a             htdocs/js      %{buildroot}/%{sd_destdir}/htdocs
cp -a             htdocs/vendor  %{buildroot}/%{sd_destdir}/htdocs
install -p -m 644 lang/*         %{buildroot}/%{sd_destdir}/lang
install -p -m 644 lib/*          %{buildroot}/%{sd_destdir}/lib
install -p -m 644 templates/*    %{buildroot}/%{sd_destdir}/templates
cp -a             vendor/*       %{buildroot}/%{sd_destdir}/vendor

## Apache configuration
mkdir -p %{buildroot}/%{_sysconfdir}/httpd/conf.d
install -m 644 %{SOURCE1} \
  %{buildroot}/%{_sysconfdir}/httpd/conf.d/service-desk.conf

# Adapt Smarty paths
sed -i \
  -e 's:/usr/share/php/smarty3:/usr/share/php/Smarty:' \
  -e 's:^#$smarty_cache_dir.*:$smarty_cache_dir = "'%{sd_cachedir}/cache'";:' \
  -e 's:^#$smarty_compile_dir.*:$smarty_compile_dir = "'%{sd_cachedir}/templates_c'";:' \
  conf/config.inc.php

# Move conf file to %%_sysconfdir
mkdir -p %{buildroot}/%{_sysconfdir}/%{name}
install -p -m 644 conf/config.inc.php \
  %{buildroot}/%{_sysconfdir}/%{name}/

# Load configuration files from /etc/service-desk/
for file in $( grep -r -l -E "\([^(]+\/conf\/[^)]+\)" %{buildroot}/%{sd_destdir} ) ; do
  sed -i -e \
    's#([^(]\+/conf/\([^")]\+\)")#("%{_sysconfdir}/%{name}/\1")#' \
    ${file}
done


%pre
# Backup old configuration to /etc/service-desk
for file in $( find %{sd_destdir}/conf -name "*.php" -type f ! -name 'config.inc.php' -printf "%f\n" 2>/dev/null );
do
    # move conf file to /etc/service-desk/*.save
    mkdir -p %{_sysconfdir}/%{name}
    mv %{sd_destdir}/conf/${file} %{_sysconfdir}/%{name}/${file}.save
done
# Move specific file config.inc.php to /etc/service-desk/config.inc.php.bak
if [[ -f "%{sd_destdir}/conf/config.inc.php"  ]]; then
    mkdir -p %{_sysconfdir}/%{name}
    mv %{sd_destdir}/conf/config.inc.php \
       %{_sysconfdir}/%{name}/config.inc.php.bak
fi


%post
# Move old configuration to /etc/self-service-password
for file in $( find %{_sysconfdir}/%{name} -name "*.save" -type f );
do
    # move previously created *.save file into its equivalent without .save
    mv ${file} ${file%.save}
done
# Clean cache
rm -rf %{sd_cachedir}/{cache,templates_c}/*


%files
%license LICENSE
%doc AUTHORS README.md
%dir %{_sysconfdir}/%{name}
%config %{_sysconfdir}/%{name}/config.inc.php
%config(noreplace) %{_sysconfdir}/httpd/conf.d/service-desk.conf
%{sd_destdir}
%dir %{sd_cachedir}
%attr(-,apache,apache) %{sd_cachedir}/cache
%attr(-,apache,apache) %{sd_cachedir}/templates_c


%changelog
* Mon Dec 02 2024 Clement Oudot <clem@ltb-project.org> - 0.6-1
- gh#52: LTB Service Desk Active Directory Support
- gh#53: Display pwdPolicySubentry
- gh#57: Add new password policy items from Behera draft 10
- gh#74: Avoid browsers prompting for storing new password
- gh#83: Set autocomplete properties for password fields
- gh#91: Add service-desk dependencies in documentation
- gh#97: update authTimestamp to pwdLastSuccess
- gh#103: Configure last auth attribute
- gh#104: Enable phpunit in CI
- gh#106: Constant FILTER_SANITIZE_STRING is deprecated
- gh#107: Replace deprecated FILTER_SANITIZE_STRING constant
- gh#109: fix Error while searching for multiple entries with smarty 4.3.4 (#108)
- gh#110: Display localized time
- gh#111: add an option for defining the timezone (#110)
- gh#115: Update doc to be consistent with main LTB project doc
- gh#116: Document required installation step for smarty3
- gh#117: Displayed message if wrong a password is tested is not really explicit
- gh#118: use new ltb-ldap v0.2
- gh#119: Display password policy when changing password
- gh#123: Configure which colums should not be sorted
- gh#124: Add allowed_lang configuration parameter
- gh#125: Possibility to block an account (different that locking)
- gh#126: Check password in history of old passwords
- gh#127: Add a comment on an action in audit log
- gh#128: Display audit log
- gh#129: Update bootstrap library
- gh#130: Update bootstrap library
- gh#132: Print buttons
- gh#133: Add options to hide lock panel and expired panel
- gh#134: Display pwdpolicysubentry
- gh#135: Options to show/hide lock and expire panels
- gh#136: Use LTB LDAP v0.2
- gh#137: #124: Adding allowed_lang configuration.
- gh#138: Remove duplicate detectLanguage code
- gh#139:  Strategy for composer dependencies
- gh#140: Added modal functionality to insert comment to audit logs when un/locking account.
- gh#141: RPM spec file cleanup
- gh#142: deb cleanup
- gh#143: add .gitignore in conf directory
- gh#144: make all search functions to use a scope
- gh#145: use new method get_first_value from ltb-ldap project
- gh#146: use new method get_first_value from ltb-ldap project (#145)
- gh#147: update ltb-ldap library name to ltb-common
- gh#148: update ltb-ldap library name to ltb-common (#147)
- gh#149: make all search functions to use a scope (#144)
- gh#150: Update ltb-common version to v0.4.0
- gh#151: #112: Fixing deprecated error messages by upgrading to smarty4.
- gh#153: add .gitignore in conf directory (#143)
- gh#154: Manage composer.lock and composer update (#139)
- gh#155: 141 rpm spec file cleanup
- gh#156: Active Directory support
- gh#157: Added functionality to display audit logs
- gh#158: Update to smarty4
- gh#159: remove verbose smarty messages
- gh#160: remove verbose smarty logs, unless smarty_debug == true (#159)
- gh#161: deb cleanup (#142)
- gh#162: Password reset checks pwdHistory
- gh#163: Update doc for Nginx
- gh#164: use local password policy feature from ltb-common (#119)
- gh#165: use new page_size parameter from ltb-common
- gh#166: Use page size parameter from ltb-common (#165)
- gh#167: Check target entry DN against LDAP configured filter
- gh#168: Add hooks for other actions than password modification
- gh#169: Block account
- gh#170: Remove obsolete code
- gh#171: Append option to sort login history in descending order
- gh#172: Possibility to edit account/password validity dates
- gh#173: Add new password policy items from behera draft 10
- gh#174: Check DN is matching configured search parameters before allowing any action on it
- gh#175: Hooks for other actions than password modification
- gh#176: Use ltb-common for detect language functionnality
- gh#177: add comments in all menus (#127)
- gh#179: remove numerous warnings
- gh#180: remove multiple warnings (#179)
- gh#181: add "sort" param for sorting LDAP multivalued attributes (#171)
- gh#183: Restore print buttons
- gh#184: Fatal error when wrong base DN configured
- gh#185: Missing message for pwdlastsuccess attribute
- gh#186: Use correct last auth attribute
- gh#187: Error when enabling/disabling/locking/unlocking account is not shown
- gh#189: 187 show error when disabling account
- gh#190: Possibility to edit account validity dates

* Wed May 17 2023 Clement Oudot <clem@ltb-project.org> - 0.5.1-1
- gh#92: Message override broken in 0.5
- gh#94: Missing replacement for lang value (issue #92)
- gh#95: Some documentation improvements
- gh#96: Add source IP in audit
- gh#98: Add IP in audit
- gh#99: Provide result codes for lock/unlock account actions

* Mon Apr 24 2023 Clement Oudot <clem@ltb-project.org> - 0.5-1
- gh#45: Do not enable lockout feature if no ppolicy associated to account or ppolicy has pwdLockout value to FALSE
- gh#47: Don't lock account until a valid ppolicy with pwdLockout=TRUE is associated (#45)
- gh#49: Timestamp value displayer
- gh#51: Displaying Items Manager & Secretary
- gh#54: Add prehook
- gh#55: Function ldap_sort is deprecated in PHP8+
- gh#56: Split debug and debug_smarty
- gh#60: PHP Fatal error:  Uncaught TypeError: Cannot access offset of type string on string
- gh#61: Check that values is an array before parsing it
- gh#62: Provide CSS map files for minified version
- gh#63: Smarty debug
- gh#64: CSS map files and bootstrap 3.4.1
- gh#65: Update Docker image with PHP 8.1 and Smarty 4
- gh#69: Ltb ldap integration providing ldapSort support for php >= 8
- gh#70: Factorize search
- gh#71: Send a mail to user after password reset
- gh#72: Log who has done the action (audit trail)
- gh#73: Send a mail to administrator
- gh#75: Append an option to enable/disable "Force user to reset its password at next login" button
- gh#77: Run composer when building distribution archive
- gh#79: Disable reset choice button
- gh#81: Update general-parameters.rst
- gh#82: Notify user and administrators by mail when password is changed
- gh#84: Display DN links
- gh#85: Displayer for address
- gh#86: Force line return for long values
- gh#88: Force line break
- gh#89: Address displayer
- gh#90: Prehook feature

* Mon May 17 2021 Clement Oudot <clem@ltb-project.org> - 0.4-1
- gh#19: Display expiration date
- gh#20: fix(undefined)
- gh#22: Configure cache dir and template cache dir
- gh#26: Remove datepicker
- gh#27: Display expiration date
- gh#30: Move cache dirs in /var
- gh#31: Docker Container
- gh#36: feat(docker)
- gh#37: Dashboard to list locked accounts
- gh#38: Dashboard page for locked accounts
- gh#39: Dashboard expired passwords
- gh#40: Dashboard will expire passwords
- gh#42: Dashboard idle accounts
- gh#44: Multi tenancy

* Mon Jun 29 2020 Clement Oudot <clem@ltb-project.org> - 0.3-1
- Bug #15: Handle the case where pwdAccountLockedTime is set but pwdLockoutDuration is not set or is equal to 0
- Feature #16: Possibility to lock an account
- Feature #17: Allow the Smarty path to be set in conf.inc.local.php

* Tue May 19 2020 Clement Oudot <clem@ltb-project.org> - 0.2-2
- Bug #13: Syntax error in resetpassword.php

* Fri May 15 2020 Clement Oudot <clem@ltb-project.org> - 0.2-1
- Bug #5: Password is marked as expired if policy do not set pwdMaxAge
- Bug #7: The pwdReset radio button is not checked by default
- Feature #9: PostHook
- Feature #10: Viewer for quota attributes

* Mon Mar 30 2020 Clement Oudot <clem@ltb-project.org> - 0.1-1
- First release
