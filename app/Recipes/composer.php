<?php

use Illuminate\Console\View\Components\Factory;

task('composer:cleanup', function () {
    run('rm -rf vendor_old && rm -rf vendor_new');
})->withLabel('Cleanup unnecessary temporary folders');

task('composer:install', function () {
    // define options based on env
    $env = env('APP_ENV');
    $options = '--verbose --prefer-dist --no-progress --no-interaction --optimize-autoloader';
    if ($env === 'production') {
        $options .= ' --no-dev';
    }
    // run install
    run(sprintf(
        '%s && "%s" "%s" %s %s && %s',
        'export COMPOSER_VENDOR_DIR=vendor_new',
        php_bin(),
        composer_bin(),
        'install',
        $options,
        'export COMPOSER_VENDOR_DIR=vendor',
    ));

    run('rm -rf vendor_old');
})->withLabel('Installing packages on "vendor_new" folder');

task('composer:move', function () {
    run('mv ./vendor ./vendor_old && mv ./vendor_new ./vendor');
})->withLabel('Move temporary packages from "vendor_new" to "vendor"');

task('composer:show', function () {
    $options = '-D -f json';
    $result = run(sprintf(
        '%s && "%s" "%s" %s %s',
        'export COMPOSER_VENDOR_DIR=vendor',
        php_bin(),
        composer_bin(),
        'show',
        $options
    ));

    return function (Factory $components) use ($result) {
        $items = json_decode($result->output(), true);
        foreach ($items['installed'] as $item) {
            $description = str($item['description'])->limit(50);
            $first = '➜';
            $first .= " {$item['name']}";
            $first .= " <fg=gray>$description</>";
            $components->twoColumnDetail($first, " <fg=blue>{$item['version']}</>");
        }
    };
})->withLabel('Showing installed packages');

task('composer', [
    'composer:cleanup',
    'composer:install',
    'composer:move',
    'composer:cleanup',
    'composer:show',
])->withGroup('Composer');
