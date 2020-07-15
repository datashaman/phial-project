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
