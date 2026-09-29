<?php

namespace CountryValidations\Canada;

class Bank extends \CountryValidations\Support\Bank
{
    public function institutionNumber(string $number): bool
    {
        return preg_match('/\\A[0-9]{3}\\z/', $number) === 1 && $number !== '000';
    }

    public function transitNumber(string $number): bool
    {
        return preg_match('/\\A[0-9]{5}\\z/', $number) === 1;
    }

    /** Electronic 0IIITTTTT or printed TTTTT-III; format only. */
    public function routingNumber(string $number): bool
    {
        if (preg_match('/\\A0([0-9]{3})([0-9]{5})\\z/', $number, $parts) === 1) {
            return $this->institutionNumber($parts[1]);
        }

        return preg_match('/\\A([0-9]{5})-([0-9]{3})\\z/', $number, $parts) === 1
            && $this->institutionNumber($parts[2]);
    }
}
