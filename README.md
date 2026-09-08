# Rapidez Postcode

Generic postcode/address lookup for Rapidez, with pluggable drivers. Listens to the
`postcode-change` Vue event already wired into Rapidez's own address form, so it works
out of the box without needing to change any checkout/address form Blade templates.

Dutch (NL) addresses only for now; postcode validation and all three drivers' APIs are
NL-specific.

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

Test credentials for `postcodeservice` are documented at
[developers.postcodeservice.com](https://developers.postcodeservice.com/#authenticating-requests)
if you want to try that driver without your own account; they're not hardcoded as a default here
since the docs note they may change without prior notice.

Switching drivers is purely a `.env`/config change; no code changes, and no changes to the
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

A driver is a class implementing `Rapidez\Postcode\Contracts\PostcodeDriver`:

```php
class MyServiceDriver implements PostcodeDriver
{
    public function lookup(string $postcode, string $houseNumber, ?string $addition = null): PostcodeResult
    {
        // Call the external API and return a PostcodeResult.
    }
}
```

Register it from your own project or package; no changes to this package needed; by extending
the manager, e.g. in a service provider's `boot()` method:

```php
$this->app->make(\Rapidez\Postcode\PostcodeManager::class)->extend(
    'myservice',
    fn () => new MyServiceDriver(config('rapidez.postcode.drivers.myservice.key')),
);
```

Then select it as usual with `POSTCODE_DRIVER=myservice`. No changes to the route, controller or
JavaScript are needed either way; they're entirely driver-agnostic.

To contribute a new driver to this package itself instead, add a `create<Name>Driver()` method to
`PostcodeManager` (the name maps to the `drivers.<name>` config key and the `POSTCODE_DRIVER`
value, e.g. `createMyserviceDriver()` for `myservice`) and a matching `drivers.<name>` section to
`config/rapidez/postcode.php`.

## License

GNU General Public License v3. Please see [License File](LICENSE) for more information.
