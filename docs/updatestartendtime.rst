Update start and end validity dates
===================================

Some LDAP directories provide attributes to define start and end account validify dates.

For OpenLDAP since 2.5 version, attributes are ``pwdStartTime`` and ``pwdEndTime``.

For Active Directory, only end time is available, in ``accountExpires`` attribute.

Show validity status
-------------------

Service Desk will display if account is valid or not. To allow this feature:

.. code-block:: php

    $show_validitystatus = true;

Update start date
-----------------

This feature allows to edit the account start validity date. This requires to have the `starttime` field defined in the attributes map.

To enable this feature:

.. code-block:: php

    $use_updatestarttime = true;

Update end date
-----------------

This feature allows to edit the account end validity date. This requires to have the `endtime` field defined in the attributes map.

To enable this feature:

.. code-block:: php

    $use_updateendtime = true;
