# phial-project

Example project for phial.

## requirements

* AWS cli
* SAM cli
* AWS credentials

## instructions

* Define your _composer_ requirements using the usual methods.
* Adapt _PHP_ invocation in `bootstrap` to suit your needs.
* Adapt bootstrap process in `bootstrap.php` to suit your needs.
* Set your function _handler_ in `template.yaml` to be anything invokable.
* The invoked code must return an array response.

## workflow

```
sam build
sam local invoke
sam deploy --guided
```

## moving parts

This example project uses the following PSR implementations, you are free to substitute your own either by calling the handler with the required parameters or using a DI container as I have done here.

You do not _have_ to use a DI container, but it does make things easier. Any PSR-11 implementation will do. The PHP-DI invoker which invokes the handler code will handle DI or no DI, it doesn't care.

To replace the DI container with another implementation, build one in _container.php_ and return it. You can also replace all this logic by rewriting _bootstrap.php_.

## PSR interfaces supported

The following PSR interfaces are supported; any compatible implementation can be used, just pass parameters into the handler constructor or use your DI container to autowire it up.

Also listed is the implementation used in this project. The implementations are wired up in _config.php_.

* PSR-3 Logger Interface - monolog/monolog
* PSR-7 HTTP Message Interface - guzzlehttp/guzzle
* PSR-11 Container Interface - php-di/php-di
* PSR-17 HTTP Factories - http-interop/http-factory-guzzle
* PSR-18 HTTP Client - guzzlehttp/guzzle
