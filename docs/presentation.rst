Presentation
============

LDAP Tool Box Service Desk is a web application for administrators and support teams.
It allows to browse accounts in an LDAP directory, view and update their status.

.. warning:: There is no authentication requested by the application.
             You must set some before opening the application to your
             trusted users. Some examples are provided in the documentation.

Features
--------

* Quick search for an account
* View main attributes
* View account and password status
* Test current password
* Reset password and force password change at next connection
* Lock and unlock account
* Update account validity dates
* Create and view audit logs
* Dashboards:

  * Accounts locked
  * Accounts with a password that will soon expire
  * Accounts with an expired password
  * Accounts idle (never connected or not connected since a number of days)
  * Accounts invalid (for which start date is in the future, or end date is in the past)
