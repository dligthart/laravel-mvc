<?php
declare(strict_types=1);

namespace Tychovbh\Mvc\Routes;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;

abstract class AbstractRoutes
{
    /**
     * @var null
     */
    private static $instance = null;

    /**
     * AbstractRoutes constructor.
     */
    private function __construct()
    {
        //
    }

    /**
     * Get Route Instance
     * @return Routes
     */
    public static function instance(): Routes
    {
        if (self::$instance === null) {
            $class = get_called_class();
            self::$instance = new $class();
        }

        return self::$instance;
    }

    /**
     * Define index route
     * @param string $name
     * @param array $options
     * @param array $middleware
     */
    public function index(string $name, array $options = [], array $middleware = [])
    {
        $this->route(
            'get',
            $this->asFromName($name, 'index'),
            'index',
            '/' . $name,
            $options,
            $middleware
        );
    }

    /**
     * Define show route
     * @param string $name
     * @param array $options
     * @param array $middleware
     */
    public function show(string $name, array $options = [], array $middleware = [])
    {
        $this->route(
            'get',
            $this->asFromName($name, 'show'),
            'show',
            '/' . $name . '/{id}',
            $options,
            $middleware
        );
    }

    /**
     * Define store route
     * @param string $name
     * @param array $options
     * @param array $middleware
     */
    public function store(string $name, array $options = [], array $middleware = [])
    {
        $this->route(
            'post',
            $this->asFromName($name, 'store'),
            'store',
            '/' . $name,
            $options,
            $middleware
        );
    }

    /**
     * Define update route
     * @param string $name
     * @param array $options
     * @param array $middleware
     */
    public function update(string $name, array $options = [], array $middleware = [])
    {
        $url = Arr::get($options, 'update.url');

        if (!$url) {
            $parts = explode('.', $name);
            $url = '/' . array_shift($parts) . '/{id}';
            if ($parts) {
                $url .= '/' . implode('/', $parts);
            }
        }

        $this->route(
            'put',
            $this->asFromName($name, 'update'),
            'update',
            $url,
            $options,
            $middleware
        );
    }

    /**
     * Generate route as from a name and action
     * @param string $name
     * @param string $action
     * @return string
     */
    private function asFromName(string $name, string $action)
    {
        $parts = explode('.', $name);
        $as = array_shift($parts) . '.' . $action;
        if ($parts) {
            $as .= '.' . implode('.', $parts);
        }

        return $as;
    }

    /**
     * Define route
     * @param string $method
     * @param string $as
     * @param string $action
     * @param string $url
     * @param array $options
     * @param array $middleware
     */
    public function route(
        string $method,
        string $as,
        string $action,
        string $url,
        array $options = [],
        array $middleware = []
    )
    {
        $app = app();
        $parts = explode('.', $as);
        $controller = ucfirst(Str::camel(Str::singular(array_shift($parts))));
        $namespace = Arr::get($options, $action . '.namespace', 'Tychovbh\Mvc\Http\Controllers') . '\\';
        $uses = Arr::get(
            $options,
            $action . '.uses',
            $namespace . $controller . 'Controller@' . Arr::get($options, $action . '.action', $action)
        );
        $middleware = array_merge(Arr::get($options, $action . '.middleware', []), $middleware);
        Arr::forget($options, $action . '.middleware');

        if (is_application() === 'lumen') {
            $app->router->{$method}($url, array_merge([
                'as' => $as,
                'uses' => $uses,
                'middleware' => $middleware
            ], Arr::get($options, $action, [])));
        }

        if (is_application() === 'laravel') {
            $route = $app['router']->{$method}($url, $uses)
                ->name($as);

            if ($middleware) {
                $route->middleware($middleware);
            }
        }
    }
}
