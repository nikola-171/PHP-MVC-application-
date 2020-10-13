<?php

namespace app\Domain;

class LoginPravno{
    private $id;
    private $naziv;
    private $maticni_broj;
    private $email;
    private $sifra_privrednog_drustva;
    private $lozinka;

    public function getId(){
        return $this->id;
    }
    public function getNaziv(){
        return $this->naziv;
    }
    public function getMaticni_broj(){
        return $this->maticni_broj;
    }
    public function getEmail(){
        return $this->email;
    }
    public function getSifra_privrednog_drustva(){
        return $this->mesec_rodjenja;
    }
    public function getLozinka(){
        return $this->lozinka;
    }
}