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

#=================================================
# Variables
#=================================================
%define sd_name      service-desk
%define sd_realname  ltb-project-%{name}
%define sd_version   0.5
%define sd_destdir   /usr/share/%{name}
%define sd_cachedir  /var/cache/%{name}

#=================================================
# Header
#=================================================
Summary: LDAP Tool Box Service Desk web interface
Name: %{sd_name}
Version: %{sd_version}
Release: 1%{?dist}
License: GPL
BuildArch: noarch

Group: Applications/Web
URL: https://ltb-project.org

Source: %{sd_realname}-%{sd_version}.tar.gz
Source1: service-desk-apache.conf
BuildRoot: %{_tmppath}/%{name}-%{version}-%{release}-root-%(%{__id_u} -n)

Prereq: coreutils
Requires: php, php-ldap

%description
Service Desk is a PHP application that allows administrators to check, unlock and reset user passwords in an LDAP directory.
Service Desk is provided by LDAP Tool Box project: https://ltb-project.org

#=================================================
# Source preparation
#=================================================
%prep
%setup -n %{sd_realname}-%{sd_version}

#=================================================
# Installation
#=================================================
%install
rm -rf %{buildroot}

# Create directories
mkdir -p %{buildroot}/%{sd_destdir}
mkdir -p %{buildroot}/%{sd_cachedir}/cache
mkdir -p %{buildroot}/%{sd_destdir}/conf
mkdir -p %{buildroot}/%{sd_destdir}/htdocs
mkdir -p %{buildroot}/%{sd_destdir}/lang
mkdir -p %{buildroot}/%{sd_destdir}/lib
mkdir -p %{buildroot}/%{sd_destdir}/templates
mkdir -p %{buildroot}/%{sd_cachedir}/templates_c
mkdir -p %{buildroot}/%{sd_cachedir}/vendor
mkdir -p %{buildroot}/etc/httpd/conf.d

# Copy files
## Program
install -m 644 conf/*         %{buildroot}/%{sd_destdir}/conf
install -m 644 htdocs/*.php   %{buildroot}/%{sd_destdir}/htdocs
cp -a          htdocs/css     %{buildroot}/%{sd_destdir}/htdocs
cp -a          htdocs/images  %{buildroot}/%{sd_destdir}/htdocs
cp -a          htdocs/vendor  %{buildroot}/%{sd_destdir}/htdocs
install -m 644 lang/*         %{buildroot}/%{sd_destdir}/lang
install -m 644 lib/*          %{buildroot}/%{sd_destdir}/lib
install -m 644 templates/*    %{buildroot}/%{sd_destdir}/templates
cp -a          vendor/*       %{buildroot}/%{sd_destdir}/vendor
## Apache configuration
install -m 644 %{SOURCE1}     %{buildroot}/etc/httpd/conf.d/service-desk.conf

# Adapt Smarty paths
sed -i 's:/usr/share/php/smarty3:/usr/share/php/Smarty:' %{buildroot}%{sd_destdir}/conf/config.inc.php
sed -i 's:^#$smarty_cache_dir.*:$smarty_cache_dir = "'%{sd_cachedir}/cache'";:' %{buildroot}%{sd_destdir}/conf/config.inc.php
sed -i 's:^#$smarty_compile_dir.*:$smarty_compile_dir = "'%{sd_cachedir}/templates_c'";:' %{buildroot}%{sd_destdir}/conf/config.inc.php

%post
#=================================================
# Post Installation
#=================================================

# Change owner
/bin/chown apache:apache %{sd_cachedir}/cache
/bin/chown apache:apache %{sd_cachedir}/templates_c

#=================================================
# Cleaning
#=================================================
%clean
rm -rf %{buildroot}

#=================================================
# Files
#=================================================
%files
%defattr(-, root, root, 0755)
%config(noreplace) %{sd_destdir}/conf/config.inc.php
%config(noreplace) /etc/httpd/conf.d/service-desk.conf
%{sd_destdir}
%{sd_cachedir}

#=================================================
# Changelog
#=================================================
%changelog
* Mon Apr 24 2023 - Clement Oudot <clem@ltb-project.org> - 0.5-1
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
* Mon May 17 2021 - Clement Oudot <clem@ltb-project.org> - 0.4-1
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
* Mon Jun 29 2020 - Clement Oudot <clem@ltb-project.org> - 0.3-1
- Bug #15: Handle the case where pwdAccountLockedTime is set but pwdLockoutDuration is not set or is equal to 0
- Feature #16: Possibility to lock an account
- Feature #17: Allow the Smarty path to be set in conf.inc.local.php
* Tue May 19 2020 - Clement Oudot <clem@ltb-project.org> - 0.2-2
- Bug #13: Syntax error in resetpassword.php
* Fri May 15 2020 - Clement Oudot <clem@ltb-project.org> - 0.2-1
- Bug #5: Password is marked as expired if policy do not set pwdMaxAge
- Bug #7: The pwdReset radio button is not checked by default
- Feature #9: PostHook
- Feature #10: Viewer for quota attributes
* Mon Mar 30 2020 - Clement Oudot <clem@ltb-project.org> - 0.1-1
- First release
