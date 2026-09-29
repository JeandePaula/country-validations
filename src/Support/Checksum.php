<?php

namespace CountryValidations\Support;

final class Checksum
{
    public static function luhn(string $digits): bool
    {
        return $digits !== '' && ctype_digit($digits) && self::mod10(substr($digits, 0, -1)) === (int) substr($digits, -1);
    }

    /** Modulo 10 check digit; also used for boleto fields. */
    public static function mod10(string $digits): int
    {
        $sum = 0;
        $weight = 2;
        for ($i = strlen($digits) - 1; $i >= 0; $i--) {
            $product = (int) $digits[$i] * $weight;
            $sum += intdiv($product, 10) + $product % 10;
            $weight = 3 - $weight;
        }

        return (10 - $sum % 10) % 10;
    }

    public static function weightedSum(string $digits, array $weights): int
    {
        $sum = 0;
        foreach ($weights as $index => $weight) {
            $sum += (int) $digits[$index] * $weight;
        }

        return $sum;
    }

    public static function mod11(string $digits): int
    {
        $sum = 0;
        $weight = 2;
        for ($i = strlen($digits) - 1; $i >= 0; $i--) {
            $sum += (int) $digits[$i] * $weight;
            $weight = $weight === 9 ? 2 : $weight + 1;
        }

        return $sum % 11;
    }

    /** Streaming arithmetic remains safe on 32-bit PHP, without BCMath. */
    public static function mod97(string $digits): int
    {
        $remainder = 0;
        for ($i = 0, $length = strlen($digits); $i < $length; $i++) {
            $remainder = ($remainder * 10 + (int) $digits[$i]) % 97;
        }

        return $remainder;
    }
}
