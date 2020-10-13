<?php

namespace app\Domain;

class PocetnaStrana{
    private $id;
    private $korisnik;
    private $datum;
    private $tekst;

  
    public function getId(){
        return $this->id;
    }
    
    public function getKorisnik(){
        return $this->korisnik;
    }
    public function getDatum(){
        return $this->datum;
    }
    public function getTekst(){
        return $this->tekst;
    }
}