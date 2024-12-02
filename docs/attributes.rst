Attributes
==========

Attributes are defined in ``$attributes_map``, where each item is an array with these keys:

* ``attribute``: name of LDAP attribute, in lower case
* ``faclass``: name of Font Awesome icon class
* ``type``: type of attribute (text, mailto, tel or date)
* ``sort``: optional, when attribute is multi-valued, sort them. Two possible values: ``ascending`` (default) or ``descending``
* ``dtorder``: optional, set value to ``disable`` to remove sorting on the column

This is used to configure how attribute is displayed.

Available types:

* ``text``: simple text
* ``mailto``: mailto link
* ``tel``: tel link
* ``boolean``: true or false
* ``date``: LDAP date converted to full date
* ``ad_date``: Active Directory date converted to full date
* ``list``: value from a list
* ``bytes``: bytes converted in KB/MB/GB/TB
* ``timestamp``: timestamp converted to full date
* ``dn_link``: convert DN into link to account display page
* ``address``: convert address string to multi-lines

.. tip:: See LDAP Tool Box White Pages documentation to get more information.

OpenLDAP and Active Directory
-----------------------------

To allow compatibilty with OpenLDAP and Active Directory, some specific attributes are configured in dedicated parameters: ``$openldap_attributes_map`` and ``$activedirectory_attributes_map``.

For example, the ``endtime`` is in ``pwdEndTime`` attribute in OpenLDAP and in ``accountExpires`` attribute in Active Directory.

If you need to change the default settings, override these parameters. They are merged into the global ``$attributes_map`` by the software itself.
