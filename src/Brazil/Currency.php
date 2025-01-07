<?php

namespace CountryValidations\Brazil;

class Currency
{
    private $config;

    public function __construct($config = [])
    {
        $this->config = $config;
    }

    /**
     * Validates if the format matches Brazilian currency (e.g., R$ 1.234,56).
     * 
     * Input format:
     * - May include the "R$" prefix (optional).
     * - Must follow the Brazilian numeric format: groups of thousands separated by dots and decimals separated by a comma.
     * 
     * @param string $value Currency value to validate.
     * @return bool True if the format is valid, false otherwise.
     */
    public function brlFormat(string $value): bool
    {
        // Remove prefix 'R$' and any surrounding whitespace
        $value = preg_replace('/^R\\$\\s?/', '', trim($value));
    
        // Validate the remaining numeric format (e.g., 1.234,56)
        return preg_match('/^\\d{1,3}(\\.\\d{3})*,\\d{2}$/', $value) === 1;
    }

    /**
     * Validates exchange rates or numeric quotations.
     * 
     * Input format:
     * - A positive decimal number with up to 4 decimal places (e.g., 3.4567 or 4.00).
     * 
     * @param string $rate Exchange rate to validate.
     * @return bool True if valid, false otherwise.
     */
    public function exchangeRate(string $rate): bool
    {
        $rate = trim($rate);
        return preg_match('/^\d+(\.\d{1,4})?$/', $rate) === 1;
    }

    /**
     * Validates if a monetary value is positive.
     * 
     * @param float $amount Monetary value to validate.
     * @return bool True if the value is greater than 0, false otherwise.
     */
    public function positiveAmount(float $amount): bool
    {
        return $amount > 0;
    }

    /**
     * Validates if a monetary value does not exceed a specified limit.
     * 
     * @param float $amount Monetary value to validate.
     * @param float $limit Maximum allowed value.
     * @return bool True if the value is within the limit, false otherwise.
     */
    public function withinLimit(float $amount, float $limit): bool
    {
        return $amount <= $limit;
    }

    /**
     * Validates if a number matches the Brazilian numeric format (e.g., 1.234,56).
     * 
     * Input format:
     * - Groups of thousands separated by dots.
     * - Decimals separated by a comma.
     * 
     * @param string $number Numeric string to validate.
     * @return bool True if valid, false otherwise.
     */
    public function brazilianNumericFormat(string $number): bool
    {
        $number = trim($number);
        return preg_match('/^\d{1,3}(\.\d{3})*,\d{2}$/', $number) === 1;
    }

    /**
     * Converts a Brazilian formatted numeric string to a float.
     * 
     * Conversion rules:
     * - Removes dots used as thousand separators.
     * - Replaces commas with dots to standardize decimal notation.
     * 
     * @param string $number Numeric string in Brazilian format.
     * @return float Converted float value.
     */
    public function convertToFloat(string $number): float
    {
        $normalized = str_replace(['.', ','], ['', '.'], trim($number));
        return (float) $normalized;
    }

    /**
     * Validates if a value is a valid percentage (0 to 100 inclusive).
     * 
     * @param float $percentage Percentage value to validate.
     * @return bool True if the value is between 0 and 100, false otherwise.
     */
    public function percentage(float $percentage): bool
    {
        return $percentage >= 0 && $percentage <= 100;
    }

    /**
     * Validates if a number has up to two decimal places.
     * 
     * Input format:
     * - A positive decimal number with at most 2 decimal places (e.g., 12.34 or 45).
     * 
     * @param float $number Number to validate.
     * @return bool True if valid, false otherwise.
     */
    public function decimalPlaces(float $number): bool
    {
        return preg_match('/^\d+(\.\d{1,2})?$/', (string) $number) === 1;
    }

    /**
     * Validates if a monetary value is within a specified range.
     * 
     * @param float $amount Monetary value to validate.
     * @param float $min Minimum allowed value.
     * @param float $max Maximum allowed value.
     * @return bool True if the value is between the minimum and maximum, inclusive, false otherwise.
     */
    public function amountInRange(float $amount, float $min, float $max): bool
    {
        return $amount >= $min && $amount <= $max;
    }
}
