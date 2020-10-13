<?php

namespace app\Core;
/*klasa koja se bavi:
     hesiranjem lozinki,
     kreira token za reset lozinke,
     i filtrira podatke pre unosa u bazu
   */
class SecurityMonitor{

    public static function encrypt_password($password): string{
        $pass = password_hash($password, PASSWORD_DEFAULT);
        return $pass;
    }

    public static function get_enc_token() : string{
         $time = time();
         $key = 'app';
         $hash = hash_hmac('sha256', $time, $key);
         return $hash;
    }

    public static function sanitaraizeString($input) : string{
        $input = htmlentities($input);
        $input = stripslashes($input);
       
        return $input;
    }

    public static function equals($password, $password_hash): bool{
        if(password_verify($password, $password_hash)){
            return true;
        }
        return false;
    }
    
}