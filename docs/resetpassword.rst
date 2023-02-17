Reset password
==============

This feature allows to reset a password and set the reset at next connection flag.

To enable this feature:

.. code-block:: php

    $use_resetpassword = true;

When changing the password, you can force the user to reset it at next connection. To configure the default value presented in the form:

.. code-block:: php

    $resetpassword_reset_default = true;

If you do not want to let the choice to reset at next connection, you can hide this button:

.. code-block:: php

    $use_resetpassword_resetchoice = false;

In this case, the value set in ``$resetpassword_reset_default`` will be applied.

Notify user by mail
-------------------

You can configure notify_on_change to true to notify password change to owner by mail.

.. code-block:: php

   $notify_on_change = true;

To do so you need to configure mail (see :ref:`config_mail`).
