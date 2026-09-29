<?php

namespace CountryValidations\Brazil;

use CountryValidations\Support\Input;

class Vehicle
{
    private $config;

    public function __construct($config = [])
    {
        $this->config = $config;
    }
    
    public function plate(string $plate): bool
    {
        return preg_match('/\A(?:[A-Z]{3}-?[0-9]{4}|[A-Z]{3}[0-9][A-Z][0-9]{2})\z/', strtoupper($plate)) === 1;
    }

    /** Eleven-digit RENAVAM and modulo 11 check digit. */
    public function renavam(string $renavam): bool
    {
        $renavam = Input::digits($renavam);

        if (strlen($renavam) !== 11 || Input::repeated($renavam)) {
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

    /** VIN with the North American check-digit rule; use vin() for format only. */
    public function chassis(string $chassis): bool
    {
        $chassis = strtoupper($chassis);

        if (!$this->vin($chassis)) {
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

    /** Single driving-licence category A-E; combination classes are outside this method. */
    public function vehicleCategory(string $category): bool
    {
        $validCategories = ['A', 'B', 'C', 'D', 'E'];
        return in_array(strtoupper($category), $validCategories, true);
    }

    /** VIN characters and length; optional North American check digit. */
    public function vin(string $vin, bool $checkDigit = false): bool
    {
        $vin = strtoupper($vin);
        if (preg_match('/\A[A-HJ-NPR-Z0-9]{17}\z/', $vin) !== 1 || Input::repeated($vin)) {
            return false;
        }

        return !$checkDigit || $this->chassis($vin);
    }
}
