<?php

use Tychovbh\Mvc\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use ReallySimpleJWT\Token;
use Tychovbh\Mvc\Repositories\Repository;
use Tychovbh\Mvc\User;

if (!function_exists('repository')) {
    /**
     * Repository factory
     * @param string $repository
     * @return Repository
     * @throws Exception
     */
    function repository(string $repository): Repository
    {
        try {
            $class = new \ReflectionClass($repository);
        } catch (Exception $exception) {
            throw new Exception('Repository ' . $repository . ' not found!');
        }

        $class = str_replace([
            $class->getNamespaceName(),
            'Controller'
        ], [
            'App\\Repositories',
            'Repository'
        ], $repository);

        $class = project_or_package_class('Repositories', $class);
        return new $class;
    }
}

if (!function_exists('project_or_package_class')) {
    /**
     * Get class from project if not exists try package.
     * @param string $type
     * @param string $class
     * @return string
     * @throws Exception
     */
    function project_or_package_class(string $type, string $class): string
    {
        $class = str_replace('Tychovbh\Mvc', 'App', $class);

        if (class_exists($class)) {
            return $class;
        }

        $class = str_replace('App', 'Tychovbh\Mvc', $class);

        if (class_exists($class)) {
            return $class;
        }

        throw new Exception($type . $class . ' not found!');
    }
}

if (!function_exists('model')) {

    /**
     * Model factory
     * @param string $model
     * @return mixed
     * @throws Exception
     */
    function model(string $model)
    {
        try {
            $class = new \ReflectionClass($model);
        } catch (Exception $exception) {
            throw new Exception('Model not found!');
        }

        $class = str_replace([
            $class->getNamespaceName(),
            'Repository',
        ], [
            'App',
            '',
        ], $model);

        $class = project_or_package_class('Model', $class);
        return new $class;

    }
}


if (!function_exists('controller')) {
    /**
     * Controller factory
     * @param string $controller
     * @return string
     * @throws Exception
     */
    function controller(string $controller): String
    {
        try {
            $class = new \ReflectionClass($controller);
        } catch (Exception $exception) {
            throw new Exception('Controller not found!');
        }
        $class = str_replace([
            $class->getNamespaceName() . '\\',
            'Controller'
        ], [
            '',
            ''
        ], $controller);

        $class = strtolower(
            preg_replace('/(?<!\ )[A-Z]/', '_$0', lcfirst($class))
        );
        return strtolower($class);
    }
}

if (!function_exists('resource')) {

    /**
     * Resource factory
     * @param string $resource
     * @return string
     * @throws Exception
     */
    function resource(string $resource): string
    {
        try {
            $class = new \ReflectionClass($resource);
        } catch (Exception $exception) {
            throw new Exception('Resource not found!');
        }

        $class = str_replace([
            $class->getNamespaceName(),
            'Controller'
        ], [
            'App\\Http\\Resources',
            'Resource'
        ], $resource);

        return project_or_package_class('Resource', $class);

    }
}

if (!function_exists('has_column')) {
    /**
     * Check if model has column
     * @param Model $model
     * @param string $key
     * @return mixed
     */
    function has_column(Model $model, string $key)
    {
        return $model->hasColumn($key);
    }
}

if (!function_exists('error')) {

    /**
     * Log warning
     *
     * @param string $message
     * @param array $context
     */
    function error(string $message, $context = [])
    {
        Log::error($message, $context);
    }
}

if (!function_exists('warning')) {

    /**
     * Log warning
     *
     * @param string $message
     * @param array $context
     */
    function warning(string $message, $context = [])
    {
        Log::warning($message, $context);
    }
}

if (!function_exists('info')) {

    /**
     * Log warning
     *
     * @param string $message
     * @param array $context
     */
    function info(string $message, $context = [])
    {
        Log::info($message, $context);
    }
}

if (!function_exists('emergency')) {
    /**
     * Log emergency
     * @param string $message
     * @param Exception $exception
     */
    function emergency(string $message, Exception $exception)
    {
        $data = [];

        $request = Arr::get($exception->getTrace(), '0.args.0');
        if (is_a($request, Request::class)) {
            $data['request'] = $request->fullUrl();
        }

        if (method_exists($exception, 'getStatusCode')) {
            $data['status'] = $exception->getStatusCode();
        }

        Log::emergency($message, array_merge($data, [
            'website' => config('app.url'),
            'exception' => $exception->getMessage(),
            'file' => str_replace(base_path(), '', $exception->getFile()),
            'line' => $exception->getLine(),
        ]));
    }
}

if (!function_exists('message')) {

    /**
     * Generate response message.
     * @param string $message
     * @param mixed ...$params
     * @return string
     */
    function message(string $message, ...$params)
    {
        $config = config('mvc-messages');
        $message = __(Arr::get($config, $message) ?? Arr::get($config, 'server.error'));

        $params = array_map('__', $params);

        return $params ? sprintf($message, ...$params) : $message;
    }
}

if (!function_exists('is_application')) {

    /**
     * Check Application type
     * @return string
     */
    function is_application(): string
    {
        $app = app();
        if ($app instanceof \Illuminate\Foundation\Application && $app->runningInConsole()) {
            return 'laravel';
        } elseif ($app instanceof \Laravel\Lumen\Application) {
            return 'lumen';
        }

        return 'laravel';
    }
}

if (!function_exists('get_route_info')) {

    /**
     * @param Request $request
     * @param string $key
     * @param mixed $default
     * @return mixed|string
     */
    function get_route_info(Request $request, string $key, $default = null)
    {
        $route = (array)$request->route();
        foreach ($route as $items) {
            if (is_array($items) && Arr::has($items, $key)) {
                return $items[$key];
            }
        }
        return $default;
    }
}

if (!function_exists('config_path')) {
    /**
     * Get the configuration path.
     *
     * @param string $path
     * @return string
     */
    function config_path($path = '')
    {
        return app()->basePath() . '/config' . ($path ? '/' . $path : $path);
    }
}

if (!function_exists('random_string')) {
    /**
     * Random string
     * @param int $length
     * @return string
     */
    function random_string(int $length = 10): string
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $string = '';

        for ($i = 0; $i < $length; $i++) {
            $index = rand(0, strlen($characters) - 1);
            $string .= $characters[$index];
        }

        return $string;
    }
}

if (!function_exists('token')) {
    /**
     * Generate token
     * @param mixed $data
     * @param int|null $expiration
     * @return string
     */
    function token($data, int $expiration = null): string
    {
        $expiration = $expiration ?? time() + config('mvc-auth.expiration');
        return Token::create(
            $data,
            config('mvc-auth.secret'),
            $expiration,
            config('mvc-auth.id')
        );
    }
}


if (!function_exists('token_validate')) {
    /**
     * validate token
     * @param string $token
     * @return bool
     */
    function token_validate(string $token)
    {
        return Token::validate($token, config('mvc-auth.secret'));
    }
}

if (!function_exists('token_value')) {
    /**
     * Get token value
     * @param string $token
     * @return mixed
     */
    function token_value(string $token)
    {
        return Token::getPayload($token, config('mvc-auth.secret'))['user_id'];
    }
}

if (!function_exists('request')) {
    /**
     * Get Request attribute
     * @param string $item
     * @param null $default
     * @return mixed
     */
    function request(string $item = '', $default = null)
    {
        $request = app('request');

        if ($item) {
            return $request->get($item) ?? $default;
        }

        return $request;
    }
}

if (!function_exists('user')) {
    /**
     * The authenticated user
     * @return User
     */
    function user(): User
    {
        try {
            $user = Auth::user();
            $class = project_or_package_class('User', User::class);
            return $class::findOrFail($user->id);
        } catch (ModelNotFoundException $exception) {
            return new User;
        } catch (Exception $exception) {
            return new User;
        }
    }
}

if (!function_exists('cant')) {
    /**
     * User cant
     * @param string $ability
     * @param String|Model $model
     * @return bool
     * @throws Exception
     */
    function cant(string $ability, $model): bool
    {
        return user()->id && user()->cant($ability, $model);
    }
}

if (!function_exists('can')) {
    /**
     * User can
     * @param string $ability
     * @param string|Model $model
     * @return bool
     * @throws Exception
     */
    function can(string $ability, $model): bool
    {
        return user()->id && user()->can($ability, $model);
    }
}

if (!function_exists('boolean')) {
    /**
     * Get boolean value
     * @param mixed $value
     * @return bool
     */
    function boolean($value): bool
    {
        return (bool)filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }
}
