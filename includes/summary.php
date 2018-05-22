<pre>
<?php

echo 'Lest:', sizeof($members), '<br>';
echo 'Aktive importerte: ', pws_booking_count_active_users('opk_booking_user_import'), '<br>';
echo 'Aktive: ', pws_booking_count_active_users('opk_booking_user'), '<br>';
echo 'Feilet:', sizeof($failed_updates), '<br>';

?>   
</pre>
