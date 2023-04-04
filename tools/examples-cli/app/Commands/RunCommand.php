<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Storage;
use LaravelZero\Framework\Commands\Command;
use Symfony\Component\Process\Process;

use function Termwind\{render};

class RunCommand extends Command
{
    protected $signature = 'run {framework} {example}';
    protected $description = 'Run a specified example.';

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

    /**
     * @return mixed
     */
    public function handle()
    {
        render(<<<"HTML"
            <div class="my-1">
                <div class="space-x-1">
                    <span class="px-1 bg-indigo-600">Run</span> <span class="font-bold text-indigo-400">Auth0 Laravel SDK Examples</span>
                </div>
            </div>
        HTML);

        $framework = $this->argument('framework');
        $example = $this->argument('example');
        $path = $framework . \DIRECTORY_SEPARATOR . $example;

        if (! in_array($framework, ['laravel-9', 'laravel-10'])) {
            $this->error('Specified framework ' . $framework . ' is not supported.');
            return self::FAILURE;
        }

        if (! in_array($example, ['api', 'web', 'web.octane'])) {
            $this->error('Specified example ' . $example . ' is not supported.');
            return self::FAILURE;
        }

        if (! Storage::directoryExists($path)) {
            $this->error('Example app ' . $this->argument('example') . ' does not exist.');
            $this->line('Have you ran `example create` yet?');
            return self::FAILURE;
        }

        $this->info('Booting example app: ' . $this->argument('example'));

        set_time_limit(0);

        $example = new Process(['php', 'artisan', 'serve'], $path, null, null, null);
        $example->start();

        $example->waitUntil(function ($type, $output) {
            $this->line($output);
            sleep(1);
        });

        $this->line('');

        return $example->isSuccessful() ? self::SUCCESS : self::FAILURE;
    }

    /**
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    public function schedule(Schedule $schedule): void
    {
    }
}
