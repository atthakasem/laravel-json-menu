# Laravel Json Menu

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Total Downloads][ico-downloads]][link-downloads]

<!-- [![Build Status][ico-travis]][link-travis]
[![StyleCI][ico-styleci]][link-styleci] -->

__This package is currently under development. Use at your own risk!__

## Installation

Via Composer

```bash
$ composer require atthakasem/laravel-json-menu
```

## Usage

1. Create a menu file in `resouces/menus/main.json` containing an array of pages. A page can be represented by a string or an object.

    ```json
    [
        "News",
        "About us",
        {
            "name": "Home",
            "url": "/"
        },
        {
            "name": "Career",
            "children": ["Our perks", "Vacancies"]
        },
        {
            "name": "Our partner",
            "url": "https://www.google.com",
            "external": true
        },
        {
            "name": "Contact",
            "route": "contact.show"
        },
        {
            "name": "Data protection",
            "class": "my-css-class another-one"
        }
    ]
    ```

2. Use the menu in your blade file via `@menu()` or `@menu('main')`. The resulting output will be:

    ```html
    <ul>
        <li><a href="http://localhost/news">News</a></li>
        <li><a href="http://localhost/about-us">About us</a></li>
        <li><a href="http://localhost" class="active">Home</a></li>
        <li>
            <a href="http://localhost/career">Career</a>
            <ul>
                <li><a href="http://localhost/career/our-perks">Our perks</a></li>
                <li><a href="http://localhost/career/vacancies">Vacancies</a></li>
            </ul>
        </li>
        <li><a href="https://www.google.com" target="_blank">Our partner</a></li>
        <li><a href="http://localhost/contact">Contact</a></li>
        <li><a href="http://localhost/data-protection" class="my-css-class another-one">Data protection</a></li>
    </ul>
    ```

## Options

These JSON properties can be used. When using object notation, only "name" is mandatory.

Property | Description | Value type | Default
--- | --- | --- | ---
name | Displayed name of the menu item | string | N/A
url | Relative URL to desired page | string | Illuminate\Support\Str::slug($name)
route | Named route to desired page | string | null
external | Open link in a new tab | boolean | false
children | Subpages of the menu item | array | []
class | CSS classes of the menu item | string | null

### Changing the path to the JSON menu files

```bash
$ php artisan vendor:publish --provider="Atthakasem\LaravelJsonMenu\LaravelJsonMenuServiceProvider"
```
will create a config file `config/laravel-json-menu.php` where you can change the path.

## Change log

Please see the [changelog](changelog.md) for more information on what has changed recently.

<!-- ## Testing

``` bash
$ composer test
``` -->

## Contributing

Please see [contributing.md](contributing.md) for details and a todolist.

## Security

If you discover any security related issues, please email author email instead of using the issue tracker.

## Credits

-   [Thitithan Atthakasem][link-author]
-   [All Contributors][link-contributors]

## License

Please see the [license file](license.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/atthakasem/laravel-json-menu.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/atthakasem/laravel-json-menu.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/atthakasem/laravel-json-menu/master.svg?style=flat-square
[ico-styleci]: https://styleci.io/repos/12345678/shield
[link-packagist]: https://packagist.org/packages/atthakasem/laravel-json-menu
[link-downloads]: https://packagist.org/packages/atthakasem/laravel-json-menu
[link-travis]: https://travis-ci.org/atthakasem/laravel-json-menu
[link-styleci]: https://styleci.io/repos/12345678
[link-author]: https://github.com/atthakasem
[link-contributors]: ../../contributors
