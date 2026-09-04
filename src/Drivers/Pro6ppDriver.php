<?php

namespace Rapidez\Postcode\Drivers;

use Illuminate\Support\Facades\Http;
use Rapidez\Postcode\Contracts\PostcodeDriver;
use Rapidez\Postcode\DataTransferObjects\PostcodeResult;

class Pro6ppDriver implements PostcodeDriver
{
    public function __construct(protected ?string $key)
    {
    }

    public function lookup(string $postcode, string $houseNumber, ?string $addition = null): PostcodeResult
    {
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ])
            ->withQueryParameters(['auth_key' => $this->key])
            ->baseUrl('https://api.pro6pp.nl/v1')
            ->get('/autocomplete', ['nl_sixpp' => $postcode]);

        if ($response->failed()) {
            return new PostcodeResult(found: false);
        }

        $result = collect($response->json('results'))
            ->first(fn ($result) => $this->isHouseNumberValid($houseNumber, $addition, $result['streetnumbers']));

        if (! $result) {
            return new PostcodeResult(found: false);
        }

        return new PostcodeResult(
            found: true,
            street: $result['street'] ?? null,
            city: $result['city'] ?? null,
            province: $result['province'] ?? null,
            postcode: $postcode,
            houseNumber: $houseNumber,
            houseNumberAddition: $addition,
        );
    }

    protected function isHouseNumberValid(string $houseNumber, ?string $addition, string $validRanges): bool
    {
        $houseNumberWithoutAddition = (int) preg_replace('/\D/', '', $houseNumber);
        $fullHouseNumber = trim($houseNumber . ($addition ? ' ' . $addition : ''));

        return collect(explode(';', $validRanges))->contains(function ($range) use ($houseNumber, $fullHouseNumber, $houseNumberWithoutAddition) {
            $range = trim($range);

            if ($range === $houseNumber || $range === $fullHouseNumber || (int) preg_replace('/\D/', '', $range) === $houseNumberWithoutAddition) {
                return true;
            }

            if (str_contains($range, '-')) {
                [$start, $end] = array_map('trim', explode('-', $range));

                return is_numeric($start) && is_numeric($end) && $houseNumberWithoutAddition >= $start && $houseNumberWithoutAddition <= $end;
            }

            return false;
        });
    }
}
