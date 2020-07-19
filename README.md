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

## workflow

```
sam build
sam local start-api
sam local invoke
sam deploy --guided
```

## moving parts

You do not _have_ to use a _DI_ container, but it does make things easier. Any _PSR-11_ implementation will do. The _PHP-DI_ invoker which invokes the handler code will handle _DI_ or no _DI_, it doesn't care.

To replace the _DI_ container with another implementation, build one in _bootstrap/handler.php_ and pass it into the invoker construction.

### PSR standards supported

The following _PSR_ interfaces are supported; any compatible implementation can be used, just pass parameters into the handler constructor or use your DI container to autowire it up.

Also listed is the implementation used in this project. The implementations are wired up in _bootstrap/config.php_.

* [PSR-3 Logger Interface](https://www.php-fig.org/psr/psr-3) - [monolog/monolog](https://packagist.org/packages/monolog/monolog)
* [PSR-7 HTTP Message Interface](https://www.php-fig.org/psr/psr-7) - [guzzlehttp/guzzle](https://packagist.org/packages/guzzlehttp/guzzle)
* [PSR-11 Container Interface](https://www.php-fig.org/psr/psr-11) - [php-di/php-di](https://packagist.org/packages/php-di/php-di)
* [PSR-17 HTTP Factories](https://www.php-fig.org/psr/psr-17) - [http-interop/http-factory-guzzle](https://packagist.org/packages/http-interop/http-factory-guzzle)
* [PSR-18 HTTP Client](https://www.php-fig.org/psr/psr-18) - [guzzlehttp/guzzle](https://packagist.org/packages/guzzlehttp/guzzle)
