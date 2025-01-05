<?php

namespace Tests\Brazil;

use PHPUnit\Framework\TestCase;
use CountryValidations\CountryValidator;

class VehicleTest extends TestCase
{
    private $vehicleValidator;

    /**
     * Setup before each test.
     */
    protected function setUp(): void
    {
        $this->vehicleValidator = CountryValidator::brazil()->vehicle();
    }

    /**
     * Tests if valid Brazilian vehicle plates are correctly validated.
     */
    public function testPlate()
    {
        $this->assertTrue($this->vehicleValidator->plate('ABC1234')); // Standard plate
        $this->assertTrue($this->vehicleValidator->plate('ABC1D23')); // Mercosul plate
        $this->assertFalse($this->vehicleValidator->plate('AB12345')); // Invalid format
        $this->assertFalse($this->vehicleValidator->plate('1234ABC')); // Invalid format
    }

    /**
     * Tests if valid RENAVAM numbers are correctly validated.
     */
    public function testRenavam()
    {
        $this->assertTrue($this->vehicleValidator->renavam('94473163410')); // Valid
        $this->assertTrue($this->vehicleValidator->renavam('21714422129')); // Valid
        $this->assertTrue($this->vehicleValidator->renavam('34457909379')); // Valid
        $this->assertTrue($this->vehicleValidator->renavam('13939262004')); // Valid
        $this->assertTrue($this->vehicleValidator->renavam('73553865159')); // Valid

        $this->assertFalse($this->vehicleValidator->renavam('1234567890'));  // Too short
        $this->assertFalse($this->vehicleValidator->renavam('123456789012')); // Too long
        $this->assertFalse($this->vehicleValidator->renavam('ABCDEFGHIJK')); // Non-numeric characters
        $this->assertFalse($this->vehicleValidator->renavam('00000000000')); // All zeros
        $this->assertFalse($this->vehicleValidator->renavam('94473163411')); // Incorrect check digit
    }

    /**
     * Tests if valid chassis numbers are correctly validated.
     */
    public function testChassis()
    {
        $this->assertTrue($this->vehicleValidator->chassis('1HGCM82633A004352')); // Honda-style VIN
        $this->assertTrue($this->vehicleValidator->chassis('3VV2B7AX5JM012345')); // Volkswagen-style VIN
        $this->assertTrue($this->vehicleValidator->chassis('5YJSA1CN6DFP12345')); // Tesla-style VIN
        $this->assertTrue($this->vehicleValidator->chassis('JN1BY1AR4BM602581')); // Nissan-style VIN
        $this->assertTrue($this->vehicleValidator->chassis('2T3DKRFV8GW123456')); // Toyota-style VIN

        $this->assertFalse($this->vehicleValidator->chassis('1HGCM82633A00435'));  // Too short
        $this->assertFalse($this->vehicleValidator->chassis('1HGCM82633A00435222')); // Too long
        $this->assertFalse($this->vehicleValidator->chassis('1HGCM82633I004352')); // Contains the letter 'I'
        $this->assertFalse($this->vehicleValidator->chassis('1HGCM82633A00435X')); // Incorrect check digit
        $this->assertFalse($this->vehicleValidator->chassis('Q9ABCDEF123456789')); // Contains the letter 'Q'
    }

    /**
     * Tests if valid vehicle categories are correctly validated.
     */
    public function testVehicleCategory()
    {
        $this->assertTrue($this->vehicleValidator->vehicleCategory('A'));
        $this->assertTrue($this->vehicleValidator->vehicleCategory('B'));
        $this->assertTrue($this->vehicleValidator->vehicleCategory('C'));

        $this->assertFalse($this->vehicleValidator->vehicleCategory('Z')); // Invalid category
        $this->assertFalse($this->vehicleValidator->vehicleCategory('1')); // Invalid format
    }
}
