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
* Additional comment (Optionally entered by Admin)

Example:

.. code-block:: json

   {
    "date":"Wed, 17 May 2023 11:12:59",
    "ip":"127.0.0.1",
    "user_dn":"uid=donald,ou=users,dc=example,dc=com",
    "done_by":"Mickey",
    "action":"lockaccount",
    "result":"accountlocked",
    "comment":"Security breach"
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

Using Apache, we could use instead:

.. code-block:: php

  $header_name_audit_admin = "Auth-User";

Using Nginx, we could use instead:

.. code-block:: php

  $header_name_audit_admin = "AUTH_USER";

In nginx.conf:

.. code-block:: nginx

  server {
          [...]
          location ~ \.php$ {
                  fastcgi_param HTTP_AUTH_USER $http_auth_user;
                  [...]
          }
          [...]
  }

.. warning:: Using Nginx, headers with underscores in their names are discarded by default. In order for these headers to be considered valid, we need to add ``underscores_in_headers on`` to ``nginx.conf``.

.. tip:: If no header defined or if header is empty, actions will be logged as "anonymous"

Display audit logs
==================

Enabling audit logs display
---------------------------

When the audit logs are enabled, they can be displayed in a table by setting the following variable:

.. code-block:: php

   $use_showauditlog = true;

Days of audit logs
------------------

The number of days that can be displayed in the table can be configured as follows:

.. code-block:: php

   $audit_log_days = 5;

.. note::

   The log file specified under $audit_log_file may only contain logs generated within the last $audit_log_days due to log rotation configuration.

Display table columns
---------------------

The table columns to be displayed can be configured with the following variable:

.. code-block:: php

   $audit_log_items = array('date','ip','dn','done_by','action','result','comment');

Audit table sorting
-------------------

The table can be sorted by default by the setting:

.. code-block:: php

   $audit_log_sortby = "date";

Audit table sorting order
-------------------------

Audit logs are usually display with the oldest first as they are being parsed from a file.
In order to have the newest audit log entries first the following configuration can reverse the order:

.. code-block:: php

   $audit_log_reverse = true;
