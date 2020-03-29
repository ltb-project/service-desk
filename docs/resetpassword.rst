Reset password
==============

This feature allows to reset a password and set the reset at next connection flag.

To enable this feature:

.. code-block:: php

    $use_resetpassword = true;

When changing the password, you can force the user to reset it at next connection. To configure the default value presented in the form:

.. code-block:: php

    $resetpassword_reset_default = true;
