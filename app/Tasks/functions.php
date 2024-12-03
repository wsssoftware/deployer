<?php

use App\Facades\Tasks;
use App\Tasks\Task;

if (! function_exists('main')) {
    function main(Closure|array $closure): Task
    {
        return Tasks::main($closure);
    }
}

if (! function_exists('task')) {
    function task(string $name, Closure|array $closure): Task
    {
        return Tasks::task($name, $closure);
    }
}
