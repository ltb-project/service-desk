# Service Desk

[![Build Status](https://github.com/ltb-project/service-desk/actions/workflows/ci.yml/badge.svg)](https://github.com/ltb-project/service-desk/actions/workflows/ci.yml)
[![Documentation Status](https://readthedocs.org/projects/service-desk/badge/?version=latest)](https://service-desk.readthedocs.io/en/latest/?badge=latest)

Application for support team who need to manage accounts in LDAP repository and check their status (locked, expired, invalid...).

Works with standard LDAPv3 directories and with Active Directory.

See [list of features](https://service-desk.readthedocs.io/en/stable/presentation.html#features).

![Screenshot](https://raw.githubusercontent.com/ltb-project/service-desk/master/ltb_sd_screenshot.png)

:exclamation: With great power comes great responsibility: this application allows to reset password of any user, you must protect it and allow access only to trusted users.

## Documentation

Documentation is available on https://service-desk.readthedocs.io/en/latest/

## Docker

We provide an [official Docker image](https://hub.docker.com/r/ltbproject/service-desk).

Create a minimal configuration file:
```
vi sd.conf.php
```
```php
<?php // My Service Desk configuration
$ldap_url = "ldap://ldap.example.com";
$ldap_binddn = "cn=admin,dc=example,dc=com";
$ldap_bindpw = 'secret';
$debug = true;
?>
```

And run:
```
docker run -p 80:80 \
    -v $PWD/sd.conf.php:/var/www/conf/config.inc.local.php \
    -it docker.io/ltbproject/service-desk:latest
```
