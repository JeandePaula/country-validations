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
     * This method validates vehicle plates according to Brazilian standards:
     * - **Standard plates**: 3 letters followed by 4 digits (e.g., `ABC1234`).
     * - **Mercosul plates**: 3 letters, 1 digit, 1 letter, 2 digits (e.g., `ABC1D23`).
     * 
     * Validation process:
     * - Removes any non-alphanumeric characters.
     * - Converts the input to uppercase for uniformity.
     * - Matches the cleaned input against the predefined patterns for standard and Mercosul plates.
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
     * Validates a Brazilian RENAVAM (National Registry of Motor Vehicles) number.
     *
     * This method ensures that a provided RENAVAM number is valid by:
     * - Removing any non-numeric characters.
     * - Checking that the number is exactly 11 digits and not all zeros.
     * - Reversing the base number (first 10 digits).
     * - Calculating a weighted sum using predefined multipliers.
     * - Computing and comparing the check digit with the last digit of the RENAVAM.
     *
     * Example:
     * Input: `12345678901`
     * Validation: True if the check digit matches the calculated one.
     *
     * @param string $renavam The RENAVAM number to validate.
     * @return bool True if the RENAVAM number is valid, false otherwise.
     */
    public function renavam(string $renavam): bool
    {
        // Remove non-numeric characters
        $renavam = preg_replace('/[^0-9]/', '', $renavam);

        // Validate length and check for all zeros
        if (strlen($renavam) !== 11 || $renavam === '00000000000') {
            return false;
        }

        $base = substr($renavam, 0, 10);
        $checkDigit = (int)$renavam[10];
        $reversedBase = strrev($base);

        $multipliers = [2, 3, 4, 5, 6, 7, 8, 9];
        $sum = 0;

        foreach (str_split($reversedBase) as $i => $digit) {
            $sum += (int)$digit * $multipliers[$i % 8];
        }

        $remainder = $sum % 11;
        $expectedCheckDigit = $remainder < 2 ? 0 : 11 - $remainder;

        return $checkDigit === $expectedCheckDigit;
    }

    /**
     * Validates a Brazilian vehicle chassis (VIN) number.
     *
     * This method validates a Vehicle Identification Number (VIN) by:
     * - Removing any whitespace and converting the input to uppercase.
     * - Ensuring the VIN is exactly 17 characters long and does not contain invalid characters (`I`, `O`, `Q`).
     * - Mapping each character to its numeric value based on predefined rules.
     * - Calculating a weighted sum of the mapped values.
     * - Comparing the computed check digit with the 9th position of the VIN.
     *
     * Example:
     * Input: `1HGCM82633A123456`
     * Validation: True if the check digit matches the calculated one.
     *
     * @param string $chassis The chassis number to validate.
     * @return bool True if the chassis number is valid, false otherwise.
     */
    public function chassis(string $chassis): bool
    {
        $chassis = strtoupper(trim($chassis));

        if (strlen($chassis) !== 17 || preg_match('/[IOQ]/', $chassis) || !preg_match('/^[A-Z0-9]+$/', $chassis)) {
            return false;
        }

        $map = array_merge(array_combine(range('A', 'H'), range(1, 8)), [
            'J' => 1, 'K' => 2, 'L' => 3, 'M' => 4, 'N' => 5,
            'P' => 7, 'R' => 9, 'S' => 2, 'T' => 3, 'U' => 4,
            'V' => 5, 'W' => 6, 'X' => 7, 'Y' => 8, 'Z' => 9,
        ]);

        $weights = [8, 7, 6, 5, 4, 3, 2, 10, 0, 9, 8, 7, 6, 5, 4, 3, 2];

        $values = array_map(function ($char) use ($map) {
            return is_numeric($char) ? (int)$char : $map[$char] ?? 0;
        }, str_split($chassis));

        $sum = 0;
        foreach ($values as $i => $value) {
            $sum += $value * $weights[$i];
        }

        $remainder = $sum % 11;
        $expectedCheckDigit = $remainder === 10 ? 'X' : (string)$remainder;

        return $chassis[8] === $expectedCheckDigit;
    }

    /**
     * Validates a vehicle category according to Brazilian standards.
     *
     * This method ensures the provided vehicle category is one of the following:
     * - `A`: Motorcycles
     * - `B`: Passenger vehicles
     * - `C`: Cargo vehicles
     * - `D`: Passenger vehicles for public transport
     * - `E`: Articulated vehicles or those requiring special training
     *
     * Validation process:
     * - Converts the input to uppercase.
     * - Checks if the input matches one of the predefined valid categories.
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
