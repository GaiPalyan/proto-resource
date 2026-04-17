<?php

declare(strict_types=1);

use Google\Protobuf\FieldMask;
use Tests\TestCase;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "pest()" function to bind a different classes or traits.
|
*/

pest()->extend(TestCase::class)->in('Unit');

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

expect()->extend('toBeOne', function (): void {
    $this->toBe(1); // @phpstan-ignore variable.undefined
});

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

/** @param array<string, mixed> $override */
function user(array $override = []): object
{
    return (object) array_merge([
        'id' => 'user-1',
        'full_name' => 'John Doe',
        'address' => (object) ['city' => 'Moscow', 'street' => 'Arbat'],
        'posts' => [],
        'metadata' => [],
    ], $override);
}

/** @param array<string> $paths */
function mask(array $paths): FieldMask
{
    $mask = new FieldMask();
    $mask->setPaths($paths);

    return $mask;
}
