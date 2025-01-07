<?php

namespace Tests\Canada;

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
        $this->personalValidator = $validator->canada()->personal();
    }

    /**
     * Tests if valid Social Insurance Numbers (SIN) are correctly validated.
     */
    public function testSin()
    {
        $this->assertTrue($this->personalValidator->sin('046-454-286')); // Valid SIN with hyphens
        $this->assertTrue($this->personalValidator->sin('046454286'));   // Valid SIN without separators
        $this->assertTrue($this->personalValidator->sin('123 456 782')); // Valid SIN with spaces

        $this->assertFalse($this->personalValidator->sin('123-456-789')); // Fails the Luhn algorithm
        $this->assertFalse($this->personalValidator->sin('123456789'));   // Fails the Luhn algorithm
        $this->assertFalse($this->personalValidator->sin('000-000-000')); // All zeros
        $this->assertFalse($this->personalValidator->sin('046-454-28'));  // Too short
        $this->assertFalse($this->personalValidator->sin('0464542860'));  // Too long
        $this->assertFalse($this->personalValidator->sin('123-45A-789')); // Contains non-numeric characters
        $this->assertFalse($this->personalValidator->sin(''));           // Empty string
        $this->assertFalse($this->personalValidator->sin('046 454 287')); // Valid format but fails the Luhn algorithm
    }

    /**
     * Tests if valid Canadian phone numbers are correctly validated.
     */
    public function testPhone()
    {
        $this->assertTrue($this->personalValidator->phone('(416) 555-1234'));
        $this->assertTrue($this->personalValidator->phone('416-555-1234'));
        $this->assertFalse($this->personalValidator->phone('123-456-7890')); // Invalid area code
        $this->assertFalse($this->personalValidator->phone('(416) 555-123')); // Too short
    }

    /**
     * Tests if valid dates of birth are correctly validated.
     */
    public function testBirthDate()
    {
        $this->assertTrue($this->personalValidator->birthDate('1990-01-01')); // Valid date in YYYY-MM-DD format
        $this->assertTrue($this->personalValidator->birthDate('2000-12-31')); // Valid date at the end of the year

        $this->assertFalse($this->personalValidator->birthDate('2999-12-31')); // Future date
        $this->assertFalse($this->personalValidator->birthDate('1990-13-01')); // Invalid month
        $this->assertFalse($this->personalValidator->birthDate('1990-00-01')); // Invalid month (zero)
        $this->assertFalse($this->personalValidator->birthDate('1990-01-32')); // Invalid day
        $this->assertFalse($this->personalValidator->birthDate('1990-02-30')); // Invalid day in February
        $this->assertFalse($this->personalValidator->birthDate('abcd-01-01')); // Invalid year
        $this->assertFalse($this->personalValidator->birthDate('1990-1-1'));   // Incorrect format
        $this->assertFalse($this->personalValidator->birthDate(''));           // Empty string
    }

    /**
     * Tests if valid full names are correctly validated.
     */
    public function testFullName()
    {
        $this->assertTrue($this->personalValidator->fullName('John Smith'));
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
        $this->assertTrue($this->personalValidator->passport('AB123456'));
        $this->assertFalse($this->personalValidator->passport('12345678')); // Missing letters
        $this->assertFalse($this->personalValidator->passport('A1234567')); // Invalid format
    }

    /**
     * Tests if valid driver’s license numbers are correctly validated by province.
     */
    public function testDriversLicense()
    {
        $this->assertTrue($this->personalValidator->driversLicense('1234567', 'AB'));
        $this->assertTrue($this->personalValidator->driversLicense('A123456', 'ON'));
        $this->assertFalse($this->personalValidator->driversLicense('123456', 'ON')); // Invalid for Ontario
        $this->assertFalse($this->personalValidator->driversLicense('ABCDEFG', 'QC')); // Invalid for Quebec
    }
}
