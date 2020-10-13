<?php

namespace app\Models;
use app\Core\Config;
use app\Core\Request;
use PDO;

class zaboravljenaLozinka extends AbstractModel{

    public function izbrisi_iz_baze($id){
        $query = <<<EOD
            delete from reset_lozinke_fizicko_lice
            where korisnik = :id
        EOD;
        $sth = $this->db->prepare($query);
        $sth->execute(['id' => $id]);
    }

    public function izbrisi_iz_baze_preduzeca($id){
        $query = <<<EOD
            delete from reset_lozinke_preduzece
            where preduzece = :id
        EOD;
        $sth = $this->db->prepare($query);
        $rez = $sth->execute(['id' => $id]);
    }

    public function promeni_lozinku_preduzece($id, $lozinka){
        $query = <<<EOD
            update preduzeca
            set lozinka = :lozinka
            where id = :id;
        EOD;
        $sth = $this->db->prepare($query);
        $sth->execute(['id' => $id, 'lozinka' => $lozinka]);
    }

    public function promeni_lozinku($id, $lozinka){
        $query = <<<EOD
            update korisnici
            set lozinka = :lozinka
            where id = :id;
        EOD;
        $sth = $this->db->prepare($query);
        $sth->execute(['id' => $id, 'lozinka' => $lozinka]);

    }

    public function daj_token_vreme_iz_baze($id_korisnika){
        $query = <<<EOD
            select token,vreme from reset_lozinke_fizicko_lice
            where korisnik = :id_korisnika;
        EOD;
        $sth = $this->db->prepare($query);
        $sth->execute(['id_korisnika' => $id_korisnika]);
        $rez = $sth->fetchAll(PDO::FETCH_ASSOC);

        if(empty($rez)){
            return null;
        }else{
            return $rez;
        }
    }

    public function daj_token_vreme_iz_baze_preduzeca($id_preduzeca){
        $query = <<<EOD
            select token,vreme from reset_lozinke_preduzece
            where preduzece = :id_preduzeca;
        EOD;
        $sth = $this->db->prepare($query);
        $sth->execute(['id_preduzeca' => $id_preduzeca]);
        $rez = $sth->fetchAll(PDO::FETCH_ASSOC);

        if(empty($rez)){
            return null;
        }else{
            return $rez;
        }
    }

    public function daj_id_korisnika($korisnicko_ime){
        $request = new Request();

        $query = <<<EOD
            select id from korisnici
            where korisnicko_ime = :korisnicko_ime;
        EOD;
        $sth = $this->db->prepare($query);
        $sth->execute(['korisnicko_ime' => $korisnicko_ime]);
        $rez = $sth->fetchAll(PDO::FETCH_ASSOC);

        if(empty($rez)){
            return null;
        }else{
            return $rez[0]['id'];
        }
    }

    public function daj_id_preduzeca($maticni_broj){
        $request = new Request();

        $query = <<<EOD
            select id from preduzeca
            where maticni_broj = :maticni_broj;
        EOD;
        $sth = $this->db->prepare($query);
        $sth->execute(['maticni_broj' => $maticni_broj]);
        $rez = $sth->fetchAll(PDO::FETCH_ASSOC);

        if(empty($rez)){
            return null;
        }else{
            return $rez[0]['id'];
        }
    }

    public function daj_email_korisnika($korisnicko_ime){
        $request = new Request();

        $query = <<<EOD
            select email from korisnici
            where korisnicko_ime = :korisnicko_ime;
        EOD;
        $sth = $this->db->prepare($query);
        $sth->execute(['korisnicko_ime' => $korisnicko_ime]);
        $rez = $sth->fetchAll(PDO::FETCH_ASSOC);

        if(empty($rez)){
            return null;
        }else{
            return $rez[0]['email'];
        }

    }

    public function daj_email_preduzeca($maticni_broj){
        $query = <<<EOD
            select email from preduzeca
            where maticni_broj = :maticni_broj;
        EOD;
        $sth = $this->db->prepare($query);
        $sth->execute(['maticni_broj' => $maticni_broj]);
        $rez = $sth->fetchAll(PDO::FETCH_ASSOC);

        if(empty($rez)){
            return null;
        }else{
            return $rez[0]['email'];
        }
    }

    public function cuvaj_podatke_o_resetovanju_preduzeca($preduzece, $vreme, $token){
        $query = <<<EOD
            select preduzece from reset_lozinke_preduzece
            where preduzece = :preduzece;
        EOD;
        $sth = $this->db->prepare($query);
        $sth->execute(['preduzece' => $preduzece]);
        $rez = $sth->fetchAll(PDO::FETCH_ASSOC);
        if(empty($rez)){
            /*prvi put menja pa ne postoji zapis u bazi */
            $query = <<<EOD
                insert into reset_lozinke_preduzece(preduzece,token,vreme)
                values(:preduzece,:token,:vreme)
            EOD;
            $sth = $this->db->prepare($query);
            $rezultat = $sth->execute(['preduzece' => $preduzece,'token' => $token, 'vreme' => $vreme]);
        
        }else{
            $query = <<<EOD
                update reset_lozinke_preduzece
                set vreme = :vreme, token = :token
                where preduzece = :preduzece;
            EOD;
            $sth = $this->db->prepare($query);
            $sth->execute(['preduzece' => $preduzece, 'vreme' => $vreme, 'token' => $token]);


            /*samo azuriram podatke i ako mnogo puta slao zabranim mu */

        }


    }

    public function cuvaj_podatke_o_resetovanju($korisnicko_ime, $vreme, $token){
        $request = new Request();
        $query = <<<EOD
            select korisnik from reset_lozinke_fizicko_lice
            where korisnik = :korisnicko_ime;
        EOD;
        $sth = $this->db->prepare($query);
        $sth->execute(['korisnicko_ime' => $korisnicko_ime]);
        $rez = $sth->fetchAll(PDO::FETCH_ASSOC);
        if(empty($rez)){
            /*prvi put menja pa ne postoji zapis u bazi */
            $query = <<<EOD
                insert into reset_lozinke_fizicko_lice(korisnik,token,vreme)
                values(:korisnik,:token,:vreme)
            EOD;
            $sth = $this->db->prepare($query);
            $rezultat = $sth->execute(['korisnik' => $korisnicko_ime,'token' => $token, 'vreme' => $vreme]);
        
        }else{
            $query = <<<EOD
                update reset_lozinke_fizicko_lice
                set vreme = :vreme, token = :token
                where korisnik = :korisnicko_ime;
            EOD;
            $sth = $this->db->prepare($query);
            $sth->execute(['korisnicko_ime' => $korisnicko_ime, 'vreme' => $vreme, 'token' => $token]);


            /*samo azuriram podatke i ako mnogo puta slao zabranim mu */

        }


    }
    public function validacija_korisnickog_imena($korisnicko_ime){
        $request = new Request();

        $query = <<<EOD
            select korisnicko_ime from korisnici
            where korisnicko_ime = :korisnicko_ime;
        EOD;
        $sth = $this->db->prepare($query);
        $sth->execute(['korisnicko_ime' => $korisnicko_ime]);
        $rez = $sth->fetchAll(PDO::FETCH_ASSOC);
        if(empty($rez)){
            return null;
        }else{
            return $rez[0]['korisnicko_ime'];
        }
    }

    public function validacija_maticnog_broja_preduzeca($maticni_broj){
        $request = new Request();

        $query = <<<EOD
            select maticni_broj from preduzeca
            where maticni_broj = :maticni_broj;
        EOD;
        $sth = $this->db->prepare($query);
        $sth->execute(['maticni_broj' => $maticni_broj]);
        $rez = $sth->fetchAll(PDO::FETCH_ASSOC);
        if(empty($rez)){
            return null;
        }else{
            return $rez[0]['maticni_broj'];
        }
    }
}