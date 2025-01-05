<?php

namespace Tests\Brazil;

use PHPUnit\Framework\TestCase;
use CountryValidations\CountryValidator;

class PersonalTest extends TestCase
{
    private $personalValidator;

    /**
     * Setup before each test.
     */
    protected function setUp(): void
    {
        $this->personalValidator = CountryValidator::brazil()->personal();
    }

    /**
     * Tests if valid CPF numbers are correctly validated.
     */
    public function testCpf()
    {
        $this->assertTrue($this->personalValidator->cpf('123.456.789-09'));
        $this->assertTrue($this->personalValidator->cpf('12345678909'));
        $this->assertFalse($this->personalValidator->cpf('111.111.111-11')); // Repeated digits
        $this->assertFalse($this->personalValidator->cpf('123.456.789-00')); // Invalid CPF
    }

    /**
     * Tests if valid RG numbers are correctly validated.
     */
    public function testRg()
    {
        $this->assertTrue($this->personalValidator->rg('12345678'));
        $this->assertTrue($this->personalValidator->rg('123456789'));
        $this->assertFalse($this->personalValidator->rg('1234')); // Too short
        $this->assertFalse($this->personalValidator->rg('1234567890')); // Too long
    }

    /**
     * Tests if valid CNS numbers are correctly validated.
     */
    public function testCns()
    {
        $this->assertTrue($this->personalValidator->cns('123456789012345')); // Valid, starts with 1
        $this->assertTrue($this->personalValidator->cns('223456789012345')); // Valid, starts with 2
        $this->assertTrue($this->personalValidator->cns('700123456789012')); // Valid, starts with 7
        $this->assertTrue($this->personalValidator->cns('800123456789012')); // Valid, starts with 8
        $this->assertTrue($this->personalValidator->cns('900123456789012')); // Valid, starts with 9

        $this->assertFalse($this->personalValidator->cns('000000000000000')); // All zeros
        $this->assertFalse($this->personalValidator->cns('323456789012345')); // Invalid first digit
        $this->assertFalse($this->personalValidator->cns('12345678901234'));  // Too short
        $this->assertFalse($this->personalValidator->cns('1234567890123456')); // Too long
        $this->assertFalse($this->personalValidator->cns('12345678901234A')); // Contains non-numeric character
    }

    /**
     * Tests if valid birth dates are correctly validated.
     */
    public function testBirthDate()
    {
        $this->assertTrue($this->personalValidator->birthDate('2000-01-01'));
        $this->assertFalse($this->personalValidator->birthDate('3000-01-01')); // Future date
        $this->assertFalse($this->personalValidator->birthDate('2000-13-01')); // Invalid month
    }

    /**
     * Tests if valid full names are correctly validated.
     */
    public function testFullName()
    {
        $this->assertTrue($this->personalValidator->fullName('John Doe'));
        $this->assertFalse($this->personalValidator->fullName('John')); // Single word
        $this->assertFalse($this->personalValidator->fullName('')); // Empty string
    }

    /**
     * Tests if valid PISPASEP numbers are correctly validated.
     */
    public function testPisPasep()
    {
        $this->assertTrue($this->personalValidator->pisPasep('639.22570.10-6'));
        $this->assertTrue($this->personalValidator->pisPasep('51847159587'));

        $this->assertFalse($this->personalValidator->pisPasep('123.45678.90-2')); // Invalid check digit
        $this->assertFalse($this->personalValidator->pisPasep('111.11111.11-1')); // Repeated digits
        $this->assertFalse($this->personalValidator->pisPasep('12345'));          // Too short
        $this->assertFalse($this->personalValidator->pisPasep('1234567890123'));  // Too long
        $this->assertFalse($this->personalValidator->pisPasep('123.456A8.90-1')); // Contains non-numeric character
        $this->assertFalse($this->personalValidator->pisPasep('000.00000.00-0')); // All zeros
    }

    /**
     * Tests if valid voter registration numbers are correctly validated.
     */
    public function testTituloEleitor()
    {
        $this->assertTrue($this->personalValidator->tituloEleitor('558055510652'));
        $this->assertTrue($this->personalValidator->tituloEleitor('280567082087'));

        $this->assertFalse($this->personalValidator->tituloEleitor('12345678901'));
        $this->assertFalse($this->personalValidator->tituloEleitor('1234567890123'));
        $this->assertFalse($this->personalValidator->tituloEleitor('12345678901A2'));
        $this->assertFalse($this->personalValidator->tituloEleitor('111111111111'));
        $this->assertFalse($this->personalValidator->tituloEleitor('123456789013'));
    }

    /**
     * Tests if valid email addresses are correctly validated.
     */
    public function testEmail()
    {
        $this->assertTrue($this->personalValidator->email('email@example.com'));
        $this->assertFalse($this->personalValidator->email('invalid-email.com')); // Missing @
        $this->assertFalse($this->personalValidator->email('email@.com')); // Missing domain
    }

    /**
     * Tests if valid CNH numbers are correctly validated.
     */
    public function testCnh()
    {
        $this->assertTrue($this->personalValidator->cnh('12345678900'));
        $this->assertFalse($this->personalValidator->cnh('12345678901')); // Invalid check digit
        $this->assertFalse($this->personalValidator->cnh('11111111111')); // Repeated digits
    }

    /**
     * Tests if valid CIN numbers are correctly validated (alias for CPF).
     */
    public function testCin()
    {
        $this->assertTrue($this->personalValidator->cin('123.456.789-09'));
        $this->assertFalse($this->personalValidator->cin('111.111.111-11')); // Invalid CPF
    }

    /**
     * Tests if valid passport numbers are correctly validated.
     */
    public function testPassport()
    {
        $this->assertTrue($this->personalValidator->passport('AB123456'));
        $this->assertFalse($this->personalValidator->passport('ABC1234567')); // Invalid format
        $this->assertFalse($this->personalValidator->passport('12345678')); // Missing letters
    }

    /**
     * Tests if valid phone numbers are correctly validated.
     */
    public function testPhone()
    {
        $this->assertTrue($this->personalValidator->phone('(11) 98765-4321'));
        $this->assertFalse($this->personalValidator->phone('12345-6789')); // Missing DDD
    }

    /**
     * Tests if valid phone numbers without DDD are correctly validated.
     */
    public function testPhoneWithoutDDD()
    {
        $this->assertTrue($this->personalValidator->phoneWithoutDDD('987654321'));
        $this->assertTrue($this->personalValidator->phoneWithoutDDD('23456789'));
        $this->assertTrue($this->personalValidator->phoneWithoutDDD('3456-7890'));
        $this->assertTrue($this->personalValidator->phoneWithoutDDD('98765-4321'));

        $this->assertFalse($this->personalValidator->phoneWithoutDDD('12345678'));
        $this->assertFalse($this->personalValidator->phoneWithoutDDD('(11) 98765-4321'));
        $this->assertFalse($this->personalValidator->phoneWithoutDDD('9876543210'));
        $this->assertFalse($this->personalValidator->phoneWithoutDDD('1234567'));
        $this->assertFalse($this->personalValidator->phoneWithoutDDD('98765A4321'));
        $this->assertFalse($this->personalValidator->phoneWithoutDDD(''));
    }
}
