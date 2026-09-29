<?php

namespace CountryValidations\Canada;

use CountryValidations\Support\Checksum;
use CountryValidations\Support\Input;

class Personal extends \CountryValidations\Support\Personal
{
    public function sin(string $sin): bool
    {
        if (preg_match('/\A(?:[0-9]{9}|[0-9]{3}([- ])[0-9]{3}\1[0-9]{3})\z/', $sin) !== 1) {
            return false;
        }
        $digits = Input::digits($sin, ' -');

        return !Input::repeated($digits) && Checksum::luhn($digits);
    }

    public function phone(string $phoneNumber): bool
    {
        return $this->nanpPhone($phoneNumber);
    }

    public function birthDate(string $dob): bool
    {
        return $this->validBirthDate($dob, 'Y-m-d', 1900);
    }

    /** Passport format only; no authenticity or expiry check. */
    public function passport(string $passport): bool
    {
        return preg_match('/\A(?:[A-Z]{2}[0-9]{6}|[A-Z][0-9]{6}[A-Z]{2})\z/', strtoupper($passport)) === 1;
    }

    /** Legacy regional format heuristics; not exhaustive or an issuance check. */
    public function driversLicense(string $license, string $province): bool
    {
        // Normalize inputs
        $license = strtoupper($license);
        $province = strtoupper($province);

        // Define patterns for each province
        $provincePatterns = [
            'AB' => '/^\d{1,7}$/D',         // Alberta: 1-7 digits
            'BC' => '/^[A-Z0-9]{7}$/D',     // British Columbia: 7 alphanumeric
            'MB' => '/^[A-Z0-9]{9}$/D',     // Manitoba: 9 alphanumeric
            'NB' => '/^\d{8}$/D',           // New Brunswick: 8 digits
            'NL' => '/^\d{7}$/D',           // Newfoundland: 7 digits
            'NS' => '/^\d{14}$/D',          // Nova Scotia: 14 digits
            'ON' => '/^[A-Z]\d{14}$/D',     // Ontario: 15 characters
            'PE' => '/^\d{5}$/D',           // Prince Edward Island: 5 digits
            'QC' => '/^[A-Z]\d{12}$/D',     // Quebec: 13 characters
            'SK' => '/^[A-Z0-9]{9}$/D',     // Saskatchewan: 9 alphanumeric
            'YT' => '/^\d{1,6}$/D',         // Yukon: 1-6 digits
        ];

        // Check pattern for the given province
        return isset($provincePatterns[$province]) && preg_match($provincePatterns[$province], $license) === 1;
    }
}
