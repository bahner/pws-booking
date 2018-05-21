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
require 'includes/constants.php';
require 'includes/functions.php';


function pws_booking_admin_menu() {

  /*
   Set role to "import" which is a bit misleading, but close enough for government work.
   All administrators will have this right.
  */

 add_menu_page('Vangen booking', 'PWS Booking', 'import', 'pws-booking', 'pws_booking_admin_menu_welcome');
 add_submenu_page('pws-booking', 'Medlemsoversikt', 'Medlemsoversikt', 'import', 'pws-booking-users', 'pws_booking_admin_menu_users');
 add_submenu_page('pws-booking', 'Oppdatering', 'Oppdatering', 'import', 'pws-booking-upload', 'pws_booking_admin_menu_upload');

}

add_action('admin_menu', 'pws_booking_admin_menu');


