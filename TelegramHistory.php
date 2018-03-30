<?php

namespace zkr;

class TelegramHistory {

    public $inFile = 'data/in.txt';
    public $tmpFile = 'data/tmp.txt';
    public $emailFile = 'data/email.data';
    public $arrEmails = [];

    public function getHistory(string $inFile) {
        $content = file_get_contents($inFile);

        $content = str_replace(["....."], '', $content);
        $content = preg_replace("/\s{2,}/", ' ', $content);
        $content = str_replace(["\n"], ' ', $content);
        $content = str_replace(" {", "{", $content);
        $content = str_replace('"', "'", $content);
        $content = str_replace('<>', '"', $content);
        $content = str_replace(",#_#_#", "\n", $content);

        file_put_contents($this->tmpFile, $content);

        return $content;
    }

    public function getEmails($item, $emailMatches) {
//        $this->arrEmails[$item[0]]['date'][] = $item[1];
        if (empty($this->arrEmails[$item[0]]['msg']) || !in_array($item[2], $this->arrEmails[$item[0]]['msg'])) {
            $this->arrEmails[$item[0]]['msg'][] = $item[2];
        }
        foreach ($emailMatches[0] as $email) {
            $this->arrEmails[$item[0]]['email'][$email] = $email;
        }
    }

    public function storeEmails() {
        ksort($this->arrEmails);
        $sEmails = '';
        foreach ($this->arrEmails as $author => $item) {
            $sEmails .= $author . ' ; ' . implode(' ', $item['email']) . "\n";
        }
        return file_put_contents($this->emailFile, $sEmails);
    }
}
