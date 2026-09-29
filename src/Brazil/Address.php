<?php

namespace CountryValidations\Brazil;

class Address
{
    /** CEP syntax only; no street lookup. */
    public function postalCode(string $code): bool
    {
        return preg_match('/\\A[0-9]{5}-?[0-9]{3}\\z/', $code) === 1 && str_replace('-', '', $code) !== '00000000';
    }

    public function cep(string $code): bool
    {
        return $this->postalCode($code);
    }

    public function state(string $state): bool
    {
        return in_array(strtoupper($state), [
            'AC', 'AL', 'AP', 'AM', 'BA', 'CE', 'DF', 'ES', 'GO',
            'MA', 'MT', 'MS', 'MG', 'PA', 'PB', 'PR', 'PE', 'PI',
            'RJ', 'RN', 'RS', 'RO', 'RR', 'SC', 'SP', 'SE', 'TO',
        ], true);
    }
}
