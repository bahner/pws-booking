<?php
/**
 * Encapsulates all database access for the opk_booking_user and
 * opk_booking_user_import tables.
 *
 * @package PWS_Booking
 */

defined( 'ABSPATH' ) || exit;

/**
 * Repository for reading/writing booking members via $wpdb.
 */
class PWS_Booking_Member_Repository {

	private const USER_TABLE        = 'opk_booking_user';
	private const USER_IMPORT_TABLE = 'opk_booking_user_import';

	/**
	 * @var wpdb
	 */
	private $wpdb;

	public function __construct( wpdb $wpdb ) {
		$this->wpdb = $wpdb;
	}

	/**
	 * Set the status of all users to inactive.
	 *
	 * This causes a small race condition, as no one can log in while the
	 * booking users are being updated. This is an acceptable trade-off
	 * given how much complexity the alternative would add.
	 */
	public function deactivate_all(): void {
		$this->wpdb->query( 'UPDATE ' . self::USER_TABLE . " SET status = 'inactive'" );
		$this->wpdb->query( 'UPDATE ' . self::USER_IMPORT_TABLE . " SET status = 'inactive'" );
	}

	/**
	 * Delete all members still marked inactive after an import, ie. members
	 * that no longer appear in the uploaded spreadsheet.
	 */
	public function delete_inactive(): void {
		$this->wpdb->query( 'DELETE FROM ' . self::USER_TABLE . " WHERE status = 'inactive'" );
		$this->wpdb->query( 'DELETE FROM ' . self::USER_IMPORT_TABLE . " WHERE status = 'inactive'" );
	}

	/**
	 * Count active users in the given table.
	 *
	 * @param string $table Either 'opk_booking_user' or 'opk_booking_user_import'.
	 */
	public function count_active( string $table ): int {

		if ( ! in_array( $table, array( self::USER_TABLE, self::USER_IMPORT_TABLE ), true ) ) {
			throw new InvalidArgumentException( "Unknown table: $table" );
		}

		return (int) $this->wpdb->get_var(
			$this->wpdb->prepare( 'SELECT COUNT(*) FROM ' . $table . ' WHERE status = %s', 'active' )
		);
	}

	/**
	 * Upsert (replace) a list of members into both booking tables.
	 *
	 * All users are expected to already have been marked inactive via
	 * deactivate_all() before this is called.
	 *
	 * @param array<int, array<string, string>> $members Member records.
	 * @return array<int|string, array<string, string>> Members that failed to update, keyed by id.
	 */
	public function upsert_members( array $members ): array {

		$failed = array();

		foreach ( $members as $member ) {

			$updated = $this->wpdb->replace( self::USER_IMPORT_TABLE, $member );

			if ( false === $updated ) {
				$failed[ $member['id'] ] = $member;
			}

			$updated = $this->wpdb->replace(
				self::USER_TABLE,
				array(
					'id'     => $member['id'],
					'userid' => $member['userid'],
					'status' => $member['status'],
				)
			);

			if ( false === $updated ) {
				$failed[ $member['id'] ] = $member;
			}
		}

		return $failed;
	}

	/**
	 * Fetch all imported members, limited to the given columns.
	 *
	 * @param array<int, string> $columns Column names to select.
	 * @return array<int, array<string, string>>
	 */
	public function get_all( array $columns ): array {

		// Column names come from our own trusted constant list, never from
		// user input; esc_sql() is used defensively regardless.
		$column_list = implode( ', ', array_map( 'esc_sql', $columns ) );

		$results = $this->wpdb->get_results(
			'SELECT ' . $column_list . ' FROM ' . self::USER_IMPORT_TABLE,
			ARRAY_A
		);

		return $results ?? array();
	}
}
