<?php

namespace Tests\Brazil;

use PHPUnit\Framework\TestCase;
use CountryValidations\CountryValidator;

class CurrencyTest extends TestCase
{
    private $currencyValidator;

    /**
     * Setup before each test.
     */
    protected function setUp(): void
    {
        $validator = new CountryValidator();
        $this->currencyValidator = $validator->brazil()->currency();
    }

    /**
     * Tests if valid Brazilian currency formats are correctly validated.
     */
    public function testBrlFormat()
    {
        $this->assertTrue($this->currencyValidator->brlFormat('R$ 1.234,56'));
        $this->assertTrue($this->currencyValidator->brlFormat('R$123,45'));
        $this->assertFalse($this->currencyValidator->brlFormat('1234,56')); // Missing R$
        $this->assertFalse($this->currencyValidator->brlFormat('R$ 1234.56')); // Invalid separator
    }

    /**
     * Tests if valid exchange rates are correctly validated.
     */
    public function testExchangeRate()
    {
        $this->assertTrue($this->currencyValidator->exchangeRate('5.4321'));
        $this->assertTrue($this->currencyValidator->exchangeRate('123'));
        $this->assertFalse($this->currencyValidator->exchangeRate('5,4321')); // Invalid separator
        $this->assertFalse($this->currencyValidator->exchangeRate('5.43210')); // Too many decimal places
    }

    /**
     * Tests if positive monetary values are correctly validated.
     */
    public function testPositiveAmount()
    {
        $this->assertTrue($this->currencyValidator->positiveAmount(100.50));
        $this->assertFalse($this->currencyValidator->positiveAmount(-50.75)); // Negative value
        $this->assertFalse($this->currencyValidator->positiveAmount(0)); // Zero is not positive
    }

    /**
     * Tests if monetary values within a limit are correctly validated.
     */
    public function testWithinLimit()
    {
        $this->assertTrue($this->currencyValidator->withinLimit(99.99, 100));
        $this->assertFalse($this->currencyValidator->withinLimit(101, 100)); // Exceeds limit
    }

    /**
     * Tests if valid Brazilian numeric formats are correctly validated.
     */
    public function testBrazilianNumericFormat()
    {
        $this->assertTrue($this->currencyValidator->brazilianNumericFormat('1.234,56'));
        $this->assertTrue($this->currencyValidator->brazilianNumericFormat('123,45'));
        $this->assertFalse($this->currencyValidator->brazilianNumericFormat('1234.56')); // Invalid separator
        $this->assertFalse($this->currencyValidator->brazilianNumericFormat('1,234.56')); // Mixed separators
    }

    /**
     * Tests if Brazilian numeric strings are correctly converted to float.
     */
    public function testConvertToFloat()
    {
        $this->assertEquals(1234.56, $this->currencyValidator->convertToFloat('1.234,56'));
        $this->assertEquals(123.45, $this->currencyValidator->convertToFloat('123,45'));
    }

    /**
     * Tests if valid percentage values are correctly validated.
     */
    public function testPercentage()
    {
        $this->assertTrue($this->currencyValidator->percentage(50));
        $this->assertTrue($this->currencyValidator->percentage(0));
        $this->assertTrue($this->currencyValidator->percentage(100));
        $this->assertFalse($this->currencyValidator->percentage(-1)); // Below 0
        $this->assertFalse($this->currencyValidator->percentage(101)); // Above 100
    }

    /**
     * Tests if numbers with up to two decimal places are correctly validated.
     */
    public function testDecimalPlaces()
    {
        $this->assertTrue($this->currencyValidator->decimalPlaces(123.45));
        $this->assertTrue($this->currencyValidator->decimalPlaces(123));
        $this->assertFalse($this->currencyValidator->decimalPlaces(123.456)); // Too many decimal places
    }

    /**
     * Tests if monetary values within a specified range are correctly validated.
     */
    public function testAmountInRange()
    {
        $this->assertTrue($this->currencyValidator->amountInRange(50, 0, 100));
        $this->assertTrue($this->currencyValidator->amountInRange(100, 0, 100));
        $this->assertFalse($this->currencyValidator->amountInRange(101, 0, 100)); // Exceeds max
        $this->assertFalse($this->currencyValidator->amountInRange(-1, 0, 100)); // Below min
    }
}
