<?php

namespace Tests\Brazil;

use PHPUnit\Framework\TestCase;
use CountryValidations\CountryValidator;

class BankTest extends TestCase
{
    private $bankValidator;

    /**
     * Setup before each test.
     */
    protected function setUp(): void
    {
        $this->bankValidator = CountryValidator::brazil()->bank();
    }

    /**
     * Tests if valid bank codes are correctly validated.
     */
    public function testBankCode()
    {
        $this->assertTrue($this->bankValidator->bankCode('001'));
        $this->assertTrue($this->bankValidator->bankCode('341'));
        $this->assertFalse($this->bankValidator->bankCode('34A'));
        $this->assertFalse($this->bankValidator->bankCode('12'));
    }

    /**
     * Tests if valid bank branch codes are correctly validated.
     */
    public function testBranch()
    {
        $this->assertTrue($this->bankValidator->branch('1234'));
        $this->assertFalse($this->bankValidator->branch('123'));
        $this->assertFalse($this->bankValidator->branch('12345'));
        $this->assertFalse($this->bankValidator->branch('ABCD'));
    }

    /**
     * Tests if valid account numbers with check digits are correctly validated.
     */
    public function testAccountNumber()
    {
        $this->assertTrue($this->bankValidator->accountNumber('123456-7'));
        $this->assertTrue($this->bankValidator->accountNumber('12345-6'));
        $this->assertFalse($this->bankValidator->accountNumber('1234567'));
        $this->assertFalse($this->bankValidator->accountNumber('12345-67'));
    }

    /**
     * Tests if valid boleto lines are correctly validated.
     */
    public function testBoleto()
    {
        $this->assertTrue($this->bankValidator->boleto('12345678901234567890123456789012345678901234567')); // 47 digits
        $this->assertTrue($this->bankValidator->boleto('123456789012345678901234567890123456789012345678')); // 48 digits
        $this->assertTrue($this->bankValidator->boleto('  12345678901234567890123456789012345678901234567  ')); // Spaces

        $this->assertFalse($this->bankValidator->boleto('1234567890123456789012345678901234567890123456')); // 46 digits
        $this->assertFalse($this->bankValidator->boleto('1234567890123456789012345678901234567890123456789')); // 49 digits
        $this->assertFalse($this->bankValidator->boleto('1234A5678901234567890123456789012345678901234567')); // Letter 'A'
        $this->assertFalse($this->bankValidator->boleto('1234-5678.9012/3456 7890 1234 5678 9012 3456 7')); // Invalid character
        $this->assertFalse($this->bankValidator->boleto('')); // Empty string
        $this->assertFalse($this->bankValidator->boleto('      ')); // Spaces only
    }

    /**
     * Tests if valid check compensation codes are correctly validated.
     */
    public function testCheckCompensationCode()
    {
        $this->assertTrue($this->bankValidator->checkCompensationCode('12345678'));
        $this->assertFalse($this->bankValidator->checkCompensationCode('1234567'));
        $this->assertFalse($this->bankValidator->checkCompensationCode('123456789'));
    }

    /**
     * Tests if valid credit/debit card numbers pass the Luhn algorithm.
     */
    public function testCardNumber()
    {
        $this->assertTrue($this->bankValidator->cardNumber('4539578763621486')); // Valid Luhn
        $this->assertFalse($this->bankValidator->cardNumber('1234567890123456')); // Invalid Luhn
    }

    /**
     * Tests if valid BIN numbers are correctly validated.
     */
    public function testBin()
    {
        $this->assertTrue($this->bankValidator->bin('123456'));
        $this->assertFalse($this->bankValidator->bin('12345'));
        $this->assertFalse($this->bankValidator->bin('1234567'));
    }

    /**
     * Tests if valid ISPB codes are correctly validated.
     */
    public function testIspb()
    {
        $this->assertTrue($this->bankValidator->ispb('12345678'));
        $this->assertFalse($this->bankValidator->ispb('1234567'));
        $this->assertFalse($this->bankValidator->ispb('123456789'));
    }

    /**
     * Tests if valid SWIFT/BIC codes are correctly validated.
     */
    public function testSwift()
    {
        $this->assertTrue($this->bankValidator->swift('DEUTDEFF')); // 8 characters
        $this->assertTrue($this->bankValidator->swift('DEUTDEFF500')); // 11 characters
        $this->assertFalse($this->bankValidator->swift('DEUTDEFFF')); // Invalid length
    }

    /**
     * Tests for the Brazilian IBAN validation function.
     */
    public function testIban()
    {
        $this->assertTrue($this->bankValidator->iban('BR1500000000000010932840814P2')); // Valid IBAN
        $this->assertTrue($this->bankValidator->iban(' BR1500000000000010932840814P2 ')); // Valid IBAN with spaces

        $this->assertFalse($this->bankValidator->iban('BR1500000000000010932840814P3')); // Incorrect checksum
        $this->assertFalse($this->bankValidator->iban('BR1500000000000010932840814P@')); // Invalid character
        $this->assertFalse($this->bankValidator->iban('BR1500000000000010932840814'));   // Too short
        $this->assertFalse($this->bankValidator->iban('BR1500000000000010932840814P200000000000000')); // Too long
        $this->assertFalse($this->bankValidator->iban('123456789012345678901234567890')); // Missing "BR" prefix
        $this->assertFalse($this->bankValidator->iban('BR1500000000000010932840814P2#')); // Invalid character
        $this->assertFalse($this->bankValidator->iban('BR1500000000000000000000000P0')); // Valid format, invalid checksum

        $this->assertFalse($this->bankValidator->iban('')); // Empty string
        $this->assertFalse($this->bankValidator->iban('BR1500000000000010932840814P3 ')); // Space and checksum error
    }
}
