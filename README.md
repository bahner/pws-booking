PWS Booking
===

A wordpress plugin for updating members in OPKs member database for booking at Vangen.

Requires:

  * Wordpress
  * PHP 8.0+
  * Composer (build-time only, not required at runtime)

TL;DR!
---
Build the module by typing
```bash
make
```

Then install the produced `pws-booking.zip` in Wordpress. That's it. No configuration is required.
`vendor/` (PhpSpreadsheet and its dependencies) is bundled into the zip, so nothing
needs to be installed separately on the webhost.

Development requirements
---
You probably want docker and docker-compose installed.
Then you can pull up a dockerized version of wordpress
with the command:
```bash
make up
```
And similarly ```make down``` to take it down again

Build requirements
---
The build process requires make, composer and zip. This is normally installed as:
```bash
sudo apt install build-essential zip composer
```

Running `make release` will:
1. Clean any previous build artifacts.
2. Run `composer install --no-dev --optimize-autoloader` to fetch PhpSpreadsheet
   and its dependencies into `pws-booking/vendor`.
3. Zip up the plugin, including `vendor/`, into `pws-booking.zip`.

Excel parsing
---
Member spreadsheets (xlsx, exported from Klubbadmin/minidrett.no) are parsed
directly in PHP using [PhpSpreadsheet](https://github.com/PHPOffice/PhpSpreadsheet).
There is no external script and no Python dependency anymore.

Schema changes
---
`id` is PersonId from NIF. This requires the current `AUTO INCREMENT` setting for id to be removed. The other id, `userid` is set to a mangled 8-char max version of `phonemobile` with a fallback to `id`.

Update the tables as follows:

```sql
ALTER TABLE `opk`.`opk_booking_user`
CHANGE COLUMN `id` `id` MEDIUMINT(8) UNSIGNED NOT NULL ,
CHANGE COLUMN `userid` `userid` CHAR(8) NOT NULL DEFAULT '' ;

ALTER TABLE `opk`.`opk_booking_user_import`
CHANGE COLUMN `id` `id` MEDIUMINT(8) UNSIGNED NOT NULL ,
CHANGE COLUMN `userid` `userid` CHAR(8) NOT NULL DEFAULT '' ;
```

2018-05-21: bahner
2026-08-17: bahner - Rewrote the plugin to be 100% PHP. Removed the Python 2.7
dependency in favor of PhpSpreadsheet, removed the unused GraphQL prototype,
added nonce/CSRF protection and prepared statements.
