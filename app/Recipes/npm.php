<?php

use Illuminate\Console\View\Components\Factory;

task('npm:clean', function () {
    if (is_dir(deploy_path('node_modules'))) {
        run('rm -rf ./node_modules');
    }
})->withLabel('Cleaning current NPM packages');

task('npm:install', function () {
    $env = env('APP_ENV');
    $options = '--verbose --prefer-dist --no-progress --no-interaction --optimize-autoloader';
    if ($env === 'production') {
        $options .= ' --no-dev';
    }
    run(sprintf(
        '"%s" "%s" %s %s',
        php_bin(),
        composer_bin(),
        'install',
        $options
    ));
})->withLabel('Installing NPM packages');

task('npm:show', function () {
    $options = '-D -f json';
    $result = run(sprintf(
        '"%s" "%s" %s %s',
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
})->withLabel('Showing installed composer packages');
