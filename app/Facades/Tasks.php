<?php

namespace App\Facades;

use App\Tasks\Task;
use Closure;
use Illuminate\Console\View\Components\Factory;
use Illuminate\Support\Facades\Facade;

/**
 * @method static void call(string $name)
 * @method static bool has(string $name)
 * @method static void initialize(Factory $components)
 * @method static Task main(Closure|array $closure)
 * @method static Task task(string $name, Closure|array $closure)
 *
 * @see \App\Tasks\Tasks
 */
class Tasks extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \App\Tasks\Tasks::class;
    }
}
