<?php defined( 'ABSPATH' ) || exit; ?>
<h2>Medlemsoversikt</h2>
<hr>
<table>
    <?php

      $repository = pws_booking_repository();
      $columns    = PWS_BOOKING_COLUMNS;

      // Create a header row.
      echo '<tr>';
      foreach ( $columns as $key ) {
        echo '<th>' . esc_html( $key ) . '</th>';
      }
      echo '</tr>';

      $members = $repository->get_all( $columns );

      // Generate a row per member.
      foreach ( $members as $member ) {
        echo '<tr>';
        foreach ( $columns as $key ) {
          echo '<td>' . esc_html( $member[ $key ] ?? '' ) . '</td>';
        }
        echo '</tr>';
      }
    ?>
</table>
