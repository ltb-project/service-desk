Hook
====

Hook feature allows to run a script before or after the password modification.

The script is called with two parameters: login and new password.

Parameters
----------

Define prehook or posthook script (and enable the feature):

.. code-block:: php

    $prehook = "/usr/share/service-desk/prehook.sh";
    $posthook = "/usr/share/service-desk/posthook.sh";

Define which attribute will be used as login:

.. code-block:: php

    $prehook_login = "uid";
    $posthook_login = "uid";

You can choose to display an error if the script return code is greater
than 0:

.. code-block:: php

   $display_prehook_error = true;
   $display_posthook_error = true;

The displayed message will be the first line of the script output.

Another option can be enabled to encode the password in base64 before
sending it to the script, which can avoid an execution issue if the
password contains special characters:

.. code-block:: php

   $prehook_password_encodebase64 = false;
   $posthook_password_encodebase64 = false;

By default with prehook script, the password will not be changed in LDAP directory if the script fails.
You can change this behavior to ignore script error. This could be useful to run prehook script and display a warning
if it fails, but still try to update password in the directory.

.. code-block:: php

    $ignore_prehook_error = true;
