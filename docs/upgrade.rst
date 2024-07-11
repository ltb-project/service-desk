Upgrade
=======

From 0.5 to 0.6
---------------

Last authentication time and idle accounts
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

You can now configure the name of the attribute storing the last authentication date.

The default value is set in `config.inc.php` so you don't have to change anything if you did not modify this file (the recommended way is to create a config.inc.local.php).

If you are using the new lastbind feature from OpenLDAP 2.5, then you must update your local configuration:

.. code-block:: php

    $ldap_lastauth_attribute = "pwdLastSuccess";
