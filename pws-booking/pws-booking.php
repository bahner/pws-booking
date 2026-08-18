<?php
/**
 * Plugin Name: PWS Booking
 * Plugin URI:  https://github.com/bahner/pws-booking
 * Description: Last opp et regneark fra Klubbadmin og oppdatér booking i gammel løsning.
 * Author:      Lars Bahner
 * Author URI:  http://flightlog.org/fl.html?l=1&a=28&user_id=7288
 * License:     GPL3
 * License URI: https://www.gnu.org/licenses/gpl-3.0.txt
 * Version:     2.0.0
 * Requires PHP: 8.2
 *
 * @package PWS_Booking
 */

defined( 'ABSPATH' ) || die( 'Not properly invoked. Plugin now dies.' );

define( 'PWS_BOOKING_DIR', plugin_dir_path( __FILE__ ) );
define( 'PWS_BOOKING_FILE_FIELD', 'pws_booking_medlemsliste' );
define( 'PWS_BOOKING_NONCE_ACTION', 'pws_booking_upload' );

if ( ! file_exists( PWS_BOOKING_DIR . 'vendor/autoload.php' ) ) {
	add_action(
		'admin_notices',
		static function () {
			echo '<div class="notice notice-error"><p>' .
				esc_html__( 'PWS Booking: vendor/autoload.php mangler. Kjør "composer install" eller installer en zip bygget med "make".', 'pws-booking' ) .
				'</p></div>';
		}
	);
	return;
}

require PWS_BOOKING_DIR . 'vendor/autoload.php';
require PWS_BOOKING_DIR . 'includes/constants.php';
require PWS_BOOKING_DIR . 'includes/class-member-repository.php';
require PWS_BOOKING_DIR . 'includes/class-member-importer.php';

/**
 * Register the admin menu and submenus.
 *
 * The 'import' capability is a bit misleadingly named for our purposes,
 * but it's close enough: all administrators have this capability.
 */
function pws_booking_admin_menu(): void {

	add_menu_page( 'Vangen booking', 'PWS Booking', 'import', 'pws-booking', 'pws_booking_admin_menu_welcome' );
	add_submenu_page( 'pws-booking', 'Medlemsoversikt', 'Medlemsoversikt', 'import', 'pws-booking-users', 'pws_booking_admin_menu_users' );
	add_submenu_page( 'pws-booking', 'Oppdatering', 'Oppdatering', 'import', 'pws-booking-upload', 'pws_booking_admin_menu_upload' );
}
add_action( 'admin_menu', 'pws_booking_admin_menu' );

/**
 * Get the shared member repository instance.
 */
function pws_booking_repository(): PWS_Booking_Member_Repository {

	global $wpdb;
	static $repository = null;

	if ( null === $repository ) {
		$repository = new PWS_Booking_Member_Repository( $wpdb );
	}

	return $repository;
}

/**
 * Render the "Oppdatering" (upload) admin page and process an upload, if any.
 */
function pws_booking_admin_menu_upload(): void {

	if ( isset( $_FILES[ PWS_BOOKING_FILE_FIELD ] ) ) {

		check_admin_referer( PWS_BOOKING_NONCE_ACTION );

		$members = pws_booking_handle_upload();

		if ( null === $members ) {
			// An error was already reported by pws_booking_handle_upload().
		} elseif ( count( $members ) > PWS_BOOKING_MIN_MEMBERS ) {

			$repository = pws_booking_repository();
			$repository->deactivate_all();
			$failed_updates = $repository->upsert_members( $members );

			// Delete old members only if there were no failed updates.
			if ( empty( $failed_updates ) ) {
				$repository->delete_inactive();
			}

			include PWS_BOOKING_DIR . 'includes/summary.php';
		} else {
			echo '<p>' . esc_html__( 'DATABASE IKKE OPPDATERT. PUSSIG FÅ MEDLEMMER!', 'pws-booking' ) . '</p>';
		}
	}

	include PWS_BOOKING_DIR . 'includes/upload.php';
}

/**
 * Render the plugin's welcome/landing admin page.
 */
function pws_booking_admin_menu_welcome(): void {
	include PWS_BOOKING_DIR . 'includes/welcome.php';
}

/**
 * Render the member overview admin page.
 */
function pws_booking_admin_menu_users(): void {
	include PWS_BOOKING_DIR . 'includes/members.php';
}

/**
 * Handle the uploaded spreadsheet: validate, store via WordPress' own
 * upload handler, parse it into member records and always delete the
 * uploaded file afterwards.
 *
 * @return array<int, array<string, string>>|null List of member records, or null on error.
 */
function pws_booking_handle_upload(): ?array {

	// 'test_form' => false avoids WordPress trying to validate this as a
	// generic post attachment upload, which isn't useful here.
	$uploaded = wp_handle_upload( $_FILES[ PWS_BOOKING_FILE_FIELD ], array( 'test_form' => false ) );

	if ( is_wp_error( $uploaded ) ) {
		echo '<p>' . esc_html__( 'Feil ved opplasting: ', 'pws-booking' ) . esc_html( $uploaded->get_error_message() ) . '</p>';
		return null;
	}

	$membersheet = $uploaded['file'];

	try {
		$importer = new PWS_Booking_Member_Importer();
		return $importer->parse_file( $membersheet );
	} catch ( Throwable $e ) {
		echo '<p>' . esc_html__( 'Feil ved lesing av regneark: ', 'pws-booking' ) . esc_html( $e->getMessage() ) . '</p>';
		return null;
	} finally {
		// The uploaded file is never needed after parsing; it must not be
		// left behind on the server.
		if ( file_exists( $membersheet ) ) {
			wp_delete_file( $membersheet );
		}
	}
}
