<?php

use Auth0\Laravel\Tests\TestCase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Artisan;

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

uses(TestCase::class)->in(__DIR__);

uses()->beforeAll(function (): void {
    // ray()->clearAll();
})->in(__DIR__);

uses()->beforeEach(function (): void {
    $this->events = [];

    Event::listen('*', function ($event) {
        $this->events[] = $event;
    });
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

// function createIdToken($claims = [], $headers = []): string

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
