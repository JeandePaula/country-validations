<?php

namespace CountryValidations\Support;

/** Internal input handling: never silently discard letters or control characters. */
final class Input
{
    public static function digits(string $value, string $separators = ''): string
    {
        if ($value === '' || preg_match('/\\A[0-9' . preg_quote($separators, '/') . ']+\\z/', $value) !== 1) {
            return '';
        }

        return str_replace(str_split($separators), '', $value);
    }

    public static function repeated(string $value): bool
    {
        return preg_match('/\\A(.)\\1*\\z/', $value) === 1;
    }
}
