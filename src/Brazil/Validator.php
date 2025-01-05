<?php

namespace CountryValidations\Brazil;

class Validator
{
    private $config;

    public function __construct($config = [])
    {
        $this->config = $config;
    }
                /**
     * Returns an instance of BrazilBank.
     *
     * @return Bank
     */
    public function bank(): Bank
    {
        return new Bank();
    }

    /**
     * Returns an instance of BrazilCompany.
     *
     * @return Company
     */
    public function company(): Company
    {
        return new Company();
    }

    /**
     * Returns an instance of BrazilCurrency.
     *
     * @return Currency
     */
    public function currency(): Currency
    {
        return new Currency();
    }

    /**
     * Returns an instance of BrazilPersonal.
     *
     * @return Personal
     */
    public function personal(): Personal
    {
        return new Personal();
    }

    /**
     * Returns an instance of BrazilVehicle.
     *
     * @return Vehicle
     */
    public function vehicle(): Vehicle
    {
        return new Vehicle();
    }
}