# laravel-mvc

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Total Downloads][ico-downloads]][link-downloads]

Laravel MVC is created by, and is maintained by Tycho, and is a Laravel/Lumen package to manage all your data via a Repository. Feel free to check out the [change log](CHANGELOG.md), [releases](https://github.com/tychovbh/laravel-mvc/releases), [license](LICENSE.md), and [contribution guidelines](CONTRIBUTING.md)

## Install

Via Composer

``` bash
$ composer require tychovbh/laravel-mvc
```

For lumen application add Service Provider to bootstrap/app.php
```php
$app->register(\Tychovbh\Mvc\MvcServiceProvider::class);
```

## Usage

### Repositories

Create a Repository:
```
// Creates a repository in app/Repositories
artisan mvc:repository UserRepository
```

Use The UserRepository in controller, but you can use it anywhere else too.
``` php
class UserController extends AbstractController
{
    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository
    }
    
    public function index()
    {
        $users = $this->repository->all();
        return response()->json($users)
    }
}
```

Make sure you have a Model that your repository can use. If you want to use save/update methods add $filled to your model.
```php
class User extends model
{
    protected $filled = ['name']
}
```

Available methods"
```php
// Get all
$this->repository->all();

// Search all resources where name: Jan
$this->repository::withParams(['name' => 'Jan'])->get();

// Search all resources where name: Jan and get first.
$this->repository::withParams(['name' => 'Jan'])->first();

// Search all resources where names in: Jan and piet
$this->repository::withParams(['name' => ['jan', 'piet']])->get();

// Order all resources by name or any other Laravel statement
$this->repository::withParams(['sort' => 'name desc')]->get();

// Paginate 10
$this->repository->paginate(10);

// Paginate 10 where country in Netherlands or Belgium.
$this->repository::withParams(['country' => ['Netherlands', 'Belgium']])->paginate(4);

// Search resource with ID: 1
$this->repository->find(1);

// Store resource in the database. This uses laravel fill make sure you add protected $filled = ['name'] to your User model.
$user = $this->repository->save(['name' => 'jan']);

// Update resource.
$user = $this->repository->update(['name' => 'piet'], 1);

// Destroy resource(s).
$this->repository->destroy([1]);
```

If you wish to override on of the methods above just add it to you repository
```php
class UserRepository extends AbstractRepository implements Repository
{
    public function find(int $id)
    {
        // add your own implementation of find
        return $user;
    }
    
    public function save($data)
    {
        // Add some logic and then call parent save
        $data['password'] = Hash:make($data['password']);
        return parent::save($data);
    }
    
    // You can add your own custom params to filter the request
    // This will be triggered when key is "search" is added to the params:
    // Let's say we want to build a search on firstname, lastname and email:
    // $repository->params(['search' => 'jan'])->all();
    // $repository->params(['search' => 'jan@gmail.com'])->all();
    // $repository->params(['search' => 'piet'])->all();
    // We can do that by adding a method, just capitalize the param key and add index{key}Param to the method name.
    public function indexSearchParam(string $search)
    {
        $this->query->where('email', $search)
                    ->orWhere('firstname', $search)
                    ->orWhere('surname', $search);    
    }
    
    // You can do the same for show methods like find
    public function showSearchParam(string $search);
}

```

### Controllers

Create a Controller:
```
// Creates a Controller in app/Http/Controllers
artisan mvc:controller UserController
```
All Laravel Resource methods are now available (index, show, store, update, destroy). See their documentation here for setting up routes: [laravel resource controllers](https://laravel.com/docs/5.8/controllers#resource-controllers).

You can override Resource methods to do project related stuff
``` php
class UserController extends AbstractController
{
    public function index()
    {
        // Do stuff before querying
        
        $response = parent::index();

        // Do stuff after querying

        return $response;
    }
}
```

### Form Requests

Create a Form Request:
```
// Creates a Form Request in app/Http/Requests
artisan mvc:request StoreUser
```

You can use the request middleware "valdiate" to validate the request. It will look for the FormRequest and validate it So for example model User:
- store request (POST /users) will look for a FormRequest with name StoreUser
- update request (UPDATE /user/{id}) will look for a FormRequest with name UpdateUser

``` php
// routes/web.php (Laravel)
$router->post('/users', 'UserController@index')
->name('user.index')
->middleware('validate');

// routes/web.php (Lumen)
$router->post('/users', [
    'middleware' => 'validate',
    'as' => 'users.index',
    'uses' => 'UserController@index'
]);

```


## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Testing

``` bash
$ composer test
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) and [CODE_OF_CONDUCT](CODE_OF_CONDUCT.md) for details.

## Security

If you discover any security related issues, please email info@bespokeweb.nl instead of using the issue tracker.

## Credits

- [Tycho][link-author]

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/tychovbh/laravel-mvc.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/tychovbh/laravel-mvc/master.svg?style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/coverage/g/tychovbh/laravel-mvc.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/tychovbh/laravel-mvc.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/tychovbh/laravel-mvc.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/tychovbh/laravel-mvc
[link-travis]: https://travis-ci.org/tychovbh/laravel-mvc
[link-scrutinizer]: https://scrutinizer-ci.com/g/tychovbh/laravel-mvc/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/tychovbh/laravel-mvc
[link-downloads]: https://packagist.org/packages/tychovbh/laravel-mvc
[link-author]: https://github.com/tychovbh
[link-contributors]: ../../contributors
