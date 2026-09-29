<?php

namespace CountryValidations\Usa;

use CountryValidations\Support\Checksum;

class Bank extends \CountryValidations\Support\Bank
{
    /** ABA routing syntax, prefix and checksum; not a live bank directory. */
    public function routingNumber(string $number): bool
    {
        if (preg_match('/\\A[0-9]{9}\\z/', $number) !== 1 || $number === '000000000') {
            return false;
        }

        $prefix = (int) substr($number, 0, 2);
        if (!(($prefix <= 12) || ($prefix >= 21 && $prefix <= 32) || ($prefix >= 61 && $prefix <= 72) || $prefix === 80)) {
            return false;
        }

        return Checksum::weightedSum($number, [3, 7, 1, 3, 7, 1, 3, 7, 1]) % 10 === 0;
    }
}
