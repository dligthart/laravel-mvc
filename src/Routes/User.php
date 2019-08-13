<?php

declare(strict_types=1);

namespace Tychovbh\Mvc\Routes;

class User extends AbstractRoutes implements Routes
{
    /**
     * @param array $options
     */
    public static function routes(array $options = [])
    {
        $instance = self::instance();
        $instance->show('users', $options);
        $instance->store('users', $options, ['validate']);
        $instance->index('users', $options);
    }
}
