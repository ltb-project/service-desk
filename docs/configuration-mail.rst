.. _config_mail:

Mail
====

LDAP Attributes
---------------

Set the LDAP attributes where user email may be stored:

.. code-block:: php

   $mail_attributes = array( "mail", "gosaMailAlternateAddress", "proxyAddresses" );

.. tip:: Only the first value of this attribute will be used to get the
  mail address.

You can also define which LDAP attribute contains the user name (will be used in mail body content):

.. code-block:: php

   $mail_username_attribute = "cn";

Sender name
-----------

You can change the default ``From`` header and add a signature:

.. code-block:: php

   $mail_from = "admin@example.com";
   $mail_from_name = "Self Service Password administrator";
   $mail_signature = "";

Change password notification
----------------------------

Use this option to send a confirmation mail to the user, just after a
successful password change:

.. code-block:: php

   $notify_on_change = true;

This option is false by default.

A mail can also be sent to administrators, see :doc:`reset password configuuration <resetpassword>`.

PHPMailer
---------

You have to set all parameters for PHPMailer if you use mail notification.

.. code-block:: php

   $mail_sendmailpath = '/usr/sbin/sendmail';
   $mail_protocol = 'smtp';
   $mail_smtp_debug = 0;
   $mail_debug_format = 'html';
   $mail_smtp_host = 'localhost';
   $mail_smtp_auth = false;
   $mail_smtp_user = '';
   $mail_smtp_pass = '';
   $mail_smtp_port = 25;
   $mail_smtp_timeout = 30;
   $mail_smtp_keepalive = false;
   $mail_smtp_secure = 'tls';
   $mail_smtp_autotls = true;
   $mail_smtp_options = array();
   $mail_contenttype = 'text/plain';
   $mail_wordwrap = 0;
   $mail_charset = 'utf-8';
   $mail_priority = 3;

.. tip:: See https://github.com/PHPMailer/PHPMailer for more
  information
