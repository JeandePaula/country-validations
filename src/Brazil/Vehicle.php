<?php

namespace CountryValidations\Brazil;

class Vehicle
{
    private $config;

    public function __construct($config = [])
    {
        $this->config = $config;
    }
                /**
     * Validates Brazilian vehicle plate format.
     *
     * Input format:
     * - Standard plates: 3 letters followed by 4 digits (e.g., ABC1234).
     * - Mercosul plates: 3 letters, 1 digit, 1 letter, 2 digits (e.g., ABC1D23).
     * Non-alphanumeric characters will be removed before validation.
     *
     * @param string $plate The vehicle plate to validate.
     * @return bool True if the plate format is valid, false otherwise.
     */
    public function plate(string $plate): bool
    {
        $plate = strtoupper(preg_replace('/[^A-Z0-9]/', '', $plate));

        $pattern = '/^[A-Z]{3}\d{4}$|^[A-Z]{3}\d[A-Z]\d{2}$/';

        return preg_match($pattern, $plate) === 1;
    }

    /**
     * Validate a Brazilian RENAVAM (National Registry of Motor Vehicles) number.
     *
     * This function checks if the provided RENAVAM number is valid by performing
     * the following steps:
     * - Removes any non-numeric characters.
     * - Ensures the length is exactly 11 digits and not all zeros.
     * - Extracts the base number and check digit.
     * - Reverses the base number.
     * - Calculates a weighted sum using predefined multipliers.
     * - Computes the expected check digit based on the weighted sum.
     * - Compares the expected check digit with the actual check digit.
     *
     * @param string $renavam The RENAVAM number to validate.
     * @return bool True if the RENAVAM number is valid, false otherwise.
     */
    public function renavam(string $renavam): bool
    {
        // Remove non-numeric characters
        $renavam = preg_replace('/[^0-9]/', '', $renavam);

        // Check length and disallow all zeros
        if (strlen($renavam) !== 11 || $renavam === '00000000000') {
            return false;
        }

        // Extract the base number and check digit
        $base = substr($renavam, 0, 10);
        $checkDigit = (int)$renavam[10];

        // Reverse the base number
        $reversedBase = strrev($base);

        // Define multipliers
        $multipliers = [2, 3, 4, 5, 6, 7, 8, 9];

        // Calculate the weighted sum
        $sum = 0;
        for ($i = 0; $i < 10; $i++) {
            $sum += (int)$reversedBase[$i] * $multipliers[$i % 8];
        }

        // Calculate the expected check digit
        $remainder = $sum % 11;
        $expectedCheckDigit = $remainder < 2 ? 0 : 11 - $remainder;

        // Return validation result
        return $checkDigit === $expectedCheckDigit;
    }

    /**
     * Validates a Brazilian vehicle chassis (VIN) number.
     *
     * This function checks the validity of a given chassis number by performing the following steps:
     * 1. Converts the chassis number to uppercase and removes any whitespace.
     * 2. Checks the length of the chassis number and ensures it does not contain forbidden characters (I, O, Q).
     * 3. Maps each character of the chassis number to its corresponding value.
     * 4. Calculates the weighted sum of the mapped values.
     * 5. Computes the expected check digit based on the weighted sum.
     * 6. Validates the check digit against the 9th position of the chassis number.
     *
     * @param string $chassis The chassis number to validate.
     * @return bool True if the chassis number is valid, false otherwise.
     */
    public function chassis(string $chassis): bool
    {
        // Convert to uppercase and remove whitespace
        $chassis = strtoupper(trim($chassis));

        // Check length and forbidden characters (I, O, Q)
        if (strlen($chassis) !== 17 || preg_match('/[IOQ]/', $chassis) || !preg_match('/^[A-Z0-9]+$/', $chassis)) {
            return false;
        }

        // VIN character-to-value mapping
        $map = array_merge(array_combine(range('A', 'H'), range(1, 8)), [
            'J' => 1, 'K' => 2, 'L' => 3, 'M' => 4, 'N' => 5,
            'P' => 7, 'R' => 9, 'S' => 2, 'T' => 3, 'U' => 4,
            'V' => 5, 'W' => 6, 'X' => 7, 'Y' => 8, 'Z' => 9,
        ]);

        // VIN weights
        $weights = [8, 7, 6, 5, 4, 3, 2, 10, 0, 9, 8, 7, 6, 5, 4, 3, 2];

        // Map each character to its value
        $values = array_map(function ($char) use ($map) {
            return is_numeric($char) ? (int)$char : $map[$char] ?? 0;
        }, str_split($chassis));

        // Calculate the weighted sum
        $sum = 0;
        foreach ($values as $i => $value) {
            $sum += $value * $weights[$i];
        }

        // Calculate the expected check digit
        $remainder = $sum % 11;
        $expectedCheckDigit = $remainder === 10 ? 'X' : (string)$remainder;

        // Validate the check digit (position 9 in VIN)
        return $chassis[8] === $expectedCheckDigit;
    }

    /**
     * Validates a vehicle category according to Brazilian standards.
     *
     * Input format: A single letter representing the vehicle category (e.g., A, B, C, D, E).
     *
     * @param string $category The vehicle category to validate.
     * @return bool True if the category is valid, false otherwise.
     */
    public function vehicleCategory(string $category): bool
    {
        $validCategories = ['A', 'B', 'C', 'D', 'E'];
        return in_array(strtoupper($category), $validCategories, true);
    }

}