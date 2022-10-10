<?php

declare(strict_types=1);

/**
 * Laravel polyfills, since we don't want to test against the complete framework suite,
 * just fill in the method gaps with placeholders.
 */
if (! function_exists('auth')) {
    function auth(...$params)
    {
    }
}

if (! function_exists('abort')) {
    function abort(...$params)
    {
    }
}
