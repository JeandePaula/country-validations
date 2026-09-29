<?php

namespace CountryValidations\Brazil;

use CountryValidations\Support\Checksum;
use CountryValidations\Support\Input;

class Personal extends \CountryValidations\Support\Personal
{
    public function cpf(string $cpf): bool
    {
        if (preg_match('/\A(?:[0-9]{11}|[0-9]{3}\.[0-9]{3}\.[0-9]{3}-[0-9]{2})\z/', $cpf) !== 1) {
            return false;
        }
        $cpf = Input::digits($cpf, '.-');
        if (Input::repeated($cpf)) {
            return false;
        }
        for ($length = 9; $length <= 10; $length++) {
            $sum = Checksum::weightedSum($cpf, range($length + 1, 2));
            if ((int) $cpf[$length] !== ($sum * 10 % 11) % 10) {
                return false;
            }
        }

        return true;
    }

    /** Legacy RG length heuristic, not a state-specific checksum or CIN check. */
    public function rg(string $rg, string $state = 'SP'): bool
    {
        $state = strtoupper($state);
        if (!(new Address())->state($state) || preg_match('/\A[0-9]{6,10}X?\z/i', $rg) !== 1) {
            return false;
        }
        $lengths = ['SP' => 9, 'RJ' => 9, 'MG' => 9, 'RS' => 10, 'PR' => 10, 'SC' => 10];
        $max = $lengths[$state] ?? 8;

        return strlen($rg) >= $max - 1 && strlen($rg) <= $max && !Input::repeated($rg);
    }

    /** Definitive and provisional CNS checksums, following the ANS algorithms. */
    public function cns(string $cns): bool
    {
        if (preg_match('/\A[12789][0-9]{14}\z/', $cns) !== 1 || Input::repeated($cns)) {
            return false;
        }
        if (in_array($cns[0], ['7', '8', '9'], true)) {
            return Checksum::weightedSum($cns, range(15, 1)) % 11 === 0;
        }

        $sum = Checksum::weightedSum($cns, range(15, 5));
        $digit = (11 - $sum % 11) % 11;
        $suffix = $digit === 10 ? '001' . (11 - ($sum + 2) % 11) : '000' . $digit;

        return substr($cns, 11) === $suffix;
    }

    public function birthDate(string $date): bool
    {
        return $this->validBirthDate($date, 'Y-m-d');
    }

    public function pisPasep(string $pisPasep): bool
    {
        if (preg_match('/\A(?:[0-9]{11}|[0-9]{3}\.[0-9]{5}\.[0-9]{2}-[0-9])\z/', $pisPasep) !== 1) {
            return false;
        }
        $digits = Input::digits($pisPasep, '.-');
        $remainder = Checksum::weightedSum($digits, [3, 2, 9, 8, 7, 6, 5, 4, 3, 2]) % 11;

        return !Input::repeated($digits) && (int) $digits[10] === ($remainder < 2 ? 0 : 11 - $remainder);
    }

    public function tituloEleitor(string $titulo): bool
    {
        if (preg_match('/\A(?:[0-9]{12}|[0-9]{4} [0-9]{4} [0-9]{4})\z/', $titulo) !== 1) {
            return false;
        }
        $digits = Input::digits($titulo, ' ');
        $state = (int) substr($digits, 8, 2);
        if ($state < 1 || $state > 28 || Input::repeated($digits)) {
            return false;
        }

        $first = Checksum::weightedSum($digits, range(2, 9)) % 11 % 10;
        $second = ((int) $digits[8] * 7 + (int) $digits[9] * 8 + $first * 9) % 11 % 10;

        return substr($digits, 10) === (string) $first . $second;
    }

    public function cnh(string $cnh): bool
    {
        if (preg_match('/\A[0-9]{11}\z/', $cnh) !== 1 || Input::repeated($cnh)) {
            return false;
        }
        $firstRemainder = Checksum::weightedSum($cnh, range(9, 1)) % 11;
        $first = $firstRemainder === 10 ? 0 : $firstRemainder;
        $secondRemainder = Checksum::weightedSum($cnh, range(1, 9)) % 11;
        $second = $secondRemainder - ($firstRemainder === 10 ? 2 : 0);
        $second = $second < 0 ? $second + 11 : ($second === 10 ? 0 : $second);

        return substr($cnh, 9) === (string) $first . $second;
    }

    public function cin(string $numero): bool
    {
        return $this->cpf($numero);
    }

    public function passport(string $passportNumber): bool
    {
        return preg_match('/\A[A-Z]{2}[0-9]{6}\z/', strtoupper($passportNumber)) === 1;
    }

    public function phone(string $phone): bool
    {
        return (new Helpers())->phone($phone);
    }

    public function phoneWithoutDDD(string $phone): bool
    {
        return (new Helpers())->phoneWithoutDDD($phone);
    }
}
