<?php

namespace CountryValidations\Canada;

class Address
{
    /** Canada Post format and allowed letters; does not confirm delivery. */
    public function postalCode(string $code): bool
    {
        return preg_match('/\\A[ABCEGHJ-NPRSTVXY][0-9][ABCEGHJ-NPRSTVWXYZ] ?[0-9][ABCEGHJ-NPRSTVWXYZ][0-9]\\z/', strtoupper($code)) === 1;
    }

    public function province(string $province): bool
    {
        return in_array(strtoupper($province), [
            'AB', 'BC', 'MB', 'NB', 'NL', 'NS', 'NT', 'NU', 'ON', 'PE', 'QC', 'SK', 'YT',
        ], true);
    }
}
