Lock and unlock account
=======================

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

Insert comment
--------------

A feature to enable comments when locking and unlocking user accounts can be enabled.

To enable this feature:

.. code-block:: php

    $use_lockcomment = true;
    $use_unlockcomment = true;

Comment required
----------------

This features ensure a comment is required before locking/unlocking a user.

.. code-block:: php

    $use_lockcomment_required = true;
    $use_unlockcomment_required = true;
