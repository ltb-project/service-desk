Search parameters
=================

Search attributes
-----------------

Configure attributes on which the search is done:

.. code-block:: php

    $search_attributes = array('uid', 'cn', 'mail');

By default, search is done with substring match. This can be changed to use exact match:
    
.. code-block:: php

    $search_use_substring_match = false;

Results display
---------------

Configure items shown when displaying results:

.. code-block:: php

    search_result_items = array('identifier', 'mail', 'mobile');

Datatables
----------

Define pagination values in dropdown:

.. code-block:: php

    $datatables_page_length_choices = array(10, 25, 50, 100, -1); // -1 means All

Set default pagination for results (can also be used to force the length without ``$datatables_page_length_choices``):

.. code-block:: php

    $datatables_page_length_default = 10;

Enable or disable autoPrint feature:

.. code-block:: php

    $datatables_auto_print = true;

