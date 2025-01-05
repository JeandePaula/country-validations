<?php

namespace CountryValidations\Canada;

use DateTime;

class Personal
{
    private $config;

    public function __construct($config = [])
    {
        $this->config = $config;
    }
                /**
     * Validates a Social Insurance Number (SIN) in the format XXX-XXX-XXX.
     * @param string $sin SIN to validate.
     * @return bool True if valid, false otherwise.
     */
    public function sin(string $sin): bool
    {
        // Remove non-numeric characters
        $sin = preg_replace('/[^0-9]/', '', $sin);

        // Validate the SIN format (9 digits)
        if (strlen($sin) !== 9 || preg_match('/^0+$/', $sin)) {
            return false;
        }

        // Apply the Luhn algorithm
        $checkSum = 0;
        for ($i = 0; $i < 9; $i++) {
            $digit = (int) $sin[$i];
            if ($i % 2 !== 0) {
                $digit *= 2;
                if ($digit > 9) {
                    $digit -= 9;
                }
            }
            $checkSum += $digit;
        }

        return $checkSum % 10 === 0;
    }

    /**
     * Validates a Canadian phone number in the format (XXX) XXX-XXXX or XXX-XXX-XXXX.
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
     * Validates a date of birth in the format YYYY-MM-DD.
     * @param string $dob Date of birth to validate.
     * @return bool True if valid, false otherwise.
     */
    public function birthDate(string $dob): bool
    {
        // Allow only numbers and dashes, ensure no unexpected characters
        $dob = preg_replace('/[^0-9\-]/', '', $dob);

        // Validate the format strictly (YYYY-MM-DD)
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $dob)) {
            return false;
        }

        // Parse the date
        $date = DateTime::createFromFormat('Y-m-d', $dob);

        // Ensure date is valid and matches the input format
        if (!$date || $date->format('Y-m-d') !== $dob) {
            return false;
        }

        // Ensure date is not in the future and year is reasonable
        $now = new DateTime();
        $minYear = 1900; // Minimum acceptable year
        $year = (int)$date->format('Y');

        if ($date >= $now || $year < $minYear) {
            return false;
        }

        return true;
    }

    /**
     * Validates if the given name contains at least two words.
     * @param string $name The full name to validate.
     * @return bool Returns true if the name contains two or more words, false otherwise.
     */
    public function fullName(string $name): bool
    {
        return preg_match('/^[\p{L}\p{M}\'-]+(\s+[\p{L}\p{M}\'-]+)+$/u', $name) === 1;
    }

    /**
     * Validates a Canadian email address.
     * @param string $email The email address to validate.
     * @return bool Returns true if the email address is valid, false otherwise.
     */
    public function email(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Validates a Canadian passport number in the format XXXXXXXX or XXXXXXXX.
     * @param string $passport Passport number to validate.
     * @return bool True if valid, false otherwise.
     */
    public function passport(string $passport): bool
    {
        // Remove non-alphanumeric characters
        $passport = preg_replace('/[^A-Z0-9]/i', '', $passport);

        return preg_match('/^[A-Z]{2}\d{6}$/', $passport) === 1;
    }

    /**
     * Validates a driver's license number based on the Canadian province.
     * @param string $license License number to validate.
     * @param string $province Two-letter province code (e.g., 'ON' for Ontario).
     * @return bool True if valid, false otherwise.
     */
    public function driversLicense(string $license, string $province): bool
    {
        // Normalize input
        $license = strtoupper(trim($license));
        $province = strtoupper(trim($province));

        // Define regex patterns for each province
        $provincePatterns = [
            'AB' => '/^\\d{1,7}$/',                        // Alberta: 1-7 digits
            'BC' => '/^[A-Z0-9]{7}$/',                     // British Columbia: 7 alphanumeric
            'MB' => '/^[A-Z0-9]{9}$/',                     // Manitoba: 9 alphanumeric
            'NB' => '/^\\d{8}$/',                          // New Brunswick: 8 digits
            'NL' => '/^\\d{7}$/',                          // Newfoundland and Labrador: 7 digits
            'NS' => '/^\\d{14}$/',                         // Nova Scotia: 14 digits
            'ON' => '/^[A-Z]{1}\\d{6,9}$/',                // Ontario: 1 letter + 6-9 digits
            'PE' => '/^\\d{5}$/',                          // Prince Edward Island: 5 digits
            'QC' => '/^[A-Z0-9]{10}$/',                    // Quebec: 10 alphanumeric
            'SK' => '/^[A-Z0-9]{9}$/',                     // Saskatchewan: 9 alphanumeric
            'YT' => '/^\\d{1,6}$/',                        // Yukon: 1-6 digits
        ];

        // Check if the pattern exists for the province and validate
        return isset($provincePatterns[$province]) && preg_match($provincePatterns[$province], $license) === 1;
    }

}