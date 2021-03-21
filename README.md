# Symfony URl Shortening Microservice

![Continuous Integration Actions Status](https://github.com/db306/url-shortener/actions/workflows/continuous-integration-workflow.yml/badge.svg)

This service can create shorter urls and redirect users to the final destination.
This service only requires running a Postgresql database as the underlying storage
mechanism.

## Running this service locally

After you've clonned this repository, you can simply either run `make start` or `docker-compose up -d`

Congrats 🎉 ! Your sevice is now available on http://localhost

## Open API / Swagger Documentation

A comprehensive swagger documentation is available at the root of the service:

http://localhost

## Running tests locally

In order to run test locally, you can install them with `make reload`

This will create a separate database called `url_shortener_test` and will automatically load
all the required fixtures in order to be able to run integration / functional tests

You can run tests locally by running `make test`