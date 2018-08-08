PWS Booking
===

A wordpress plugin for updating members in OPKs member database for booking at Vangen.

Requires:

  * Wordpress
  * PHP5.6 +
  * Python2.7
    * openpyxl
    * vaildate_email

TL;DR!
---
Build the module by typing 
```bash
make
```

Then install the produced `pws-booking.zip` in Wordpress. That's it. No configuration is required.

Build requirements
---
The build process normally requires make and unzip. YMMV! This is normally installed as:
```bash
sudo apt install buildessential zip
```

Python
---
The python packages can be installed with pip. On domeneshop, we don't have administrative access, but the packages can be installed for ourselves like this:
```
pip2 install --user openpyxl validate_email
```
This is true for our hosting at Domeneshop.

Any version of PHP higher than 5.6 should work.

NB! Later of version of python may be used, but is not available in an usable fashion at domeneshop.

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
