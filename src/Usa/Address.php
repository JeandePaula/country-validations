<?php

namespace CountryValidations\Usa;

class Address
{
    public function postalCode(string $code): bool
    {
        return preg_match('/\\A[0-9]{5}(?:-[0-9]{4})?\\z/', $code) === 1 && substr($code, 0, 5) !== '00000';
    }

    public function zipCode(string $code): bool
    {
        return $this->postalCode($code);
    }

    /** USPS states, possessions, freely associated states and military codes. */
    public function state(string $state): bool
    {
        return in_array(strtoupper($state), [
            'AL', 'AK', 'AZ', 'AR', 'CA', 'CO', 'CT', 'DE', 'DC', 'FL',
            'GA', 'HI', 'ID', 'IL', 'IN', 'IA', 'KS', 'KY', 'LA', 'ME',
            'MD', 'MA', 'MI', 'MN', 'MS', 'MO', 'MT', 'NE', 'NV', 'NH',
            'NJ', 'NM', 'NY', 'NC', 'ND', 'OH', 'OK', 'OR', 'PA', 'RI',
            'SC', 'SD', 'TN', 'TX', 'UT', 'VT', 'VA', 'WA', 'WV', 'WI',
            'WY', 'AS', 'GU', 'MP', 'PR', 'VI', 'FM', 'MH', 'PW', 'AA', 'AE', 'AP',
        ], true);
    }
}
