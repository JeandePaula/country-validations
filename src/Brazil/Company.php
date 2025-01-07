<?php

namespace CountryValidations\Brazil;

class Company
{
    private $config;

    public function __construct($config = [])
    {
        $this->config = $config;
    }

    /**
     * Validates a Brazilian CNPJ (National Registry of Legal Entities) number.
     *
     * Input format: XX.XXX.XXX/XXXX-XX or XXXXXXXXXXXXXX (digits only).
     * Non-numeric characters will be removed before validation.
     *
     * Validation steps:
     * 1. Remove all non-numeric characters from the input.
     * 2. Ensure the length of the CNPJ is exactly 14 digits.
     * 3. Check for repeated sequences of digits (e.g., "11111111111111").
     * 4. Calculate the first verification digit using predefined multipliers.
     * 5. Calculate the second verification digit using predefined multipliers.
     * 6. Compare the calculated verification digits with the provided ones.
     *
     * @param string $cnpj The CNPJ number to validate.
     * @return bool Returns true if the CNPJ is valid, false otherwise.
     */
    public function cnpj(string $cnpj): bool
    {
        // Remove non-numeric characters
        $cnpj = preg_replace('/[^0-9]/', '', $cnpj);

        // Validate length
        if (strlen($cnpj) !== 14) {
            return false;
        }

        // Check for repeated sequences
        if (preg_match('/^(\d)\1{13}$/', $cnpj)) {
            return false;
        }

        $multipliers1 = [5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
        $multipliers2 = [6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];

        // Calculate the first verification digit
        $sum = 0;
        for ($i = 0; $i < 12; $i++) {
            $sum += $cnpj[$i] * $multipliers1[$i];
        }
        $firstCheck = ($sum % 11) < 2 ? 0 : 11 - ($sum % 11);
        if ($cnpj[12] != $firstCheck) {
            return false;
        }

        // Calculate the second verification digit
        $sum = 0;
        for ($i = 0; $i < 13; $i++) {
            $sum += $cnpj[$i] * $multipliers2[$i];
        }
        $secondCheck = ($sum % 11) < 2 ? 0 : 11 - ($sum % 11);
        return $cnpj[13] == $secondCheck;
    }

    /**
     * Validates a Brazilian corporate name.
     * 
     * Input format: Any alphanumeric string with at least 5 characters.
     * The name may include letters, numbers, spaces, and basic punctuation.
     *
     * @param string $name The corporate name to validate.
     * @return bool Returns true if the name is valid, false otherwise.
     */
    public function corporateName(string $name): bool
    {
        return preg_match('/^[\p{L}\d\s\.,&()%#-]+$/u', $name) === 1 && strlen($name) >= 5;
    }

    /**
     * Validates a Brazilian corporate phone number.
     * 
     * Input format: (XX) XXXXX-XXXX or XXXXXXXXXX (digits only).
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
     * Validates if a Brazilian corporate phone number is without DDD (area code).
     * 
     * Input format: XXXXX-XXXX or XXXXXXXX (digits only).
     *
     * @param string $phone The phone number to validate.
     * @return bool Returns true if the phone number is without DDD, false otherwise.
     */
    public function phoneWithoutDDD(string $phone): bool
    {
        $helpers = new Helpers();
        return $helpers->phoneWithoutDDD($phone);
    }

    /**
     * Validates a Brazilian corporate email address.
     * 
     * Input format: A valid email address (e.g., example@domain.com).
     *
     * @param string $email The email address to validate.
     * @return bool Returns true if the email address is valid, false otherwise.
     */
    public function email(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Validates a Brazilian state registration (Inscrição Estadual) number.
     * 
     * Input format: A numeric string between 9 and 14 digits.
     * Non-numeric characters will be removed before validation.
     *
     * @param string $stateRegistration The state registration number to validate.
     * @return bool Returns true if the state registration number is valid, false otherwise.
     */
    public function stateRegistration(string $stateRegistration): bool
    {
        $stateRegistration = preg_replace('/[^0-9]/', '', $stateRegistration);

        return preg_match('/^\d{9,14}$/', $stateRegistration) === 1;
    }

    /**
     * Validates a Brazilian NIRE (Número de Identificação do Registro de Empresas).
     * 
     * Input format: A numeric string of exactly 11 digits.
     * Non-numeric characters will be removed before validation.
     *
     * @param string $nire The NIRE number to validate.
     * @return bool Returns true if the NIRE number is valid, false otherwise.
     */
    public function nire(string $nire): bool
    {
        $nire = preg_replace('/[^0-9]/', '', $nire);

        return preg_match('/^\d{11}$/', $nire) === 1;
    }
}
