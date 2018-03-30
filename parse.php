<?php

$inFile = 'in.txt';
$outFile = 'out.txt';
$emailFile = 'email.data';

$content = file_get_contents($inFile);

$content = str_replace(["....."], '', $content);
$content = preg_replace("/\s{2,}/", ' ', $content);
$content = str_replace(["\n"], ' ', $content);
$content = str_replace(" {", "{", $content);
$content = str_replace('"', "'", $content);
$content = str_replace('<>', '"', $content);
$content = str_replace(",#_#_#", "\n", $content);

file_put_contents($outFile, $content);

$handle = @fopen($outFile, "r");
$arrEmails = [];
while ($buffer = fgets($handle)) {
    $item = json_decode($buffer, true);
    if (!preg_match_all('/([a-z0-9_\.\-])+\@(([a-z0-9\-])+\.)+([a-z0-9]{2,4})+/i', $item[2], $matches)
        || strpos($item[2], "FWD")
//        || strpos($item[0], "[@MetalPlaceHelper]")
    ) {
        continue;
    }
//    $arrHistory[$item[0]]['date'][] = $item[1];
    if (empty($arrEmails[$item[0]]['msg']) || !in_array($item[2], $arrEmails[$item[0]]['msg'])) {
        $arrEmails[$item[0]]['msg'][] = $item[2];
    }
    foreach ($matches[0] as $email) {
        $arrEmails[$item[0]]['email'][$email] = $email;
    }
}
fclose($handle);

ksort($arrEmails);
$sEmails = '';
foreach ($arrEmails as $author => $item) {
    $sEmails .= $author . ' ; ' . implode(' ', $item['email']) . "\n";
}

file_put_contents($emailFile, $sEmails);

unset($content, $arrEmails, $sEmails);
