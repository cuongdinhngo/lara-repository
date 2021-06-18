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
php artisan make:repository --interface=User/UserInterface --repository=User/UserRepository --model=Models/User --controller=User/UserController

```

_`--interface` is to indicate the Interface file._

_`--repository` is to indicate the Repository file._

_`--model` is to allocate the Model. If not exists, it will confirm to create a model._

_`--controller` option is to create the Controller file. With `@resource`, it generates a resource controller class._


## Sample Usage


Let start `code less` by running the command:

```php
php artisan make:repository --interface=Staff/StaffInterface --repository=Staff/StaffRepository --model=Models/Staff --controller=Staff/StaffController@resource

```

The `Repositories` directory is created when you run the command. All interface and repository files are stores in the `app/Repositories/Language` directory.

Now inside `StaffController`, we inject `Interface` into the `construct` method. This action is called `Dependency Injection`

```php
<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Repositories\Staff\StaffInterface;
use Illuminate\Http\Request;

class StaffController extends Controller
{
    protected $staffInterface;

    public function __construct(StaffInterface $staffInterface)
    {
        $this->staffInterface = $staffInterface;        
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}

```

Please take a look the auto-generated `repositories.php` in `config` directory

```php
<?php
//config/repositories.php
return [
    \App\Repositories\User\UserInterface::class => \App\Repositories\User\UserRepository::class,
    \App\Repositories\Staff\StaffInterface::class => \App\Repositories\Staff\StaffRepository::class,
    \App\Repositories\Language\LanguageInterface::class => \App\Repositories\Language\LanguageRepository::class,

];

```

The Repository Pattern also allows us to write less code inside our Controllers and that makes it even better rather having a giant code in the Controller which isn't what we want if we are aiming for better maintainability and readability. Let's keep it that way, clean controllers.

## Demo

This is demo soure code.
[app/Repositories](https://github.com/cuongnd88/lara-colab/tree/master/alpha/app/Repositories)
[Staff/StaffController.php](https://github.com/cuongnd88/lara-colab/blob/master/alpha/app/Http/Controllers/Staff/StaffController.php)