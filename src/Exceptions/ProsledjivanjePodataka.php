<?php

namespace app\Exceptions;

use Exception;

class ProsledjivanjePodataka extends Exception {
    public function __construct($message = null) {
        $message = $message ?: 'greska prilikom prosledjivanja podataka serveru';
        die($message);
    }
}