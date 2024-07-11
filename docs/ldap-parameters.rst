LDAP parameters
===============

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

.. tip:: You can use the LDAP admin account or any service account. The account needs to read users, password policy entries and write ``userPassword`` and ``pwdReset`` attributes in user entries. Note that using the LDAP admin account will bypass any password policy like minimal size or password history when reseting the password.

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

Size limit
----------

It is advised to set a search limit on client side if no limit is set by the server:

.. code-block:: php

    $ldap_size_limit = 100;

Default password policy
-----------------------

Set ``$ldap_default_ppolicy`` value if a default policy is configured in your LDAP directory.

.. code-block:: php

    $ldap_default_ppolicy = "cn=default,ou=ppolicy,dc=example,dc=com";

.. tip:: Password policy is first searched in ``pwdPolicySubentry`` attribute of user entry, then fallback to default policy.

Last authentication attribute
-----------------------------

The last authentication date can be stored in different attributes depending on your OpenLDAP version or configuration.

.. code-block:: php

    $ldap_lastauth_attribute = "pwdLastSuccess";
