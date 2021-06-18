# Auto-generated Repository Pattern in Laravel

A repository is a separation between a domain and a persistent layer. The repository provides a collection interface to access data stored in a database, file system or external service. Data is returned in the form of objects.

The main idea to use `Repository Pattern` in a Laravel application is to create a bridge between models and controllers. This pattern keeps your Laravel code clean and safe, it is worth using repositories to separate the responsibility for which the model should never be responsible.

This package assists to automatically generate the Interface and Repository files in saving your time and supporting to focus on implementing the logic. `Especially, you do not need binding the interface and repository class in Service provider class`

1-Install `cuongnd88/lara-repository` using Composer.

```php
$ composer require cuongnd88/lara-repository
```

2-Add the following service provider in `config/app.php`

```php
<?php
// config/app.php
return [
    // ...
    'providers' => [
        // ...
        Cuongnd88\LaraRepo\LaraRepoServiceProvider::class,
    ]
    // ...
];
```

3-Run `make:repository` command

```php
php artisan make:repository --interface=User/UserInterface --repository=User/UserRepository --model=Models/User

```

_`--interface` option is to indicate the Interface file._

_`--repository` option is to indicate the Repository file._

_`--model` option is to allocate the Model. If not exists, it will confirm to create a model._

## Sample Usage


Let start `code less` by running the command:

```php
php artisan make:repository --interface=Language/LanguageInterface --repository=Language/LanguageRepository --model=Models/Language

```

The `Repositories` directory is created when you run the command. All interface and repository files are stores in the `app/Repositories/Language` directory.

Now inside `LanguageController`, we inject `LanguageInterface` into the `construct` method. This action is called `Dependency Injection`

```php
. . . .
use App\Repositories\Language\LanguageInterface;

class LanguageController extends Controller
{
    protected $langRepo;

    public function __construct(LanguageInterface $langRepo)
    {
        $this->langRepo = $langRepo;        
    }
. . . .
```

Please take a look the auto-generated `repositories.php` in `config` directory

```php
<?php
//config/repositories.php
return [
	\App\Repositories\Language\LanguageInterface::class => \App\Repositories\Language\LanguageRepository::class,

];

```

The Repository Pattern also allows us to write less code inside our Controllers and that makes it even better rather having a giant code in the Controller which isn't what we want if we are aiming for better maintainability and readability. Let's keep it that way, clean controllers.

## Demo

This is demo soure code.
[Laravel Colab](https://github.com/cuongnd88/lara-colab/blob/master/alpha/app/Http/Controllers/Language/LanguageController.php)

