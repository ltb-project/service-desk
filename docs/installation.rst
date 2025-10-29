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
* php-ldap
* php-fpm >= 7.3
* smarty (version 3 or 4)

Debian / Ubuntu
---------------

.. Important::
    The GPG key for debian has been updated on August 2025. Take care to use the new one by following the instructions below.


.. warning:: Due to a `bug`_ in old Debian and Ubuntu `smarty3`_ package, you may face the error ``syntax error, unexpected token "class"``.
   In this case, install a newer version of the package:

   ``# wget http://ftp.us.debian.org/debian/pool/main/s/smarty3/smarty3_3.1.47-2_all.deb``

   ``# dpkg -i smarty3_3.1.47-2_all.deb``

.. _smarty3: https://packages.debian.org/sid/smarty3
.. _bug: https://github.com/ltb-project/self-service-password/issues/681

Import the PGP key:

.. prompt:: bash #

    apt install curl gpg
    curl https://ltb-project.org/documentation/_static/ltb-project-debian-keyring.gpg | gpg --dearmor > /usr/share/keyrings/ltb-project-debian-keyring.gpg

Configure the apt repository:

.. prompt:: bash #

    vi /etc/apt/sources.list.d/ltb-project.sources

.. code-block:: ini

    Types: deb
    URIs: https://ltb-project.org/debian/stable
    Suites: stable
    Components: main
    Signed-By: /usr/share/keyrings/ltb-project-debian-keyring.gpg
    Architectures: amd64

.. note::

    You can also use the old-style source.list format. Edit ``ltb-project.list`` and add::

        deb [arch=amd64 signed-by=/usr/share/keyrings/ltb-project-debian-keyring.gpg] https://ltb-project.org/debian/stable stable main

Then update:

.. prompt:: bash #

    apt update

You are now ready to install:

.. prompt:: bash #

    apt install service-desk

You should now proceed to :ref:`Apache installation and configuration <apache_configuration>`
or to :ref:`Nginx installation and configuration <nginx_configuration>`

CentOS / RedHat
---------------

Configure the yum repository: (take care to configure the name of the GPG key, see below)

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

    dnf update

Import repository key:

On RHEL 7 or 8:

.. prompt:: bash #

    rpm --import https://ltb-project.org/documentation/_static/RPM-GPG-KEY-LTB-project

On RHEL 9:

.. prompt:: bash #

    rpm --import https://ltb-project.org/documentation/_static/RPM-GPG-KEY-LTB-PROJECT-SECURITY


You are now ready to install:

.. prompt:: bash #

    dnf install service-desk

You should now proceed to :ref:`Apache installation and configuration <apache_configuration>`
or to :ref:`Nginx installation and configuration <nginx_configuration>`

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
