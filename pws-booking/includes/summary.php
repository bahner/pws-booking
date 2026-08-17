<?php defined( 'ABSPATH' ) || exit; ?>
<pre>
<?php

$repository = pws_booking_repository();

echo 'Lest: ' . esc_html( count( $members ) ) . '<br>';
echo 'Aktive importerte: ' . esc_html( $repository->count_active( 'opk_booking_user_import' ) ) . '<br>';
echo 'Aktive: ' . esc_html( $repository->count_active( 'opk_booking_user' ) ) . '<br>';
echo 'Feilet: ' . esc_html( count( $failed_updates ) ) . '<br>';

?>
</pre>
