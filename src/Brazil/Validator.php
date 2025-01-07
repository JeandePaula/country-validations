<?php

namespace CountryValidations\Brazil;

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
     * Creates and returns an instance of the BrazilBank class.
     * This class provides methods for validating bank-related data specific to Brazil.
     *
     * @return Bank Instance of BrazilBank.
     */
    public function bank(): Bank
    {
        return new Bank($this->config);
    }

    /**
     * Creates and returns an instance of the BrazilCompany class.
     * This class provides methods for validating company-related data specific to Brazil.
     *
     * @return Company Instance of BrazilCompany.
     */
    public function company(): Company
    {
        return new Company($this->config);
    }

    /**
     * Creates and returns an instance of the BrazilCurrency class.
     * This class provides methods for currency-related validations specific to Brazil.
     *
     * @return Currency Instance of BrazilCurrency.
     */
    public function currency(): Currency
    {
        return new Currency($this->config);
    }

    /**
     * Creates and returns an instance of the BrazilPersonal class.
     * This class provides methods for validating personal data specific to Brazil.
     *
     * @return Personal Instance of BrazilPersonal.
     */
    public function personal(): Personal
    {
        return new Personal($this->config);
    }

    /**
     * Creates and returns an instance of the BrazilVehicle class.
     * This class provides methods for validating vehicle-related data specific to Brazil.
     *
     * @return Vehicle Instance of BrazilVehicle.
     */
    public function vehicle(): Vehicle
    {
        return new Vehicle($this->config);
    }
}
