<?php
$urls = [
    'Sciel' => 'https://static.wikia.nocookie.net/clair-obscur/images/2/24/COE33_char_icon_Sciel.png',
    'Verso' => 'https://static.wikia.nocookie.net/clair-obscur/images/e/e0/COE33_char_icon_Verso.png',
    'Monoco' => 'https://static.wikia.nocookie.net/clair-obscur/images/a/a5/COE33_char_icon_Monoco.png'
];

foreach ($urls as $name => $url) {
    echo "Checking $name : $url\n";
    $h = @get_headers($url);
    if ($h) {
        echo "  Status: " . $h[0] . "\n";
    } else {
        echo "  Failed to connect.\n";
    }
}
