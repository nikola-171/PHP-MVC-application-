<?php

namespace app\Controllers;

use app\Core\Config;
use app\Core\Request;
use app\Models\Register;
use app\Core\SecurityMonitor;
use app\Core\Kontejner_jezik;
use app\Core\Logger_main;


class registerController extends AbstractController{
    protected const ulogovan_korisnik = 'ulogovan korisnik pristupa stranici za registraciju';
    protected const registracija_fizicko_lice = 'pristup stranici za registraciju fizickog lica';
    protected const registracija_preduzece = 'pristup stranici za registraciju preduzeca';

    protected const uspesna_registracija = 'korisnik se upsesno registrovao';
    protected const registracija_greska = 'korisnicko ime vec postoji u bazi';
    protected const greska = 'greska prilikom registrovanja';

    public static $uspesno = null;
    public static $duplikat = null;
    public static $greska = null;
    public static $duplikat_pravni = null;

    public static $ime = null;
    public static $ime_unos = null;
    public static $prezime = null;
    public static $prezime_unos = null;

    public static $korisnicko_ime = null;
    public static $korisnicko_ime_unos = null;

    public static $dan = null;
    public static $mesec = null;
    public static $godina = null;
    public static $godina_unos = null;

    public static $naslov = null;
    public static $naziv = null;
    public static $naziv_unos = null;
    public static $maticni = null;
    public static $maticni_unos = null;
    public static $lozinka = null;
    public static $lozinka_unos = null;
    public static $sifra_drustva = null;
    public static $sifra_drustva_unos = null;
    public static $email = null;
    public static $email_unos = null;

    public static $opste_reci = null;

    public static function daj_reci_fizicko_lice(){
        $reci = self::$opste_reci;
        $reci += array('naslov' => self::$naslov,
        'ime' => self::$ime,'ime_unos' => self::$ime_unos,'prezime' => self::$prezime,
        'prezime_unos' => self::$prezime_unos,'korisnicko_ime' => self::$korisnicko_ime,
        'korisnicko_ime_unos' => self::$korisnicko_ime_unos,
        'lozinka' => self::$lozinka,'lozinka_unos' => self::$lozinka_unos,'email' => self::$email,
        'email_unos' => self::$email_unos,'dan' => self::$dan,'mesec' => self::$mesec,
        'godina' => self::$godina,
        'godina_unos' => self::$godina_unos);
        return $reci;
    }

    public static function daj_reci_preduzece(){
        $reci = self::$opste_reci;
        $reci += array( 'naslov' => self::$naslov,
         'naziv' => self::$naziv,
        'naziv_unos' => self::$naziv_unos,'maticni' => self::$maticni,
        'maticni_unos' => self::$maticni_unos,'lozinka' => self::$lozinka,
        'lozinka_unos' => self::$lozinka_unos,'sifra_drustva' => self::$sifra_drustva,
        'sifra_drustva_unos' => self::$sifra_drustva_unos,
        'email' => self::$email,'email_unos' => self::$email_unos);
        return $reci;
    }

    public function showAllPravno(){
        $request = new Request();
        /*prikaz stranice za registraciju preduzeca */
        if($request->getSession()->has('admin')){
            Header('Location: /');
        }
        if($request->getSession()->has('user')){
            $user = $request->getSession()->get('user');
            $nivo_pristupa = $request->getSession()->get('auth');
            Logger_main::log_warning(self::ulogovan_korisnik." (id-${user},auth-${nivo_oristupa})");
            Header("Location: /logovanje/pocetnaStrana/$user");
        }

        $reci = self::daj_reci_preduzece();
        Logger_main::log_info(self::registracija_preduzece);
        return $this->render('registerPravno.twig', $reci);
    }

    public function showAll(){
        $request = new Request();
        /*prikaz stranice za registraciju fizickih lica */

        $reci = self::daj_reci_fizicko_lice();
        if($request->getSession()->has('admin')){
            Header('Location: /');
        }
        if($request->getSession()->has('user')){
            $user = $request->getSession()->get('user');
            $nivo_pristupa = $request->getSession()->get('auth');
            Logger_main::log_warning(self::ulogovan_korisnik." (id-${user},auth-${nivo_oristupa})");
            Header("Location: /logovanje/pocetnaStrana/$user");
        }

        Logger_main::log_info(self::registracija_fizicko_lice);
        return $this->render('register.twig', $reci);
    }

    public function checkPravno(){
        $request = new Request();
        $reci = self::daj_reci_preduzece();
        if($request->isGet()){
            /*ovde promena sa logovanje na /logovanje */
            Header('Location: /logovanje');
        }
        if($request->getParams()->has('naziv') && $request->getParams()->has('maticniBroj') &&
           $request->getParams()->has('lozinka') && $request->getParams()->has('sifraDrustva') &&
           $request->getParams()->has('email')){

           $reg = new Register($this->db);

           if($reg->proveraUnikatnostiPravnogLica(SecurityMonitor::sanitaraizeString($request->getParams()->get('maticniBroj')))){
                $password = SecurityMonitor::encrypt_password($request->getParams()->get('lozinka'));
                if($reg->ubaciPreduzece(SecurityMonitor::sanitaraizeString($request->getParams()->get('naziv')),
                   SecurityMonitor::sanitaraizeString($request->getParams()->get('maticniBroj')),
                   $password ,SecurityMonitor::sanitaraizeString($request->getParams()->get('sifraDrustva')),
                   SecurityMonitor::sanitaraizeString($request->getParams()->get('email')))){

                    $reci += array('uspeh' => self::$uspesno);
                    Logger_main::log_info(self::uspesna_registracija . '(preduzece)');
                    return $this->render('registerPravno.twig', $reci);
        
                }
           }else{
               $reci += array('uspeh' => self::$duplikat_pravni);
               Logger_main::log_info(self::registracija_greska);

               return $this->render('registerPravno.twig', $reci);

           }
        }
        $reci += array('uspeh' => self::$greska);
        Logger_main::log_error(self::greska);

        return $this->render('registerPravno.twig', $reci);

    }

    public function check(){
        $request = new Request();        
        if($request->isGet()){
            /*ovde promena sa logovanje na /logovanje */
            Header('Location: /logovanje');
        }
        
        $reci = self::daj_reci_fizicko_lice();

        if($request->getParams()->has('ime') && $request->getParams()->has('prezime') && $request->getParams()->has('korisnickoIme') && $request->getParams()->has('lozinka') && $request->getParams()->has('email') && $request->getParams()->has('dan') && $request->getParams()->has('mesec') && $request->getParams()->has('godina')){
            /*ubacivanje u bazi*/
            $reg = new Register($this->db);

            if($reg->proveraUnikatnosti(SecurityMonitor::sanitaraizeString($request->getParams()->get('korisnickoIme')))){
                $config = new Config();
                $password = SecurityMonitor::encrypt_password($request->getParams()->get('lozinka'));
                $reci += array('uspeh' => self::$uspesno);
                if($reg->addData(SecurityMonitor::sanitaraizeString($request->getParams()->get('ime')),
                   SecurityMonitor::sanitaraizeString($request->getParams()->get('prezime')),
                   SecurityMonitor::sanitaraizeString($request->getParams()->get('korisnickoIme')),$password,
                   SecurityMonitor::sanitaraizeString($request->getParams()->get('godina')), 
                   $request->getParams()->get('mesec'), $request->getParams()->get('dan'),
                   SecurityMonitor::sanitaraizeString($request->getParams()->get('email')))){
                    /*uspesna registracija */
                    Logger_main::log_info(self::uspesna_registracija . '(fizicko lice)');
                    return $this->render('register.twig', $reci);
    
                }
            }else{
                Logger_main::log_info(self::registracija_greska);
                /*duplikat, vec postoji u bazi */
                $reci += array('uspeh' => self::$duplikat);
                 return $this->render('register.twig', $reci);

            }
            
            $reci += array('uspeh' => self::$greska);
            Logger_main::log_error(self::greska);

            return $this->render('register.twig', $reci);
        }
    }
}
$jezik = Kontejner_jezik::daj_jezik();
$config = Kontejner_jezik::daj_config();

registerController::$naslov = $config->get_app_data('register')["$jezik"]['naslov'];
registerController::$ime = $config->get_app_data('register')["$jezik"]['ime'];
registerController::$ime_unos = $config->get_app_data('register')["$jezik"]['ime_unos'];
registerController::$prezime = $config->get_app_data('register')["$jezik"]['prezime'];
registerController::$prezime_unos = $config->get_app_data('register')["$jezik"]['prezime_unos'];
registerController::$korisnicko_ime = $config->get_app_data('register')["$jezik"]['korisnicko_ime'];
registerController::$korisnicko_ime_unos = $config->get_app_data('register')["$jezik"]['korisnicko_ime_unos'];
registerController::$lozinka = $config->get_app_data('register')["$jezik"]['lozinka'];
registerController::$lozinka_unos = $config->get_app_data('register')["$jezik"]['lozinka_unos'];
registerController::$email = $config->get_app_data('register')["$jezik"]['email'];
registerController::$email_unos = $config->get_app_data('register')["$jezik"]['email_unos'];
registerController::$dan = $config->get_app_data('register')["$jezik"]['dan'];
registerController::$mesec = $config->get_app_data('register')["$jezik"]['mesec'];
registerController::$godina = $config->get_app_data('register')["$jezik"]['godina'];
registerController::$godina_unos = $config->get_app_data('register')["$jezik"]['godina_unos'];

registerController::$uspesno = $config->get_app_data('register')["$jezik"]['uspesno'];
registerController::$duplikat = $config->get_app_data('register')["$jezik"]['duplikat'];
registerController::$greska = $config->get_app_data('register')["$jezik"]['greska'];

registerController::$naziv = $config->get_app_data('registerPravno')["$jezik"]['naziv'];
registerController::$naziv_unos = $config->get_app_data('registerPravno')["$jezik"]['naziv_unos'];
registerController::$maticni = $config->get_app_data('registerPravno')["$jezik"]['maticni'];
registerController::$maticni_unos = $config->get_app_data('registerPravno')["$jezik"]['maticni_unos'];
registerController::$sifra_drustva = $config->get_app_data('registerPravno')["$jezik"]['sifra_drustva'];
registerController::$sifra_drustva_unos = $config->get_app_data('registerPravno')["$jezik"]['sifra_drustva_unos'];
registerController::$duplikat_pravni = $config->get_app_data('registerPravno')["$jezik"]['duplikat'];

registerController::$opste_reci = Kontejner_jezik::daj_opste_reci();




