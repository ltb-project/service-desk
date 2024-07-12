Lock account
============

Show lock status
----------------

If password policy allows to lock an account, Service Desk will display if account is locked or not.

This panel can be hidden by configuration:

.. code-block:: php

    $show_lockstatus = false;

Lock account
------------

This feature allows to lock the account permanently. The button is only displayed if the account is not locked.

To enable this feature:

.. code-block:: php

    $use_lockaccount = true;

Unlock account
--------------

This feature allows to unlock the account. It is only displayed if the account is already locked.

To enable this feature:

.. code-block:: php

    $use_unlockaccount = true;
