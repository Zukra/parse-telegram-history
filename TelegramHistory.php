<?php

namespace zkr;

use libphonenumber\PhoneNumberUtil;

class TelegramHistory {

    public $inFile = 'data/in.txt';
    public $tmpFile = 'data/tmp.txt';
    public $emailFile = 'data/email.data';
    public $phoneFile = 'data/phone.data';
    public $arrEmails = [];
    public $arrPhones = [];
    public $countryCodes = ['RU', 'KZ'];
//    public $countryCodes = ['RU', 'BY', 'KZ', 'UA'];

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

            foreach ($emailMatches[0] as $email) {
                $this->arrEmails[$item[0]]['email'][$email] = $email;
            }
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

    public function getPhones($item) {
        //        $this->arrPhones[$item[0]]['date'][] = $item[1];
        $phoneNumberUtil = PhoneNumberUtil::getInstance();
        $text = $item[2];
        foreach ($this->countryCodes as $code) {
            $phoneNumberMatcher = $phoneNumberUtil->findNumbers($text, $code);
            foreach ($phoneNumberMatcher as $phoneNumberMatch) {

                if (empty($this->arrPhones[$item[0]]['msg']) || !in_array($item[2], $this->arrPhones[$item[0]]['msg'])) {
                    $this->arrPhones[$item[0]]['msg'][] = $item[2];
                }

                $number = $phoneNumberMatch->number();
                $key = $number->getCountryCode() . '_' . $number->getNationalNumber();
                $this->arrPhones[$item[0]]['phone'][$key] = $number->getCountryCode() . ' ' . $number->getNationalNumber();
            }
        }

        return $this->arrPhones;
    }

    public function storePhones() {
        ksort($this->arrPhones);
        $sPhones = '';
        foreach ($this->arrPhones as $author => $item) {
            $sPhones .= $author . ' ; ' . implode(' ', $item['phone']) . "\n";
        }

        return file_put_contents($this->phoneFile, $sPhones);
    }
}
