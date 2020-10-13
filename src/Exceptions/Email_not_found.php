<?php

namespace app\Exceptions;

use Exception;

class Email_not_found extends Exception {
    public function __construct($message = null) {
        $message = $message ?: 'email ne postoji u bazi';
        die($message);
    }
}