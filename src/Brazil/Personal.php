<?php

namespace CountryValidations\Brazil;

use DateTime;

class Personal
{
    private $config;

    public function __construct($config = [])
    {
        $this->config = $config;
    }

    /**
     * Validates a Brazilian CPF number.
     *
     * Input format: XXX.XXX.XXX-XX or XXXXXXXXXXX (digits only).
     * Non-numeric characters will be removed before validation.
     * 
     * Validation criteria:
     * - The CPF must have exactly 11 digits.
     * - Cannot be composed of repeated sequences of the same digit (e.g., "11111111111").
     * - Must pass the CPF verification algorithm based on its two check digits.
     *
     * @param string $cpf The CPF number to be validated.
     * @return bool Returns true if the CPF is valid, false otherwise.
     */
    public function cpf(string $cpf): bool
    {
        $cpf = preg_replace('/[^0-9]/', '', $cpf);

        // Validate length and repeated sequences
        if (strlen($cpf) !== 11 || preg_match('/^(\d)\1{10}$/', $cpf)) {
            return false;
        }

        // Check digits validation
        for ($t = 9; $t < 11; $t++) {
            $sum = 0;
            for ($i = 0; $i < $t; $i++) {
                $sum += $cpf[$i] * (($t + 1) - $i);
            }
            $digit = ((10 * $sum) % 11) % 10;
            if ($cpf[$t] != $digit) {
                return false;
            }
        }

        return true;
    }

    /**
     * Validates a Brazilian RG (Registro Geral) number based on state rules.
     *
     * Input format: Numeric string with optional "X" as the last character.
     * 
     * Validation steps:
     * - Remove non-alphanumeric characters.
     * - Ensure the length matches the state-specific rules.
     * - Validate that, if present, the letter "X" is the last character.
     * 
     * @param string $rg The RG number to validate.
     * @param string $state The state abbreviation (e.g., "SP", "RJ"). Default is "SP".
     * @return bool True if the RG is valid, false otherwise.
     */
    public function rg(string $rg, string $state = 'SP'): bool
    {
        $rg = preg_replace('/[^0-9A-Za-z]/', '', $rg);

        // Check for invalid letters (only "X" is allowed)
        if (preg_match('/[A-WY-Za-wy-z]/', $rg)) {
            return false;
        }

        // State-specific length rules
        $stateRules = [
            'SP' => 9,  // São Paulo
            'RJ' => 9,  // Rio de Janeiro
            'MG' => 9,  // Minas Gerais
            'RS' => 10, // Rio Grande do Sul
            'PR' => 10, // Paraná
            'SC' => 10, // Santa Catarina
        ];

        $defaultRule = [7, 8]; // Default for other states

        // Validate length based on state
        if (isset($stateRules[$state])) {
            $length = $stateRules[$state];
            if (strlen($rg) != $length && strlen($rg) != $length - 1) {
                return false;
            }
        } else {
            if (strlen($rg) < $defaultRule[0] || strlen($rg) > $defaultRule[1]) {
                return false;
            }
        }

        // Ensure "X" is the last character if present
        if (strpos($rg, 'X') !== false && substr($rg, -1) !== 'X') {
            return false;
        }

        return true;
    }

    /**
     * Validates a Brazilian National Health Card (CNS).
     *
     * Input format: A numeric string of exactly 15 digits.
     * 
     * Validation criteria:
     * - Must have exactly 15 numeric digits.
     * - Cannot be "000000000000000".
     * - Must start with one of the digits: 1, 2, 7, 8, or 9.
     *
     * @param string $cns The CNS number to validate.
     * @return bool True if the CNS is valid, false otherwise.
     */
    public function cns(string $cns): bool
    {
        if (!preg_match('/^\d{15}$/', $cns)) {
            return false;
        }

        if ($cns === '000000000000000') {
            return false;
        }

        return in_array($cns[0], ['1', '2', '7', '8', '9'], true);
    }

    /**
     * Validates a birth date string in the format 'YYYY-MM-DD'.
     *
     * Validation criteria:
     * - The date must be a valid calendar date.
     * - The date must not be in the future.
     *
     * @param string $date The birth date string to validate.
     * @return bool Returns true if the date is valid, false otherwise.
     */
    public function birthDate(string $date): bool
    {
        $dateObject = DateTime::createFromFormat('Y-m-d', $date);

        if (!$dateObject || $dateObject->format('Y-m-d') !== $date) {
            return false;
        }

        return $dateObject < new DateTime();
    }

    /**
     * Validates a Brazilian full name.
     *
     * Validation criteria:
     * - The name must contain at least two words separated by spaces.
     * - Words may include letters, accents, and special characters like hyphens.
     *
     * @param string $name The full name to validate.
     * @return bool Returns true if the name contains two or more words, false otherwise.
     */
    public function fullName(string $name): bool
    {
        return preg_match('/^[\p{L}\p{M}\'-]+(\s+[\p{L}\p{M}\'-]+)+$/u', $name) === 1;
    }

    /**
     * Validates a Brazilian PIS/PASEP number.
     *
     * This method ensures that a provided PIS/PASEP number is valid based on:
     * - Removing any non-numeric characters from the input.
     * - Ensuring the number is exactly 11 digits long.
     * - Checking that the number is not composed entirely of repeated digits.
     * - Calculating and validating the check digit using a weighted sum algorithm.
     *
     * Example:
     * Input: "123.45678.90-1"
     * Processed: "12345678901"
     * Validation: True if the check digit matches the calculated value.
     *
     * @param string $pisPasep The PIS/PASEP number to validate.
     * @return bool True if the PIS/PASEP number is valid, false otherwise.
     */
    public function pisPasep(string $pisPasep): bool
    {
        // Remove non-numeric characters
        $pisPasep = preg_replace('/\D/', '', $pisPasep);

        // Validate length and repeated digits
        if (strlen($pisPasep) !== 11 || preg_match('/^(\d)\1{10}$/', $pisPasep)) {
            return false;
        }

        $multipliers = [3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
        $sum = 0;

        // Calculate the weighted sum for the first 10 digits
        for ($i = 0; $i < 10; $i++) {
            $sum += (int)$pisPasep[$i] * $multipliers[$i];
        }

        // Calculate the check digit
        $remainder = $sum % 11;
        $checkDigit = $remainder < 2 ? 0 : 11 - $remainder;

        // Validate the check digit
        return (int)$pisPasep[10] === $checkDigit;
    }

    /**
     * Validates a Brazilian Voter Registration number (Título de Eleitor).
     *
     * This method validates the Título de Eleitor by:
     * - Removing non-numeric characters.
     * - Ensuring the number is exactly 12 digits long.
     * - Calculating two verification digits (d1 and d2) based on a weighted sum.
     * - Comparing the calculated digits with the input's verification digits.
     *
     * Example:
     * Input: "123456789012"
     * Processed: "123456789012"
     * Validation: True if the verification digits match the calculated ones.
     *
     * @param string $titulo The Título de Eleitor number to validate.
     * @return bool True if the Título de Eleitor is valid, false otherwise.
     */
    public function tituloEleitor(string $titulo): bool
    {
        // Remove non-numeric characters
        $titulo = preg_replace('/[^0-9]/', '', $titulo);

        // Validate length
        if (strlen($titulo) !== 12) {
            return false;
        }

        $d1 = 0;
        $d2 = 0;

        // Calculate the first verification digit (d1)
        $peso = 2;
        for ($i = 9; $i >= 0; $i--) {
            $d1 += (int)$titulo[$i] * $peso;
            $peso = $peso < 9 ? $peso + 1 : 2;
        }
        $d1 %= 11;
        $d1 = $d1 > 9 ? 0 : $d1;

        // Calculate the second verification digit (d2)
        $peso = 2;
        for ($i = 10; $i >= 0; $i--) {
            $d2 += (int)$titulo[$i] * $peso;
            $peso = $peso < 9 ? $peso + 1 : 2;
        }
        $d2 %= 11;
        $d2 = $d2 > 9 ? 0 : $d2;

        // Validate the verification digits
        return (int)$titulo[10] === $d1 && (int)$titulo[11] === $d2;
    }

    /**
     * Validates a Brazilian email address.
     *
     * This method uses PHP's built-in filter to validate the format of an email address.
     *
     * Example:
     * Input: "user@example.com"
     * Validation: True if the input is a properly formatted email address.
     *
     * @param string $email The email address to validate.
     * @return bool True if the email address is valid, false otherwise.
     */
    public function email(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Validates a Brazilian CNH (Carteira Nacional de Habilitação) number.
     *
     * This method validates a CNH number by:
     * - Removing any non-numeric characters.
     * - Ensuring the number is exactly 11 digits long.
     * - Checking for repeated sequences of digits.
     * - Using a weighted sum algorithm to validate two verification digits.
     *
     * Example:
     * Input: "12345678900"
     * Processed: "12345678900"
     * Validation: True if both verification digits match the calculated ones.
     *
     * @param string $cnh The CNH number to be validated.
     * @return bool True if the CNH number is valid, false otherwise.
     */
    public function cnh(string $cnh): bool
    {
        $cnh = preg_replace('/[^0-9]/', '', $cnh);

        if (strlen($cnh) !== 11 || preg_match('/^(\d)\1{10}$/', $cnh)) {
            return false;
        }

        $sum1 = 0;
        for ($i = 0; $i < 9; $i++) {
            $sum1 += $cnh[$i] * (9 - $i);
        }
        $firstCheck = $sum1 % 11;
        $firstCheck = $firstCheck >= 10 ? 0 : $firstCheck;

        if ($cnh[9] != $firstCheck) {
            return false;
        }

        $sum2 = 0;
        for ($i = 0; $i < 9; $i++) {
            $sum2 += $cnh[$i] * (1 + $i);
        }
        $sum2 += $firstCheck * 2;
        $secondCheck = $sum2 % 11;
        $secondCheck = $secondCheck >= 10 ? 0 : $secondCheck;

        return $cnh[10] == $secondCheck;
    }

    /**
     * Validates a Brazilian identification number (CIN).
     *
     * This method serves as an alias for CPF validation.
     * It uses the same logic as the `cpf` method.
     *
     * @param string $numero The identification number to validate.
     * @return bool True if the identification number is valid, false otherwise.
     */
    public function cin(string $numero): bool
    {
        return self::cpf($numero);
    }

    /**
     * Validates a Brazilian passport number.
     *
     * Input format:
     * - Two uppercase letters followed by six digits (e.g., AB123456).
     * - Non-alphanumeric characters are removed before validation.
     *
     * Example:
     * Input: "AB123456"
     * Validation: True if the input matches the required format.
     *
     * @param string $passportNumber The passport number to validate.
     * @return bool True if the passport number is valid, false otherwise.
     */
    public function passport(string $passportNumber): bool
    {
        $passportNumber = preg_replace('/[^A-Za-z0-9]/', '', $passportNumber);

        return preg_match('/^[A-Za-z]{2}[0-9]{6}$/', $passportNumber) === 1;
    }

    /**
     * Validates a Brazilian phone number.
     *
     * This method checks if a given phone number is valid in the Brazilian format.
     *
     * Input format:
     * - (XX) XXXXX-XXXX (standard format with DDD).
     * - XXXXXXXXXX (digits only format with DDD).
     *
     * Validation process:
     * - The method delegates the validation logic to a helper class (`Helpers`).
     * - Ensures that the phone number conforms to Brazilian standards.
     *
     * Example:
     * Input: "(11) 91234-5678" or "11912345678"
     * Validation: True if the phone number matches the required format.
     *
     * @param string $phone The phone number to validate.
     * @return bool Returns true if the phone number is valid, false otherwise.
     */
    public function phone(string $phone): bool
    {
        $helpers = new Helpers();
        return $helpers->phone($phone);
    }

    /**
     * Validates if a Brazilian phone number is without DDD (area code).
     *
     * This method checks if a given phone number is in the correct Brazilian format
     * without the area code (DDD).
     *
     * Input format:
     * - XXXXX-XXXX (standard format).
     * - XXXXXXXX (digits only format).
     *
     * Validation process:
     * - Removes non-numeric characters from the input.
     * - Delegates the validation logic to a helper class (`Helpers`).
     * - Ensures the phone number complies with Brazilian standards for numbers without DDD.
     *
     * Example:
     * Input: "91234-5678" or "912345678"
     * Validation: True if the phone number matches the required format without DDD.
     *
     * @param string $phone The phone number to validate.
     * @return bool Returns true if the phone number is without DDD and valid, false otherwise.
     */
    public function phoneWithoutDDD(string $phone): bool
    {
        $helpers = new Helpers();
        return $helpers->phoneWithoutDDD($phone);
    }

}