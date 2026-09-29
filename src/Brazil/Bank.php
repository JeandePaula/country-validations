<?php

namespace CountryValidations\Brazil;

use CountryValidations\Support\Checksum;

class Bank extends \CountryValidations\Support\Bank
{
    /** COMPE format only, not a bank directory lookup. */
    public function bankCode(string $code): bool
    {
        return preg_match('/\A[0-9]{3}\z/', $code) === 1;
    }

    public function branch(string $branch): bool
    {
        return preg_match('/\A[0-9]{4}\z/', $branch) === 1;
    }

    /** Legacy account format only; banks have different account and DV rules. */
    public function accountNumber(string $account): bool
    {
        return preg_match('/\A[0-9]{5,12}-[0-9]\z/', $account) === 1;
    }

    public function boleto(string $boleto): bool
    {
        return Boleto::validate($boleto);
    }

    public function checkCompensationCode(string $code): bool
    {
        return preg_match('/\A[0-9]{8}\z/', $code) === 1;
    }

    /** Checks the bundled historical snapshot, or the caller-supplied ISPB map. */
    public function ispb(string $ispb): bool
    {
        return preg_match('/\A[0-9]{8}\z/', $ispb) === 1
            && array_key_exists($ispb, (new Helpers($this->config))->getIspbList());
    }

    /** Brazilian BBAN structure and modulo 97, including printed spaces. */
    public function iban(string $iban): bool
    {
        $iban = str_replace(' ', '', strtoupper($iban));
        if (preg_match('/\ABR[0-9]{25}[A-Z][A-Z0-9]\z/', $iban) !== 1) {
            return false;
        }
        $checkDigits = (int) substr($iban, 2, 2);
        if ($checkDigits < 2 || $checkDigits > 98) {
            return false;
        }
        $numeric = preg_replace_callback('/[A-Z]/', static function (array $match): string {
            return (string) (ord($match[0]) - 55);
        }, substr($iban, 4) . substr($iban, 0, 4));

        return Checksum::mod97($numeric) === 1;
    }
}
