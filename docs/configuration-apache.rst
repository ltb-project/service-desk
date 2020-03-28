Apache configuration
====================

Virtual host
^^^^^^^^^^^^

Here is a sample virtual host configuration:

.. code-block:: apache

 <VirtualHost *:80>
    ServerName sd.example.com

    DocumentRoot /usr/share/service-desk/htdocs
    DirectoryIndex index.php

    <Directory /usr/share/service-desk/htdocs>
        AllowOverride None
        Require all granted
    </Directory>

    LogLevel warn
    ErrorLog /var/log/apache2/sd_error.log
    CustomLog /var/log/apache2/sd_access.log combined
 </VirtualHost>

.. tip:: The application can also be published in a directory inside the default host

.. warning:: You must protect the access to the application, else everyone will be able to reset any user password!

LDAP authentication and authorization
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

You can use Apache `mod_authnz_ldap`_. This module checks user credentials against the LDAP directory and can restrict access to users or groups.

.. _mod_authnz_ldap: https://httpd.apache.org/docs/current/mod/mod_authnz_ldap.html

.. code-block:: apache

    <Directory /usr/share/service-desk/htdocs>
        AllowOverride None
        AuthType basic
        AuthName "LTB Service Desk"
        AuthBasicProvider ldap
        AuthLDAPURL ldap://ldap.example.com/dc=example,dc=com?uid
        Require ldap-group cn=support,ou=groups,dc=example,dc=com
    </Directory>

External authentication
^^^^^^^^^^^^^^^^^^^^^^^

You can use any authentication source and authentication protocols, like CAS, SAML or OpenID Connect.
Configuring these solutions is out of scope of the current documentation.
