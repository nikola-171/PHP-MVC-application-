<?php

namespace app\Models;

use PDO;

class Register extends AbstractModel{
    const CLASSNAME = '\app\Domain\Register';

    public function proveraUnikatnosti($ime){
        $query = 'select korisnicko_ime from korisnici where korisnicko_ime = :ime';
        $sth = $this->db->prepare($query);
        $sth->execute(['ime' => $ime]);
        $result = $sth->fetchAll(PDO::FETCH_CLASS, self::CLASSNAME);
        if(empty($result)){
            return true;
        }
        return false;

    }

    public function proveraUnikatnostiPravnogLica($maticni){
        $query = 'select maticni_broj from preduzeca where maticni_broj = :maticni';
        $sth = $this->db->prepare($query);
        $sth->execute(['maticni' => $maticni]);
        /*napravi klasu */
        $result = $sth->fetchAll(/*PDO::FETCH_CLASS, self::CLASSNAME*/);
        if(empty($result)){
            return true;
        }
        return false;

    }

    public function addData($ime, $prezime, $korisnicko, $lozinka, $godina, $mesec, $dan, $email){

        $query = "insert into korisnici(ime, prezime, godina_rodjenja, mesec_rodjenja, dan_rodjenja, email ,korisnicko_ime, lozinka) values (:ime, :prezime, :godina, :mesec, :dan, :email ,:korisnicko, :lozinka)";

        $sth = $this->db->prepare($query);
        $rez = $sth->execute(['ime' => $ime, 'prezime' => $prezime, 'godina' => $godina, 'mesec' => $mesec, 'dan' => $dan, 'email' => $email, 'korisnicko' => $korisnicko, 'lozinka' => $lozinka]);
        if($rez){
            return true;
        }
        
        return false;
    }

    public function ubaciPreduzece($naziv, $matBroj, $lozinka, $sifraDrustva, $email){
        $query = "insert into preduzeca(naziv,lozinka,maticni_broj,sifra_privrednog_drustva,email) values (:naziv,:lozinka,:matBroj,:sifraDrustva,:email)";
        $sth = $this->db->prepare($query);
        $rez = $sth->execute(['naziv' => $naziv, 'lozinka' => $lozinka, 'matBroj' => $matBroj, 'sifraDrustva' => $sifraDrustva, 'email' => $email]);
        if($rez){
            return true;
        }
        
        return false;
    }

}