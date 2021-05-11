Dashboards
==========

Locked accounts
---------------

This page will list all currently locked accounts.

To enable it:

.. code-block:: php

    $use_searchlocked = true;

It is possible to unlock an account directly from this page. This requires to enable the feature :doc:`unlockaccount`.

Soon expired passwords
----------------------

This page will list all accounts with a password that will expire in the next days.

To enable it:

.. code-block:: php

    $use_searchwillexpire = true;

You can also configure the number of days before expiration:

.. code-block:: php

    $willexpiredays = 14;

Expired passwords
-----------------

This page will list all accounts with an expired password.

To enable it:

.. code-block:: php

    $use_searchexpired = true;
