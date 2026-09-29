<?php

namespace CountryValidations\Support;

use DateTimeImmutable;

/** Shared format checks. Names are a two-word application policy, not identity proof. */
abstract class Personal
{
    protected $config;

    public function __construct($config = [])
    {
        $this->config = $config;
    }

    public function email(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    public function fullName(string $name): bool
    {
        $word = "[\\p{L}][\\p{L}\\p{M}]*(?:['’\\-][\\p{L}][\\p{L}\\p{M}]*)*";

        return preg_match('/\\A' . $word . '(?: +' . $word . ')+\\z/u', $name) === 1;
    }

    protected function validBirthDate(string $value, string $format, int $minYear = 1): bool
    {
        // Exact round trip rejects rolled-over dates, trailing bytes and NULs before parsing.
        $pattern = $format === 'Y-m-d' ? '/\\A[0-9]{4}-[0-9]{2}-[0-9]{2}\\z/' : '/\\A[0-9]{2}\\/[0-9]{2}\\/[0-9]{4}\\z/';
        if (preg_match($pattern, $value) !== 1) {
            return false;
        }

        $date = DateTimeImmutable::createFromFormat('!' . $format, $value);

        return $date !== false
            && $date->format($format) === $value
            && (int) $date->format('Y') >= $minYear
            && $date <= new DateTimeImmutable('today');
    }

    /** NANP syntax, not a country-specific allocation or active-line lookup. */
    protected function nanpPhone(string $phone): bool
    {
        if (preg_match('/\\A(?:\\+?1[ .-]?)?(?:[2-9][0-9]{2}|\\([2-9][0-9]{2}\\))[ .-]?[2-9][0-9]{2}[ .-]?[0-9]{4}\\z/', $phone) !== 1) {
            return false;
        }

        $digits = Input::digits($phone, '+ ().-');
        $digits = strlen($digits) === 11 ? substr($digits, 1) : $digits;

        return substr($digits, 1, 2) !== '11' && substr($digits, 4, 2) !== '11';
    }
}
