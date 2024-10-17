Password policy
===============

Size
----

Set minimal and maximal length in ``$pwd_min_length`` and
``$pwd_max_length``:

.. code-block:: php

   $pwd_min_length = 4;
   $pwd_max_length = 8;

.. tip:: Set ``0`` in ``$pwd_max_length`` to disable maximal length
  checking.

Characters
----------

You can set the minimal number of lower, upper, digit and special
characters:

.. code-block:: php

   $pwd_min_lower = 3;
   $pwd_min_upper = 1;
   $pwd_min_digit = 1;
   $pwd_min_special = 1;

Special characters are defined with a regular expression, by default:

.. code-block:: php

   $pwd_special_chars = "^a-zA-Z0-9";

This means special characters are all characters except alphabetical
letters and digits.

You can check that these special characters are not at beginning or end
of the password:

.. code-block:: php

   $pwd_no_special_at_ends = true;

You can also disallow characters from being in password, with
``$pwd_forbidden_chars``:

.. code-block:: php

   $pwd_forbidden_chars = "@%";

This means that ``@`` and ``%`` could not be present in a password.

You can define how many different class of characters (lower, upper,
digit, special) are needed in the password:

.. code-block:: php

   $pwd_complexity = 2;

Pwned Passwords
---------------

Allows to check if the password was already compromised, using
https://haveibeenpwned.com/ database:

.. code-block:: php

   $use_pwnedpasswords = true;

Forbidden words
---------------

Give a list of forbidden words that the password should not contain:

.. code-block:: php

   $pwd_forbidden_words = array("azerty", "qwerty", "password");

Forbidden LDAP fields
---------------------

Give a list of LDAP fields which values should not be present in the password:

.. code-block:: php

   $pwd_forbidden_ldap_fields = array('cn', 'givenName', 'sn', 'mail');

Show policy
-----------

Password policy can be displayed to user by configuring
``$pwd_show_policy``. Three values are accepted:

-  ``always``: policy is always displayed
-  ``never``: policy is never displayed
-  ``onerror``: policy is only displayed if password is rejected because
   of it, and the user provided his old password correctly.

.. code-block:: php

   $pwd_show_policy = "never";

You can also configure if the policy will be displayed above or below
the form:

.. code-block:: php

   $pwd_show_policy_pos = "above";

Entropy
-------

When the user is typing his new password, you can enable an entropy bar,
showing the strength of the password.

.. code-block:: php

    $pwd_display_entropy = true;

You can also require the entropy bar to hit a minimum level for the
password to be accepted:

.. code-block:: php

    # enforce password entropy check
    $pwd_check_entropy = true;

    # minimum entropy level required (when $pwd_check_entropy enabled)
    $pwd_min_entropy = 3;

``$pwd_min_entropy`` must be an integer between 0 (very risky) and 4 (very strong).

.. tip:: The entropy check is computed by the
         `zxcvbn library <https://github.com/dropbox/zxcvbn>`_


