<?php

namespace CountryValidations\Usa;

class Validator
{
    private $config;

    public function __construct($config = [])
    {
        $this->config = $config;
    }
            
    /**
     * Returns an instance of UsaPersonal.
     *
     * @return Personal
     */
    public function personal(): Personal
    {
        return new Personal();
    }
}