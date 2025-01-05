<?php

namespace CountryValidations\Brazil;

class Helpers
{
    private $config;

    public function __construct($config = [])
    {
        $this->config = $config;
    }
                /**
     * Validates a Brazilian phone number.
     *
     * @param string $phone The phone number to validate.
     * @return bool Returns true if the phone number is valid, false otherwise.
     */
    public function phone(string $phone): bool
    {
        // Validate mask format before cleaning
        if (!preg_match('/^\(?\d{2}\)? ?\d{4,5}-\d{4}$/', $phone) && !preg_match('/^\d{10,11}$/', $phone)) {
            return false;
        }
    
        // Remove any non-numeric characters
        $phone = preg_replace('/\D/', '', $phone);
    
        // Check if the length is valid (10 or 11 digits)
        $length = strlen($phone);
        if ($length !== 10 && $length !== 11) {
            return false;
        }
    
        // Extract the DDD (first two digits)
        $ddd = substr($phone, 0, 2);
    
        // List of valid DDDs
        $validDDD = [
            '11', '12', '13', '14', '15', '16', '17', '18', '19',
            '21', '22', '24', '27', '28',
            '31', '32', '33', '34', '35', '37', '38',
            '41', '42', '43', '44', '45', '46',
            '47', '48', '49',
            '51', '53', '54', '55',
            '61', '62', '63', '64', '65', '66', '67', '68', '69',
            '71', '73', '74', '75', '77', '79',
            '81', '82', '83', '84', '85', '86', '87', '88', '89',
            '91', '92', '93', '94', '95', '96', '97', '98', '99'
        ];
    
        // Check if the DDD is valid
        if (!in_array($ddd, $validDDD)) {
            return false;
        }
    
        // Validate the local number format after the DDD
        $localNumber = substr($phone, 2); // Part after the DDD
    
        if ($length === 10) {
            // Landline: 4 digits + 4 digits, starting with 2-8
            if (!preg_match('/^[2-8]\d{3}\d{4}$/', $localNumber)) {
                return false;
            }
        } elseif ($length === 11) {
            // Mobile: 5 digits + 4 digits, starting with 9
            if (!preg_match('/^9\d{4}\d{4}$/', $localNumber)) {
                return false;
            }
        }
    
        return true;
    }
    
    /**
     * Validates if a Brazilian phone number, without the DDD (area code), has a valid length and format.
     *
     * Input format: XXXXX-XXXX, XXXXXXXX, or XXXXXXXXX (digits only).
     * Non-numeric characters will be removed before validation.
     *
     * @param string $phone The phone number to validate.
     * @return bool Returns true if the phone number is valid without DDD, false otherwise.
     */
    public function phoneWithoutDDD(string $phone): bool
    {
        // Validate the mask before cleaning
        if (!preg_match('/^\d{4,5}-\d{4}$/', $phone) && !preg_match('/^\d{8,9}$/', $phone)) {
            return false;
        }

        // Remove non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // Check for valid length: 8 (landline) or 9 (mobile) digits without DDD
        if (!preg_match('/^\d{8,9}$/', $phone)) {
            return false;
        }

        // Validate the format: landline (8 digits, starting with 2-8) or mobile (9 digits, starting with 9)
        if (strlen($phone) === 8) {
            // Landline: 4 digits + 4 digits, starting with 2-8
            return preg_match('/^[2-8]\d{7}$/', $phone) === 1;
        } elseif (strlen($phone) === 9) {
            // Mobile: 5 digits + 4 digits, starting with 9
            return preg_match('/^9\d{8}$/', $phone) === 1;
        }

        return false;
    }

    /**
     * Calculates the remainder of a number when divided by a modulus.
     *
     * This function takes a number as a string and a modulus as a string,
     * converts the modulus to an integer, and then calculates the remainder
     * of the number when divided by the modulus using a manual division algorithm.
     *
     * @param string $number The number to be divided, represented as a string.
     * @param string $modulus The modulus to divide by, represented as a string.
     * @return int The remainder of the division.
     */
    public function genericBcmod(string $numericString): int
    {
        $remainder = 0;
        foreach (str_split($numericString, 9) as $chunk) {
            $remainder = (int)(($remainder . $chunk) % 97);
        }
        return $remainder;
    }

}