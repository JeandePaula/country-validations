<?php

namespace CountryValidations\Brazil;

class Bank
{
    private $config;

    public function __construct($config = [])
    {
        $this->config = $config;
    }
    
    /**
     * Validates a COMPE bank code (FEBRABAN) in the format XXX.
     * Accepts input with non-numeric characters, which are removed before validation.
     *
     * A valid bank code must:
     * - Contain exactly 3 digits.
     * - Be a numeric-only value after cleaning.
     *
     * @param string $code The bank code to validate.
     * @return bool True if the code is valid, false otherwise.
     */
    public function bankCode(string $code): bool
    {
        // Remove non-numeric characters
        $code = preg_replace('/\D/', '', $code);

        // Validate if it has exactly 3 digits
        return preg_match('/^\d{3}$/', $code) === 1;
    }

    /**
     * Validates a bank branch code in the format XXXX.
     * Removes any non-numeric characters before validation.
     *
     * A valid branch code must:
     * - Contain exactly 4 numeric digits after cleaning.
     *
     * @param string $branch Branch code to validate.
     * @return bool True if valid, false otherwise.
     */
    public function branch(string $branch): bool
    {
        $branch = preg_replace('/\D/', '', $branch);

        return preg_match('/^\d{4}$/', $branch) === 1;
    }

    /**
     * Validates a bank account number with a check digit in the format XXXXXXX-X.
     * Removes any non-numeric characters before validation.
     *
     * A valid account number must:
     * - Have between 5 and 12 numeric digits before the hyphen.
     * - Have exactly 1 numeric digit after the hyphen.
     *
     * @param string $account Account number to validate.
     * @return bool True if valid, false otherwise.
     */
    public function accountNumber(string $account): bool
    {
        $account = preg_replace('/[^\d-]/', '', $account);

        return preg_match('/^\d{5,12}-\d{1}$/', $account) === 1;
    }

    /**
     * Validates a bank boleto line in the format 47 or 48 numeric characters.
     * Removes any non-numeric characters and trims whitespace.
     *
     * A valid boleto line must:
     * - Contain only numeric characters.
     * - Be exactly 47 or 48 digits long.
     *
     * @param string $boleto Boleto line to validate.
     * @return bool True if valid, false otherwise.
     */
    public function boleto(string $boleto): bool
    {
        $boleto = trim($boleto);

        if (!ctype_digit($boleto)) {
            return false;
        }

        $boleto = preg_replace('/\D/', '', $boleto);

        return strlen($boleto) === 47 || strlen($boleto) === 48;
    }

    /**
     * Validates a check compensation code in the format XXXXXXXX.
     * Removes any non-numeric characters before validation.
     *
     * A valid code must:
     * - Contain exactly 8 numeric digits.
     *
     * @param string $code Compensation code to validate.
     * @return bool True if valid, false otherwise.
     */
    public function checkCompensationCode(string $code): bool
    {
        $code = preg_replace('/\D/', '', $code);

        return preg_match('/^\d{8}$/', $code) === 1;
    }

    /**
     * Validates a credit/debit card number using the Luhn algorithm.
     * Removes any non-numeric characters before validation.
     *
     * The Luhn algorithm:
     * - Alternates doubling every other digit from the rightmost digit.
     * - Subtracts 9 if the result of doubling is greater than 9.
     * - Validates if the sum of all digits is divisible by 10.
     *
     * @param string $cardNumber Card number to validate.
     * @return bool True if valid, false otherwise.
     */
    public function cardNumber(string $cardNumber): bool
    {
        $cardNumber = preg_replace('/\D/', '', $cardNumber);

        $sum = 0;
        $alt = false;

        // Loop through each digit from right to left
        for ($i = strlen($cardNumber) - 1; $i >= 0; $i--) {
            $n = (int) $cardNumber[$i];
            if ($alt) {
                $n *= 2;
                if ($n > 9) {
                    $n -= 9;
                }
            }
            $sum += $n;
            $alt = !$alt;
        }

        return $sum % 10 === 0;
    }

    /**
     * Validates a BIN (Bank Identification Number) in the format XXXXXX.
     * Removes any non-numeric characters before validation.
     *
     * A valid BIN must:
     * - Contain exactly 6 numeric digits.
     *
     * @param string $bin BIN to validate.
     * @return bool True if valid, false otherwise.
     */
    public function bin(string $bin): bool
    {
        $bin = preg_replace('/\D/', '', $bin);

        return preg_match('/^\d{6}$/', $bin) === 1;
    }

    /**
     * Validates an ISPB code (Brazilian Payment System Identifier) in the format XXXXXXXX.
     * Checks if the ISPB is numeric, has exactly 8 digits, and exists in the official list.
     *
     * @param string $ispb ISPB code to validate.
     * @return bool True if valid, false otherwise.
     */
    public function ispb(string $ispb): bool
    {
        $ispb = preg_replace('/\D/', '', $ispb);

        if (!preg_match('/^\d{8}$/', $ispb)) {
            return false;
        }

        $helpers = new Helpers();
        $listispb = $helpers->getIspbList();

        return array_key_exists($ispb, $listispb);
    }

    /**
     * Validates a SWIFT/BIC code in the format 8 or 11 alphanumeric characters.
     * A valid SWIFT/BIC code must:
     * - Contain only uppercase letters and numbers.
     * - Be exactly 8 or 11 characters long.
     *
     * @param string $swift SWIFT/BIC code to validate.
     * @return bool True if valid, false otherwise.
     */
    public function swift(string $swift): bool
    {
        return preg_match('/^[A-Z0-9]{8}([A-Z0-9]{3})?$/', $swift) === 1;
    }

    /**
     * Validates a Brazilian IBAN (International Bank Account Number).
     * Removes spaces, normalizes to uppercase, and checks against IBAN rules.
     *
     * A valid IBAN must:
     * - Be exactly 29 characters long.
     * - Start with "BR".
     * - Contain only alphanumeric characters.
     * - Pass the modulo 97 check.
     *
     * @param string $iban IBAN to validate.
     * @return bool True if valid, false otherwise.
     */
    public function iban(string $iban): bool
    {
        $iban = strtoupper(trim($iban));

        if (strpos($iban, ' ') !== false) {
            return false;
        }

        if (
            strlen($iban) !== 29 ||          
            substr($iban, 0, 2) !== 'BR' || 
            !ctype_alnum($iban)             
        ) {
            return false;
        }

        $ibanRearranged = substr($iban, 4) . substr($iban, 0, 4);

        $numericIban = preg_replace_callback(
            '/[A-Z]/',
            function ($match) {
                return ord($match[0]) - 55;
            },
            $ibanRearranged
        );

        $helpers = new Helpers();
        return $helpers->genericBcmod($numericIban) === 1;
    }
}
