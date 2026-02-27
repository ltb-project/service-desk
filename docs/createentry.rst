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

Take care about the mandatory attributes defined in the ``$attributes_map`` parameter. (see :doc:`attributes documentation <attributes>`)


DN
--

Choose which items will be used to compute the DN (RDN):

.. code-block:: php

   $create_dn_items = array('identifier');

By default, the branch in which the entry is created is set in configuration:

.. code-block:: php

   $create_branch_type = "base";
   $create_base = "ou=service,ou=users,dc=example,dc=com";


But you can also let the user decide in which branch the entry will be created.

It can ba a static list:

.. code-block:: php

   $create_branch_type = "static_list";
   $create_staticlist = array('ou=orga,dc=example,dc=com' => 'Organization A', 'ou=orgb,dc=example,dc=com' => 'Organization B');

It can also be a dynamic list:

.. code-block:: php

   $create_branch_type = "list";
   $create_list = array('base'=>'ou=organizations,dc=my-domain,dc=com', 'filter'=>'(&(objectClass=organizationalUnit)(!(ou=organizations)))', 'key'=>'entrydn', 'value'=>'description');

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
