<?php

namespace CountryValidations;

use CountryValidations\Brazil\Validator as BrazilValidator;
use CountryValidations\Canada\Validator as CanadaValidator;
use CountryValidations\Usa\Validator as UsaValidator;

class CountryValidator
{
    private $config;

    /**
     * Initializes the CountryValidator class with optional configuration.
     *
     * @param array $config Optional configuration settings for validators.
     */
    public function __construct($config = [])
    {
        $this->config = $config;
    }

    /**
     * Creates and returns an instance of the BrazilValidator.
     * This validator provides specific validation methods for Brazilian data.
     *
     * @return BrazilValidator Instance of BrazilValidator.
     */
    public function brazil(): BrazilValidator
    {
        return new BrazilValidator($this->config);
    }

    /**
     * Creates and returns an instance of the CanadaValidator.
     * This validator provides specific validation methods for Canadian data.
     *
     * @return CanadaValidator Instance of CanadaValidator.
     */
    public function canada(): CanadaValidator
    {
        return new CanadaValidator($this->config);
    }

    /**
     * Creates and returns an instance of the UsaValidator.
     * This validator provides specific validation methods for US data.
     *
     * @return UsaValidator Instance of UsaValidator.
     */
    public function usa(): UsaValidator
    {
        return new UsaValidator($this->config);
    }
}
