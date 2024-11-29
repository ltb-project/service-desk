Hook
====

Hook feature allows to run a script before or after an action:
* Password reset
* Password lock
* Password unlock
* Account enable
* Account disable

The script must return 0 if no error occured. Any text printed on STDOUT
will be displayed as an error message (see options).

Login
-----

Define which attribute will be used as login in prehook and posthook scripts:

.. code-block:: php

    $prehook_login = "uid";
    $posthook_login = "uid";

Password reset
--------------

The script is called with two parameters: login and new password.

Define prehook or posthook script (and enable the feature):

.. code-block:: php

    $prehook = "/usr/share/service-desk/prehook.sh";
    $posthook = "/usr/share/service-desk/posthook.sh";

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

Password lock
-------------

The script is called with one parameter: login.

Define prehook or posthook script (and enable the feature):

.. code-block:: php

    $prehook_lock = "/usr/share/service-desk/prehook_lock.sh";
    $posthook_lock = "/usr/share/service-desk/posthook_lock.sh";

To display hook error:

.. code-block:: php

   $display_prehook_lock_error = true;
   $display_posthook_lock_error = true;

To ignore prehook error:

.. code-block:: php

    $ignore_prehook_lock_error = true;

Password unlock
---------------

The script is called with one parameter: login.

Define prehook or posthook script (and enable the feature):

.. code-block:: php

    $prehook_unlock = "/usr/share/service-desk/prehook_unlock.sh";
    $posthook_unlock = "/usr/share/service-desk/posthook_unlock.sh";

To display hook error:

.. code-block:: php

   $display_prehook_unlock_error = true;
   $display_posthook_unlock_error = true;

To ignore prehook error:

.. code-block:: php

    $ignore_prehook_unlock_error = true;

Account enable
--------------

The script is called with one parameter: login.

Define prehook or posthook script (and enable the feature):

.. code-block:: php

    $prehook_enable = "/usr/share/service-desk/prehook_enable.sh";
    $posthook_enable = "/usr/share/service-desk/posthook_enable.sh";

To display hook error:

.. code-block:: php

   $display_prehook_enable_error = true;
   $display_posthook_enable_error = true;

To ignore prehook error:

.. code-block:: php

    $ignore_prehook_enable_error = true;

Account disable
---------------

The script is called with one parameter: login.

Define prehook or posthook script (and disable the feature):

.. code-block:: php

    $prehook_disable = "/usr/share/service-desk/prehook_disable.sh";
    $posthook_disable = "/usr/share/service-desk/posthook_disable.sh";

To display hook error:

.. code-block:: php

   $display_prehook_disable_error = true;
   $display_posthook_disable_error = true;

To ignore prehook error:

.. code-block:: php

    $ignore_prehook_disable_error = true;
