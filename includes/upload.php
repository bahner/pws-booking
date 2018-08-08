<h2>Last opp medlemsliste</h2>
Filen hentes fra minidrett.no i Klubbadmin. Filen *må* inneholde feltene:
<ul>
  <li>Fornavn</li>
  <li>Etternavn</li>
  <li>Tlf. mobil</li>
  <li>E-post</li>
</ul>
Vangen booking vil bruke <strong>mobilnummer</strong> som innlogging.
<I>
Filen som lastes opp vil bli behandlet hvis data er korrekte, MEN filen vil alltid slettes i efterkant. Dette er et design-valg. Det skal ikke ligge løse versjoner igjen, så snart den er lest.
</I>
<form  method="post" enctype="multipart/form-data">
  <input type='file' id='pws_booking_medlemsliste' name='pws_booking_medlemsliste'></input>
  <?php submit_button('Last opp') ?>
</form>
