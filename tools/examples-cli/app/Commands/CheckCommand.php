<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;
use Composer\Semver\Comparator;
use Symfony\Component\Process\Process;
use Illuminate\Support\Facades\Storage;

use function Termwind\{render};

class CheckCommand extends Command
{
    protected $signature = 'check';
    protected $description = 'Verify that your environment is configured to run examples.';

    private function checkPhp(): array
    {
        $phpVersion = PHP_VERSION;
        $phpCheck = Comparator::greaterThanOrEqualTo(PHP_VERSION, '8.0.0');

        return [$phpVersion, $phpCheck];
    }

    private function checkComposer(): array
    {
        $composerVersion = new Process(['./composer', '--version']);
        $composerVersion->run();
        $composerCheck = $composerVersion->isSuccessful();
        $composerVersion = $composerVersion->getOutput();

        if ($composerCheck) {
            $composerVersion = explode(' ', $composerVersion)[2];
            $composerCheck = Comparator::greaterThanOrEqualTo($composerVersion, '2.0.0');
        } else {
            $composerVersion = 'Not installed';
        }

        return [$composerVersion, $composerCheck];
    }

    private function checkCli(): array
    {
        $cliVersion = new Process(['./auth0', '--version']);
        $cliVersion->run();
        $cliCheck = $cliVersion->isSuccessful();
        $cliVersion = $cliVersion->getOutput();

        if ($cliCheck) {
            $cliVersion = explode(' ', $cliVersion)[2];
        } else {
            $cliVersion = 'Not installed';
        }

        return [$cliVersion, $cliCheck];
    }

    private function checkAuthState(): string
    {
        $authToken = new Process(['./auth0', 'apps', 'list', '--json']);
        $authToken->run();
        $authCheck = $authToken->isSuccessful();
        $authToken = $authToken->getOutput();
        $authState = $authCheck ? 'Authenticated' : 'Unauthenticated';

        return $authState;
    }

    private function checkEnvState(): string
    {
        $envState = 'Present';

        if (Storage::exists('.env') === false) {
            $envState = 'Missing';
        }

        return $envState;
    }

    /**
     * @return mixed
     */
    public function handle()
    {
        [$phpVersion, $phpCheck] = $this->checkPhp();
        [$composerVersion, $composerCheck] = $this->checkComposer();
        [$cliVersion, $cliCheck] = $this->checkCli();

        $phpColor = $phpCheck ? 'green' : 'red';
        $composerColor = $composerCheck ? 'green' : 'red';
        $cliColor = $cliCheck ? 'green' : 'red';

        if ($cliCheck) {
            $authState = $this->checkAuthState();
            $authColor = $authState === 'Authenticated' ? 'green' : 'red';
        } else {
            $authState = 'Skipped';;
            $authColor = 'gray';
        }

        $envState = $this->checkEnvState();
        $envColor = $envState === 'Present' ? 'green' : 'red';

        $summarySuccess = $phpCheck && $composerCheck && $cliCheck && $authState === 'Authenticated' && $envState === 'Present';
        $summary = $summarySuccess ? '<span class="pr-1">✅</span> No issues detected.' : '<span class="pr-1">⚠️</span> Issues detected.';
        $summaryColor = $summarySuccess ? 'green' : 'red';
        $summaryIssues = [];

        if (! $phpCheck) {
            $summaryIssues[] = ['PHP ^8.0 is required.', 'Please upgrade your environment.'];
        }

        if (! $composerCheck) {
            $summaryIssues[] = ['Composer ^2.0 is required.', 'Run <span class="bg-gray px-1">examples setup</span> to download to the examples directory.'];
        }

        if (! $cliCheck) {
            $summaryIssues[] = ['<span class="bg-red px-1">auth0</span> can not be found.', 'Run <span class="bg-gray px-1">examples setup</span> to download to the examples directory.'];
        }

        if ($cliCheck === true && $authState === 'Unauthenticated') {
            $summaryIssues[] = ['<span class="bg-red px-1">auth0</span> is not authenticated.', 'Run <span class="bg-gray px-1">auth0 login</span> to authenticate.'];
        }

        if ($envState === 'Missing') {
            $summaryIssues[] = ['<span class="bg-red px-1">.env</span> file missing from the root of the examples directory.', 'Run <span class="bg-gray px-1">examples setup</span> to generate one.'];
        }

        $summaryText = '';

        if ([] !== $summaryIssues) {
            $summaryText .= '<ul>';

            foreach ($summaryIssues as [$issue, $solution]) {
                $summaryText .= <<<"HTML"
                    <li class="pt-1">
                        <span class="text-red">{$issue}</span>
                        <div class="pt-1 pl-2">{$solution}</div>
                    </li>
                HTML;
            }

            $summaryText .= '</ul>';
        }

        render(<<<"HTML"
            <div class="my-1 mr-1">
                <div class="space-x-1">
                    <span class="px-1 bg-indigo-600">Check</span> <span class="font-bold text-indigo-400">Auth0 Laravel SDK Examples</span>
                </div>

                <div class="mt-1">
                    <span class="font-bold text-indigo-400">Dependencies</span>

                    <div class="flex space-x-1">
                        <span class="font-bold">PHP</span>
                        <span class="flex-1 content-repeat-[.] text-gray"></span>
                        <span class="font-bold text-{$phpColor}">{$phpVersion}</span>
                    </div>

                    <div class="flex space-x-1">
                        <span class="font-bold">Composer</span>
                        <span class="flex-1 content-repeat-[.] text-gray"></span>
                        <span class="font-bold text-{$composerColor}">{$composerVersion}</span>
                    </div>

                    <div class="flex space-x-1">
                        <span class="font-bold">Auth0 CLI</span>
                        <span class="flex-1 content-repeat-[.] text-gray"></span>
                        <span class="font-bold text-{$cliColor}">{$cliVersion}</span>
                    </div>
                </div>

                <div class="mt-1">
                    <span class="font-bold text-indigo-400">State</span>

                    <div class="flex space-x-1">
                        <span class="font-bold">Configuration (.env)</span>
                        <span class="flex-1 content-repeat-[.] text-gray"></span>
                        <span class="font-bold text-{$envColor}">{$envState}</span>
                    </div>

                    <div class="flex space-x-1">
                        <span class="font-bold">Authenticated</span>
                        <span class="flex-1 content-repeat-[.] text-gray"></span>
                        <span class="font-bold text-{$authColor}">{$authState}</span>
                    </div>
                </div>

                <div class="mt-1">
                    <div class="font-bold text-{$summaryColor}">{$summary}</div>
                    {$summaryText}
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
