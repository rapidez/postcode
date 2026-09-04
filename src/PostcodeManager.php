<?php

namespace Rapidez\Postcode;

use Illuminate\Support\Manager;
use Rapidez\Postcode\Contracts\PostcodeDriver;
use Rapidez\Postcode\Drivers\PostcodeEuDriver;
use Rapidez\Postcode\Drivers\PostcodeserviceDriver;
use Rapidez\Postcode\Drivers\Pro6ppDriver;

class PostcodeManager extends Manager
{
    public function getDefaultDriver(): string
    {
        return $this->config->get('rapidez.postcode.driver');
    }

    public function createPostcodeeuDriver(): PostcodeDriver
    {
        return new PostcodeEuDriver(
            $this->config->get('rapidez.postcode.drivers.postcodeeu.key'),
            $this->config->get('rapidez.postcode.drivers.postcodeeu.secret'),
        );
    }

    public function createPro6ppDriver(): PostcodeDriver
    {
        return new Pro6ppDriver(
            $this->config->get('rapidez.postcode.drivers.pro6pp.key'),
        );
    }

    public function createPostcodeserviceDriver(): PostcodeDriver
    {
        return new PostcodeserviceDriver(
            $this->config->get('rapidez.postcode.drivers.postcodeservice.client_id'),
            $this->config->get('rapidez.postcode.drivers.postcodeservice.secure_code'),
        );
    }
}
