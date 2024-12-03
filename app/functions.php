<?php

use Illuminate\Contracts\Process\ProcessResult;
use Illuminate\Support\Facades\Process;

use function Illuminate\Filesystem\join_paths;
use function Illuminate\Support\php_binary;

if (! function_exists('artisan')) {
    function artisan(?string $command = null, null|string|array $args = null): string
    {
        $runCommand = [php_bin(), 'artisan'];
        if (! empty($command)) {
            $runCommand[] = $command;
        }
        if (! empty($args)) {
            $runCommand[] = is_array($args) ? implode(' ', $args) : $args;
        }

        return run($runCommand)->output();
    }
}

if (! function_exists('composer_bin')) {
    function composer_bin(): string
    {
        if (! defined('COMPOSER_BIN')) {
            $path = str(run('which composer')->output())
                ->deduplicate('/')
                ->remove("\n")
                ->toString();
            define('COMPOSER_BIN', $path);
        }

        return COMPOSER_BIN;
    }
}

if (! function_exists('deploy_path')) {
    function deploy_path(?string $path = null): string
    {
        return join_paths(DEPLOY_PATH, $path);
    }
}

if (! function_exists('php_bin')) {
    function php_bin(): string
    {
        return php_binary();
    }
}

if (! function_exists('run')) {
    function run(array|string|null $command = null, ?callable $output = null): ProcessResult
    {
        return Process::path(deploy_path())
            ->run($command, $output)
            ->throw();
    }
}
