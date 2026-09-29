<?php

namespace CountryValidations\Brazil;

use CountryValidations\Support\Input;

class Company
{
    private $config;

    public function __construct($config = [])
    {
        $this->config = $config;
    }

    /** Numeric and alphanumeric CNPJ, including both Receita Federal check digits. */
    public function cnpj(string $cnpj): bool
    {
        $cnpj = strtoupper($cnpj);
        if (preg_match('/\A(?:[A-Z0-9]{12}[0-9]{2}|[A-Z0-9]{2}\.[A-Z0-9]{3}\.[A-Z0-9]{3}\/[A-Z0-9]{4}-[0-9]{2})\z/', $cnpj) !== 1) {
            return false;
        }
        $cnpj = str_replace(['.', '/', '-'], '', $cnpj);
        if (Input::repeated($cnpj)) {
            return false;
        }
        foreach ([12, 13] as $length) {
            $sum = 0;
            $weight = 2;
            for ($i = $length - 1; $i >= 0; $i--) {
                $sum += (ord($cnpj[$i]) - 48) * $weight;
                $weight = $weight === 9 ? 2 : $weight + 1;
            }
            $digit = $sum % 11 < 2 ? 0 : 11 - $sum % 11;
            if ((int) $cnpj[$length] !== $digit) {
                return false;
            }
        }

        return true;
    }

    public function corporateName(string $name): bool
    {
        return preg_match('/\A[\p{L}\p{M}0-9 .,&()%#\'’\-]{5,}\z/u', $name) === 1
            && preg_match('/[\p{L}0-9]/u', $name) === 1;
    }

    public function phone(string $phone): bool
    {
        return (new Helpers())->phone($phone);
    }

    public function phoneWithoutDDD(string $phone): bool
    {
        return (new Helpers())->phoneWithoutDDD($phone);
    }

    public function email(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /** Legacy length check only: state-specific IE algorithms are not implemented. */
    public function stateRegistration(string $stateRegistration): bool
    {
        $digits = Input::digits($stateRegistration, '.-');

        return preg_match('/\A[0-9]{9,14}\z/', $digits) === 1 && !Input::repeated($digits);
    }

    /** NIRE syntax only. */
    public function nire(string $nire): bool
    {
        $digits = Input::digits($nire, '.-');

        return strlen($digits) === 11 && !Input::repeated($digits);
    }
}
