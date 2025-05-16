Update entry
============

Disable or enable feature
-------------------------

If feature is enabled, a modify button is shown on entry display page.
To disable it:

.. code-block:: php

   $use_update = false;

Items
-----

You can choose which items will be available for the update:

.. code-block:: php

   $update_items = array('firstname', 'lastname', 'title', 'businesscategory', 'employeenumber', 'employeetype', 'mail', 'mailquota', 'phone', 'mobile', 'fax', 'postaladdress', 'street', 'postalcode', 'l', 'state', 'organizationalunit', 'organization', 'manager', 'secretary');

.. tip:: Other items will be read-only if they are listed in display items

Macros
------

You may need to update additional attributes based on submitted items.
This is possible by defining a macro for the corresponding item:

.. code-block:: php

   $update_items_macros = array('fullname' => '%firstname% %lastname%');
