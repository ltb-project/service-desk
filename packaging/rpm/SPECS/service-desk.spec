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
%define sd_version   0.1
%define sd_destdir   /usr/share/%{name}

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
Requires: php, php-ldap, php-Smarty

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
mkdir -p %{buildroot}/%{sd_destdir}/cache
mkdir -p %{buildroot}/%{sd_destdir}/conf
mkdir -p %{buildroot}/%{sd_destdir}/htdocs
mkdir -p %{buildroot}/%{sd_destdir}/lang
mkdir -p %{buildroot}/%{sd_destdir}/lib
mkdir -p %{buildroot}/%{sd_destdir}/templates
mkdir -p %{buildroot}/%{sd_destdir}/templates_c
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
## Apache configuration
install -m 644 %{SOURCE1}     %{buildroot}/etc/httpd/conf.d/service-desk.conf

# Adapt Smarty path
sed -i 's:/usr/share/php/smarty3:/usr/share/php/Smarty:' %{buildroot}%{sd_destdir}/conf/config.inc.php

%post
#=================================================
# Post Installation
#=================================================

# Change owner
/bin/chown apache:apache %{sd_destdir}/cache
/bin/chown apache:apache %{sd_destdir}/templates_c

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

#=================================================
# Changelog
#=================================================
%changelog
* Mon Mar 30 2020 - Clement Oudot <clem@ltb-project.org> - 0.1-1
- First release
