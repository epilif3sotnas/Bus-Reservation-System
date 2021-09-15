<?php

require '../vendor/autoload.php';

use phpseclib3\Crypt\AES;
use phpseclib3\Crypt\Random;

$cypher = getCypherRSA();

class SessionSecurity {
    public function encryptRSA ($text) {
        global $cypher;
        return $cypher->encrypt($text);
    }

    public function decryptRSA ($text) {
        global $cypher;
        return $cypher->decrypt($text);
    }
}

function getCypherRSA () {
    $cypher = new AES('ctr');
    $cypher->setIV(Random::string(16));
    $cypher->setKey(Random::string(16));
    return $cypher;
}

?>