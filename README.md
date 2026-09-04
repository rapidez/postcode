# Rapidez Postcode

Generic postcode/address lookup for Rapidez, with pluggable drivers. Listens to the
`postcode-change` Vue event already wired into Rapidez's own address form, so it works
out of the box without needing to change any checkout/address form Blade templates.

Ships with three drivers out of the box, each calling its own API directly - no Magento
configuration required:

- `postcodeeu` - [Postcode.eu](https://www.postcode.eu/)
- `pro6pp` - [Pro6pp](https://pro6pp.nl/)
- `postcodeservice` - [Postcodeservice](https://www.postcodeservice.com/)

## Requirements

- PHP ^8.2
- `rapidez/core` ^5.0

## Installation

```
composer require rapidez/postcode
```

## Configuration

Publish the config with:
```
php artisan vendor:publish --tag=rapidez-postcode-config
```

This adds `config/rapidez/postcode.php`, which picks the active driver via `POSTCODE_DRIVER` and
holds each driver's credentials. Pick one driver and fill in its `.env` values:

```env
POSTCODE_DRIVER=postcodeeu
POSTCODE_EU_API_KEY=
POSTCODE_EU_API_SECRET=
```

```env
POSTCODE_DRIVER=pro6pp
PRO6PP_API_KEY=
```

```env
POSTCODE_DRIVER=postcodeservice
POSTCODESERVICE_CLIENT_ID=
POSTCODESERVICE_SECURE_CODE=
```

`postcodeservice`'s defaults are the public test credentials, so that driver works out of the box
without any configuration for testing purposes.

Switching drivers is purely a `.env`/config change - no code changes, and no changes to the
route, controller or JavaScript.

## Response shape

The `/api/postcode` endpoint (and each driver's `lookup()` method) returns:

```json
{
    "found": true,
    "street": "Dam",
    "city": "Amsterdam",
    "province": "Noord-Holland",
    "postcode": "1012JS",
    "houseNumber": "1",
    "houseNumberAddition": "",
    "houseNumberAdditions": [""]
}
```

`found` is `false` (with all other fields `null` or empty) for an invalid or non-existent
postcode/house number combination.

## Adding a driver

1. Create a class implementing `Rapidez\Postcode\Contracts\PostcodeDriver`:
   ```php
   class MyServiceDriver implements PostcodeDriver
   {
       public function lookup(string $postcode, string $houseNumber, ?string $addition = null): PostcodeResult
       {
           // Call the external API and return a PostcodeResult.
       }
   }
   ```
2. Add a `create<Name>Driver()` method to `Rapidez\Postcode\PostcodeManager` that resolves it from
   config (the name maps to the `drivers.<name>` config key and the `POSTCODE_DRIVER` value, e.g.
   `createMyserviceDriver()` for `myservice`).
3. Add a `drivers.<name>` section to `config/rapidez/postcode.php` for its credentials/settings.

No changes to the route, controller or JavaScript are needed - the manager resolves whichever
driver is configured, and the controller/JS are entirely driver-agnostic.

## License

GNU General Public License v3. Please see [License File](LICENSE) for more information.
