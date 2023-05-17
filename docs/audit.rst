.. _audit:

Audit
=====

You can enable audit to log all actions done through Service Desk.

The items provided in the audit log are:

* Date
* IP of connected admin
* DN of account being updated
* Who has done the action (see Admin name below)
* Action
* Result of the action

Example:

.. code-block:: json

   {
    "date":"Wed, 17 May 2023 11:12:59",
    "ip":"127.0.0.1",
    "user_dn":"uid=donald,ou=users,dc=example,dc=com",
    "done_by":"Mickey",
    "action":"lockaccount",
    "result":"accountlocked"
   }

Audit log file
--------------

Set the file where actions are logged:

.. code-block:: php

   $audit_log_file = "/var/log/service-desk/audit.log";

.. tip:: The file must be writable by the PHP or WebServer process

Admin name
----------

The admin name must be set into an HTTP header.

.. code-block:: php

   $header_name_audit_admin = "Auth-User";

Using Nginx, we could use instead:

.. code-block:: php

    $header_name_audit_admin = "AUTH_USER";

In nginx.conf:

.. code-block:: nginx
  
   server {
       [...]
       location ~ \.php {
           fastcgi_param HTTP_AUTH_USER $http_auth_user;
           [...]
       }
       [...]
   }

.. warning:: Using Nginx, headers with underscores in their names are discarded by default. In order for these headers to be considered valid, we need to add ``underscores_in_headers on`` to ``nginx.conf``.

.. tip:: If no header defined or if header is empty, actions will be logged as "anonymous"
