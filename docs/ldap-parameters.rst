LDAP parameters
===============

Type of directory
-----------------

You can define the type of LDAP directory (``openldap`` or ``activedirectory``). The default value is ``openldap``.

.. code-block:: php

    $ldap_type = "openldap";

.. tip:: Other configuration parameters could be impacted by this choice, check their documentation.

Server address
--------------

Use an LDAP URI to configure the location of your LDAP server in ``$ldap_url``:

.. code-block:: php

    $ldap_url = "ldap://localhost:389";

You can set several URI, so that next server will be tried if the previous is down:

.. code-block:: php

    $ldap_url = "ldap://server1 ldap://server2";

To use SSL, set ldaps in the URI:

.. code-block:: php

    $ldap_url = "ldaps://localhost";

To use StartTLS, set ``true`` in ``$ldap_starttls``:

.. code-block:: php

    $ldap_starttls = true;

.. tip:: LDAP certificate management in PHP relies on LDAP system libraries. Under Linux, you can configure ``/etc/ldap.conf`` (or ``/etc/ldap/ldap.conf`` on Debian/Ubuntu, or ``C:\OpenLDAP\sysconf\ldap.conf`` for Windows). Provide the certificate from the certificate authority that issued your LDAP server's certificate.

Credentials
-----------

Configure DN and password in ``$ldap_bindn`` and ``$ldap_bindpw``:

.. code-block:: php

    $ldap_binddn = "cn=manager,dc=example,dc=com";
    $ldap_bindpw = "secret";

.. tip:: You can use the LDAP admin account or any service account. The account needs to read users, password policy entries and write password and some other related attributes in user entries. On OpenLDAP, using the LDAP admin account will bypass any password policy like minimal size or password history when reseting the password.

LDAP Base
---------

You can set global base in ``$ldap_base``:

.. code-block:: php

    $ldap_base = "dc=example,dc=com";

User search parameters
----------------------

You can set base of the search in ``$ldap_user_base``:

.. code-block:: php

    $ldap_user_base = "ou=users,".$ldap_base;

The filter can be set in ``$ldap_user_filter``:

.. code-block:: php

    $ldap_user_filter = "(objectClass=inetOrgPerson)";

You can set the scope for each search in ``$ldap_scope``:

.. code-block:: php

   $ldap_scope = "sub";

.. tip:: sub is the default value. Possible values are sub, one, or base

You can retrieve users with a paged search, for example if your directory does not allow you to get all entries at once.
You can enable this feature by setting a non-zero value to the page size parameter:

.. code-block:: php

   $ldap_page_size = 100;

.. tip:: when setting a ``$ldap_page_size`` value > 0, service-desk sends a ``LDAP_CONTROL_PAGEDRESULTS`` control along with the search, and loop for each page


Size limit
----------

It is advised to set a search limit on client side if no limit is set by the server:

.. code-block:: php

    $ldap_size_limit = 100;

Password policies
-----------------

Configure the filter to match password policy configuration objects:

.. code-block:: php

   $ldap_ppolicy_filter = "(objectClass=pwdPolicy)";

Define which attribute value will be displayed as password policy name:

.. code-block:: php

   $ldap_ppolicy_name_attribute = "cn";

Set ``$ldap_default_ppolicy`` value if a default policy is configured in your LDAP directory.

.. code-block:: php

    $ldap_default_ppolicy = "cn=default,ou=ppolicy,dc=example,dc=com";

.. tip:: Password policy is first searched in ``pwdPolicySubentry`` attribute of user entry, then fallback to default policy.

You can override some policies, like lockout duration or password maximal age:

.. code-block:: php

    $ldap_lockout_duration = 3600; # 1 hour
    $ldap_password_max_age = 7889400; # 3 months

Last authentication attribute
-----------------------------

The last authentication date can be stored in different attributes depending on your OpenLDAP version or configuration.

.. code-block:: php

    $ldap_lastauth_attribute = "pwdLastSuccess";

.. tip:: This attribute is automatically configured for Active Directory.

Samba 3
-------

To manage compatibility with Windows world, Samba stores a specific hash
of the password in a second attribute (``sambaNTpassword``). It also
store modification date in ``sambaPwdLastSet``. Use ``$samba_mode`` to
manage these attributes:

.. code-block:: php

   $samba_mode = true;

You can also update ``sambaPwdCanChange`` and ``sambaPwdMustChange``
attributes by settings minimal and maximal age, in days:

.. code-block:: php

   $samba_options['min_age'] = 5;
   $samba_options['max_age'] = 45;

To set an expiration date for a Samba account (attribute
``sambaKickofftime``), configure a maximal age, in days:

.. code-block:: php

   $samba_options['expire_days'] = 90;

.. tip:: Samba modifications will only be done on entries of class
  ``sambaSamAccount``

