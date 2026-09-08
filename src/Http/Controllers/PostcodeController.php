<?php

namespace Rapidez\Postcode\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Rapidez\Postcode\PostcodeManager;

class PostcodeController
{
    public function __construct(protected PostcodeManager $manager) {}

    public function __invoke(Request $request)
    {
        $request->merge([
            'postcode' => strtoupper(str_replace(' ', '', $request->postcode)),
        ])->validate([
            'postcode' => ['required', 'string', 'regex:/^[1-9][0-9]{3}[A-Z]{2}$/'],
            'housenumber' => ['required', 'string'],
            'addition' => ['nullable', 'string'],
        ]);

        $driver = config('rapidez.postcode.driver');
        $addition = $request->addition ?: null;
        $cacheKey = "postcode-{$driver}-{$request->postcode}-{$request->housenumber}-{$addition}";

        $result = Cache::rememberForever($cacheKey, fn () => $this->manager->driver()->lookup(
            $request->postcode,
            $request->housenumber,
            $addition,
        ));

        return response()->json($result->toArray());
    }
}
