<?php

namespace Tests\Usa;

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
        $validator = new CountryValidator();
        $this->personalValidator = $validator->usa()->personal();
    }

    /**
     * Tests if valid Social Security Numbers (SSN) are correctly validated.
     */
    public function testSsn()
    {
        $this->assertTrue($this->personalValidator->ssn('123-45-6789'));
        $this->assertTrue($this->personalValidator->ssn('123456789'));
        $this->assertFalse($this->personalValidator->ssn('000-00-0000')); // Invalid SSN
        $this->assertFalse($this->personalValidator->ssn('123-45-678')); // Too short
    }

    /**
     * Tests if valid US phone numbers are correctly validated.
     */
    public function testPhone()
    {
        $this->assertTrue($this->personalValidator->phone('(415) 555-2671'));
        $this->assertTrue($this->personalValidator->phone('415-555-2671'));
        $this->assertFalse($this->personalValidator->phone('123-456-7890')); // Invalid area code
        $this->assertFalse($this->personalValidator->phone('(415) 555-267')); // Too short
    }

    /**
     * Tests if valid dates of birth are correctly validated.
     */
    public function testBirthDate()
    {
        $this->assertTrue($this->personalValidator->birthDate('01/01/1990'));
        $this->assertFalse($this->personalValidator->birthDate('01/01/3000')); // Future date
        $this->assertFalse($this->personalValidator->birthDate('13/01/1990')); // Invalid month
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
     * Tests if valid email addresses are correctly validated.
     */
    public function testEmail()
    {
        $this->assertTrue($this->personalValidator->email('email@example.com'));
        $this->assertFalse($this->personalValidator->email('invalid-email.com')); // Missing @
        $this->assertFalse($this->personalValidator->email('email@.com')); // Missing domain
    }

    /**
     * Tests if valid passport numbers are correctly validated.
     */
    public function testPassport()
    {
        $this->assertTrue($this->personalValidator->passport('A12345678'));
        $this->assertFalse($this->personalValidator->passport('12345678')); // Missing letters
        $this->assertFalse($this->personalValidator->passport('A1234@678')); // Invalid characters
    }

    /**
     * Tests if valid driver’s license numbers are correctly validated by state.
     */
    public function testDriversLicense()
    {
        $this->assertTrue($this->personalValidator->driversLicense('A1234567', 'CA')); // Valid for California
        $this->assertTrue($this->personalValidator->driversLicense('12345678', 'TX')); // Valid for Texas
        $this->assertTrue($this->personalValidator->driversLicense('A123456789012', 'FL')); // Valid for Florida
        $this->assertTrue($this->personalValidator->driversLicense('A1234567', 'NY')); // Valid for New York
        $this->assertTrue($this->personalValidator->driversLicense('A123456789012', 'IL')); // Valid for Illinois

        $this->assertFalse($this->personalValidator->driversLicense('1234567', 'CA')); // Invalid for California: Missing letter
        $this->assertFalse($this->personalValidator->driversLicense('ABCDEFG', 'TX')); // Invalid for Texas: Letters not allowed
        $this->assertFalse($this->personalValidator->driversLicense('123456789012', 'FL')); // Invalid for Florida: Missing letter
        $this->assertFalse($this->personalValidator->driversLicense('ABCDEFG', 'NY')); // Invalid for New York: Invalid format
        $this->assertFalse($this->personalValidator->driversLicense('123456789012', 'IL')); // Invalid for Illinois: Missing letter
    }
}
