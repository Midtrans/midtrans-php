> Warning: This note is for developer/maintainer of this package only

## Updating Package

- Make your changes
- Update `version` value on `composer.json`
- Update library version header on `ApiRequestor.php`
- Commit and push changes to Github master branch
- Create a [Github Release](https://github.com/Midtrans/midtrans-php/releases) with the target version
- Github Release and Master Branch is automatically synced to [the Packagist version](https://packagist.org/packages/midtrans/midtrans-php)
  - Because of configured integration on Github & Packagist
  - To edit integration config sign in with the Midtrans Packagist Account
