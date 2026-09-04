<?php

namespace Rapidez\Postcode\Drivers;

use Illuminate\Support\Facades\Http;
use Rapidez\Postcode\Contracts\PostcodeDriver;
use Rapidez\Postcode\DataTransferObjects\PostcodeResult;

class PostcodeserviceDriver implements PostcodeDriver
{
    public function __construct(protected ?string $clientId, protected ?string $secureCode) {}

    public function lookup(string $postcode, string $houseNumber, ?string $addition = null): PostcodeResult
    {
        $response = Http::withHeaders([
            'X-ClientId' => $this->clientId,
            'X-SecureCode' => $this->secureCode,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ])
            ->baseUrl('https://api.postcodeservice.com')
            ->get('/nl/v5/getAddress', ['zipcode' => $postcode, 'houseno' => $houseNumber]);

        $data = $response->json();

        if ($response->failed() || ! ($data['city'] ?? null) || ! ($data['street'] ?? null)) {
            return new PostcodeResult(found: false);
        }

        return new PostcodeResult(
            found: true,
            street: $data['street'] ?? null,
            city: $data['city'] ?? null,
            postcode: $postcode,
            houseNumber: $houseNumber,
        );
    }
}
