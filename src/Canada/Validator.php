<?php

namespace CountryValidations\Canada;

class Validator
{
    private $config;

    /**
     * Initializes the Validator class with optional configuration.
     *
     * @param array $config Optional configuration settings for the validator.
     */
    public function __construct($config = [])
    {
        $this->config = $config;
    }

    /**
     * Creates and returns an instance of the CanadaPersonal class.
     * This class provides methods for validating personal data specific to Canada.
     *
     * @return Personal Instance of CanadaPersonal.
     */
    public function personal(): Personal
    {
        return new Personal($this->config);
    }
}
