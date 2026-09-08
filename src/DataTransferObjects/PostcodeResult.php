<?php

namespace Rapidez\Postcode\DataTransferObjects;

final class PostcodeResult
{
    public function __construct(
        public readonly bool $found,
        public readonly ?string $street = null,
        public readonly ?string $city = null,
        public readonly ?string $province = null,
        public readonly ?string $postcode = null,
        public readonly ?string $houseNumber = null,
        public readonly ?string $houseNumberAddition = null,
        public readonly array $houseNumberAdditions = [],
    ) {}

    public function toArray(): array
    {
        return [
            'found' => $this->found,
            'street' => $this->street,
            'city' => $this->city,
            'province' => $this->province,
            'postcode' => $this->postcode,
            'houseNumber' => $this->houseNumber,
            'houseNumberAddition' => $this->houseNumberAddition,
            'houseNumberAdditions' => $this->houseNumberAdditions,
        ];
    }
}
