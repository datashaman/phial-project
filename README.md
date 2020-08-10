# phial-project

Example project for phial.

## requirements

* AWS cli
* SAM cli
* AWS credentials

## instructions

* Define version of PHP in _.settings_.
* Add any system and PHP packages in _Dockerfile_. PHP module packages are installed like `yum install -y php74-php-gd`.
* Edit _php.ini_ to configure PHP or enable modules.
* Define your _composer_ dependencies using the usual methods.
* Adapt bootstrap process in _bootstrap.php_ to suit your needs.
* Set your function _handler_ in _template.yaml_ to be anything invokable.

## handlers

Regular handlers have the following signature:

```
myHandler(array $event, ContextInterface $context);
```

Parameters are optional, you can type-hint anything from the container.

If you are developing an _HTTP API_, use the `RequestHandlerAdapter` which marshals the _request_ and _reponse_ to and from a _PSR-17_ `RequestHandlerInterface`.

## workflow

```
sam build
sam deploy --guided
sam local start-api
sam local invoke
```

## moving parts

You do not _have_ to use a _DI_ container, but it does make things easier. Any _PSR-11_ implementation will do. The _PHP-DI_ invoker which invokes the handler code will handle _DI_ or no _DI_, it doesn't care.

To replace the _DI_ container with another implementation, build one in [bootstrap.php](bootstrap.php) and pass it into the invoker construction.

### PSR standards supported

The following _PSR_ interfaces are supported; any compatible implementation can be used, just pass parameters into the handler constructor or use your DI container to autowire it up.

Also listed is the implementation used in this project. The implementations are configured in the config folder, and wired up in service providers.

* [PSR-3 Logger Interface](https://www.php-fig.org/psr/psr-3) - [monolog/monolog](https://packagist.org/packages/monolog/monolog)
* [PSR-7 HTTP Message Interface](https://www.php-fig.org/psr/psr-7) - [laminas/laminas-diactoros](https://packagist.org/packages/laminas/laminas-diactoros)
* [PSR-11 Container Interface](https://www.php-fig.org/psr/psr-11) - [php-di/php-di](https://packagist.org/packages/php-di/php-di)
* [PSR-17 HTTP Factories](https://www.php-fig.org/psr/psr-17) - [laminas/laminas-diactoros](https://packagist.org/packages/laminas/laminas-diactoros)
