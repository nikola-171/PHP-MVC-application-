<?php

namespace app\Domain;

class Login{
    private $id;
    private $ime;
    private $prezime;
    private $godina_rodjenja;
    private $mesec_rodjenja;
    private $dan_rodjenja;
    private $korisnicko_ime;
    private $lozinka;

    public function getId(){
        return $this->id;
    }
    public function getIme(){
        return $this->ime;
    }
    public function getPrezime(){
        return $this->prezime;
    }
    public function getGodina_rodjenja(){
        return $this->godina_rodjenja;
    }
    public function getMesec_rodjenja(){
        return $this->mesec_rodjenja;
    }
    public function getDan_rodjenja(){
        return $this->dan_rodjenja;
    }
    public function getKorisnicko_ime(){
        return $this->korisnicko_ime;
    }
    public function getLozinka(){
        return $this->lozinka;
    }
}