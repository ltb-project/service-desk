Dashboards
==========

List of all accounts
--------------------

This page list all accounts.

To enable it:

.. code-block:: php

    $use_searchall = true;

Locked accounts
---------------

This page will list all currently locked accounts.

To enable it:

.. code-block:: php

    $use_searchlocked = true;

It is possible to unlock an account directly from this page. This requires to enable the feature :doc:`lockaccount`.

Disabled accounts
-----------------

This page will list all currently disabled accounts.

To enable it:

.. code-block:: php

    $use_searchdisabled = true;

It is possible to enable an account directly from this page. This requires to enable the feature :doc:`enableaccount`.

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

Idle accounts
-------------

This page will list all accounts never connected, or not connected since a number of days.

.. tip:: This requires the ``authTimestamp`` attribute which is provided by the ``lastbind`` overlay.

To enable it:

.. code-block:: php

    $use_searchidle = true;

You can also configure the number of idle days:

.. code-block:: php

    $idledays = 60;

Invalid accounts
----------------

This page will list all invalid accounts.

To enable it:

.. code-block:: php

    $use_searchinvalid = true;
