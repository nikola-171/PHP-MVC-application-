<?php

namespace app\Models;
use app\Core\Config;
use app\Core\Request;
use PDO;

class PocetnaStrana extends AbstractModel{
    const CLASSNAME = '\app\Domain\PocetnaStrana';

    public function daj_info_objava($id){
        $query = <<<EOD
        select * from objava
        where id = :id
        EOD;
        $sth = $this->db->prepare($query);
        $sth->execute(['id' => $id]);
        $rez = $sth->fetchAll(PDO::FETCH_ASSOC);
        return $rez;

    }

    public function ucitaj_jos_objava($id, $datum, $datumParam, $naziv, $naslov, $sadrzi){
        $request = new Request();
        $query = <<<EOD
        select objava.orijentacija ,objava.datum,objava.putanja_slike,objava.naslov,
               objava.preduzece,objava.id, objava.tekst, preduzeca.naziv 
               from objava inner join preduzeca on objava.preduzece = preduzeca.id 
               where true and objava.id < $id
               
        EOD;

        if($datum != ''){
            $query .= <<<EOD
                and objava.datum $datumParam '$datum'
            EOD;
        }
        if($naziv != ''){
            $query .= <<<EOD
                and preduzeca.naziv = '$naziv'
            EOD;
        }
        if($naslov != ''){
            $query .= <<<EOD
                and objava.naslov like '%$naslov%'
            EOD;
        }
        if($sadrzi != ''){
            $query .= <<<EOD
                and objava.tekst like '%$sadrzi%'
            EOD;
        }
        

        $query .= <<<EOD
               order by objava.datum_postavljanja desc
               limit 3;

        EOD;
        $sth = $this->db->prepare($query);
        $sth->execute(['id' => $id]);
        $rez = $sth->fetchAll(PDO::FETCH_ASSOC);
        $rezultat = array();
        foreach($rez as $row){
            $orijentacija = $row['orijentacija'];
            $datum = $row['datum'];
            $putanja_slike = $row['putanja_slike'];
            $naslov = $row['naslov'];
            $preduzece = $row['preduzece'];
            $id = $row['id'];
            $tekst = $row['tekst'];
            $naziv = $row['naziv'];
            $rezultat[] = array('orijentacija' => $orijentacija, 'datum' => $datum,
                                'putanja_slike' => $putanja_slike,'naslov' => $naslov,
                                'preduzece' => $preduzece, 'id' => $id,
                                'tekst' => $tekst, 'naziv' => $naziv);
        }
        return $rezultat;

    }

    public function getContent($datum,$datumParam,$naziv,$naslov,$sadrzi){
        $request = new Request();
        $pdo_string = '';
        $query2 = <<<EOD
        select objava.orijentacija,objava.datum,objava.putanja_slike,objava.naslov,
               objava.preduzece,objava.id, objava.tekst, preduzeca.naziv 
               from objava inner join preduzeca on objava.preduzece = preduzeca.id
               where true
        EOD;
        if($datum != ''){
            $query2 .= <<<EOD
                and objava.datum $datumParam '$datum'
            EOD;
        }
        if($naziv != ''){
            $query2 .= <<<EOD
                and preduzeca.naziv = '$naziv'
            EOD;
        }
        if($naslov != ''){
            $query2 .= <<<EOD
                and objava.naslov like '%${naslov}%'
            EOD;
        }
        if($sadrzi != ''){
            $query2 .= <<<EOD
                and objava.tekst like '%${sadrzi}%'
            EOD;
        }

        $query2 .= <<<EOD
               order by objava.datum_postavljanja desc
               limit 3;
        EOD;
        
        $sth = $this->db->prepare($query2);
        $sth->execute([]);
        $rez = $sth->fetchAll(PDO::FETCH_CLASS, self::CLASSNAME);
        /*ovo nije bas najbolje */
        $id = 0;
        foreach($rez as $obj){
            
            $id = $obj->getId();

            
        }
        if(empty($rez)){
            return false;
        }
        $sadrzaj = ['sadrzaj' => $rez, 'id_zadnjeg' => $id];
        return $sadrzaj;

    }

    

    public function ubaci_sliku_za_komentar($komentar, $putanja, $orijentacija){
        $query = <<<EOD
                update objava 
                set putanja_slike = :putanja, orijentacija = :orijentacija 
                where id = :id;
        EOD;
        $sth = $this->db->prepare($query);
        $rez = $sth->execute(['putanja' => $putanja,'orijentacija' => $orijentacija ,'id' => $komentar]);
        if(!$rez){
            die('doslo je do greske ovde');
        }

    }

    public function postaviKomentar($sadrzaj, $naslov, $datum_odrzavanja){
        date_default_timezone_set('Europe/Belgrade');
        $request = new Request();
        $preduzece = $request->getSession()->get('user');
        $dat_odrzavanja = date("Y-m-d",strtotime($datum_odrzavanja));
        $datum = date("Y-m-d H:i:s");   

        $query = <<<EOD
                insert into objava(preduzece, datum, datum_postavljanja,tekst,naslov) 
                values (:preduzece, :datum, :datum_postavljanja,:tekst,:naslov);
        EOD;   


        $sth = $this->db->prepare($query);
       
        $rez = $sth->execute(['preduzece' => $preduzece, 'datum' => $datum_odrzavanja,'datum_postavljanja' => $datum, 'tekst' => $sadrzaj, 'naslov' => $naslov]);
        $insertId = $this->db->lastInsertId();
        if(!$rez){
            die('doslo je do greske sada');
        }
        return $insertId;

    }



    public function izbrisiObjavu($objava_id){

        $query = 'select putanja_slike from objava where id = :id';
        $sth = $this->db->prepare($query);
        $rez = $sth->execute(['id' => $objava_id]);
        if(!$rez){
            die('doslo je do greske');
        }
        $rez = $sth->fetchAll();
        $putanja = $rez[0]['putanja_slike'];
        if($putanja != null){
            unlink($putanja);

        }

        $query = 'delete from objava where id = :id';
        $sth = $this->db->prepare($query);
        $rez = $sth->execute(['id' => $objava_id]);

        if(!$rez){
            die('doslo je do greske');
        }

    }
    

}