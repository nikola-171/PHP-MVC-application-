<?php

namespace app\Models;

use PDO;

class Login extends AbstractModel{
    const CLASSNAME = '\app\Domain\Login';
    const CLASSNAMEPRAVNO = '\app\Domain\LoginPravno';

    public function checkPravno($pravno){
        $query = 'select * from preduzeca where maticni_broj = :pravno';
        $sth = $this->db->prepare($query);
        $sth->execute(['pravno' => $pravno]);
        $rez = $sth->fetchAll(PDO::FETCH_CLASS, self::CLASSNAMEPRAVNO);
        if(empty($rez)){
            return true;
        }
        return false;
    }

    public function getPasswordPravno($pravno){
        $query = 'select lozinka from preduzeca where maticni_broj = :pravno';
        $sth = $this->db->prepare($query);
        $sth->execute(['pravno' => $pravno]);

        $rez = $sth->fetchAll(PDO::FETCH_CLASS, self::CLASSNAMEPRAVNO);

        if(empty($rez)){
            return null;
        }

        return $rez[0]->getLozinka();

    }

    public function getIdPravno($pravno){
        $query = 'select id from preduzeca where maticni_broj = :pravno';
        $sth = $this->db->prepare($query);
        $sth->execute(['pravno' => $pravno]);

        $rez = $sth->fetchAll(PDO::FETCH_CLASS, self::CLASSNAMEPRAVNO);

        if(empty($rez)){
            return null;
        }
        return $rez[0]->getId();

    }

    public function checkUsername($user){
        $query = 'select * from korisnici where korisnicko_ime = :ime';
        $sth = $this->db->prepare($query);
        $sth->execute(['ime' => $user]);

        $rez = $sth->fetchAll(PDO::FETCH_CLASS, self::CLASSNAME);

        if(empty($rez)){
            return true;
        }
        return false;
    }

    public function get_username($id){
        $query = 'select korisnicko_ime from korisnici where id = :id';
        $sth = $this->db->prepare($query);
        $sth->execute(['id' => $id]);

        $rez = $sth->fetchAll(PDO::FETCH_CLASS, self::CLASSNAME);
        if(empty($rez)){
            return null;
        }
        return $rez[0]->getKorisnicko_ime();

    }

    /*uzimamo id da bismo prosledili korisnika do pocetne stranice */
    public function getId($user){
        $query = 'select id from korisnici where korisnicko_ime = :ime';
        $sth = $this->db->prepare($query);
        $sth->execute(['ime' => $user]);

        $rez = $sth->fetchAll(PDO::FETCH_CLASS, self::CLASSNAME);

        if(empty($rez)){
            return null;
        }
        return $rez[0]->getId();
    }

    public function getPassword($user){
        $query = 'select lozinka from korisnici where korisnicko_ime = :ime';
        $sth = $this->db->prepare($query);
        $sth->execute(['ime' => $user]);

        $rez = $sth->fetchAll(PDO::FETCH_CLASS, self::CLASSNAME);

        if(empty($rez)){
            return null;
        }

        return $rez[0]->getLozinka();
    }
}