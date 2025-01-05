<?php

namespace CountryValidations;

use CountryValidations\Brazil\Validator as BrazilValidator;
use CountryValidations\Canada\Validator as CanadaValidator;
use CountryValidations\Usa\Validator as UsaValidator;

class CountryValidator
{
    private $config;

    public function __construct($config = [])
    {
        $this->config = $config;
    }
                /**
     * Creates an instance of BrazilValidator.
     *
     * @return BrazilValidator
     */
    public function brazil(): BrazilValidator
    {
        return new BrazilValidator();
    }

    /**
     * Creates an instance of CanadaValidator.
     *
     * @return CanadaValidator
     */
    public function canada(): CanadaValidator
    {
        return new CanadaValidator();
    }

    /**
     * Creates an instance of UsaValidator.
     *
     * @return UsaValidator
     */
    public function usa(): UsaValidator
    {
        return new UsaValidator();
    }
}