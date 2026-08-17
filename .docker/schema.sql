-- Dev/testing schema for pws-booking's two legacy tables.
--
-- These tables predate this plugin and normally already exist on
-- production (Domeneshop). A fresh WordPress/MariaDB instance (eg. via
-- docker-compose.yaml) doesn't have them, so this script recreates a
-- reasonable schema for local testing.
--
-- Column types/constraints for `id` and `userid` follow the notes in
-- README.md (id = PersonId from NIF, no AUTO_INCREMENT; userid = mangled
-- 8-char phone number with fallback to id).

CREATE TABLE IF NOT EXISTS `opk_booking_user` (
  `id`     MEDIUMINT(8) UNSIGNED NOT NULL,
  `userid` CHAR(8) NOT NULL DEFAULT '',
  `status` VARCHAR(20) NOT NULL DEFAULT 'active',
  PRIMARY KEY (`id`),
  UNIQUE KEY `userid` (`userid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `opk_booking_user_import` (
  `id`             MEDIUMINT(8) UNSIGNED NOT NULL,
  `fullname`       VARCHAR(255) NOT NULL DEFAULT '',
  `status`         VARCHAR(20) NOT NULL DEFAULT 'active',
  `email`          VARCHAR(255) NOT NULL DEFAULT '',
  `address1`       VARCHAR(255) NOT NULL DEFAULT '',
  `phonemobile`    VARCHAR(50) NOT NULL DEFAULT '',
  `postaladdress`  VARCHAR(255) NOT NULL DEFAULT '',
  `postalcode`     VARCHAR(20) NOT NULL DEFAULT '',
  `userid`         CHAR(8) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
