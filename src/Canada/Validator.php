<?php

namespace CountryValidations\Canada;

class Validator
{
    private $config;

    public function __construct($config = [])
    {
        $this->config = $config;
    }
            
    /**
     * Returns an instance of CanadaPersonal.
     *
     * @return Personal
     */
    public function personal(): Personal
    {
        return new Personal();
    }
}