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
     * Validates a bank code (FEBRABAN) in the format XXX.
     * Removes any non-numeric characters before validation.
     * @param string $code Bank code to validate.
     * @return bool True if valid, false otherwise.
     */
    public function bankCode(string $code): bool
    {
        // Remove non-numeric characters
        $code = preg_replace('/\D/', '', $code);

        return preg_match('/^\d{3}$/', $code) === 1;
    }

    /**
     * Validates a bank branch code in the format XXXX.
     * Removes any non-numeric characters before validation.
     * @param string $branch Branch code to validate.
     * @return bool True if valid, false otherwise.
     */
    public function branch(string $branch): bool
    {
        // Remove non-numeric characters
        $branch = preg_replace('/\D/', '', $branch);

        return preg_match('/^\d{4}$/', $branch) === 1;
    }

    /**
     * Validates a bank account number with a check digit in the format XXXXXXX-X.
     * Removes any non-numeric characters before validation.
     * @param string $account Account number to validate.
     * @return bool True if valid, false otherwise.
     */
    public function accountNumber(string $account): bool
    {
        // Remove non-numeric characters except for the hyphen
        $account = preg_replace('/[^\d-]/', '', $account);

        return preg_match('/^\d{5,12}-\d{1}$/', $account) === 1;
    }

    /**
     * Validates a bank boleto line in the format 47 or 48 numeric characters.
     * @param string $boleto Boleto line to validate.
     * @return bool True if valid, false otherwise.
     */
    public function boleto(string $boleto): bool
    {
        // Remover espaços em branco
        $boleto = trim($boleto);

        // Verificar se contém apenas números (sem caracteres especiais ou letras)
        if (!ctype_digit($boleto)) {
            return false;
        }

        // Limpar para pegar somente os números
        $boleto = preg_replace('/\D/', '', $boleto);

        // Validar o tamanho (47 ou 48 dígitos)
        return strlen($boleto) === 47 || strlen($boleto) === 48;
    }


    /**
     * Validates a check compensation code in the format XXXXXXXX.
     * Removes any non-numeric characters before validation.
     * @param string $code Compensation code to validate.
     * @return bool True if valid, false otherwise.
     */
    public function checkCompensationCode(string $code): bool
    {
        // Remove non-numeric characters
        $code = preg_replace('/\D/', '', $code);

        return preg_match('/^\d{8}$/', $code) === 1;
    }

    /**
     * Validates a credit/debit card number using the Luhn algorithm.
     * Removes any non-numeric characters before validation.
     * @param string $cardNumber Card number to validate.
     * @return bool True if valid, false otherwise.
     */
    public function cardNumber(string $cardNumber): bool
    {
        // Remove non-numeric characters
        $cardNumber = preg_replace('/\D/', '', $cardNumber);

        $sum = 0;
        $alt = false;

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
     * @param string $bin BIN to validate.
     * @return bool True if valid, false otherwise.
     */
    public function bin(string $bin): bool
    {
        // Remove non-numeric characters
        $bin = preg_replace('/\D/', '', $bin);

        return preg_match('/^\d{6}$/', $bin) === 1;
    }

    /**
     * Validates an ISPB code (Brazilian Payment System Identifier) in the format XXXXXXXX.
     * Removes any non-numeric characters before validation.
     * @param string $ispb ISPB code to validate.
     * @return bool True if valid, false otherwise.
     */
    public function ispb(string $ispb): bool
    {
        // Remove non-numeric characters
        $ispb = preg_replace('/\D/', '', $ispb);

        return preg_match('/^\d{8}$/', $ispb) === 1;
    }

    /**
     * Validates a SWIFT/BIC code in the format 8 or 11 alphanumeric characters.
     * @param string $swift SWIFT/BIC code to validate.
     * @return bool True if valid, false otherwise.
     */
    public function swift(string $swift): bool
    {
        return preg_match('/^[A-Z0-9]{8}([A-Z0-9]{3})?$/', $swift) === 1;
    }

    public function iban(string $iban): bool
    {
        // Normalize: remove spaces from the beginning and end, and convert to uppercase
        $iban = strtoupper(trim($iban));

        // Reject if there are spaces in the middle
        if (strpos($iban, ' ') !== false) {
            return false;
        }

        // Basic validations
        if (
            strlen($iban) !== 29 ||          // Fixed length of 29 characters
            substr($iban, 0, 2) !== 'BR' || // Must start with "BR"
            !ctype_alnum($iban)             // Must contain only alphanumeric characters
        ) {
            return false;
        }

        // Rearrange IBAN: Move the first 4 characters to the end
        $ibanRearranged = substr($iban, 4) . substr($iban, 0, 4);

        // Replace letters with their numeric values (A = 10, B = 11, ..., Z = 35)
        $numericIban = preg_replace_callback(
            '/[A-Z]/',
            function ($match) {
                return ord($match[0]) - 55;
            },
            $ibanRearranged
        );

        // Calculate mod 97
        $helpers = new Helpers();
        return $helpers->genericBcmod($numericIban) === 1;
    }


}