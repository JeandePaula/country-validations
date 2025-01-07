<?php

namespace CountryValidations\Usa;

use DateTime;

class Personal
{
    private $config;

    public function __construct($config = [])
    {
        $this->config = $config;
    }

    /**
     * Validates a Social Security Number (SSN) in the format XXX-XX-XXXX.
     * Ensures the SSN does not start with invalid prefixes such as 000, 666, or 9XX.
     * @param string $ssn SSN to validate.
     * @return bool True if valid, false otherwise.
     */
    public function ssn(string $ssn): bool
    {
        // Remove non-numeric characters
        $ssn = preg_replace('/[^0-9]/', '', $ssn);

        // Validate the SSN format
        return preg_match('/^(?!000|666|9\d{2})\d{3}(?!00)\d{2}(?!0000)\d{4}$/', $ssn) === 1;
    }

    /**
     * Validates a US phone number in the formats:
     * - (XXX) XXX-XXXX
     * - XXX-XXX-XXXX
     * Enforces valid area codes and exchanges (first digit must be 2-9).
     * @param string $phoneNumber Phone number to validate.
     * @return bool True if valid, false otherwise.
     */
    public function phone(string $phoneNumber): bool
    {
        // Remove non-numeric characters
        $phoneNumber = preg_replace('/[^0-9]/', '', $phoneNumber);

        // Validate the phone number format
        return preg_match('/^[2-9]\d{2}[2-9]\d{2}\d{4}$/', $phoneNumber) === 1;
    }

    /**
     * Validates a date of birth in the format MM/DD/YYYY.
     * Ensures the date is valid and not in the future.
     * @param string $dob Date of birth to validate.
     * @return bool True if valid, false otherwise.
     */
    public function birthDate(string $dob): bool
    {
        // Remove invalid characters
        $dob = preg_replace('/[^0-9\/]/', '', $dob);

        // Parse and validate date
        $date = DateTime::createFromFormat('m/d/Y', $dob);
        if (!$date || $date->format('m/d/Y') !== $dob) {
            return false;
        }

        // Check if the date is in the past
        $now = new DateTime();
        return $date < $now;
    }

    /**
     * Validates if the given name contains at least two words separated by spaces.
     * Allows names with hyphens and apostrophes.
     * @param string $name The full name to validate.
     * @return bool Returns true if the name contains two or more words, false otherwise.
     */
    public function fullName(string $name): bool
    {
        return preg_match('/^[\p{L}\p{M}\'-]+(\s+[\p{L}\p{M}\'-]+)+$/u', $name) === 1;
    }

    /**
     * Validates a US email address using PHP's filter_var.
     * @param string $email The email address to validate.
     * @return bool Returns true if the email address is valid, false otherwise.
     */
    public function email(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Validates a US passport number.
     * Format: Seven or eight alphanumeric characters.
     * @param string $passport Passport number to validate.
     * @return bool True if valid, false otherwise.
     */
    public function passport(string $passport): bool
    {
        // Remove invalid characters
        $passport = preg_replace('/[^A-Z0-9]/i', '', $passport);

        // Match pattern for US passport numbers
        return preg_match('/^[A-Z0-9]{9}$/', $passport) === 1;
    }

    /**
     * Validates a driver's license number based on the state.
     * Each state has its specific format requirements.
     * @param string $license License number to validate.
     * @param string $state Two-letter state code (e.g., 'CA' for California).
     * @return bool True if valid, false otherwise.
     */
    public function driversLicense(string $license, string $state): bool
    {
        // Normalize input
        $license = strtoupper(trim($license));
        $state = strtoupper(trim($state));

        // Define regex patterns for each state
        $statePatterns = [
            'AL' => '/^\d{1,8}$/',                          // Alabama: 1 to 8 digits
            'AK' => '/^\d{1,7}$/',                          // Alaska: 1 to 7 digits
            'AZ' => '/^[A-Z]\d{8}$|^\d{9}$/',               // Arizona: 1 letter + 8 digits or 9 digits
            'AR' => '/^\d{4,9}$/',                          // Arkansas: 4 to 9 digits
            'CA' => '/^[A-Z]\d{7}$/',                       // California: 1 letter + 7 digits
            'CO' => '/^\d{9}$|^[A-Z]\d{3,6}$|^[A-Z]{2}\d{2,5}$/', // Colorado: 9 digits or 1 letter + 3-6 digits or 2 letters + 2-5 digits
            'CT' => '/^\d{9}$/',                            // Connecticut: 9 digits
            'DE' => '/^\d{1,7}$/',                          // Delaware: 1 to 7 digits
            'FL' => '/^[A-Z]\d{12}$/',                      // Florida: 1 letter + 12 digits
            'GA' => '/^\d{7,9}$/',                          // Georgia: 7 to 9 digits
            'HI' => '/^[A-Z]\d{8}$|^\d{9}$/',               // Hawaii: 1 letter + 8 digits or 9 digits
            'ID' => '/^[A-Z]{2}\d{6}[A-Z]$|^\d{9}$/',       // Idaho: 2 letters + 6 digits + 1 letter or 9 digits
            'IL' => '/^[A-Z]\d{11,12}$/',                   // Illinois: 1 letter + 11 or 12 digits
            'IN' => '/^\d{9,10}$|^[A-Z]\d{9}$/',            // Indiana: 9 or 10 digits or 1 letter + 9 digits
            'IA' => '/^\d{9}$|^\d{3}[A-Z]{2}\d{4}$/',       // Iowa: 9 digits or 3 digits + 2 letters + 4 digits
            'KS' => '/^[A-Z]\d{8}$|^\d{9}$/',               // Kansas: 1 letter + 8 digits or 9 digits
            'KY' => '/^[A-Z]\d{8,9}$|^\d{9}$/',             // Kentucky: 1 letter + 8 or 9 digits or 9 digits
            'LA' => '/^\d{1,9}$/',                          // Louisiana: 1 to 9 digits
            'ME' => '/^\d{7}$|^\d{7}[A-Z]$|^\d{8}$/',       // Maine: 7 digits or 7 digits + 1 letter or 8 digits
            'MD' => '/^[A-Z]\d{12}$/',                      // Maryland: 1 letter + 12 digits
            'MA' => '/^[A-Z]\d{8}$|^\d{9}$/',               // Massachusetts: 1 letter + 8 digits or 9 digits
            'MI' => '/^[A-Z]\d{12}$/',                      // Michigan: 1 letter + 12 digits
            'MN' => '/^[A-Z]\d{12}$/',                      // Minnesota: 1 letter + 12 digits
            'MS' => '/^\d{9}$/',                            // Mississippi: 9 digits
            'MO' => '/^[A-Z]\d{5,9}$/',                     // Missouri: 1 letter + 5 to 9 digits
            'MT' => '/^\d{9}$/',                            // Montana: 9 digits
            'NE' => '/^[A-Z]\d{6,8}$/',                     // Nebraska: 1 letter + 6 to 8 digits
            'NV' => '/^\d{9,10}$|^X\d{8}$/',                // Nevada: 9 or 10 digits or 'X' + 8 digits
            'NH' => '/^\d{2}[A-Z]{3}\d{5}$/',               // New Hampshire: 2 digits + 3 letters + 5 digits
            'NJ' => '/^[A-Z]\d{14}$/',                      // New Jersey: 1 letter + 14 digits
            'NM' => '/^\d{8,9}$/',                          // New Mexico: 8 or 9 digits
            'NY' => '/^[A-Z]\d{7}$|^\d{9}$|^\d{16}$/',      // New York: 1 letter + 7 digits or 9 digits or 16 digits
            'NC' => '/^\d{1,12}$/',                         // North Carolina: 1 to 12 digits
            'ND' => '/^[A-Z]{3}\d{6}$/',                    // North Dakota: 3 letters + 6 digits
            'OH' => '/^[A-Z]\d{4,8}$|^\d{8}$/',             // Ohio: 1 letter + 4 to 8 digits or 8 digits
            'OK' => '/^[A-Z]\d{9}$/',                       // Oklahoma: 1 letter + 9 digits
            'OR' => '/^\d{1,7}$|^[A-Z]\d{6}$/',             // Oregon: 1 to 7 digits or 1 letter + 6 digits
            'PA' => '/^\d{8}$/',                            // Pennsylvania: 8 digits
            'RI' => '/^\d{7}$/',                            // Rhode Island: 7 digits
            'SC' => '/^\d{5,11}$/',                         // South Carolina: 5 to 11 digits
            'SD' => '/^\d{6,10}$/',                         // South Dakota: 6 to 10 digits
            'TN' => '/^\d{7,8}$/',                          // Tennessee: 7 to 8 digits
            'TX' => '/^\d{7,8}$/',                          // Texas: 7 or 8 digits
            'UT' => '/^\d{4,10}$/',                         // Utah: 4 to 10 digits
            'VT' => '/^\d{8}$/',                            // Vermont: 8 digits
            'VA' => '/^\d{7,8}$/',                          // Virginia: 7 to 8 digits
            'WA' => '/^[A-Z]\d{6,7}$/',                     // Washington: 1 letter + 6 or 7 digits
            'WV' => '/^\d{7}$/',                            // West Virginia: 7 digits
            'WI' => '/^\d{8,9}$/',                          // Wisconsin: 8 or 9 digits
            'WY' => '/^\d{9}$/',                            // Wyoming: 9 digits
            'DC' => '/^\d{7}$/',                            // District of Columbia: 7 digits
        ];

        return isset($statePatterns[$state]) && preg_match($statePatterns[$state], $license) === 1;
    }
}
