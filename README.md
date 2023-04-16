# A pjax middleware for Laravel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/spatie/laravel-pjax.svg?style=flat-square)](https://packagist.org/packages/spatie/laravel-pjax)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
![GitHub Workflow Status](https://img.shields.io/github/actions/workflow/status/spatie/laravel-pjax/run-tests.yml?label=tests&style=flat-square)
[![Total Downloads](https://img.shields.io/packagist/dt/spatie/laravel-pjax.svg?style=flat-square)](https://packagist.org/packages/spatie/laravel-pjax)

[Pjax](https://github.com/defunkt/jquery-pjax) is a jQuery plugin that leverages ajax to
speed up the loading time of your pages. It works by only fetching specific html fragments
from the server, and client-side updating only happens on certain parts of the page.

The package provides a middleware that can return the response that the jQuery plugin expects.

There's a [Vue-PJAX Adapter](https://github.com/riverskies/vue-pjax-adapter) equivalent by @barnabaskecskes which doesn't require jQuery.

Spatie is a webdesign agency based in Antwerp, Belgium. You'll find an overview of all our open source
projects [on our website](https://spatie.be/opensource).

## Support us

[<img src="https://github-ads.s3.eu-central-1.amazonaws.com/laravel-pjax.jpg?t=1" width="419px" />](https://spatie.be/github-ad-click/laravel-pjax)

We invest a lot of resources into creating [best in class open source packages](https://spatie.be/open-source). You can support us by [buying one of our paid products](https://spatie.be/open-source/support-us).

We highly appreciate you sending us a postcard from your hometown, mentioning which of our package(s) you are using. You'll find our address on [our contact page](https://spatie.be/about-us). We publish all received postcards on [our virtual postcard wall](https://spatie.be/open-source/postcards).

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

### Laravel cache busting tip
When using Laravel Mix to manage your frontend cache busting, you can use it to your advantage to bust pjax's cache. Simply include the `mix` method as the content of the `x-pjax-version` meta tag:

```html
<meta http-equiv="x-pjax-version" content="{{ mix('/css/app.css') }}">
```

Multiple files:

```html
<meta http-equiv="x-pjax-version" content="{{ mix('/css/app.css') . mix('/css/app2.css') }}">
```

This way, anytime your frontend's cache gets busted, pjax's cache gets automatically busted as well!

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Testing

``` bash
$ composer test
```

## Contributing

Please see [CONTRIBUTING](https://github.com/spatie/.github/blob/main/CONTRIBUTING.md) for details.

## Security

If you've found a bug regarding security please mail [security@spatie.be](mailto:security@spatie.be) instead of using the issue tracker.

## Credits

- [Freek Van der Herten](https://github.com/freekmurze)
- [All Contributors](../../contributors)

The middleware in this package was originally written by [Jeffrey Way](https://twitter.com/jeffrey_way) for the [Laracasts](https://laracasts.com)-lesson
on [pjax](https://laracasts.com/lessons/faster-page-loads-with-pjax). His original code
can be found [in this repo on GitHub](https://github.com/laracasts/Pjax-and-Laravel).

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
