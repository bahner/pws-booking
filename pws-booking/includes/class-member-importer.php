<?php
/**
 * Parses a Klubbadmin/NIF member export (xlsx) into member records ready
 * for the database.
 *
 * Replaces the old Python 2.7 handle_upload.py/common.py scripts.
 *
 * @package PWS_Booking
 */

defined( 'ABSPATH' ) || exit;

use PhpOffice\PhpSpreadsheet\IOFactory;
use Egulias\EmailValidator\EmailValidator;
use Egulias\EmailValidator\Validation\RFCValidation;

/**
 * Reads an uploaded spreadsheet and turns each row into a member array
 * matching the opk_booking_user(_import) table columns.
 */
class PWS_Booking_Member_Importer {

	/**
	 * Parse the given spreadsheet file into a list of member records.
	 *
	 * @param string $file Absolute path to the uploaded xlsx file.
	 * @return array<int, array<string, string>> List of member records.
	 * @throws Exception If the file cannot be read.
	 */
	public function parse_file( string $file ): array {

		$spreadsheet = IOFactory::load( $file );
		$sheet       = $spreadsheet->getActiveSheet();

		$rows = $sheet->toArray( null, true, true, false );

		if ( empty( $rows ) ) {
			return array();
		}

		$header = array_shift( $rows );
		$header = array_map( 'trim', $header );

		$members = array();

		foreach ( $rows as $row ) {

			// Skip completely empty rows.
			if ( empty( array_filter( $row, static fn( $value ) => '' !== trim( (string) $value ) ) ) ) {
				continue;
			}

			$row_data = array_combine( $header, array_pad( $row, count( $header ), null ) );
			$member   = $this->map_row( $row_data );

			// Only keep members whose userid ended up numeric, mirroring
			// the behaviour of the previous Python importer.
			if ( null === $member || ! ctype_digit( (string) $member['userid'] ) ) {
				continue;
			}

			$members[] = $member;
		}

		return $members;
	}

	/**
	 * Map a single spreadsheet row (keyed by header) to a member record.
	 *
	 * @param array<string, mixed> $row_data Row values keyed by header.
	 * @return array<string, string>|null Member record, or null if it lacks required fields.
	 */
	private function map_row( array $row_data ): ?array {

		$map = PWS_BOOKING_ATTRIBUTE_MAP;

		$firstname = trim( (string) ( $row_data[ $map['firstname'] ] ?? '' ) );
		$lastname  = trim( (string) ( $row_data[ $map['lastname'] ] ?? '' ) );

		if ( '' === $firstname && '' === $lastname ) {
			return null;
		}

		$member = array(
			'fullname' => trim( "$firstname $lastname" ),
			'status'   => 'active',
		);

		foreach ( $map as $our_key => $sheet_key ) {

			$value = $row_data[ $sheet_key ] ?? null;

			if ( 'email' === $our_key ) {
				$member['email'] = $this->extract_email( $value ) ?? PWS_BOOKING_DEFAULT_EMAIL;
				continue;
			}

			if ( 'userid' === $our_key ) {
				$member['userid'] = $this->generate_userid( $value ) ?? (string) ( $row_data[ $map['id'] ] ?? '' );
				continue;
			}

			if ( null !== $value && '' !== trim( (string) $value ) ) {
				$member[ $our_key ] = trim( (string) $value );
			}
		}

		return $member;
	}

	/**
	 * Normalize a phone number to its rightmost 8 digits, eg:
	 * "+47 92 88 44 92" => "92884492".
	 *
	 * @param mixed $value Raw phone number value.
	 * @return string|null Normalized userid, or null if no digits are present.
	 */
	private function generate_userid( $value ): ?string {

		if ( null === $value ) {
			return null;
		}

		$digits = preg_replace( '/\D/', '', (string) $value );

		if ( '' === $digits ) {
			return null;
		}

		return substr( $digits, -8 );
	}

	/**
	 * Extract the first valid email address from a (possibly ";"-separated
	 * list of) email value(s).
	 *
	 * @param mixed $value Raw email cell value.
	 * @return string|null First valid email address, or null if none found.
	 */
	private function extract_email( $value ): ?string {

		if ( null === $value || '' === trim( (string) $value ) ) {
			return null;
		}

		$validator = new EmailValidator();

		foreach ( explode( ';', (string) $value ) as $address ) {

			$address = trim( $address );

			if ( '' !== $address && $validator->isValid( $address, new RFCValidation() ) ) {
				return $address;
			}
		}

		return null;
	}
}
