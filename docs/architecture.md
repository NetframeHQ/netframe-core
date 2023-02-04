Netframe's Architecture
=======================

This document introduces the Netframe architecture.

By reading it, you'll be able to understand the different components of the Netframe application.


Components
----------

Netframe currently includes the following components:

* the [Netframe application](#netrame-application)
* a [broadcast server](#broadcast-server)
* a [document collaboration server](#collaboration-server)
* an OnlyOffice Document Server
* a MySQL (or equivalent) database
* a Redis key-value store
* an ElasticSearch search engine

In the local development environment, we have an Nginx proxy.


### Netframe application

This component is the main application, mainly based on [Laravel 9](https://laravel.com/docs/9.x/) and some [VueJS 2.6](https://vuejs.org/v2/guide/).

We use Composer 2 for PHP dependencies management and npm 5.6.0 for front-end dependencies management (upgrade to newer version in progress).

> This is the component which will be decoupled in multiple services and a single page application with VueJS.

This application manages everything about authentication and all Netframe features (documents, social part, settings, location, …).


### Broadcast server

The broadcast server is a [Laravel Echo server](https://github.com/tlaverdure/laravel-echo-server) which uses SocketIO for events transmission between connected users.

It is mainly use for discussions notifications.


### Collaboration server

The collaboration server is a NodeJS application which uses [ProseMirror](https://prosemirror.net/).

This server manages the collaborative notes edition.


More reading
------------

If you want to work on the project locally, the [local development environment document](./local-developement-environment.md) is mandatory.
