.. _nginx_configuration:

Nginx configuration
===================

Apache or Nginx is not installed by default. You must choose one.

Current section explains how to install and configure Nginx.

Install and configure Nginx
---------------------------

On Debian:

.. code-block:: shell

  apt install nginx

  # enable the default website
  cd /etc/nginx/sites-enabled/ && ln -s ../sites-availables/service-desk

On RHEL systems:

.. code-block:: shell

  dnf install nginx

Virtual host configuration
--------------------------

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
