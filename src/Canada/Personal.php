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
     * Uses the Luhn algorithm to verify the checksum.
     * @param string $sin SIN to validate.
     * @return bool True if valid, false otherwise.
     */
    public function sin(string $sin): bool
    {
        // Remove non-numeric characters
        $sin = preg_replace('/[^0-9]/', '', $sin);

        // Validate the SIN format (9 digits, not all zeros)
        if (strlen($sin) !== 9 || preg_match('/^0+$/', $sin)) {
            return false;
        }

        // Apply the Luhn algorithm
        $checkSum = 0;
        for ($i = 0; $i < 9; $i++) {
            $digit = (int) $sin[$i];
            if ($i % 2 !== 0) { // Double every second digit
                $digit *= 2;
                if ($digit > 9) { // Subtract 9 if greater than 9
                    $digit -= 9;
                }
            }
            $checkSum += $digit;
        }

        // Check if the checksum is divisible by 10
        return $checkSum % 10 === 0;
    }

    /**
     * Validates a Canadian phone number in the formats:
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
     * Validates a date of birth in the format YYYY-MM-DD.
     * Ensures the date is valid, not in the future, and within a reasonable range.
     * @param string $dob Date of birth to validate.
     * @return bool True if valid, false otherwise.
     */
    public function birthDate(string $dob): bool
    {
        // Remove invalid characters
        $dob = preg_replace('/[^0-9\-]/', '', $dob);

        // Check format strictly
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $dob)) {
            return false;
        }

        // Parse and validate date
        $date = DateTime::createFromFormat('Y-m-d', $dob);
        if (!$date || $date->format('Y-m-d') !== $dob) {
            return false;
        }

        // Check if the date is in the past and year is reasonable
        $now = new DateTime();
        $minYear = 1900;
        $year = (int)$date->format('Y');

        return $date < $now && $year >= $minYear;
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
     * Validates an email address using PHP's filter_var.
     * @param string $email The email address to validate.
     * @return bool Returns true if the email address is valid, false otherwise.
     */
    public function email(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Validates a Canadian passport number.
     * Format: Two uppercase letters followed by six digits.
     * @param string $passport Passport number to validate.
     * @return bool True if valid, false otherwise.
     */
    public function passport(string $passport): bool
    {
        // Remove invalid characters
        $passport = preg_replace('/[^A-Z0-9]/i', '', $passport);

        // Match pattern for Canadian passport numbers
        return preg_match('/^[A-Z]{2}\d{6}$/', $passport) === 1;
    }

    /**
     * Validates a driver's license number based on the province.
     * Each province has its specific format requirements.
     * @param string $license License number to validate.
     * @param string $province Two-letter province code (e.g., 'ON' for Ontario).
     * @return bool True if valid, false otherwise.
     */
    public function driversLicense(string $license, string $province): bool
    {
        // Normalize inputs
        $license = strtoupper(trim($license));
        $province = strtoupper(trim($province));

        // Define patterns for each province
        $provincePatterns = [
            'AB' => '/^\d{1,7}$/',         // Alberta: 1-7 digits
            'BC' => '/^[A-Z0-9]{7}$/',     // British Columbia: 7 alphanumeric
            'MB' => '/^[A-Z0-9]{9}$/',     // Manitoba: 9 alphanumeric
            'NB' => '/^\d{8}$/',           // New Brunswick: 8 digits
            'NL' => '/^\d{7}$/',           // Newfoundland: 7 digits
            'NS' => '/^\d{14}$/',          // Nova Scotia: 14 digits
            'ON' => '/^[A-Z]\d{6,9}$/',    // Ontario: 1 letter + 6-9 digits
            'PE' => '/^\d{5}$/',           // Prince Edward Island: 5 digits
            'QC' => '/^[A-Z0-9]{10}$/',    // Quebec: 10 alphanumeric
            'SK' => '/^[A-Z0-9]{9}$/',     // Saskatchewan: 9 alphanumeric
            'YT' => '/^\d{1,6}$/',         // Yukon: 1-6 digits
        ];

        // Check pattern for the given province
        return isset($provincePatterns[$province]) && preg_match($provincePatterns[$province], $license) === 1;
    }
}
