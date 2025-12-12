Attributes
==========

Attributes map
--------------

Attributes are defined in ``$attributes_map``, where each item is an array with these keys:

* ``attribute``: name of LDAP attribute, in lower case
* ``mandatory``: indicate if the attribute is mandatory. mandatory is an array with these possible values: ``all``, ``create``, ``update``
* ``faclass``: name of Font Awesome icon class
* ``type``: type of attribute (text, mailto, tel or date)
* ``sort``: optional, when attribute is multi-valued, sort them. Two possible values: ``ascending`` (default) or ``descending``
* ``dtorder``: optional, set value to ``disable`` to remove sorting on the column
* ``display``: optional. If the type is a list or static_list, configure if the ``key`` or ``value`` should be displayed

This is used to configure how attribute is displayed and edited.

Available types:

* ``text``: simple text
* ``mailto``: mailto link
* ``tel``: tel link
* ``boolean``: true or false
* ``date``: LDAP date converted to full date
* ``ad_date``: Active Directory date converted to full date
* ``list``: value from a list computed with a LDAP request (see below)
* ``static_list``: value from a static key / value list (see below)
* ``bytes``: bytes converted in KB/MB/GB/TB
* ``timestamp``: timestamp converted to full date
* ``dn_link``: convert DN into link to account display page (see below)
* ``address``: convert address string to multi-lines

.. tip:: See LDAP Tool Box White Pages documentation to get more information.



list
----

When you set an attribute as a list, you must also define the list parameters.

You have to declare an attributes_list structure, as shown below:


.. code-block:: php

    <?
    $attributes_map = array(
         ...
         'organizationalunit' => array ( 'attribute' => 'ou', 'faclass' => 'building-o', 'type' => 'list', 'display' => 'value' ),
         ...
    );

    $attributes_list['organizationalunit'] = array(
        'base'=>'ou=organizations,dc=my-domain,dc=com',
        'filter'=>'(objectClass=organizationalUnit)',
        'key'=>'ou',
        'value'=>'description'
    );
    ?>

In this example, if you create a new user entry, the values for organizationalunit attribute would be taken from a list computed by a LDAP search with the given base DN and filter.

The stored value would be the key (``ou``) grabbed from the LDAP search.

Because of the ``'display' => 'value'`` parameter, the list displayed in the interface would be the value (description), i.e. the values of description attribute grabbed from the LDAP search.


static_list
-----------

When you set an attribute as a static_list, you must also define the list parameters.

You have to declare an attributes_static_list structure, as shown below:


.. code-block:: php

    <?
    $attributes_map = array(
         ...
         'title' => array( 'attribute' => 'title', 'faclass' => 'user-o', 'type' => 'static_list', 'display' => 'value' ),
         ...
    );

    $attributes_static_list['title'] = array(
        'Mr' => 'Mister',
        'Mrs' => 'Madam'
    );
    ?>

In this example, if you create a new user entry, the values for title attribute would be taken from the static list defined above.

The stored value would be the key (``Mr``, ``Mrs``).

Because of the ``'display' => 'value'`` parameter, the list displayed in the interface would be the value (``Mister``, ``Madam``).


DN Link
-------

It is possible to configure which attribute is displayed as value. An array is defined, and the first attribute found is used:

.. code-block:: php

    $dn_link_label_attributes = array("cn");

The component ``dn_link`` can be used when updating an entry. In this case it is an autocomplete field that will search for entries in the directory.

Some configuration parameters can be used:

* What to display as search result label: it can be useful to use more thanone attribute to display the entry found by the search. This is possible by configuring a macro. For example to display the full name with the email in parenthesis:

.. code-block:: php

    $dn_link_search_display_macro = "%fullname% (%mail%)";

* Minimal characters needed to launch the search (default is 3):

.. code-block:: php

    $dn_link_search_min_chars = 2;

* Maximal number of entries to return (default is 10):

.. code-block:: php

    $dn_link_search_size_limit = 5;

OpenLDAP and Active Directory
-----------------------------

To allow compatibilty with OpenLDAP and Active Directory, some specific attributes are configured in dedicated parameters: ``$openldap_attributes_map`` and ``$activedirectory_attributes_map``.

For example, the ``endtime`` is in ``pwdEndTime`` attribute in OpenLDAP and in ``accountExpires`` attribute in Active Directory.

If you need to change the default settings, override these parameters. They are merged into the global ``$attributes_map`` by the software itself.
