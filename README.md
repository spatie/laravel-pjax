# A pjax middleware for Laravel 5

[![Latest Version on Packagist](https://img.shields.io/packagist/v/spatie/laravel-pjax.svg?style=flat-square)](https://packagist.org/packages/spatie/laravel-pjax)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/spatie/laravel-pjax/master.svg?style=flat-square)](https://travis-ci.org/spatie/laravel-pjax)
[![SensioLabsInsight](https://img.shields.io/sensiolabs/i/89249e40-536c-4b1b-b1fb-f8b807b2b51d.svg?style=flat-square)](https://insight.sensiolabs.com/projects/89249e40-536c-4b1b-b1fb-f8b807b2b51d)
[![Quality Score](https://img.shields.io/scrutinizer/g/spatie/laravel-pjax.svg?style=flat-square)](https://scrutinizer-ci.com/g/spatie/laravel-pjax)
[![Total Downloads](https://img.shields.io/packagist/dt/spatie/laravel-pjax.svg?style=flat-square)](https://packagist.org/packages/spatie/laravel-pjax)

[Pjax](https://github.com/defunkt/jquery-pjax) is jquery plugin that leverages ajax to 
speed up the loading time of your pages. It works by only fetching specific html fragments
from the server, and client-side updating only certain parts of the page.

The package provides a middleware that can return the reponse that the jquery plugin expects.

Spatie is a webdesign agency based in Antwerp, Belgium. You'll find an overview of all our open source 
projects [on our website](https://spatie.be/opensource).

## Installation

You can install the package via composer:
``` bash
$ composer require spatie/laravel-pjax
```

Next you must add the `\Spatie\Pjax\Middleware\FilterIfPjax`-middleware to the kernel.
```php
// app/Http/Kernel.php

...
protected $middleware = [
    ...
    \Spatie\Pjax\Middleware\FilterIfPjax::class,
];
```

## Usage

The provided middleware provides [the behaviour that the pjax plugin expects of the server](https://github.com/defunkt/jquery-pjax#server-side):

> An X-PJAX request header is set to differentiate a pjax request from normal XHR requests. 
> In this case, if the request is pjax, we skip the layout html and just render the inner
> contents of the container.

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Testing

``` bash
$ composer test
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email freek@spatie.be instead of using the issue tracker.

## Credits

- [Freek Van der Herten](https://github.com/freekmurze)
- [All Contributors](../../contributors)

The middleware in this package was originally written by [Jeffrey Way](https://twitter.com/jeffrey_way) for the [Laracasts](https://laracasts.com)-lesson
on [pjax](https://laracasts.com/lessons/faster-page-loads-with-pjax). His original code
can be found [in this repo on GitHub](https://github.com/laracasts/Pjax-and-Laravel).

## About Spatie
Spatie is a webdesign agency based in Antwerp, Belgium. You'll find an overview of all our open source projects [on our website](https://spatie.be/opensource).

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
