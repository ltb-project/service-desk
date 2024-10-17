Check password
==============

This feature allows to enter a password and check authentication.

.. warning:: the authentification can fail even if the password is correct.
             This is the case if account is locked or password is expired.

To enable this feature:

.. code-block:: php

    $use_checkpassword = true;

Check password history
----------------------

When verifying the password, Service Desk can parse the password history to check if the current is part of it. This can be useful to tell a user that the password was working before but has been changed since.

To enable this feature:

.. code-block:: php

    $use_checkpasswordhistory = true;
    
.. tip:: This feature only works with OpenLDAP.