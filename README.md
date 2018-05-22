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

Vi setter `id` til "være PersonId fra NIf: Derfor må id kollonen endres, slik at vi ikke har
`AUTO INCREMENT` på den. userid må utvides fra nuværende 8 tegn til 16 (egentig 15, men det
er greit å ha et tegn å gå på).
Ref. https://en.wikipedia.org/wiki/Telephone_numbering_plan

Oppdateringen består i følgende kode:

```sql
ALTER TABLE `opk`.`opk_booking_user`
CHANGE COLUMN `id` `id` MEDIUMINT(8) UNSIGNED NOT NULL ,
CHANGE COLUMN `userid` `userid` VARCHAR(16) NOT NULL DEFAULT '' ;

ALTER TABLE `opk`.`opk_booking_user_import`
CHANGE COLUMN `id` `id` MEDIUMINT(8) UNSIGNED NOT NULL ,
CHANGE COLUMN `userid` `userid` VARCHAR(16) NOT NULL DEFAULT '' ;
```

Build module
---
To pack type:
```
make
```
Then upload `pws_booking.zip` to Wordpress as a plugin. Make sure `openpyxl`
is installed as descrived above. This has been done at domeneshop already.

2018-05-21: bahner
