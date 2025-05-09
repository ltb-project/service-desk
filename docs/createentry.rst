Create entry
============

This allows to create a new account in the directory.

Disable or enable feature
-------------------------

If feature is enabled, a new button is shown in the menu.
To disable it:

.. code-block:: php

   $use_create = false;

Items
-----

You can choose which items will be asked for the entry creation:

.. code-block:: php

   $create_items = array('firstname', 'lastname', 'title', 'businesscategory', 'mail');

DN
--

Choose which items will be used to compute the DN (RDN):

.. code-block:: php

   $create_dn_items = array('identifier');

Set the branch where entries are created (by default this is the user search base):

.. code-block:: php

   $create_base = "ou=service,ou=users,dc=example,dc=com";


Object classes
--------------

Set which object classes are used to create the entry:

.. code-block:: php

   $create_objectclass = array('top', 'person', 'organizationalPerson', 'inetOrgPerson');

Macros
------

You may need to create additional attributes based on submitted items.
This is possible by defining a macro for the corresponding item:

.. code-block:: php

   $create_items_macros = array('fullname' => '%firstname% %lastname%');
