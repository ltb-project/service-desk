.. _audit:

Audit
=====

You can enable audit to log all actions done through Service Desk.

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

.. tip:: If no header defined or if header is empty, actions will be logged as "anonymous"
