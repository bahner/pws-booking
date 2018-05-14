<?php

/*
Plugin Name: PWS Booking
Description: Last opp et regneark-fra klubbadmin og oppdatér booking i gammel løsning
Author: Lars Bahner
Version: 0.0.1
*/

add_action('admin_menu', 'pws_booking_setup_menu');

# The parser to use
$WP_PLUGIN_DIR = plugin_dir_path( __FILE__ );
$PARSER = "${WP_PLUGIN_DIR}parse_upload.py";

function pws_booking_setup_menu() {

/*
 Set role to "import" which is a bit misleading, but close enough for government work.
 All administrators will have this right.

*/


 add_menu_page('Oppdater gammel medlemsdatabase for Vangen booking', 'PWS Booking', 'import', 'pws-booking', 'pws_booking_init');

}


function pws_booking_init() {

 pws_booking_handle_post();

?>
<h1>Oppdater gammel medlemsdatabase for Vangen booking</h1>
<h2>Last opp medlemsliste</h2>
Filen hentes fra minidrett.no i Klubbadmin. Filen *må* inneholde feltene:
<ul>
  <li>PersonId</li>
  <li>Fornavn</li>
  <li>Etternavn</li>
  <li>Tlf. mobil</li>
</ul>
Vangen booking vil bruke <strong>mobilnummer</strong> som innlogging.
<form  method="post" enctype="multipart/form-data">
  <input type='file' id='pws_booking_medlemsliste' name='pws_booking_medlemsliste'></input>
  <?php submit_button('Last opp') ?>
</form>
<?php
}


function pws_booking_handle_post(){

  global $PARSER;

  // First check if the file appears on the _FILES array
  if(isset($_FILES['pws_booking_medlemsliste'])){
  
    $uploaded=wp_handle_upload($_FILES['pws_booking_medlemsliste'], array('test_form' => FALSE));

    // Error checking using WP functions
    if(is_wp_error($uploaded)){
      echo "Feil ved opplasting: " . $uploaded->get_error_message();
    }else{
      $membersheet = $uploaded['file'];
      echo exec("/usr/bin/python2.7 $PARSER $membersheet");
    }
  }
}
?>
