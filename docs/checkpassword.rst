Check password
==============

This feature allows to enter a password and check authentication.

.. warning:: the authentification can fail even if the password is correct.
             This is the case if account is locked or password is expired.

To enable this feature:

.. code-block:: php

    $use_checkpassword = true;
