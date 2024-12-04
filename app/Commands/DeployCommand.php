<?php

namespace App\Commands;

use App\Facades\Tasks;
use Dotenv\Dotenv;
use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

use function Laravel\Prompts\error;
use function Laravel\Prompts\info;

class DeployCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'deploy {--P|path= : The path of application to be deployed}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deploy current application';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $deployPath = dirname(str(base_path(''))->after('phar://')->toString());
        define('DEPLOY_PATH', $this->option('path') ?? $deployPath);

        if (file_exists(deploy_path('.env'))) {
            Dotenv::createImmutable(DEPLOY_PATH)->load();
        }

        $deployFile = deploy_path('deploy.php');
        if (! file_exists($deployFile) || ! is_readable($deployFile)) {
            $base = deploy_path();
            error("No deploy file (deploy.php, deploy.yaml or deploy.yml) found on project root ($base).");

            return self::FAILURE;
        }
        info('Deploying application using deploy config: '.$deployFile);

        require base_path('app/Recipes/artisan.php');
        require base_path('app/Recipes/composer.php');
        require base_path('app/Recipes/npm.php');
        require $deployFile;

        if (! Tasks::has('main')) {
            error('Main task not implemented. Declare it using main() function.');

            return self::FAILURE;
        }
        Tasks::initialize($this->components, $this->output);

        Tasks::call('main');

        return self::SUCCESS;
    }

    /**
     * Define the command's schedule.
     */
    public function schedule(Schedule $schedule): void
    {
        // $schedule->command(static::class)->everyMinute();
    }
}
