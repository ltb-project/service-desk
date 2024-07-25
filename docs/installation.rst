Installation
============

From tarball
------------

Uncompress and unarchive the tarball:

.. prompt:: bash $

    tar -zxvf ltb-project-service-desk-*.tar.gz

Install files in ``/usr/share/``:

.. prompt:: bash #

    mv ltb-project-service-desk-* /usr/share/service-desk

You need to install these prerequisites:

* Apache or another web server
* php
* php-ldap
* Smarty (version 3)

Debian / Ubuntu
---------------

Import the PGP key:

.. prompt:: bash #

    apt install curl gpg
    curl https://ltb-project.org/documentation/_static/RPM-GPG-KEY-LTB-project | gpg --dearmor > /usr/share/keyrings/ltb-project-openldap-archive-keyring.gpg

Configure the repository:

.. prompt:: bash #

    vi /etc/apt/sources.list.d/ltb-project.list

.. code-block:: ini


    deb [arch=amd64 signed-by=/usr/share/keyrings/ltb-project-openldap-archive-keyring.gpg] https://ltb-project.org/debian/stable stable main

Then update:

.. prompt:: bash #

    apt update

You are now ready to install:

.. prompt:: bash #

    apt install service-desk smarty3

CentOS / RedHat
---------------

.. warning:: You may need to install first the package `php-Smarty`_ which is not in official repositories.

.. _php-Smarty: https://pkgs.org/download/php-Smarty

Configure the yum repository:

.. prompt:: bash #

    vi /etc/yum.repos.d/ltb-project.repo
.. code-block:: ini

    [ltb-project-noarch]
    name=LTB project packages (noarch)
    baseurl=https://ltb-project.org/rpm/$releasever/noarch
    enabled=1
    gpgcheck=1
    gpgkey=file:///etc/pki/rpm-gpg/RPM-GPG-KEY-LTB-project

Then update:

.. prompt:: bash #

    yum update

Import repository key:

.. prompt:: bash #

    rpm --import https://ltb-project.org/documentation/_static/RPM-GPG-KEY-LTB-project

You are now ready to install:

.. prompt:: bash #

    yum install service-desk

Docker
------

Prepare a local configuration file for Service Desk, for example ``/home/test/servicedesk.conf.php``.

Start container, mounting that configuration file:

.. prompt:: bash #

    docker run -p 80:80 \
        -v /home/test/servicedesk.conf.php:/var/www/conf/config.inc.local.php \
        -it docker.io/ltbproject/service-desk:latest


From git repository, for developpers only
-----------------------------------------

You can get the content of git repository

Update composer dependencies:

.. prompt:: bash

   composer update

Depending on your php version, this command will determine the versions of composer dependencies, and create a ``composer.lock`` file. Then it will download these dependencies and put them in vendor/ directory.

Then you can follow the instructions from `From tarball`_, especially the prerequisites.
