<?php

use Auth0\Laravel\Tests\TestCase;
use Auth0\SDK\Token;
use Auth0\SDK\Token\Generator;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "uses()" function to bind a different classes or traits.
|
*/

define('AUTH0_LARAVEL_RUNNING_TESTS', 1);

uses(TestCase::class)->in(__DIR__);

// uses()->beforeAll(function (): void {

// })->in(__DIR__);

uses()->beforeEach(function (): void {
    $this->events = [];

    Event::listen('*', function ($event) {
        $this->events[] = $event;
    });

    Cache::flush();

    config()->set('auth', [
        'defaults' => [
            'guard' => 'legacyGuard',
            'passwords' => 'users',
        ],
        'guards' => [
            'web' => [
                'driver' => 'session',
                'provider' => 'users',
            ],
            'legacyGuard' => [
                'driver' => 'auth0.guard',
                'configuration' => 'web',
                'provider' => 'auth0-provider',
            ],
            'auth0-session' => [
                'driver' => 'auth0.authenticator',
                'configuration' => 'web',
                'provider' => 'auth0-provider',
            ],
            'auth0-api' => [
                'driver' => 'auth0.authorizer',
                'configuration' => 'api',
                'provider' => 'auth0-provider',
            ],
        ],
        'providers' => [
            'users' => [
                'driver' => 'eloquent',
                'model' => App\Models\User::class,
            ],
            'auth0-provider' => [
                'driver' => 'auth0.provider',
                'repository' => 'auth0.repository',
            ],
        ],
    ]);
})->in(__DIR__);

// uses()->afterEach(function (): void {
//     $commands = ['optimize:clear'];

//     foreach ($commands as $command) {
//         Artisan::call($command);
//     }
// })->in(__DIR__);

uses()->compact();

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

// expect()->extend('toBeOne', function () {
//     return $this->toBe(1);
// });

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

// function something()
// {
//     // ..
// }

function mockIdToken(
    string $algorithm = Token::ALGO_RS256,
    array $claims = [],
    array $headers = []
): string {
    $secret = createRsaKeys()->private;

    $claims = array_merge([
        "iss" => 'https://' . config('auth0.guards.default.domain') . '/',
        'sub' => 'hello|world',
        'aud' => config('auth0.guards.default.clientId'),
        'exp' => time() + 60,
        'iat' => time(),
        'email' => 'john.doe@somewhere.test'
    ], $claims);

    return (string) Generator::create($secret, $algorithm, $claims, $headers);
}

function mockAccessToken(
    string $algorithm = Token::ALGO_RS256,
    array $claims = [],
    array $headers = []
): string {
    $secret = createRsaKeys()->private;

    $claims = array_merge([
        "iss" => 'https://' . config('auth0.guards.default.domain') . '/',
        'sub' => 'hello|world',
        'aud' => config('auth0.guards.default.clientId'),
        'iat' => time(),
        'exp' => time() + 60,
        'azp' => config('auth0.guards.default.clientId'),
        'scope' => 'openid profile email',
    ], $claims);

    return (string) Generator::create($secret, $algorithm, $claims, $headers);
}

function createRsaKeys(
    string $digestAlg = 'sha256',
    int $keyType = OPENSSL_KEYTYPE_RSA,
    int $bitLength = 2048
): object
{
    $config = [
        'digest_alg' => $digestAlg,
        'private_key_type' => $keyType,
        'private_key_bits' => $bitLength,
    ];

    $privateKeyResource = openssl_pkey_new($config);

    if ($privateKeyResource === false) {
        throw new RuntimeException("OpenSSL reported an error: " . getSslError());
    }

    $export = openssl_pkey_export($privateKeyResource, $privateKey);

    if ($export === false) {
        throw new RuntimeException("OpenSSL reported an error: " . getSslError());
    }

    $publicKey = openssl_pkey_get_details($privateKeyResource);

    $resCsr = openssl_csr_new([], $privateKeyResource);
    $resCert = openssl_csr_sign($resCsr, null, $privateKeyResource, 30);
    openssl_x509_export($resCert, $x509);

    return (object) [
        'private' => $privateKey,
        'public' => $publicKey['key'],
        'cert' => $x509,
        'resource' => $privateKeyResource,
    ];
}

function getSslError(): string
{
    $errors = [];

    while ($error = openssl_error_string()) {
        $errors[] = $error;
    }

    return implode(', ', $errors);
}
