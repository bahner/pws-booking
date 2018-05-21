<h1>Oppdater gammel medlemsdatabase for Vangen booking</h1>
<h2>Last opp medlemsliste</h2>
Filen hentes fra minidrett.no i Klubbadmin. Filen *må* inneholde feltene:
<ul>
  <li>Fornavn</li>
  <li>Etternavn</li>
  <li>Tlf. mobil</li>
</ul>
Vangen booking vil bruke <strong>mobilnummer</strong> som innlogging.
<form  method="post" enctype="multipart/form-data">
  <input type='file' id='pws_booking_medlemsliste' name='pws_booking_medlemsliste'></input>
  <?php submit_button('Last opp') ?>
</form>
