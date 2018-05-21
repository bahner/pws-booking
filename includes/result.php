<h2>Medlemsoversikt</h2>
<hr>
<table>
    <?php

      global $wpdb;
      global $COLUMNS;
      
      // Create a header row
      echo '<tr>';
        foreach ($COLUMNS as $key) { 
          echo '<th>', $key , '</th>';
        }
      echo '</tr>';

      // Generate a query to fetch COLUMNS from database.
      $columnlist = implode(', ', $COLUMNS);
      $members = $wpdb->get_results('SELECT ' . $columnlist . ' FROM opk_booking_user_import;', ARRAY_A); 

      // Genrate a row per member
      foreach ($members as $member) {
        echo '<tr>';
          foreach ($COLUMNS as $key) { 
            echo '<td>', $member[$key] , '</td>';
          }
        echo '</tr>';
      }
    ?>
</table>
