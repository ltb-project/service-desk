Posthook parameters
===================

Posthook feature allows to run a script after the password modification.

The script is called with two parameters: login and new password.

Parameters
----------

Define posthook script (and enable the feature):

.. code-block:: php

    $posthook = "/usr/share/service-desk/posthook.sh";

Define which attribute will be used as login:

.. code-block:: php

    $posthook_login = "uid";

Display posthook error:

.. code-block:: php

    $display_posthook_error = true;

Encode passwords sent to posthook script as base64:

.. code-block:: php

    $posthook_password_encodebase64 = true;


.. tip:: This will prevent alteration of the passwords if set to true. To read the actual password in the posthook script, use a base64_decode function/tool.
