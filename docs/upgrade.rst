Upgrade
=======

From 0.6 to 0.7
---------------

date specifier
~~~~~~~~~~~~~~

A new javascript date specifier has been added to the default configuration file.

If you did some modifications to ``$date_specifiers``, you should adapt them as well to ``$js_date_specifiers``.

.. code-block:: php

    $js_date_specifiers = "YYYY-MM-DD HH:mm:ss (Z)";

See :ref:`date format<date_format>` for more information.


From 0.5 to 0.6
---------------

Bundled dependencies
~~~~~~~~~~~~~~~~~~~~

The dependencies are now explicitly listed in the service-desk package, including the bundled ones.

You can find bundled dependencies list:

* in package description in debian package
* in Provides field in rpm package


Configuration
~~~~~~~~~~~~~

The configuration files are now in ``/etc/service-desk`` directory.

During the upgrade process towards 0.6, the previous configuration files present in ``/usr/share/service-desk/conf`` (all .php files) are migrated to ``/etc/service-desk/``:

* ``config.inc.php`` is migrated as a ``config.inc.php.bak`` file,
* all other php file names are preserved. (including local conf, domain conf, and customized lang files)

Please take in consideration that ``config.inc.php`` is now replaced systematically by the version in the RPM package. A .rpmsave backup will be done with the current version. The deb package will continue asking which file to use, it is advised to replace the current one with the version in the package.

Avoid as much as possible editing the ``/etc/service-desk/config.inc.php`` file. Prefer modifying the ``/etc/service-desk/config.inc.local.php``.

Password policy
~~~~~~~~~~~~~~~

When you change the password for a user, you can now configure a local password policy for ensuring the password strength is sufficient.

Most of the criteria are checked dynamically, while the password is being typed, and they are also enforced at server side.

You can give a look to the :doc:`password policy documentation <ppolicy>` for more information.

.. tip::

   The local password policy is now defined in a library: `ltb-common <https://github.com/ltb-project/ltb-common>`_.


Cache cleaning
~~~~~~~~~~~~~~

Now the cache is being cleaned-up during service-desk upgrade / install.

This is intended to avoid smarty problems due to service-desk templates upgrade, and possibly smarty upgrade itself.


Dependencies update
~~~~~~~~~~~~~~~~~~~

Removed packaged dependencies:

* old php module for apache2/httpd is no more required. The migration is done towards php-fpm.
* apache2/httpd is not required any more. You can installed nginx or httpd by hand.

Packaged dependencies:

* smarty is now a required package. service-desk will work with either version 3 or 4. On debian, ``config.inc.php`` will be configured to use smarty4 if available
* php-fpm >= 7.3 is now a required dependency, replacing old php module for apache/httpd. On debian, if apache2 is already installed, php-fpm configuration for apache2 will be done automatically
* php-ldap has been kept as dependency

Bundled dependencies:

* js-bootstrap has been updated from version v3.4.1 to version v5.3.2
* js-jquery has been updated from version v1.10.2 to version v3.7.1
* js-datatables.net-datatables.net has been updated from version 1.10.16 to version 2.1.2
* js-datatables.net-datatables.net-bs5 has been updated from version 1.10.16 to version 2.0.8
* js-datatables.net-datatables.net-buttons has been updated from version 1.5.1 to version 3.1.0
* js-datatables.net-datatables.net-buttons-bs5 has been updated from version 1.5.1 to version 3.0.2
* fontawesome-fonts has been updated from version 4.7.0 to version 6.5.2
* php-ltb-project-ltb-common has been updated from version 0.1 to version 0.3.0
* php-phpmailer has been updated from version 6.8.0 to version v6.9.1
* php-bjeavons-zxcvbn-php version 1.3.1 has been added
* php-guzzlehttp-guzzle version 7.8.1 has been added
* php-guzzlehttp-promises version 2.0.2 has been added
* php-guzzlehttp-psr7 version 2.6.2 has been added
* php-mxrxdxn-pwned-passwords version 2.1.0 has been added
* php-phpmailer version 6.9.1 has been added
* php-psr-http-client version 1.0.3 has been added
* php-psr-http-factory version 1.0.2 has been added
* php-psr-http-message version 2.0 has been added
* php-ralouphie-getallheaders version 3.0.3 has been added
* php-symfony-deprecation-contracts version 2.5.1 has been added
* php-symfony-finder version 7.0.0 has been added
* php-symfony-polyfill version v1.31.0 has been added
* php-symfony-deprecation-contracts version v2.5.3 has been added
* php-symfony-var-exporter version v5.4.40 has been added
* php-psr-container version 1.1.2 has been added
* php-symfony-service-contracts version v2.5.3 has been added
* php-psr-cache version 1.0.1 has been added
* php-symfony-cache-contracts version v2.5.3 has been added
* php-psr-log version 1.1.4 has been added
* php-symfony-cache version v5.4.42 has been added
* php-predis-predis version v2.2.2 has been added

Removed bundled dependencies:

.. code-block::

    myclabs/deep-copy, doctrine/instantiator,
    nikic/php-parser, phar-io/version, phpunit/php-code-coverage, phpunit/phpunit,
    phpunit/php-timer, phpunit/php-invoker, phpunit/php-text-template,
    phpunit/php-file-iterator, sebastian/recursion-context,
    sebastian/version, sebastian/complexity, sebastian/environment,
    sebastian/object-enumerator, sebastian/global-state,
    sebastian/resource-operations, sebastian/comparator,
    sebastian/exporter, sebastian/type, sebastian/code-unit,
    sebastian/lines-of-code, sebastian/diff, sebastian/object-reflector,
    sebastian/code-unit-reverse-lookup, sebastian/cli-parser, theseer/tokenizer

Note that hidden files (.gitignore,...) from bundled dependencies are now removed from packages.


Last authentication time and idle accounts
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

You can now configure the name of the attribute storing the last authentication date.

The default value is set in `config.inc.php` so you don't have to change anything if you did not modify this file (the recommended way is to create a config.inc.local.php).

If you are using the new lastbind feature from OpenLDAP 2.5, then you must update your local configuration:

.. code-block:: php

    $ldap_lastauth_attribute = "pwdLastSuccess";


New ldap parameter
~~~~~~~~~~~~~~~~~~

You can now retrieve users with a paged search, for example if your directory does not allow you to get all entries at once.

You can enable this feature by setting a non-zero value to the page size parameter:

.. code-block:: php

   $ldap_page_size = 100;

Account validity
~~~~~~~~~~~~~~~~

Account validity feature is enabled by default. For OpenLDAP it relies on ``pwdStartTime`` and ``pwdEndTime`` attributes available since OpenLDAP 2.5.
For Active Directory, only the end time is available, in ``accountExpires`` attribute.

You can disable this new feature if you don't want to use it:

.. code-block:: php

   $show_validitystatus = false;
   $use_updatestarttime = false;
   $use_updateendtime = false;
   $use_searchinvalid = false;
