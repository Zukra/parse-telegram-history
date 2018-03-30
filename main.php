<?php

/** Error reporting */

use zkr\TelegramHistory;

require_once __DIR__ . '/vendor/autoload.php';

error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);


$history = new TelegramHistory();
$history->getHistory($history->inFile);

$handle = @fopen($history->tmpFile, "r");
while ($buffer = fgets($handle)) {
    $item = json_decode($buffer, true);
    if (strpos($item[2], "FWD") || strpos($item[0], "[@metal_place_lotto_bot]")
        //        || strpos($item[0], "[@MetalPlaceHelper]")
    ) {
        continue;
    }
    // email only
    if (preg_match_all('/([a-z0-9_\.\-])+\@(([a-z0-9\-])+\.)+([a-z0-9]{2,4})+/i', $item[2], $emailMatches)) {
        $history->getEmails($item, $emailMatches);
    }
    // phone only
    $history->getPhones($item);
}
fclose($handle);

$history->storeEmails();
$history->storePhones();
