<?php

namespace CountryValidations\Brazil;

use CountryValidations\Support\Checksum;
use CountryValidations\Support\Input;

/** Internal FEBRABAN linha digitavel checks; no payment/registration lookup. */
final class Boleto
{
    public static function validate(string $value): bool
    {
        $digits = Input::digits($value, ' .-');
        if (strlen($digits) === 47) {
            return self::bank($digits);
        }
        if (strlen($digits) === 48) {
            return self::collection($digits);
        }

        return false;
    }

    private static function bank(string $line): bool
    {
        if (substr($line, 0, 3) === '000' || $line[3] !== '9') {
            return false;
        }
        foreach ([[0, 9], [10, 10], [21, 10]] as [$start, $length]) {
            if (Checksum::mod10(substr($line, $start, $length)) !== (int) $line[$start + $length]) {
                return false;
            }
        }
        $barcode = substr($line, 0, 4) . substr($line, 33)
            . substr($line, 4, 5) . substr($line, 10, 10) . substr($line, 21, 10);
        $digit = 11 - Checksum::mod11($barcode);
        $digit = $digit >= 10 || $digit === 0 ? 1 : $digit;

        return (int) $line[32] === $digit;
    }

    private static function collection(string $line): bool
    {
        if ($line[0] !== '8' || strpos('12345679', $line[1]) === false || strpos('6789', $line[2]) === false) {
            return false;
        }
        $mod10 = $line[2] === '6' || $line[2] === '7';
        $barcode = '';
        for ($offset = 0; $offset < 48; $offset += 12) {
            $block = substr($line, $offset, 11);
            if (self::collectionDigit($block, $mod10) !== (int) $line[$offset + 11]) {
                return false;
            }
            $barcode .= $block;
        }

        return (int) $barcode[3] === self::collectionDigit(substr($barcode, 0, 3) . substr($barcode, 4), $mod10);
    }

    private static function collectionDigit(string $digits, bool $mod10): int
    {
        if ($mod10) {
            return Checksum::mod10($digits);
        }
        $remainder = Checksum::mod11($digits);

        return $remainder < 2 ? 0 : 11 - $remainder;
    }
}
