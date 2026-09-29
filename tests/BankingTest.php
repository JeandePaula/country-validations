<?php

namespace Tests;

use CountryValidations\CountryValidator;
use PHPUnit\Framework\TestCase;

class BankingTest extends TestCase
{
    public function testSharedBanking(): void
    {
        $v = new CountryValidator();
        foreach ([$v->brazil()->bank(), $v->canada()->bank(), $v->usa()->bank()] as $bank) {
            foreach (['4111111111111111', '4111 1111 1111 1111', '378282246310005', '6011111111111117'] as $number) {
                $this->assertTrue($bank->cardNumber($number));
            }
            foreach (['', '0', '0000000000000000', '4111111111111112', 'abc4111111111111111', "4111111111111111\n", '41111111111111111111'] as $number) {
                $this->assertFalse($bank->cardNumber($number));
            }
            $this->assertTrue($bank->swift('DEUTDEFF500'));
            $this->assertTrue($bank->swift('deutdeff'));
            $this->assertFalse($bank->swift('12345678'));
            $this->assertFalse($bank->swift('DEUT12FF'));
            $this->assertFalse($bank->swift("DEUTDEFF\n"));
            $this->assertTrue($bank->bin('12345678'));
            $this->assertFalse($bank->bin('1234567'));
        }
    }

    public function testUsRoutingNumbers(): void
    {
        $bank = (new CountryValidator())->usa()->bank();
        foreach (['021000021', '011000015', '121000248', '000090007'] as $number) {
            $this->assertTrue($bank->routingNumber($number), $number);
            for ($position = 0; $position < 9; $position++) {
                $mutated = $number;
                $mutated[$position] = (string) (((int) $number[$position] + 1) % 10);
                $this->assertFalse($bank->routingNumber($mutated), $mutated);
            }
        }
        foreach (['000000000', '990000009', '02100002', '021-000-021', "021000021\n"] as $number) {
            $this->assertFalse($bank->routingNumber($number));
        }
    }

    public function testCanadianRouting(): void
    {
        $bank = (new CountryValidator())->canada()->bank();
        $this->assertTrue($bank->institutionNumber('004'));
        $this->assertFalse($bank->institutionNumber('000'));
        $this->assertFalse($bank->institutionNumber('4'));
        $this->assertTrue($bank->transitNumber('12345'));
        $this->assertTrue($bank->transitNumber('00000'));
        $this->assertFalse($bank->transitNumber('123456'));
        $this->assertTrue($bank->routingNumber('000412345'));
        $this->assertTrue($bank->routingNumber('12345-004'));
        foreach (['100412345', '000012345', '12345-000', '00412345', "000412345\n"] as $number) {
            $this->assertFalse($bank->routingNumber($number));
        }
    }

    public function testBrazilianIbanOfficialRegistryExample(): void
    {
        $bank = (new CountryValidator())->brazil()->bank();
        $this->assertTrue($bank->iban('BR1800360305000010009795493C1'));
        $this->assertTrue($bank->iban('br18 0036 0305 0000 1000 9795 493c 1'));
        $this->assertFalse($bank->iban('BR1800360305000010009795493C2'));
        $this->assertFalse($bank->iban('BR180036030500001000979549311'));
        // Remainder alone would accept this, but 00 is not a valid IBAN check field.
        $this->assertFalse($bank->iban('BR0000000000000000000000047C1'));
        $this->assertFalse($bank->iban("BR1800360305000010009795493C1\n"));
    }
}
