<?php

namespace app\Models;
use app\Core\Config;
use app\Core\Request;
use app\Core\SecurityMonitor;
use PDO;

class Administrator extends AbstractModel{

    public function vadi_podatke_iz_baze($administrator_ime){
        $query = <<<EOD
            select * from administrator
            where administrator_ime = :administrator_ime;
        EOD;
        $sth = $this->db->prepare($query);
        $sth->execute(['administrator_ime' => $administrator_ime]);
        $rez = $sth->fetchAll(PDO::FETCH_ASSOC);

        return $rez;
    }

    public function izbrisi_objavu($id_objave){
        $query = <<<EOD
        SET FOREIGN_KEY_CHECKS=0
        EOD;
        $sth = $this->db->prepare($query);
        $sth->execute([]);

        $query = <<<EOD
            delete from objava
            where id = :id
        EOD;
        $sth = $this->db->prepare($query);
        $sth->execute(['id' => $id_objave]);
        $count = $sth->rowCount();

        $query = <<<EOD
        SET FOREIGN_KEY_CHECKS=0
        EOD;
        $sth = $this->db->prepare($query);
        $sth->execute([]);
        return $count;
    }

    public function izbrisi_preduzece($id){
        $query = <<<EOD
            delete from preduzeca
            where id = :id
        EOD;
        $sth = $this->db->prepare($query);
        $sth->execute(['id' => $id]);
        $count = $sth->rowCount();
        return $count;
    }

    public function izbrisi_fizicko_lice($id){
        $query = <<<EOD
            delete from korisnici
            where id = :id
        EOD;
        $sth = $this->db->prepare($query);
        $sth->execute(['id' => $id]);
        $count = $sth->rowCount();
        return $count;
    }

    public function daj_sva_preduzeca(){
        $query = <<<EOD
            select * from preduzeca
        EOD;
        $sth = $this->db->prepare($query);
        $sth->execute([]);
        $rez = $sth->fetchAll(PDO::FETCH_ASSOC);

        return $rez;

    }

    public function daj_sve_objave(){
        $query = <<<EOD
            select objava.id, objava.datum, objava.datum_postavljanja, objava.naslov, preduzeca.naziv,
             objava.tekst, objava.putanja_slike, objava.orijentacija 
            from objava inner join preduzeca on objava.preduzece = preduzeca.id
        EOD;
        $sth = $this->db->prepare($query);
        $sth->execute([]);
        $rez = $sth->fetchAll(PDO::FETCH_ASSOC);

        return $rez;
    }

    public function daj_sva_fizicka_lica(){
        $query = <<<EOD
            select * from korisnici
        EOD;
        $sth = $this->db->prepare($query);
        $sth->execute([]);
        $rez = $sth->fetchAll(PDO::FETCH_ASSOC);

        return $rez;
    }


    public function provera_da_li_postoji_admin(){
        $query = <<<EOD
        select administrator_ime from administrator;
        EOD;

        $sth = $this->db->prepare($query);
        $sth->execute([]);
        $rez = $sth->fetchAll(PDO::FETCH_ASSOC);
        if(empty($rez)){
            return false;
        }else{
            return true;
        }
    }

    public function ubaci_podatke_u_bazi($ime, $prezime, $email, $admin_ime, $lozinka){
        $query = <<<EOD
            insert into administrator(ime, prezime, email,administrator_ime,lozinka)
            values(:ime, :prezime, :email, :administrator_ime, :lozinka);
        EOD;
        $sth = $this->db->prepare($query);
    
        $sth->execute(['ime' => $ime, 'prezime' => $prezime, 'email' => $email, 'administrator_ime' => $admin_ime, 'lozinka' => $lozinka]);
        $rez = $sth->fetchAll(PDO::FETCH_ASSOC);
        return $rez;
    }
}