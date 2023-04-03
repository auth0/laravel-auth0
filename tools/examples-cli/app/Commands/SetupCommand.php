<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;
use Symfony\Component\Process\Process;

use rename;
use file_exists;

use function Termwind\{render};

class SetupCommand extends Command
{
    protected $signature = 'setup';
    protected $description = 'Prepare your environment to run examples.';

    private function checkComposer(): void
    {
        $this->task('Composer', function () {
            $version = new Process(['composer', '--version']);
            $version->disableOutput();
            $version->run();

            if ($version->isSuccessful()) {
                return true;
            }

            return $this->downloadComposer();
        }, '');
    }

    private function downloadComposer(): bool
    {
        if (file_exists('./composer')) {
            return true;
        }

        $download = new Process(['curl', '-sSfL', 'https://getcomposer.org/installer']);
        $download->run();

        if ($download->isSuccessful()) {
            $build = new Process(['php'], null, null, $download->getOutput());
            $build->disableOutput();
            $build->run();

            if ($build->isSuccessful()) {
                rename('composer.phar', 'composer');
            }

            return $build->isSuccessful();
        }

        return false;
    }

    private function checkCli(): void
    {
        $this->task('Auth0 CLI', function () {
            $version = new Process(['./auth0', '--version']);
            $version->disableOutput();
            $version->run();

            if ($version->isSuccessful()) {
                return true;
            }

            return $this->downloadCli();
        }, '');
    }

    private function downloadCli(): bool
    {
        if (file_exists('./auth0')) {
            return true;
        }

        $download = new Process(['curl', '-sSfL', 'https://raw.githubusercontent.com/auth0/auth0-cli/main/install.sh']);
        $download->run();

        if ($download->isSuccessful()) {
            $build = new Process(['sh', '-s', '--', '-b', '.', 'v1.0.0-beta.3'], null, null, $download->getOutput());
            $build->disableOutput();
            $build->run();

            return $build->isSuccessful();
        }

        return false;
    }

    private function checkAuthentication(): void {
        $this->task('Authenticating', function () {
            $authenticated = new Process(['./auth0', 'apps', 'list', '--json']);
            $authenticated->disableOutput();
            $authenticated->run();

            if ($authenticated->isSuccessful()) {
                return true;
            }

            $this->warn('Follow the prompts and authenticate with Auth0.');

            $login = new Process(['./auth0', 'login', '--no-input']);
            $login->start();

            $login->waitUntil(function ($type, $output) {
                $this->line($output);
                sleep(1);
            });

            if ($login->isSuccessful()) {
                return true;
            }

            return false;
        }, '');
    }

    /**
     * @return mixed
     */
    public function handle()
    {
        render(<<<"HTML"
            <div class="my-1">
                <div class="space-x-1">
                    <span class="px-1 bg-indigo-600">Setup</span> <span class="font-bold text-indigo-400">Auth0 Laravel SDK Examples</span>
                </div>
            </div>
        HTML);

        $this->checkComposer();
        $this->checkCli();
        $this->checkAuthentication();

        file_put_contents('.env', '');

        render(<<<"HTML"
            <div class="mt-1">
                <div class="space-x-1">
                    <span class="text-indigo-400">Setup complete.</span>
                </div>
            </div>
        HTML);

        return self::SUCCESS;
    }

    /**
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    public function schedule(Schedule $schedule): void
    {
    }
}
