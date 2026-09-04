<?php

namespace Rapidez\Postcode\Contracts;

use Rapidez\Postcode\DataTransferObjects\PostcodeResult;

interface PostcodeDriver
{
    public function lookup(string $postcode, string $houseNumber, ?string $addition = null): PostcodeResult;
}
