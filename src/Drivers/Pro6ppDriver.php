<?php

namespace Rapidez\Postcode\Drivers;

use Illuminate\Support\Facades\Http;
use Rapidez\Postcode\Contracts\PostcodeDriver;
use Rapidez\Postcode\DataTransferObjects\PostcodeResult;

class Pro6ppDriver implements PostcodeDriver
{
    public function __construct(protected ?string $key)
    {
        if (! $this->key) {
            throw new \InvalidArgumentException('The pro6pp driver requires PRO6PP_API_KEY to be set.');
        }
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

    /**
     * Pro6pp returns one `streetnumbers` string per street, listing every house number (and
     * addition) that exists on it, separated by semicolons, e.g. "1;11-13;21-27;1 A;1 B" means
     * house numbers 1, 11 through 13 and 21 through 27, and 1A/1B, all exist on this street.
     *
     * This checks whether the given house number (with its addition) is covered by that
     * list, either as an exact entry or within one of the numeric ranges.
     */
    protected function isHouseNumberValid(string $houseNumber, ?string $addition, string $validRanges): bool
    {
        $houseNumberWithoutAddition = (int) preg_replace('/\D/', '', $houseNumber);
        $fullHouseNumber = trim($houseNumber . ($addition ? ' ' . $addition : ''));

        return collect(explode(';', $validRanges))->contains(function ($range) use ($houseNumber, $fullHouseNumber, $houseNumberWithoutAddition) {
            $range = trim($range);

            if ($range === $houseNumber || $range === $fullHouseNumber || (int) preg_replace('/\D/', '', $range) === $houseNumberWithoutAddition) {
                return true;
            }

            // A numeric range entry, e.g. "11-13": valid if the house number falls within it.
            if (str_contains($range, '-')) {
                [$start, $end] = array_map('trim', explode('-', $range));

                return is_numeric($start) && is_numeric($end) && $houseNumberWithoutAddition >= $start && $houseNumberWithoutAddition <= $end;
            }

            return false;
        });
    }
}
