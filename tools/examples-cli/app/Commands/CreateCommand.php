<?php

namespace App\Commands;

use Dotenv\Dotenv;
use Exception;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Storage;
use LaravelZero\Framework\Commands\Command;
use Symfony\Component\Process\Process;

use function Termwind\{render};

class CreateCommand extends Command
{
    protected $signature = 'create';
    protected $description = 'Create the example applications.';
    protected $stateIssues = [];

    private function checkApplicationState(): void
    {
        $this->task('Validate associated Auth0 Application', function () {
            $process = new Process(['./auth0', 'apps', 'show', env('AUTH0_CLIENT_ID', ''), '--json', '--reveal-secrets']);
            $process->run();

            if (! $process->isSuccessful()) {
                return false;
            }

            $app = json_decode($process->getOutput(), true);

            if ($app['client_secret'] !== env('AUTH0_CLIENT_SECRET', '')) {
                $this->stateIssues[] = 'The client secret in your .env file does not match the one in Auth0.';
            };

            if ($app['app_type'] !== 'regular_web') {
                $this->stateIssues[] = 'The application type in Auth0 is not set to "Regular Web Application".';
            };

            if (! in_array('http://localhost:8000/callback', $app['callbacks'])) {
                $this->stateIssues[] = 'The callback URL in Auth0 is not set to "http://localhost:8000/callback".';
            };

            if (! in_array('http://localhost:8000', $app['allowed_logout_urls'])) {
                $this->stateIssues[] = 'The logout URL in Auth0 is not set to "http://localhost:8000".';
            };

            if ($app['token_endpoint_auth_method'] !== 'client_secret_post') {
                $this->stateIssues[] = 'The token endpoint authentication method in Auth0 is not set to "Post".';
            };

            if (! in_array('authorization_code', $app['grant_types'])) {
                $this->stateIssues[] = 'Grant types should include "Authorization Code."';
            };

            if (! in_array('refresh_token', $app['grant_types'])) {
                $this->stateIssues[] = 'Grant types should include "Refresh Token."';
            };

            if ($app['jwt_configuration']['alg'] !== 'RS256') {
                $this->stateIssues[] = 'The algorithm in Auth0 is not set to "RS256".';
            };

            if ([] !== $this->stateIssues) {
                return false;
            }

            return true;
        }, '⏳');
    }

    private function createApplication(): void
    {
        $this->task('Create Auth0 Application', function () {
            $app = new Process(['./auth0', 'apps', 'create', '--json', '--name', 'Laravel SDK Examples', '--auth-method', 'post', '--type', 'regular', '--callbacks', '"http://localhost:8000/callback"', '--logout-urls', '"http://localhost:8000"', '--reveal-secrets']);
            $app->run();

            if (! $app->isSuccessful()) {
                return false;
            }

            $tenant = new Process(['./auth0', 'tenants', 'list']);
            $tenant->run();

            if (! $tenant->isSuccessful()) {
                return false;
            }

            try {
                preg_match('/^\=\=\= (.*)$/m', $tenant->getErrorOutput(), $domain);
                $domain = trim($domain[1] ?? '');

                $response = $app->getOutput();
                $json = json_decode($response, true);

                $clientId = $json['client_id'];
                $clientSecret = $json['client_secret'];
                $cookieSecret = bin2hex(random_bytes(32));

                Storage::delete('.env');
                Storage::append('.env', 'AUTH0_DOMAIN=' . $domain);
                Storage::append('.env', 'AUTH0_CLIENT_ID=' . $clientId);
                Storage::append('.env', 'AUTH0_CLIENT_SECRET=' . $clientSecret);
                Storage::append('.env', 'AUTH0_COOKIE_SECRET=' . $cookieSecret);

                return true;
            } catch (Exception) {
            }

            return false;
        }, '⏳');
    }

    private function createApi(): void
    {
        $this->task('Create Auth0 API', function () {
            $api = new Process(['./auth0', 'apis', 'create', '--name', 'Laravel SDK Examples API', '--identifier', 'https://github.com/auth0/laravel-auth0', '--token-lifetime', '86400', '--scopes', '"example:scope"', '--offline-access', '--no-input', '--json']);
            $api->run();

            if (! $api->isSuccessful()) {
                return false;
            }

            try {
                $response = $api->getOutput();
                $json = json_decode($response, true);

                $apiId = $json['id'];
                $apiIdentifier = $json['identifier'];

                Storage::append('.env', ''); // Add a blank line to differentiate from the previous entries.
                Storage::append('.env', 'AUTH0_API_ID=' . $apiId);
                Storage::append('.env', 'AUTH0_AUDIENCE=' . $apiIdentifier);

                return true;
            } catch (Exception) {
            }

            return false;
        }, '⏳');
    }

    private function createToken(string $clientId, string $apiAudience): void
    {
        $this->task('Create access token', function () use ($clientId, $apiAudience) {
            $command = ['./auth0', 'test', 'token', $clientId, '--audience', $apiAudience, '--scopes', '"example:scope"', '--no-input', '--json'];
            $token = new Process($command);
            $token->start();

            $this->line('');
            $this->line('Executing  `' . implode(' ', $command) . '`');
            $this->line('Please follow the prompts to create a test access token.');
            $this->line('');

            $token->waitUntil(function ($type, $output) {
                if (strpos($output, 'authorize?audience=') !== false) {
                    // Check if there are line breaks in the output. If so, break apart the output into an array.
                    $lines = explode(PHP_EOL, $output);

                    foreach ($lines as $line) {
                        if (strpos($line, 'authorize?audience=') !== false) {
                            $output = str_replace(PHP_EOL, '', $output);
                            $line = trim(str_replace(' ▸', '', $output));
                            $line = substr($line, strpos($line, ': https://') + 2);
                            $url = trim($line);

                            $this->warn('Open a browser to the following URL to complete authentication:');
                            $this->line($url);

                            $browser = new Process(['open', $url]);
                            $browser->run();
                        }
                    }
                }
                sleep(1);
            });

            $this->line('');

            if (! $token->isSuccessful()) {
                return false;
            }

            try {
                $response = $token->getOutput();
                $json = json_decode($response, true);

                $accessToken = $json['access_token'];
                $expires = $json['expires_in'];

                Storage::append('.env', ''); // Add a blank line to differentiate from the previous entries.
                Storage::append('.env', '# The following token expires ' . \date('r', time() + $expires));
                Storage::append('.env', 'AUTH0_EXAMPLE_ACCESS_TOKEN=' . $accessToken);

                // $this->info('🛂 Access token created:');
                // $this->line($accessToken);
                // $this->line('');
                // $this->line('The token has been saved to your .env file.');
                // $this->line('');
            } catch (Exception) {
                return false;
            }

            return true;
        }, '⏳');
    }

    private function createExample(
        string $path,
        string $version
    ): void {
        if (! Storage::directoryExists('.cache/' . $version)) {
            $projectCreated = $this->task($path . ' → `composer create-project laravel/laravel:' . $version . ' .cache/' . $version . '`', function () use ($version) {
                $process = new Process(['./composer', 'create-project', 'laravel/laravel:' . $version, '.cache/' . $version, '--prefer-dist', '--no-interaction']);
                $process->run();
                return $process->isSuccessful();
            }, '⏳');

            if (! $projectCreated) {
                $this->error('Could not create Laravel framework cache..');
                exit;
            }
        }

        $projectCopied = $this->task($path . ' → `cp -R ./cache/' . $version . ' ' . $path . '`', function () use ($version, $path) {
            $process = new Process(['cp', '-R', '.cache/' . $version, $path]);
            $process->run();
        }, '⏳');

        if (! $projectCopied) {
            $this->error('Failed to copy Laravel framework cache.');
            exit;
        }

        $gitSetup = $this->task($path . ' → `git init -b main`', function () use ($path) {
            $process = new Process(['git', 'init', '-b', 'main', $path]);
            $process->run();
            return $process->isSuccessful();
        }, '⏳');

        if (! $gitSetup) {
            $this->warn('Skipping git tasks for ' . $path . '.');
        } else {
            $gitAdded = $this->task($path . ' → `git add --all`', function () use ($path) {
                $process = new Process(['git', 'add', '--all'], $path);
                $process->run();

                return $process->isSuccessful();
            }, '⏳');

            if ($gitAdded) {
                $this->task($path . ' → `git commit -m "clean commit"`', function () use ($path) {
                    $process = new Process(['git', 'commit', '-m', '"clean commit"'], $path);
                    $process->run();

                    return $process->isSuccessful();
                }, '⏳');
            }
        }

        $this->task($path . ' → `composer config minimum-stability dev`', function () use ($path) {
            $process = new Process(['./composer', 'config', 'minimum-stability', 'dev', '--working-dir', $path]);
            $process->run();
            return $process->isSuccessful();
        }, '⏳');

        $this->task($path . ' → `composer require auth0/login`', function () use ($path) {
            $process = new Process(['./composer', 'require', 'auth0/login:dev-main', '--working-dir', $path]);
            $process->run();
            return $process->isSuccessful();
        }, '⏳');

        $this->task($path . ' → `artisan vendor:publish --tag=auth0-config`', function () use ($path) {
            $process = new Process(['php', 'artisan', 'vendor:publish', '--tag=auth0-config', '--force'], $path);
            $process->run();
            return $process->isSuccessful();
        }, '⏳');

        $this->task($path . ' → Update `' . $path . '/.env`', function () use ($path) {
            Storage::append($path . '/.env', Storage::get('.env'));
            return true;
        }, '⏳');

        $alterations = Storage::allFiles('.diff/' . $path);

        foreach($alterations as $file) {
            $relativePath = str_replace('.diff/' . $path . '/', '', $file);

            $source = realpath(getcwd() . DIRECTORY_SEPARATOR . $file);
            $destination = realpath(getcwd() . DIRECTORY_SEPARATOR . $path) . DIRECTORY_SEPARATOR . $relativePath;

            $this->task($path . ' → Update `' . $relativePath . '`', function () use ($destination, $source) {
                file_put_contents($destination, file_get_contents($source));
                return true;
            }, '⏳');
        }

        $this->task($path . ' → `php artisan config:cache`', function () use ($version, $path) {
            $process = new Process(['php', 'artisan', 'config:cache', $path]);
            $process->run();
        }, '⏳');

        $this->info('🚀 `examples run ' . $path . '` is ready to go!');
    }

    private function createApiRequestExamples(
        string $path
    ): void
    {
        $this->task($path . ' → Create `EXAMPLES.CURL.md`', function () use ($path) {
            $process = new Process(['cp', '../../.diff/EXAMPLES.CURL.md', 'EXAMPLES.CURL.md'], $path);
            $process->run();

            if ($process->isSuccessful()) {
                $example = file_get_contents($path . '/EXAMPLES.CURL.md');
                $example = str_replace('%TOKEN%', env('AUTH0_EXAMPLE_ACCESS_TOKEN'), $example);
                Storage::append($path . '/.gitignore', "\nEXAMPLES.CURL.md\n");
                file_put_contents($path . '/EXAMPLES.CURL.md', $example);
                return true;
            }
        }, '⏳');
    }

    /**
     * @return mixed
     */
    public function handle()
    {
        render(<<<"HTML"
            <div class="my-1">
                <div class="space-x-1">
                    <span class="px-1 bg-indigo-600">Create</span> <span class="font-bold text-indigo-400">Auth0 Laravel SDK Examples</span>
                </div>
            </div>
        HTML);

        if (Storage::exists('.env')) {
            $dotenv = Dotenv::createImmutable(getcwd());
            $dotenv->load();
        }

        if (env('AUTH0_CLIENT_ID') === null) {
            $this->createApplication();
        } else {
            $this->checkApplicationState();

            if ([] !== $this->stateIssues) {
                $issuesList = '<ul class="pt-1">';

                foreach ($this->stateIssues as $issue) {
                    $issuesList .= <<<"HTML"
                        <li>
                            <span class="text-red">{$issue}</span>
                        </li>
                    HTML;
                }

                $issuesList .= '</ul>';

                render($issuesList);

                return self::FAILURE;
            }
        }

        $this->createApi();
        $this->line('');

        $dotenv = Dotenv::createImmutable(getcwd());
        $dotenv->load();

        $this->info('🧪 Creating your examples ...');

        $this->createExample('laravel-9/api', '^9.0');
        $this->line('');

        $this->warn('laravel-9/web → SKIPPED');
        $this->warn('laravel-9/web.octane → SKIPPED');
        $this->warn('laravel-10/api → SKIPPED');
        $this->warn('laravel-10/web → SKIPPED');
        $this->warn('laravel-10/web.octane → SKIPPED');
        $this->line('');

        // $this->createExample('laravel-9/web', '^9.0');
        // $this->line('');

        // $this->createExample('laravel-10/api', '^10.0');
        // $this->line('');

        // $this->createExample('laravel-10/web', '^10.0');
        // $this->line('');

        $this->info('🔒 Creating test tokens ...');

        $this->createToken(env('AUTH0_CLIENT_ID', ''), env('AUTH0_AUDIENCE', ''));
        $this->line('');

        $dotenv = Dotenv::createImmutable(getcwd());
        $dotenv->load();

        $this->info('🤓 Creating personalized documentation for your examples ...');

        $this->createApiRequestExamples('laravel-9/api', '^9.0');
        $this->warn('laravel-9/web → SKIPPED');
        $this->warn('laravel-9/web.octane → SKIPPED');
        $this->warn('laravel-10/api → SKIPPED');
        $this->warn('laravel-10/web → SKIPPED');
        $this->warn('laravel-10/web.octane → SKIPPED');
        $this->line('');

        // $this->createApiRequestExamples('laravel-10/api', '^10.0');
        // $this->line('');

        $this->info('🎉 Done! Run `examples run` to start an example.');

        return self::SUCCESS;
    }

    /**
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    public function schedule(Schedule $schedule): void
    {
        // $schedule->command(static::class)->everyMinute();
    }
}
