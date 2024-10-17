# Some notes on packaging Service Desk

## 0 - Version update

Update version in following files:

* htdocs/index.php
* packaging/rpm/SPECS/service-desk.spec
* packaging/debian/changelog

## 1 - Update dependencies and run tests

From the service-desk root directory, run:

```
composer update
```

Run tests:

```
XDEBUG_MODE=coverage vendor/bin/phpunit --coverage-text --configuration tests/phpunit.xml
```

After the tests, remove the useless dependencies:

```
composer update --no-dev
```

## 2 - Archive tar.gz

From current directory, do:

```
./makedist.sh VERSION
```

with VERSION the current verion of the package

For example:

```
./makedist.sh 0.6
```


## 2 - Debian

Form current directory, do:

```
dpkg-buildpackage -b -kLTB
```

If you do not have LTB GPG secret key, do:

```
dpkg-buildpackage -b -us -uc
```

## 3 - RPM (RHEL, CentOS, Fedora, ...)

Prepare your build environment, for example in /home/clement/build.

You should have a ~/.rpmmacros like this:

```
%_topdir /home/clement/build
%dist .el5
%distribution .el5
%_signature gpg
%_gpg_name 6D45BFC5
%_gpgbin /usr/bin/gpg
%packager Clement OUDOT <clem.oudot@gmail.com>
%vendor LTB-project
```

Copy packaging files from current directory to build directory:

```
cp -Ra rpm/* /home/clement/build
```

Copy Self Service Archive to SOURCES/:

```
cp ltb-project-service-desk-VERSION.tar.gz /home/clement/build/SOURCES
```

Go in build directory and build package:

```
cd /home/clement/build
rpmbuild -ba SPECS/service-desk.spec
```

Sign RPM:

```
rpm --addsign RPMS/noarch/service-desk*
```

## 4 - Docker

From current directory, do:

```
docker build -t service-desk -f ./docker/Dockerfile ../
```
