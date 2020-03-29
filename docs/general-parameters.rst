General parameters
==================

Configuration files
-------------------

The default configuration file is ``conf/config.inc.php``, it contains all default values.
To edit configuration, you should create ``conf/config.inc.local.php`` and override needed parameters:

.. code-block:: php

    <?php
    // Override config.inc.php parameters below

    ?>

.. warning:: 
  Do not copy ``config.inc.php`` into ``config.inc.local.php``, as the first one includes the second.
  You would then create an infinite loop and crash your application.

Language
--------

.. tip:: Lang is selected from browser configuration. If no matching language is found, the default language is used.

Set default language in ``$lang``:

.. code-block:: php

    $lang = "en";


.. tip:: You can override messages by creating lang files in ``conf/``, for example ``conf/en.inc.php``.

Dates
-----

You can adapt how dates are displayed with specifiers (see `strftime reference`_):

.. _strftime reference: https://www.php.net/strftime

.. code-block:: php

    $date_specifiers = "%Y-%m-%d %H:%M:%S (%Z)";

Graphics
--------

Logo
^^^^

You change the default logo with your own. Set the path to your logo in ``$logo``:

.. code-block:: php

    $logo = "images/ltb-logo.png";

Background
^^^^^^^^^^

You change the background image with your own. Set the path to image in ``$background_image``:

.. code-block:: php

     $background_image = "images/unsplash-space.jpeg";

Custom CSS
^^^^^^^^^^

To easily customize CSS, you can use a separate CSS file:

.. code-block:: php

    $custom_css = "css/custom.css";

Footer 
^^^^^^

You can hide the footer bar:

.. code-block:: php

    $display_footer = false;

Debug
-----

You can turn on debug mode with ``$debug``:

.. code-block:: php

    $debug = true;

Smarty
------

You need to define where Smarty is installed:

.. code-block:: php

    define("SMARTY", "/usr/share/php/smarty3/Smarty.class.php");

