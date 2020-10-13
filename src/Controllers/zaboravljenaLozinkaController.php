<?php

namespace app\Controllers;

use app\Core\Config;
use app\Core\Request;
use app\Models\zaboravljenaLozinka;
use app\Core\SecurityMonitor;
use app\Core\Mail_sender;
use app\Core\Kontejner_jezik;
use app\Core\Logger_main;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use app\Exceptions\Email_not_found;
require 'vendor/autoload.php';

class zaboravljenaLozinkaController extends AbstractController{

    protected const promena_lozinke_preduzece_get = 'pristup strani za promenu lozinke preduzeca GET metodom';
    protected const promena_lozinke_fizicko_get = 'pristup strani za promenu lozinke preduzeca GET metodom';
    protected const promena_lozinke_preduzece_nevalidna_sesija = 'pristup strani za promenu lozinke preduzeca nevalidnom sesijom';
    protected const istekla = 'istekla sesija';
    protected const istekla_promena = 'istekla sesija ili uspesno promenjena lozinka';
    protected const uspesno = 'uspesno promenjena lozinka';
    protected const zaboravljena_lozinka_preduzece = 'pristup stranici za zaboravljenu lozinku preduzece';
    protected const zaboravljena_lozinka_fizicko = 'pristup stranici za zaboravljenu lozinku fizicko lice';
    protected const neispravni_podaci = 'neispravni podaci prilikom promene lozinke';
    protected const poslat_email = 'poslat email za reser lozinke';

    /* reči uključeni u stranici  */
    public static $NEISPRAVNI_PODACI = 'neispravni podaci';

    public static  $maticni_unos = null;
    public static  $posalji = null;
    public static  $naslov = null;

    public static $opste_reci = null;

    public static $ne_validna_sesija = null;
    public static $istekla = null;
    public static $uspesno = null;
    public static $istekla_ili_promena = null;
    public static $ne_poklapanje = null;

    public static $preduzece_ne_postoji = null;
    public static $poslata_email_poruka = null;

    public static $promena_lozinke = null;
    public static $lozinka_unos = null;
    public static $lozinka_unos_ponovo = null;

    public static $ne_postoji_korisnik = null;

    public static $resetovanje = null;

    public static  $korisnicko_ime = null;

    public static function daj_reci(){
        $reci = self::$opste_reci;
        $reci += array('resetovanje' => self::$resetovanje, 'korisnicko_ime' => self::$korisnicko_ime,
        'posalji' => self::$posalji);
        return $reci;
    }

    public static function daj_reci_validacija(){
        $reci = self::$opste_reci;
        $reci += array('ne_poklapanje' => self::$ne_poklapanje,'promena_lozinke' => self::$promena_lozinke,
        'lozinka_unos' => self::$lozinka_unos,'lozinka_unos_ponovo' => self::$lozinka_unos_ponovo);
        return $reci;
    }

    public static function daj_reci_validacija_fizicko(){
        $reci = self::$opste_reci;
        $reci += array('ne_poklapanje' => self::$ne_poklapanje,'promena_lozinke' => self::$promena_lozinke,
        'lozinka_unos' => self::$lozinka_unos,'lozinka_unos_ponovo' => self::$lozinka_unos_ponovo);
        return $reci;
    }

    /*PROMENA LOZINKE ZA PREDUZEĆA */
    public function promeni_lozinku_preduzece(){
        $baza = new zaboravljenaLozinka($this->db);
        $request = new Request();
        if($request->getSession()->has('user') && $request->getSession()->has('auth')){
            Header('Location: /');
        }
        if($request->getSession()->has('admin')){
            Header('Location: /');
        }
        if($request->isGet()){
            Logger_main::log_warning(self::promena_lozinke_preduzece_get);
            Header('Location: /');
        }
        /*NIJE DOBRO MORAM DA PROVERIM DAL JE VALIDAN TOKEN I VREME I ID */
        if($request->getSession()->has('token') && $request->getSession()->has('vreme') && $request->getSession()->has('id')){
            $rezultat = $baza->daj_token_vreme_iz_baze_preduzeca($request->getSession()->get('id'));
            $token_iz_baze = $rezultat[0]['token'];
            $vreme_iz_baze = $rezultat[0]['vreme'];

            if($request->getSession()->get('token') != $token_iz_baze || $request->getSession()->has('vreme') != $vreme_iz_baze){
                Logger_main::log_warning(promena_lozinke_preduzece_nevalidna_sesija);
                die(self::$ne_validna_sesija);
            }

            $id = $request->getParams()->get('id');
            $trenutno_vreme = time();
           
            $starost = $trenutno_vreme - $vreme_iz_baze;
            
            if($starost > 3600){
                Logger_main::log_warning(self::istekla);
                echo self::$istekla;

                /*brisanje iz baze */
            }else{
                $nova_lozinka = $request->getParams()->get('lozinka');
                $id = $request->getSession()->get('id');
                $nova_lozinka_hash = SecurityMonitor::encrypt_password($nova_lozinka);
                $baza->promeni_lozinku_preduzece($id,$nova_lozinka_hash);
    
                $jezik = 'srp';
                if($request->getSession()->has('jezik')){
                    if($request->getSession()->get('jezik') != ''){
                        $jezik = $request->getSession()->get('jezik');
                    }
                }
                // novo dodato
                session_unset();
                $_SESSION = array();
                $request = new Request();
                
                $request->setSession('jezik', $jezik);
                $baza->izbrisi_iz_baze_preduzeca($id);
                Logger_main::log_info(self::uspesno);
                echo self::$uspesno;
            }
        }else{
            Logger_main::log_warning(self::istekla_promena." (preduzece id-${id})");
            echo self::$istekla_ili_promena;
        }
    }

    /*PROMENA LOZINKE ZA FIZIČKA LICA */
    public function promeni_lozinku(){

        $baza = new zaboravljenaLozinka($this->db);

        $request = new Request();
        if($request->isGet()){
            Logger_main::log_info(self::promena_lozinke_fizicko_get);
            Header('Location: /');
        }
        if($request->getSession()->has('admin')){
            Header('Location: /');
        }
        if($request->getSession()->has('user') && $request->getSession()->has('auth')){
            Header('Location: /');
        }
        
        if($request->getSession()->has('token') && $request->getSession()->has('vreme') && $request->getSession()->has('id')){
            $rezultat = $baza->daj_token_vreme_iz_baze($request->getSession()->get('id'));
            $token_iz_baze = $rezultat[0]['token'];
            $vreme_iz_baze = $rezultat[0]['vreme'];

            if($request->getSession()->get('token') != $token_iz_baze || $request->getSession()->has('vreme') != $vreme_iz_baze){
                Logger_main::log_warning(self::nevalidna);
                die(self::$ne_validna_sesija);
            }
            $id = $request->getParams()->get('id');
            $trenutno_vreme = time();
           
            $starost = $trenutno_vreme - $vreme_iz_baze;
        
            if($starost > 360){
                echo self::$istekla;

                /*brisanje iz baze */
            }else{
                $nova_lozinka = $request->getParams()->get('lozinka');
                $id = $request->getSession()->get('id');
                $nova_lozinka_hash = SecurityMonitor::encrypt_password($nova_lozinka);
                $baza->promeni_lozinku($id,$nova_lozinka_hash);
    
                $jezik = 'srp';
                if($request->getSession()->has('jezik')){
                    if($request->getSession()->get('jezik') != ''){
                        $jezik = $request->getSession()->get('jezik');
                    }
                }

                session_unset();
                $_SESSION = array();
                $request = new Request();
                
                $request->setSession('jezik', $jezik);
                $baza->izbrisi_iz_baze($id);
                Logger_main::log_info(self::uspesno." (fizicko lice id-${id})");
                echo self::$uspesno;
            }
        }else{
            Logger_main::log_warning(self::istekla_promena);
            echo self::$istekla_ili_promena;
        }

    }

    /*STRANA ZA ZABORAVLJENU LOZINKU - PREDUZEĆA */
    public function zaboravljena_lozinka_preduzece(){
        $request = new Request();
        if($request->getSession()->has('admin')){
            Header('Location: /');
        }

        if($request->getSession()->has('user') && $request->getSession()->has('auth')){
            Header('Location: /');
        }
        $reci = self::daj_reci();
        $reci += array('maticni_unos' => self::$maticni_unos);
        Logger_main::log_info(self::zaboravljena_lozinka_preduzece);
        return $this->render('zaboravljena_lozinka_preduzece.twig',$reci);


    }
    /*STRANA ZA ZABORAVLJENU LOZINKU - FIZIČKA LICA */
    public function zaboravljena_lozinka_fizicko(){
        
        $request = new Request();
        if($request->getSession()->has('admin')){
            Header('Location: /');
        }
        if($request->getSession()->has('user') && $request->getSession()->has('auth')){
            Header('Location: /');
        }
        $reci = self::daj_reci();
        Logger_main::log_info(self::zaboravljena_lozinka_fizicko);
        return $this->render('zaboravljena_lozinka_fizicko.twig', $reci);
    }

    /*VALIDACIJA PREDUZEĆA - DA LI PREDUZEĆE POSTOJI U BAZI */
    public function validacijaPreduzece(){
        $request = new Request();
        $baza = new zaboravljenaLozinka($this->db);
        if($request->getSession()->has('admin')){
            Header('Location: /');
        }
        if($request->getSession()->has('user') && $request->getSession()->has('auth')){
            Header('Location: /');
        }
        $reci = self::daj_reci_validacija();
        if($request->getParams()->has('token') && $request->getParams()->has('time')){
           
            $rezultat = $baza->daj_token_vreme_iz_baze_preduzeca($request->getParams()->get('id'));
            if(empty($rezultat)){
                $reci += array('error' => self::$istekla);
                Logger_main::log_warning(self::istekla." preduzece");
                return $this->render('resetLozinkePreduzece.twig', $reci);

            }
            $token = $rezultat[0]['token'];
            $vreme = $rezultat[0]['vreme'];
            $id = $request->getParams()->get('id');
            if($token != $request->getParams()->get('token')){
                $reci += array('error' => self::$NEISPRAVNI_PODACI);
                Logger_main::log_warning(self::istekla." preduzece");

                return $this->render('resetLozinkePreduzece.twig',$reci);
            }
            

            $trenutno_vreme = time();
           
            $starost = $trenutno_vreme - $vreme;
            if($starost > 360){

                $baza->izbrisi_iz_baze($id);
                $reci += array('error' => self::$istekla);
                Logger_main::log_warning(self::istekla. " preduzece id-${id}");

                return $this->render('resetLozinkePreduzece.twig', );

            }else{

                $request->setSession('token', $token);
                $request->setSession('vreme', $vreme);
                $request->setSession('id', $id);
                return $this->render('resetLozinkePreduzece.twig',$reci);

            }
        }
        if(!$request->getParams()->has('korisnicko_ime')){
            die('došlo je do greške');
        }

        $matični_broj = $request->getParams()->get('korisnicko_ime');
    
        $baza = new zaboravljenaLozinka($this->db);
        $rezultat = $baza->validacija_maticnog_broja_preduzeca($matični_broj);
        if(empty($rezultat)){
            echo self::$preduzece_ne_postoji;
        }else{
            $hash = SecurityMonitor::get_enc_token();

            $id = $baza->daj_id_preduzeca($matični_broj);
            $email = $baza->daj_email_preduzeca($matični_broj);
            if(empty($email)){
                throw new Email_not_found();
            }
            $sadrzaj = self::$poslata_email_poruka;
            $naslov = 'Reset';
            $tip = 'preduzece';
            $time = time();
            Mail_sender::posalji_mejl($email, $naslov, $sadrzaj, $hash, $time, $id, $tip,self::$poslata_email_poruka);
            $baza->cuvaj_podatke_o_resetovanju_preduzeca($id, $time, $hash);
            Logger_main::log_info(self::poslat_email. " (preduzece email-${email},id-${id})");

        }
    }

    /*VALIDACIJA FIZIČKIH LICA - DA LI FIZIČKO LICE POSTOJI U BAZI */
    public function validacija(){
        $request = new Request();
        $baza = new zaboravljenaLozinka($this->db);
        if($request->getSession()->has('admin')){
            Header('Location: /');
        }
        if($request->getSession()->has('user') && $request->getSession()->has('auth')){
            Header('Location: /');
        }
        $reci = self::daj_reci_validacija_fizicko();
        if($request->getParams()->has('token') && $request->getParams()->has('time')){
            
            $rezultat = $baza->daj_token_vreme_iz_baze($request->getParams()->get('id'));
            if(empty($rezultat)){
                $reci += array('error' => self::$istekla);
                return $this->render('resetLozinkeFizicko.twig', $reci);

            }
            $token = $rezultat[0]['token'];
            $vreme = $rezultat[0]['vreme'];
            $id = $request->getParams()->get('id');

            if($token != $request->getParams()->get('token')){
                $reci += array('error' => self::$NEISPRAVNI_PODACI);
                Logger_main::log_warning(self::neispravni_podaci. ' fizicko lice');
                return $this->render('resetLozinkeFizicko.twig', $reci);
            }
            
            
            $trenutno_vreme = time();
           
            $starost = $trenutno_vreme - $vreme;
            if($starost > 360){
                $baza->izbrisi_iz_baze($id);
                $reci += array('error' => self::$istekla);
                Logger_main::log_warning(self::istekla. " fizicko lice id-${id}");
                return $this->render('resetLozinkeFizicko.twig', $reci);

                /*brisanje iz baze */
            }else{
                $request->setSession('token', $token);
                $request->setSession('vreme', $vreme);
                $request->setSession('id', $id);

                return $this->render('resetLozinkeFizicko.twig', $reci);

            }
        }
        if(!$request->getParams()->has('korisnicko_ime')){
            die('error occured');
        }
        $korisnicko_ime = $request->getParams()->get('korisnicko_ime');
    
        $baza = new zaboravljenaLozinka($this->db);
        $rezultat = $baza->validacija_korisnickog_imena($korisnicko_ime);
        if(empty($rezultat)){
            echo self::$ne_postoji_korisnik;
        }else{
            
            $hash = SecurityMonitor::get_enc_token();
            $id = $baza->daj_id_korisnika($korisnicko_ime);
            $email = $baza->daj_email_korisnika($korisnicko_ime);
            if(empty($email)){
                throw new Email_not_found();
            }
            $sadrzaj = self::$poslata_email_poruka;
            $naslov = 'Reset';
            $tip = 'fizicko';

            Mail_sender::posalji_mejl($email, $naslov, $sadrzaj, $hash, $time, $id, $tip,self::$poslata_email_poruka);
            $baza->cuvaj_podatke_o_resetovanju($id, $time, $hash);
            Logger_main::log_info(self::poslat_email. " (fizicko lice email-${email},id-${id})");
        }   
    }
}

/*PODEŠAVANJE JEZIKA  */
$jezik = Kontejner_jezik::daj_jezik();
$config = Kontejner_jezik::daj_config();

zaboravljenaLozinkaController::$maticni_unos = $config->get_app_data('zaboravljenja_lozinka_preduzece')["$jezik"]['maticni_unos'];
zaboravljenaLozinkaController::$posalji = $config->get_app_data('zaboravljenja_lozinka_preduzece')["$jezik"]['posalji'];
zaboravljenaLozinkaController::$promena_lozinke = $config->get_app_data('zaboravljenja_lozinka_preduzece')["$jezik"]['promena_lozinke'];
zaboravljenaLozinkaController::$lozinka_unos = $config->get_app_data('zaboravljenja_lozinka_preduzece')["$jezik"]['lozinka_unos'];
zaboravljenaLozinkaController::$lozinka_unos_ponovo = $config->get_app_data('zaboravljenja_lozinka_preduzece')["$jezik"]['lozinka_unos_ponovo'];
zaboravljenaLozinkaController::$preduzece_ne_postoji = $config->get_app_data('zaboravljenja_lozinka_preduzece')["$jezik"]['preduzece_ne_postoji'];
zaboravljenaLozinkaController::$poslata_email_poruka = $config->get_app_data('zaboravljenja_lozinka_preduzece')["$jezik"]['poslata_email_poruka'];
zaboravljenaLozinkaController::$ne_poklapanje = $config->get_app_data('zaboravljenja_lozinka_preduzece')["$jezik"]['ne_poklapanje'];

zaboravljenaLozinkaController::$korisnicko_ime = $config->get_app_data('zaboravljena_lozinka')["$jezik"]['korisnicko_ime'];
zaboravljenaLozinkaController::$resetovanje = $config->get_app_data('zaboravljena_lozinka')["$jezik"]['resetovanje'];
zaboravljenaLozinkaController::$ne_postoji_korisnik = $config->get_app_data('zaboravljena_lozinka')["$jezik"]['ne_postoji'];
zaboravljenaLozinkaController::$ne_validna_sesija = $config->get_app_data('zaboravljena_lozinka')["$jezik"]['ne_validna_sesija'];
zaboravljenaLozinkaController::$istekla = $config->get_app_data('zaboravljena_lozinka')["$jezik"]['istekla'];
zaboravljenaLozinkaController::$uspesno = $config->get_app_data('zaboravljena_lozinka')["$jezik"]['uspesno'];
zaboravljenaLozinkaController::$istekla_ili_promena = $config->get_app_data('zaboravljena_lozinka')["$jezik"]['istekla_ili_promena'];

zaboravljenaLozinkaController::$opste_reci = Kontejner_jezik::daj_opste_reci();