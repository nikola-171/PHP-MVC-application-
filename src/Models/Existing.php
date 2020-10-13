<?php

namespace app\Models;

use PDO;

class Existing extends AbstractModel{
    const CLASSNAME = '\app\Domain\Login';

    public function check($user){
        $query = 'select * from korisnici where korisnicko_ime = :ime';
        $sth = $this->db->prepare($query);
        $sth->execute(['ime' => $user]);

        $rez = $sth->fetchAll(PDO::FETCH_CLASS, self::CLASSNAME);

        if(empty($rez)){
            return true;
        }
        return false;
    }
}