<?php

namespace Tests;

use CountryValidations\CountryValidator;
use PHPUnit\Framework\TestCase;

class AddressTest extends TestCase
{
    /** @dataProvider postalProvider */
    public function testPostalCodes(string $country, string $value, bool $expected): void
    {
        $this->assertSame($expected, (new CountryValidator())->country($country)->address()->postalCode($value));
    }

    public static function postalProvider(): array
    {
        return [
            ['BR', '01310-100', true], ['BR', '01310100', true],
            ['BR', '00000000', false], ['BR', '0131-0100', false], ['BR', '013101000', false],
            ['BR', 'a01310100', false], ['BR', "01310100\n", false],
            ['CA', 'K1A 0B1', true], ['CA', 'k1a0b1', true], ['CA', 'H0H 0H0', true],
            ['CA', 'D1A 0B1', false], ['CA', 'W1A 0B1', false], ['CA', 'Z1A 0B1', false],
            ['CA', 'K1D 0B1', false], ['CA', 'K1A 0U1', false], ['CA', 'K1A-0B1', false],
            ['CA', "K1A 0B1\n", false], ['CA', 'K1A  0B1', false],
            ['US', '00501', true], ['US', '90210-1234', true],
            ['US', '00000', false], ['US', '00000-1234', false],
            ['US', '902101234', false], ['US', '9021', false], ['US', "90210\n", false],
        ];
    }

    public function testRegionListsAndAliases(): void
    {
        $v = new CountryValidator();
        foreach (explode(' ', 'AC AL AP AM BA CE DF ES GO MA MT MS MG PA PB PR PE PI RJ RN RS RO RR SC SP SE TO') as $state) {
            $this->assertTrue($v->brazil()->address()->state(strtolower($state)));
        }
        foreach (explode(' ', 'AB BC MB NB NL NS NT NU ON PE QC SK YT') as $province) {
            $this->assertTrue($v->canada()->address()->province($province));
        }
        foreach (['CA', 'DC', 'PR', 'VI', 'AA', 'AE', 'AP', 'FM', 'MH', 'PW'] as $state) {
            $this->assertTrue($v->usa()->address()->state($state));
        }
        $this->assertFalse($v->brazil()->address()->state('XX'));
        $this->assertFalse($v->canada()->address()->province('ZZ'));
        $this->assertFalse($v->usa()->address()->state('US'));
        $this->assertTrue($v->brazil()->address()->cep('01310-100'));
        $this->assertTrue($v->usa()->address()->zipCode('00501'));
    }
}
