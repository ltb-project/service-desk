Group membership
================

A dedicated page to add or remove user from groups.

If feature is enabled, a card is shown inside the user display page, showing the number of groups the user is member of, and a button to edit this list of groups.

When clicking on the button, a new page is displayed with the list of groups found in the directory, by showing first the groups the user is member of. A switch allow to add or remove the user from a group. A group can be searched quickly in the table search field.

Disable or enable feature
-------------------------

To disable it:

.. code-block:: php

   $use_groupmembership = false;

LDAP configuration
------------------

You need to define how find groups in your LDAP directory:

* Search base
* Search filter
* Name of member attribute
* Size limit

.. code-block:: php

   $ldap_group_base = "ou=groups,".$ldap_base;
   $ldap_group_filter = "(objectClass=groupOfUniqueNames)";
   $ldap_group_member_attribute = "uniqueMember"; 
   $ldap_group_size_limit = 50;

Items
-----

By default, name and description of groups are shown in the table, but you can change this list:

.. code-block:: php

   $search_result_group_items = array('fullname','description','owner');
