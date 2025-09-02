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
dpkg-buildpackage -b -k"LTB-Project Debian"
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

Pre-requisites:

* docker / podman
* if docker: a version with buildkit (included by default in Docker Engine
  as of version 23.0, but can be enabled in previous versions with
  DOCKER_BUILDKIT=1 in build command line)

From "packaging" directory, do:

```
DOCKER_BUILDKIT=1 docker build -t service-desk -f ./docker/Dockerfile ../
```

You can also build with podman:

```
podman build --no-cache -t service-desk -f ./docker/Dockerfile ../
```

For Alpine linux image :

```
DOCKER_BUILDKIT=1 docker build -t service-desk-alpine -f ./docker/Dockerfile.alpine ../
```

Tag the defautl and alpine images with the major and minor version, for example:

```
docker tag service-desk:latest ltbproject/service-desk:1.6.1
docker tag service-desk:latest ltbproject/service-desk:1.6
docker tag service-desk:latest ltbproject/service-desk:latest
docker tag service-desk-alpine:latest ltbproject/service-desk:alpine-1.6.1
docker tag service-desk-alpine:latest ltbproject/service-desk:alpine-1.6
docker tag service-desk-alpine:latest ltbproject/service-desk:alpine-latest
```
