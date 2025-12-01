Hook
====

Hook feature allows to run an external script or a php function before or after an event.

Here is an example of configuration to set in you ``config.inc.local.php``:

.. code-block:: php

    <?php

    $hook_login_attribute = "uid";

    $hook_config = array(
        "passwordReset" => array(
            "before" => array(
                "externalScript" => "/usr/share/service-desk/hook.sh",
                "function" => "hookFunction",
                "displayError" => false,
                "encodebase64" => false,
                "ignoreError" => false
            ),
            "after" => array(
                "externalScript" => "/usr/share/service-desk/hook.sh",
                "function" => "hookFunction",
                "displayError" => false,
                "encodebase64" => false
            )
        ),
        "passwordCheck" => array(),
        "passwordLock" => array(),
        "passwordUnlock" => array(),
        "accountEnable" => array(),
        "accountDisable" => array(),
        "updateValidityDates" => array(),
        "createAccount" => array(),
        "updateAccount" => array(),
        "deleteAccount" => array(),
        "renameAccount" => array()
    );

    ?>

Entrypoints
-----------

You can call hooks during these events:

* ``passwordReset`` called when admin is changing the password at user entry display screen
* ``passwordCheck`` called when admin is checking if a user password is correct
* ``passwordLock`` called when admin is locking an account
* ``passwordUnlock`` called when admin is unlocking an account
* ``accountEnable`` called when enabling an account
* ``accountDisable`` called when disabling an account
* ``updateValidityDates`` called when changing the start time or end time of an account
* ``createAccount`` called when creating a user account
* ``updateAccount`` called when modifying a user account
* ``deleteAccount`` called when removing a user account
* ``renameAccount`` called when renaming a user account

Steps
-----

The hook can be called at two special steps:

* ``before`` before the entrypoint
* ``after`` after the entrypoint

If called before the entrypoint, the return code can be checked to prevent the corresponding event if an error occurred.

If called after the entrypoint, you are ensured the corresponding event has been successfully completed.


API
---

The input and output parameters are described there:

passwordReset
^^^^^^^^^^^^^

* External script / function input: login, new password
* External script output: first line: error message
* External script return code: 0 is a success, any other value means an error
* Function return values: return code, error message

passwordCheck
^^^^^^^^^^^^^

* External script / function input: login, new password
* External script output: first line: error message
* External script return code: 0 is a success, any other value means an error
* Function return values: return code, error message

passwordLock
^^^^^^^^^^^^

* External script / function input: login
* External script output: first line: error message
* External script return code: 0 is a success, any other value means an error
* Function return values: return code, error message

passwordUnlock
^^^^^^^^^^^^^^

* External script / function input: login
* External script output: first line: error message
* External script return code: 0 is a success, any other value means an error
* Function return values: return code, error message

accountEnable
^^^^^^^^^^^^^

* External script / function input: login
* External script output: first line: error message
* External script return code: 0 is a success, any other value means an error
* Function return values: return code, error message

accountDisable
^^^^^^^^^^^^^^

* External script / function input: login
* External script output: first line: error message
* External script return code: 0 is a success, any other value means an error
* Function return values: return code, error message

updateValidityDates
^^^^^^^^^^^^^^^^^^^

* External script / function input: login, start date, end date
* External script output: first line: error message
* External script return code: 0 is a success, any other value means an error
* Function return values: return code, error message

createAccount
^^^^^^^^^^^^^

* External script / function input: login, ldap entry
* External script output: for step=before external script, the expected output is: first line: error message, all other lines: ldap entry in json format.
* External script return code: 0 is a success, any other value means an error
* Function return values: for step=before function, the expected returned values are: return code, error message, ldap entry, for step=after, the expected returned values are: return code, error message

updateAccount
^^^^^^^^^^^^^

* External script / function input: login, ldap entry
* External script output: for step=before external script, the expected output is: first line: error message, all other lines: ldap entry in json format.
* External script return code: 0 is a success, any other value means an error
* Function return values: for step=before function, the expected returned values are: return code, error message, ldap entry, for step=after, the expected returned values are: return code, error message

deleteAccount
^^^^^^^^^^^^^

* External script / function input: login
* External script output: first line: error message
* External script return code: 0 is a success, any other value means an error
* Function return values: return code, error message

deleteAccount
^^^^^^^^^^^^^

* External script / function input: login, dn, new_rdn, parent
* External script output: first line: error message
* External script return code: 0 is a success, any other value means an error
* Function return values: return code, error message


Configuration parameters
------------------------


* ``$hook_login_attribute = "uid";``: define which attribute will be used as login in hooks
* ``externalScript``: path of the script that is called. "before" script or function should return 0, else action will be aborted, unless error is ignored
* ``function``: the hook can also be a function. Write your own file.php in hooks/ directory
* ``displayError``: display an error if the script or function returns an error
* ``ignoreError``: only for before hooks, ignore error returned by the script or function
* ``encodebase64``: passwordReset and passwordCheck entrypoints only, encode the password in base64 before sending it

