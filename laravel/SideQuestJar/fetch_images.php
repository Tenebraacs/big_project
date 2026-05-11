<?php
$url1 = 'https://ibb.co/twT5520K';
$url2 = 'https://ibb.co/RT9Ty540';

function getUrl($url) {
    $html = file_get_contents($url);
    if (preg_match('/"(https:\/\/i\.ibb\.co\/[^"]+)"/', $html, $matches)) {
        echo $matches[1] . "\n";
    }
}

getUrl($url1);
getUrl($url2);
