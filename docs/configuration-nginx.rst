.. _nginx_configuration:

Nginx configuration
====================

Apache or Nginx is not installed by default. You must choose one.

Current section explains how to install and configure Nginx.

Install and configure Nginx
----------------------------

On Debian:

.. code-block:: shell

  apt install nginx php-fpm

  # configure php-fpm for Nginx
  a2enmod proxy_fcgi setenvif
  a2enconf php*-fpm.conf

  # enable the default website
  cd /etc/nginx/sites-enabled/ && ln -s ../sites-availables/service-desk

On RHEL systems:

.. code-block:: shell

  dnf install nginx php-fpm

Block server
------------

Here is a sample block server configuration:

.. code-block:: nginx

  server {
          listen 80;
          server_name sd.example.com;
          root /usr/share/service-desk/htdocs;
  
          index index.php;
  
          location ~ \.php$ {
                  try_files $uri $uri/ =404;
                  fastcgi_pass unix:/run/php/php-fpm.sock;
                  include /etc/nginx/fastcgi_params;
                  fastcgi_param SCRIPT_FILENAME   $request_filename;
          }
  } 

.. warning:: You must protect the access to the application, else everyone will be able to reset any user password!

External authentication
-----------------------

You can use any authentication source and authentication protocols, like CAS, SAML or OpenID Connect.
Configuring these solutions is out of scope of the current documentation.
