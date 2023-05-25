<?php

if (isset($_GET['domain'])) {
    $domain = $_GET['domain'] . ".ly";
    $socket = @fsockopen("whois.nic.ly", 43, $errno, $errstr, 5);

    if (!$socket) {
        header("HTTP/1.1 500 Internal Server Error");
        echo "Could not connect to WHOIS server. Error: $errno - $errstr";
        error_log("Could not connect to WHOIS server: Error $errno - $errstr");
        exit;
    }

    $query = "$domain\r\n";
    fputs($socket, $query);
    $response = '';

    while (!feof($socket)) {
        $response .= fgets($socket, 128);
    }

    fclose($socket);

    if (strpos($response, 'Not Found') !== false) {
        echo "Domain $domain is available.<br><br>";
    } else {
        echo "Domain $domain is not available.<br><br>";
    }

    echo "Raw WHOIS response:<br>";
    echo "<pre>" . htmlentities($response) . "</pre>";
} else {
    echo "Please provide a domain parameter (?domain=example).";
}
