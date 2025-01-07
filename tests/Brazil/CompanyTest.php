<?php

namespace Tests\Brazil;

use PHPUnit\Framework\TestCase;
use CountryValidations\CountryValidator;

class CompanyTest extends TestCase
{
    private $companyValidator;

    /**
     * Setup before each test.
     */
    protected function setUp(): void
    {
        $validator = new CountryValidator();
        $this->companyValidator = $validator->brazil()->company();
    }

    /**
     * Tests if valid CNPJ numbers are correctly validated.
     */
    public function testCnpj()
    {
        $this->assertTrue($this->companyValidator->cnpj('12.345.678/0001-95')); // Valid CNPJ with formatting
        $this->assertTrue($this->companyValidator->cnpj('12345678000195'));     // Valid CNPJ without formatting
        $this->assertFalse($this->companyValidator->cnpj('12.345.678/0001-96')); // Invalid check digits
        $this->assertFalse($this->companyValidator->cnpj('1234567890123'));      // Invalid length
        $this->assertFalse($this->companyValidator->cnpj('11111111111111'));     // Repeated sequence
    }

    /**
     * Tests if valid corporate names are correctly validated.
     */
    public function testCorporateName()
    {
        $this->assertTrue($this->companyValidator->corporateName('Valid Company Name Ltda.'));
        $this->assertFalse($this->companyValidator->corporateName('A')); // Too short
        $this->assertFalse($this->companyValidator->corporateName('Invalid@Name!')); // Invalid characters
    }

    /**
     * Tests if valid corporate phone numbers are correctly validated.
     */
    public function testPhone()
    {
        $this->assertTrue($this->companyValidator->phone('(11) 98765-4321')); // Valid (mobile)
        $this->assertTrue($this->companyValidator->phone('11987654321'));     // Valid (mobile without format)
        $this->assertTrue($this->companyValidator->phone('(11) 8765-4321'));  // Valid (landline)

        $this->assertFalse($this->companyValidator->phone('12345-6789'));     // Missing DDD
        $this->assertFalse($this->companyValidator->phone('(11) 9876-54321')); // Invalid (4+5 incorrect format)
        $this->assertFalse($this->companyValidator->phone('(11) 9765-4321'));  // Invalid (landline starting with 9)
        $this->assertFalse($this->companyValidator->phone('(00) 98765-4321')); // Invalid DDD
        $this->assertFalse($this->companyValidator->phone('(11) 98765-432A')); // Contains a letter
    }

    /**
     * Tests if valid corporate phone numbers without DDD are correctly validated.
     */
    public function testPhoneWithoutDDD()
    {
        $this->assertTrue($this->companyValidator->phoneWithoutDDD('98765-4321'));  // Mobile with mask
        $this->assertTrue($this->companyValidator->phoneWithoutDDD('987654321'));   // Mobile without mask
        $this->assertTrue($this->companyValidator->phoneWithoutDDD('2765-4321'));   // Landline with mask
        $this->assertTrue($this->companyValidator->phoneWithoutDDD('27654321'));    // Landline without mask

        $this->assertFalse($this->companyValidator->phoneWithoutDDD('11987654321')); // Contains DDD
        $this->assertFalse($this->companyValidator->phoneWithoutDDD('1234-56789'));  // Invalid format (landline starting with 1)
        $this->assertFalse($this->companyValidator->phoneWithoutDDD('2765-432A'));   // Contains a letter
        $this->assertFalse($this->companyValidator->phoneWithoutDDD('9765-4321'));   // Landline starting with 9
        $this->assertFalse($this->companyValidator->phoneWithoutDDD('98765432'));    // Mobile missing one digit
        $this->assertFalse($this->companyValidator->phoneWithoutDDD(''));            // Empty input
    }

    /**
     * Tests if valid corporate email addresses are correctly validated.
     */
    public function testEmail()
    {
        $this->assertTrue($this->companyValidator->email('contact@company.com'));
        $this->assertTrue($this->companyValidator->email('info@domain.co'));
        $this->assertFalse($this->companyValidator->email('invalid-email@com')); // Missing domain suffix
        $this->assertFalse($this->companyValidator->email('invalid@.com')); // Missing domain name
    }

    /**
     * Tests if valid state registration numbers are correctly validated.
     */
    public function testStateRegistration()
    {
        $this->assertTrue($this->companyValidator->stateRegistration('123456789'));
        $this->assertTrue($this->companyValidator->stateRegistration('12345678901234'));
        $this->assertFalse($this->companyValidator->stateRegistration('12345678')); // Too short
        $this->assertFalse($this->companyValidator->stateRegistration('123456789012345')); // Too long
    }

    /**
     * Tests if valid NIRE numbers are correctly validated.
     */
    public function testNire()
    {
        $this->assertTrue($this->companyValidator->nire('12345678901'));
        $this->assertFalse($this->companyValidator->nire('1234567890')); // Too short
        $this->assertFalse($this->companyValidator->nire('123456789012')); // Too long
        $this->assertFalse($this->companyValidator->nire('ABCDEFGHIJK')); // Invalid characters
    }
}
