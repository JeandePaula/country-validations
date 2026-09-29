<?php

namespace CountryValidations\Usa;

use CountryValidations\Support\Checksum;
use CountryValidations\Support\Input;

class Personal extends \CountryValidations\Support\Personal
{
    public function ssn(string $ssn): bool
    {
        if (preg_match('/\A(?:[0-9]{9}|[0-9]{3}-[0-9]{2}-[0-9]{4})\z/', $ssn) !== 1) {
            return false;
        }

        return preg_match('/\A(?!000|666|9[0-9]{2})[0-9]{3}(?!00)[0-9]{2}(?!0000)[0-9]{4}\z/', Input::digits($ssn, '-')) === 1;
    }

    public function phone(string $phoneNumber): bool
    {
        return $this->nanpPhone($phoneNumber);
    }

    public function birthDate(string $dob): bool
    {
        return $this->validBirthDate($dob, 'm/d/Y');
    }

    /** Passport format only; no authenticity or expiry check. */
    public function passport(string $passport): bool
    {
        return preg_match('/\A(?:[0-9]{9}|[A-Z][0-9]{8})\z/', strtoupper($passport)) === 1;
    }

    /** Legacy regional format heuristics; not exhaustive or an issuance check. */
    public function driversLicense(string $license, string $state): bool
    {
        // Normalize input
        $license = strtoupper($license);
        $state = strtoupper($state);

        // Define regex patterns for each state
        $statePatterns = [
            'AL' => '/^\d{1,8}$/D',                          // Alabama: 1 to 8 digits
            'AK' => '/^\d{1,7}$/D',                          // Alaska: 1 to 7 digits
            'AZ' => '/^[A-Z]\d{8}$|^\d{9}$/D',               // Arizona: 1 letter + 8 digits or 9 digits
            'AR' => '/^\d{4,9}$/D',                          // Arkansas: 4 to 9 digits
            'CA' => '/^[A-Z]\d{7}$/D',                       // California: 1 letter + 7 digits
            'CO' => '/^\d{9}$|^[A-Z]\d{3,6}$|^[A-Z]{2}\d{2,5}$/D', // Colorado: 9 digits or 1 letter + 3-6 digits or 2 letters + 2-5 digits
            'CT' => '/^\d{9}$/D',                            // Connecticut: 9 digits
            'DE' => '/^\d{1,7}$/D',                          // Delaware: 1 to 7 digits
            'FL' => '/^[A-Z]\d{12}$/D',                      // Florida: 1 letter + 12 digits
            'GA' => '/^\d{7,9}$/D',                          // Georgia: 7 to 9 digits
            'HI' => '/^[A-Z]\d{8}$|^\d{9}$/D',               // Hawaii: 1 letter + 8 digits or 9 digits
            'ID' => '/^[A-Z]{2}\d{6}[A-Z]$|^\d{9}$/D',       // Idaho: 2 letters + 6 digits + 1 letter or 9 digits
            'IL' => '/^[A-Z]\d{11,12}$/D',                   // Illinois: 1 letter + 11 or 12 digits
            'IN' => '/^\d{9,10}$|^[A-Z]\d{9}$/D',            // Indiana: 9 or 10 digits or 1 letter + 9 digits
            'IA' => '/^\d{9}$|^\d{3}[A-Z]{2}\d{4}$/D',       // Iowa: 9 digits or 3 digits + 2 letters + 4 digits
            'KS' => '/^[A-Z]\d{8}$|^\d{9}$/D',               // Kansas: 1 letter + 8 digits or 9 digits
            'KY' => '/^[A-Z]\d{8,9}$|^\d{9}$/D',             // Kentucky: 1 letter + 8 or 9 digits or 9 digits
            'LA' => '/^\d{1,9}$/D',                          // Louisiana: 1 to 9 digits
            'ME' => '/^\d{7}$|^\d{7}[A-Z]$|^\d{8}$/D',       // Maine: 7 digits or 7 digits + 1 letter or 8 digits
            'MD' => '/^[A-Z]\d{12}$/D',                      // Maryland: 1 letter + 12 digits
            'MA' => '/^[A-Z]\d{8}$|^\d{9}$/D',               // Massachusetts: 1 letter + 8 digits or 9 digits
            'MI' => '/^[A-Z]\d{12}$/D',                      // Michigan: 1 letter + 12 digits
            'MN' => '/^[A-Z]\d{12}$/D',                      // Minnesota: 1 letter + 12 digits
            'MS' => '/^\d{9}$/D',                            // Mississippi: 9 digits
            'MO' => '/^[A-Z]\d{5,9}$/D',                     // Missouri: 1 letter + 5 to 9 digits
            'MT' => '/^\d{9}$/D',                            // Montana: 9 digits
            'NE' => '/^[A-Z]\d{6,8}$/D',                     // Nebraska: 1 letter + 6 to 8 digits
            'NV' => '/^\d{9,10}$|^X\d{8}$/D',                // Nevada: 9 or 10 digits or 'X' + 8 digits
            'NH' => '/^\d{2}[A-Z]{3}\d{5}$/D',               // New Hampshire: 2 digits + 3 letters + 5 digits
            'NJ' => '/^[A-Z]\d{14}$/D',                      // New Jersey: 1 letter + 14 digits
            'NM' => '/^\d{8,9}$/D',                          // New Mexico: 8 or 9 digits
            'NY' => '/^[A-Z]\d{7}$|^\d{9}$|^\d{16}$/D',      // New York: 1 letter + 7 digits or 9 digits or 16 digits
            'NC' => '/^\d{1,12}$/D',                         // North Carolina: 1 to 12 digits
            'ND' => '/^[A-Z]{3}\d{6}$/D',                    // North Dakota: 3 letters + 6 digits
            'OH' => '/^[A-Z]\d{4,8}$|^\d{8}$/D',             // Ohio: 1 letter + 4 to 8 digits or 8 digits
            'OK' => '/^[A-Z]\d{9}$/D',                       // Oklahoma: 1 letter + 9 digits
            'OR' => '/^\d{1,7}$|^[A-Z]\d{6}$/D',             // Oregon: 1 to 7 digits or 1 letter + 6 digits
            'PA' => '/^\d{8}$/D',                            // Pennsylvania: 8 digits
            'RI' => '/^\d{7}$/D',                            // Rhode Island: 7 digits
            'SC' => '/^\d{5,11}$/D',                         // South Carolina: 5 to 11 digits
            'SD' => '/^\d{6,10}$/D',                         // South Dakota: 6 to 10 digits
            'TN' => '/^\d{7,8}$/D',                          // Tennessee: 7 to 8 digits
            'TX' => '/^\d{7,8}$/D',                          // Texas: 7 or 8 digits
            'UT' => '/^\d{4,10}$/D',                         // Utah: 4 to 10 digits
            'VT' => '/^\d{8}$/D',                            // Vermont: 8 digits
            'VA' => '/^\d{7,8}$/D',                          // Virginia: 7 to 8 digits
            'WA' => '/^[A-Z]\d{6,7}$/D',                     // Washington: 1 letter + 6 or 7 digits
            'WV' => '/^\d{7}$/D',                            // West Virginia: 7 digits
            'WI' => '/^\d{8,9}$/D',                          // Wisconsin: 8 or 9 digits
            'WY' => '/^\d{9}$/D',                            // Wyoming: 9 digits
            'DC' => '/^\d{7}$/D',                            // District of Columbia: 7 digits
        ];

        return isset($statePatterns[$state]) && preg_match($statePatterns[$state], $license) === 1;
    }
}
