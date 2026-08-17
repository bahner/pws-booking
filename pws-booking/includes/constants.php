<?php
/**
 * Constants and configuration used throughout the plugin.
 *
 * @package PWS_Booking
 */

defined( 'ABSPATH' ) || exit;

/**
 * Columns present in the opk_booking_user_import table, in display order.
 */
const PWS_BOOKING_COLUMNS = array(
	'id',
	'fullname',
	'status',
	'email',
	'address1',
	'phonemobile',
	'postaladdress',
	'postalcode',
	'userid',
);

/**
 * Email address used when the import doesn't contain a valid one.
 */
const PWS_BOOKING_DEFAULT_EMAIL = 'MANGLER.EPOST@OPK.NO';

/**
 * Maps our internal member field names to the column headers used in the
 * Klubbadmin/NIF export spreadsheet.
 */
const PWS_BOOKING_ATTRIBUTE_MAP = array(
	'address1'      => 'Adresse',
	'email'         => 'E-post',
	'firstname'     => 'Fornavn',
	'lastname'      => 'Etternavn',
	'phonemobile'   => 'Tlf. mobil',
	'postaladdress' => 'Postadresse',
	'postalcode'    => 'Postnr',
	'userid'        => 'Tlf. mobil',
	'id'            => 'PersonId',
);

/**
 * Minimum number of members required in an import before the database is
 * updated. Guards against accidentally wiping the member list with a
 * malformed or empty spreadsheet.
 */
const PWS_BOOKING_MIN_MEMBERS = 100;
