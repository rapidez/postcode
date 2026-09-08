<?php

namespace Rapidez\Postcode\Drivers;

use Illuminate\Support\Facades\Http;
use InvalidArgumentException;
use Rapidez\Postcode\Contracts\PostcodeDriver;
use Rapidez\Postcode\DataTransferObjects\PostcodeResult;

class PostcodeEuDriver implements PostcodeDriver
{
    public function __construct(protected ?string $key, protected ?string $secret)
    {
        if (! $this->key || ! $this->secret) {
            throw new InvalidArgumentException('The postcodeeu driver requires POSTCODE_EU_API_KEY and POSTCODE_EU_API_SECRET to be set.');
        }
    }

    public function lookup(string $postcode, string $houseNumber, ?string $addition = null): PostcodeResult
    {
        $response = Http::withBasicAuth($this->key, $this->secret)
            ->get("https://api.postcode.eu/nl/v1/addresses/postcode/{$postcode}/{$houseNumber}/" . ($addition ?? ''));

        if ($response->failed()) {
            return new PostcodeResult(found: false);
        }

        $data = $response->json();

        return new PostcodeResult(
            found: true,
            street: $data['street'] ?? null,
            city: $data['city'] ?? null,
            province: $data['province'] ?? null,
            postcode: $data['postcode'] ?? null,
            houseNumber: $data['houseNumber'] ?? null,
            houseNumberAddition: $data['houseNumberAddition'] ?? null,
            houseNumberAdditions: $data['houseNumberAdditions'] ?? [],
        );
    }
}
