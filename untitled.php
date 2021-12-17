<?php

/**
 *      30 2 * * * /usr/local/cpanel/3rdparty/bin/php -q /usr/local/cpanel/whostmgr/docroot/cgi/cpanel-account-dns-check/cron.php
 */
$hostname = gethostname();

$output=null;
$retval=null;
exec('php -q /usr/local/cpanel/whostmgr/docroot/cgi/cpanel-account-dns-check/dns-check.php', $output, $retval);
$salida = implode($output);


$to = 'alertas@itfinden.com';
$subject = $hostname . ' - DNS Check Account WHM Plugin';
$message = $salida;
$headers = 'From: root@' . $hostname . "\r\n" .
        'X-Mailer: PHP/' . phpversion() . "\r\n" .
        'Content-Type: text/html; charset=ISO-8859-1\r\n';

mail($to, $subject, $message, $headers);

?>