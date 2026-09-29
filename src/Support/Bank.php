<?php

namespace CountryValidations\Support;

abstract class Bank
{
    protected $config;

    public function __construct($config = [])
    {
        $this->config = $config;
    }

    /** PAN length and Luhn; does not establish issuance or available funds. */
    public function cardNumber(string $cardNumber): bool
    {
        $digits = Input::digits($cardNumber, ' -');

        return strlen($digits) >= 12 && strlen($digits) <= 19
            && !Input::repeated($digits) && Checksum::luhn($digits);
    }

    /** SwiftNet FIN BIC shape (4-letter prefix); no country/institution assignment lookup. */
    public function swift(string $swift): bool
    {
        return preg_match('/\\A[A-Z]{4}[A-Z]{2}[A-Z0-9]{2}(?:[A-Z0-9]{3})?\\z/', strtoupper($swift)) === 1;
    }

    public function bin(string $bin): bool
    {
        return preg_match('/\\A(?:[0-9]{6}|[0-9]{8})\\z/', $bin) === 1;
    }
}
