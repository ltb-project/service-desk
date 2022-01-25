Attributes
==========

Attributes are defined in ``$attributes_map``, where each item is an array with these keys:

* ``attribute``: name of LDAP attribute, in lower case
* ``faclass``: name of Font Awesome icon class
* ``type``: type of attribute (text, mailto, tel or date)

This is used to configure how attribute is displayed.

Available types:

* ``text``: simple text
* ``mailto``: mailto link
* ``tel``: tel link
* ``boolean``: true or false
* ``date``: LDAP date converted to full date
* ``list``: value from a list
* ``bytes``: bytes converted in KB/MB/GB/TB
* ``timestamp``: timestamp converted to full date

.. tip:: See LDAP Tool Box White Pages documentation to get more information.

