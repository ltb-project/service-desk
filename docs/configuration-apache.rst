.. _apache_configuration:

Apache configuration
====================

Apache or Nginx is not installed by default. You must choose one.

Current section explains how to install and configure Apache.

Install and configure Apache
----------------------------

On Debian:

.. code-block:: shell

  apt install apache2

  # configure php-fpm for apache2
  a2enmod proxy_fcgi setenvif
  a2enconf php*-fpm.conf

  # enable the default website
  a2ensite service-desk

Then you can configure your virtual host. A default one is provided in ``/etc/apache2/sites-available``

On RHEL systems:

.. code-block:: shell

  dnf install httpd


Then you can configure your virtual host. A default one is provided in ``/etc/httpd/conf.d/``

Virtual host
------------

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
