<?php

/*
Plugin Name: PWS Booking
Plugin URI: https://github.com/bahner/pws-booking
Description: Last opp et regneark-fra klubbadmin og oppdatér booking i gammel løsning
Author URI: http://flightlog.org/fl.html?l=1&a=28&user_id=7288
Author: Lars Bahner
License URI: https://www.gnu.org/licenses/gpl-3.0.txt
License: GPL3
Version: 0.0.1
*/

defined( 'ABSPATH' ) or die( 'Not properly invoked. Plugin now dies.' );

# Some constants and possible config
$WP_PLUGIN_DIR = plugin_dir_path( __FILE__ );
require "${WP_PLUGIN_DIR}/includes/constants.php";


function pws_booking_admin_menu() {

  /*
   Set role to "import" which is a bit misleading, but close enough for government work.
   All administrators will have this right.
  */

 add_menu_page('Vangen booking', 'PWS Booking', 'import', 'pws-booking', 'pws_booking_admin_menu_welcome');
 add_submenu_page('pws-booking', 'Medlemsoversikt', 'Medlemsoversikt', 'import', 'pws-booking-medlemmer', 'pws_booking_admin_menu_users');
 add_submenu_page('pws-booking', 'Vedlikehold', 'Vedlikehold', 'import', 'pws-booking', 'pws_booking_admin_menu_upload');

}

add_action('admin_menu', 'pws_booking_admin_menu');

function pws_booking_admin_menu_welcome() {

  global $WELCOME;
  include $WELCOME;

}

function pws_booking_admin_menu_users() {

  global $RESULT;
  include $RESULT;

  echo "Showing users!";

}

function pws_booking_admin_menu_upload() {

  global $RESULT;
  global $UPLOAD;

  if(isset($_FILES['pws_booking_medlemsliste'])) {

    $members = pws_booking_handle_post();

    if (sizeof($members) > 100) {

      pws_booking_deactivate_all_bookers();
      pws_booking_upsert_users($members);

      include $RESULT;
      return true;

    } else {
    
      echo "DATABASE IKKE OPPDATERT. PUSSIG FÅ MEDLEMMER!";
    }
  }
  
  include $UPLOAD;
}


function pws_booking_handle_post(){

  /*

    This function finds the uploaded filename and passes
    the filename to a bundled python-script. Python is
    orders of magnitude better at handle Spreadsheets.
 
    The python script used is Python 2.7 because this is
    the only version available to us in production at the
    time of writing. This is also the reason it's hardcoded.

    Returns an array of associative arrays of users

  */

  global $WP_PLUGIN_DIR;

  // First check if the file appears on the _FILES array
  // The key_value is defined in the the post form.
  if(isset($_FILES['pws_booking_medlemsliste'])){

    /*
      Get the upload data into an associative aray (dict). The
      'test_form' => False is required to avoid wordpress trying
      to do some form of parsing / validating, which isn't really
      useful  anyways.
      It's important, so leave it for now!
    */

    $uploaded=wp_handle_upload($_FILES['pws_booking_medlemsliste'], array('test_form' => FALSE));

    // Error checking using WP functions
    if(is_wp_error($uploaded)){
      echo "Feil ved opplasting: " . $uploaded->get_error_message();
    }else{

      $membersheet = $uploaded['file'];
      $parser = "${WP_PLUGIN_DIR}parse_upload.py";

      $json = exec("/usr/bin/python2.7 $parser $membersheet");

      return json_decode($json, true); // Return a list of asoociative arrays of users.

    }
  }
}

function pws_booking_deactivate_all_bookers() {

  /*
    This function simply sets the status of all users to inactive.
    This causes a small race condition, as no one can log in while
    the booking users are being updated.
    This seems like a risk that's acceptable seeing how much complexity
    such a simple solution renders redundant.

    This plugin then reads in all the active users and updates their
    status as required. No one is deleted, so old bookings will still
    exist - unless you have changed your telephone number .....
  */

  global $wpdb;

  $wpdb->query('UPDATE opk_booking_user SET status = "inactive"');
  $wpdb->query('UPDATE opk_booking_user_import SET status = "inactive"');

}

function pws_booking_upsert_users($members) {

  /*
    Receive a list users in JSON format. Convert this list to
    a list of associative arrays og users.

    Don't know if arrays are better than objects, but I am more
    comfortable with them. Feel free to rewrite for performance.

    Then upsert, than is: update existing users, but insert any
    possible new users.

    All users already have status set to inactive before calling
    this.

    The parse_upload.py script should already have structured
    the data correctly, as python does this much better. As such
    it is now possible to match the data 1-2-1 to the database
    structure.

  */

  global $wpdb;

  //echo var_dump($members);

  foreach ($members as $member) {

    // Update existing users, or create new.
    // Ref. https://codex.wordpress.org/Class_Reference/wpdb#REPLACE_row
    $wpdb->replace(
      'opk_booking_user_import',
      $member
    );
    $wpdb->replace(
      'opk_booking_user',
      array (
        'id' => $member['id'],
        'userid' => $member['userid'],
        'status' => $member['status'],
      )
    );
  }
}

?>
