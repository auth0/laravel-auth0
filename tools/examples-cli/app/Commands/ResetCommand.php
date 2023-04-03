<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;
use Symfony\Component\Process\Process;
use Illuminate\Support\Facades\Storage;
use Dotenv\Dotenv;
use Exception;

use function Termwind\{render};

class ResetCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'reset';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Delete example files, as well as the example Auth0 Application and API.';

    private function deregisterApplication(?string $clientId): void
    {
        if (null === $clientId) {
            $this->warn('Delete example application: Skipped');
            return;
        }

        if ($this->confirm('Delete the generated Auth0 Application, "Laravel SDK Examples" (' . $clientId . ')?', true)) {
            $deleted = $this->task('Delete example application', function () use ($clientId) {
                $process = new Process(['./auth0', 'apps', 'delete', $clientId]);
                $process->run();
                return $process->isSuccessful();
            }, '⏳');

            if (! $deleted) {
                $this->error('Failed to delete example application. Please try again.');
                exit;
            }

            $this->deregisterApi(env('AUTH0_API_ID'));

            Storage::delete('.env');
        }
    }

    private function deregisterApi(?string $apiId): void
    {
        if (null === $apiId) {
            $this->warn('Delete example API: Skipped');
            return;
        }

        if ($this->confirm('Delete the generated Auth0 API, "Laravel SDK Examples API" (' . $apiId . ')?', true)) {
            $this->task('Delete example API', function () use ($apiId) {
                $process = new Process(['./auth0', 'apis', 'delete', $apiId]);
                $process->run();
                return $process->isSuccessful();
            }, '⏳');
        }
    }

    private function deleteFiles(): void
    {
        $this->task('Delete laravel-9/api', function () {
            Storage::deleteDirectory('laravel-9/api/');
        }, '⏳');

        $this->task('Delete laravel-9/web', function () {
            Storage::deleteDirectory('laravel-9/web/');
        }, '⏳');

        $this->task('Delete laravel-9/web.octane', function () {
            Storage::deleteDirectory('laravel-9/web.octane/');
        }, '⏳');

        $this->task('Delete laravel-10/api', function () {
            Storage::deleteDirectory('laravel-10/api/');
        }, '⏳');

        $this->task('Delete laravel-10/web', function () {
            Storage::deleteDirectory('laravel-10/web/');
        }, '⏳');

        $this->task('Delete laravel-10/web.octane', function () {
            Storage::deleteDirectory('laravel-10/web.octane/');
        }, '⏳');
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        render(<<<"HTML"
            <div class="my-1">
                <div class="space-x-1">
                    <span class="px-1 bg-indigo-600">Reset</span> <span class="font-bold text-indigo-400">Auth0 Laravel SDK Examples</span>
                </div>
            </div>
        HTML);

        if (Storage::exists('.env')) {
            $dotenv = Dotenv::createImmutable(getcwd());
            $dotenv->load();
        }

        $this->deregisterApplication(env('AUTH0_CLIENT_ID'));
        $this->line('');

        $this->info('🗑️ Deleting examples...');
        $this->deleteFiles();
        $this->line('');

        $this->info('🎉 Reset complete! You can now run `examples create` to start fresh.');
        $this->line('For convenience, the Auth0 CLI remains authenticated. Run `./auth0 logout` to log out.');
    }

    /**
     * Define the command's schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    public function schedule(Schedule $schedule): void
    {
        // $schedule->command(static::class)->everyMinute();
    }
}
