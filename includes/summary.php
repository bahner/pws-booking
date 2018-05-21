<pre>
<?php

global $failed_updates;
global $members;

echo 'Lest:', sizeof($members);
echo 'Aktive importerte: ', pws_booking_count_active_users('opk_booking_user_import');
echo 'Aktive: ', pws_booking_count_active_users('opk_booking_user');
echo 'Feilet:', sizeof($failed_updates);

?>   
</pre>
