<?php

namespace CountryValidations\Brazil;

class Currency
{
    private $config;

    public function __construct($config = [])
    {
        $this->config = $config;
    }

    public function brlFormat(string $value): bool
    {
        $value = preg_replace('/\AR\$ ?/', '', $value);

        return $this->brazilianNumericFormat($value);
    }

    public function exchangeRate(string $rate): bool
    {
        return preg_match('/\A[0-9]+(?:\.[0-9]{1,4})?\z/', $rate) === 1
            && is_finite((float) $rate) && (float) $rate > 0;
    }

    public function positiveAmount(float $amount): bool
    {
        return is_finite($amount) && $amount > 0;
    }

    public function withinLimit(float $amount, float $limit): bool
    {
        return is_finite($amount) && is_finite($limit) && $amount <= $limit;
    }

    public function brazilianNumericFormat(string $number): bool
    {
        return preg_match('/\A(?:[0-9]+|[0-9]{1,3}(?:\.[0-9]{3})+),[0-9]{2}\z/', $number) === 1;
    }

    public function convertToFloat(string $number): float
    {
        if (!$this->brazilianNumericFormat($number)) {
            throw new \InvalidArgumentException('Expected a Brazilian decimal amount, e.g. 1.234,56.');
        }
        $amount = (float) str_replace(['.', ','], ['', '.'], $number);
        if (!is_finite($amount)) {
            throw new \InvalidArgumentException('Amount is outside the supported floating-point range.');
        }

        return $amount;
    }

    public function percentage(float $percentage): bool
    {
        return is_finite($percentage) && $percentage >= 0 && $percentage <= 100;
    }

    public function decimalPlaces(float $number): bool
    {
        return is_finite($number) && $number >= 0 && round($number, 2) === $number;
    }

    public function amountInRange(float $amount, float $min, float $max): bool
    {
        return is_finite($amount) && is_finite($min) && is_finite($max) && $amount >= $min && $amount <= $max;
    }
}
