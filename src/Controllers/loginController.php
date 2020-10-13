<?php 

namespace app\Controllers;

use app\Models\Login;
use app\Core\Request;
use app\Controllers\pocetnaStranaController;
use app\Core\Session;
use app\Core\SecurityMonitor;
use app\Core\Kontejner_jezik;
use app\Core\Logger_main;
use app\Exceptions\ProsledjivanjePodataka;

class loginController extends AbstractController{

    protected const AUTH_TIP_COMPANY = 'company';
    protected const login = 'login.twig';
    protected const login_prevno = 'loginPravno.twig';
    protected const AUTH_TIP_FIZICKO_LICE = 'user';
    protected const USER = 'user';
    protected const log_ulogovan_admin = 'ulogovan admin pristupa stranici login.twig, biva preusmeren na loby';
    protected const log_ulogovan_admin_preduzece = 'ulogovan admin pristupa stranici loginPravno.twig, biva preusmeren na loby';
    protected const log_ulogovan_korisnik = 'ulogovan korisnik pristupa stranici login.twig, biva preusmeren na loby';
    protected const log_ulogovan_korisnik_preduzece = 'ulogovan korisnik pristupa stranici loginPreduzece.twig, biva preusmeren na loby';
    protected const log = 'nelogovan korisnik pristupa stranici login.twig';
    protected const log_preduzece = 'nelogovan korisnik pristupa stranici loginPravno.twig';
    protected const greska_prosledjivanja_podataka = 'greska prilikom prosledjivanja podataka serveru - loginController.php';
    protected const korisnik_se_ulogovao = 'korisnik se ulogovao';
    protected const preduzece_se_ulogovalo = 'preduzece se ulogovao';
    protected const pogresni_uneti_podaci = 'pogresni_uneti_podaci';
    protected const korisnik_ne_postoji = 'korisnik ne postoji';
    protected const pristupanje_stranici_za_validaciju_get_metodom = 'pristupanje stranici za validaciju get metodom loginController.php';

    /*reči za stranicu login.twig -> za fizička lica */
    public static $naslov = null;
    public static $korisnicko_ime = null;
    public static $korisnicko_ime_unos = null;
    public static $lozinka = null;
    public static $lozinka_unos = null;
    public static $zapamti_me = null;
    public static $zaboravili = null;
    public static $obavezno = null;
    /*reči za stranicu loginPravno.twig -> za preduzeća */

    public static $naslov_preduzece = null;
    public static $korisnicko_ime_preduzece = null;
    public static $korisnicko_ime_unos_preduzece = null;
    public static $lozinka_preduzece = null;
    public static $lozinka_unos_preduzece = null;
    public static $zapamti_me_preduzece = null;
    public static $zaboravili_preduzece = null;

    /*opšte reči */
    public static $opste_reci = null;

    /*reči u slučaju da je došlo do greške u toku logovanja */
    public static $nije_pronadjen = null;
    public static $pogresni_podaci = null;

    /*ovo je vektor reči za fizička lica koji ćemo kasnije da dopunimo odgovarajućom rečju ukoliko je
    došlo do neke greške prilikom logovanja fizičkog lica */
    public static function daj_vektor_reci_fizicka_lica(){
        $reci = self::$opste_reci;
        $reci += array(
            'naslov' => self::$naslov,
            'korisnicko_ime' => self::$korisnicko_ime,
            'korisnicko_ime_unos' => self::$korisnicko_ime_unos,
            'lozinka' => self::$lozinka,
            'lozinka_unos' => self::$lozinka_unos,
            'zapamti_me' => self::$zapamti_me,'zaboravili' => self::$zaboravili);

        return $reci;
    }
    /*ovo je vektor reči za preduzeća koji ćemo kasnije da dopunimo odgovarajućom rečju ukoliko je
    došlo do neke greške prilikom logovanja preduzeća */
    public static function daj_vektor_reci_pravna_lica(){
        $reci = self::$opste_reci;
        $reci += array('naslov_preduzece' => self::$naslov_preduzece,
            'korisnicko_ime_preduzece' => self::$korisnicko_ime_preduzece,
            'korisnicko_ime_unos_preduzece' => self::$korisnicko_ime_unos_preduzece,
            'lozinka_preduzece' => self::$lozinka_preduzece,
            'lozinka_unos_preduzece' => self::$lozinka_unos_preduzece,
            'zapamti_me_preduzece' => self::$zapamti_me_preduzece,
            'zaboravili_preduzece' => self::$zaboravili_preduzece);

        return $reci;
    }

    public function showAll(){
        $request = new Request();
        /*ukoliko je ulogovan administrator mi zabranjujemo prikaz ove stranice tj login.twig */
        if($request->getSession()->has('admin')){
            Logger_main::log_info(self::log_ulogovan_admin);
            Header('Location: /');
        }
        /*a ukoliko fizičko lice ulogovano, mi ga upućujemo na početnu stranicu */
        if($request->getSession()->has('user')){
            $user = $request->getSession()->get('user');
            Logger_main::log_info(self::log_ulogovan_korisnik."id:$user");

            Header("Location: /logovanje/pocetnaStrana/$user");
        }
        Logger_main::log_info(self::log);
        return $this->render('login.twig', self::daj_vektor_reci_fizicka_lica());

    }

    public function showAllPravno(){
        $request = new Request();
        /*ukoliko je ulogovan administrator mi zabranjujemo prikaz ove stranice tj loginPravno.twig*/
        if($request->getSession()->has('admin')){
            Logger_main::log_info(self::log_ulogovan_admin_preduzece);
            Header('Location: /');
        }
        /*a ukoliko fizičko lice ulogovano, mi ga upućujemo na početnu stranicu */
        if($request->getSession()->has('user')){
            $user = $request->getSession()->get('user');
            Logger_main::log_info(self::log_ulogovan_korisnik."id:$user");
            Header("Location: /logovanje/pocetnaStrana/$user");
        }
        Logger_main::log_info(self::log_preduzece);

        return $this->render('loginPravno.twig', self::daj_vektor_reci_pravna_lica());
    }

    public function checkPravno(){
        $loginModel = new Login($this->db);
        $request = new Request();
        $reci = self::daj_vektor_reci_pravna_lica();
        /*ne dozovljavamo pristup ovoj stranici GET metodom */
        if($request->isGet()){
            Logger_main::log_warning(self::pristupanje_stranici_za_validaciju_get_metodom);
            Header('Location: /');
        }else{
            if($request->getParams()->has('maticniBroj') && $request->getParams()->has('lozinka')){
                $rez = $loginModel->checkPravno($request->getParams()->get('maticniBroj'));
                if($rez){
                    /*korisnik ne postoji u bazi*/
                    $reci += array('error' => self::$nije_pronadjen);
                    Logger_main::log_error(self::korisnik_ne_postoji);
                    return $this->render('loginPravno.twig', $reci);

                }else{
                    $password_hash = $loginModel->getPasswordPravno($request->getParams()->get('maticniBroj'));
                    $password = $request->getParams()->get('lozinka');
                    if(!SecurityMonitor::equals($password, $password_hash)){
                        /*lozinka se ne poklapa */
                        $reci += array('error' => self::$pogresni_podaci);
                        Logger_main::log_error(self::pogresni_uneti_podaci);
                        return $this->render('loginPravno.twig', $reci);

                    }else{
                         /*POSTAVLJAMO NIVO PRISTUPA */
                         $ime = $request->getParams()->get('maticniBroj');
                         $id = $loginModel->getIdPravno($ime);
                         $request->setSession('user', $id);
                         $request->setSession('auth',self::AUTH_TIP_COMPANY);
                         Logger_main::log_info(self::preduzece_se_ulogovalo." id:$id");
                         Header("Location: /logovanje/pocetnaStrana/$id");
                         
                    }
                }
            }else{
                Logger_main::log_error(self::greska_prosledjivanja_podataka);
                throw new ProsledjivanjePodataka();
            }
        }
    }
    
    public function checkUsername() {
        $loginModel = new Login($this->db);
        $request = new Request();
        $reci = self::daj_vektor_reci_fizicka_lica();
        /*ne dozovljavamo pristup ovoj stranici GET metodom */
        if($request->isGet()){
            Logger_main::log_warning(self::pristupanje_stranici_za_validaciju_get_metodom);
            Header('Location: /');
        }else{
            if($request->getParams()->has('korisnickoIme') && $request->getParams()->has('lozinka')){
                $rez = $loginModel->checkUsername($request->getParams()->get('korisnickoIme'));

                if($rez){
                    /*KORISNIK NE POSTOJI U BAZI */
                    $reci += array('error' => self::$nije_pronadjen);
                    Logger_main::log_error(self::korisnik_ne_postoji);
                    return $this->render('login.twig', $reci);

                }else{
                    $password_hash = $loginModel->getPassword($request->getParams()->get('korisnickoIme'));
                    
                    $password = $request->getParams()->get('lozinka');
                    if(!SecurityMonitor::equals($password, $password_hash)){
                        /*lozinke se ne poklapaju */
                        $reci += array('error' => self::$pogresni_podaci);
                        Logger_main::log_error(self::pogresni_uneti_podaci);

                        return $this->render('login.twig', $reci);

                    }else{
                        /*POSTAVLJAMO NIVO PRISTUPA */
                        $ime = $request->getParams()->get('korisnickoIme');
                        $id = $loginModel->getId($ime);
                        $request->setSession('user', $id);
                        $request->setSession('auth',self::AUTH_TIP_FIZICKO_LICE);
                        Logger_main::log_info(self::korisnik_se_ulogovao." id:$id");

                        Header("Location: /logovanje/pocetnaStrana/$id");
                    }
                }
            }else{
                Logger_main::log_error(self::greska_prosledjivanja_podataka);
                throw new ProsledjivanjePodataka();
            }
        }
    } 
}

/*REČNIK STRANICE */
$jezik = Kontejner_jezik::daj_jezik();
$config = Kontejner_jezik::daj_config();
/*ovde uzimamo opšte reči */
loginController::$opste_reci = Kontejner_jezik::daj_opste_reci();
/*ovde reči za stranicu login.twig */
loginController::$naslov = $config->get_app_data('login')["$jezik"]['naslov'];
loginController::$korisnicko_ime = $config->get_app_data('login')["$jezik"]['korisnicko_ime'];
loginController::$korisnicko_ime_unos = $config->get_app_data('login')["$jezik"]['korisnicko_ime_unos'];
loginController::$lozinka = $config->get_app_data('login')["$jezik"]['lozinka'];
loginController::$lozinka_unos = $config->get_app_data('login')["$jezik"]['lozinka_unos'];
loginController::$zapamti_me = $config->get_app_data('login')["$jezik"]['zapamti_me'];
loginController::$zaboravili = $config->get_app_data('login')["$jezik"]['zaboravili'];
/*ovde reči za stranicu loginPravno.twig */
loginController::$naslov_preduzece = $config->get_app_data('login_pravno')["$jezik"]['naslov'];
loginController::$korisnicko_ime_preduzece = $config->get_app_data('login_pravno')["$jezik"]['korisnicko_ime'];
loginController::$korisnicko_ime_unos_preduzece = $config->get_app_data('login_pravno')["$jezik"]['korisnicko_ime_unos'];
loginController::$lozinka_preduzece = $config->get_app_data('login_pravno')["$jezik"]['lozinka'];
loginController::$lozinka_unos_preduzece = $config->get_app_data('login_pravno')["$jezik"]['lozinka_unos'];
loginController::$zapamti_me_preduzece = $config->get_app_data('login_pravno')["$jezik"]['zapamti_me'];
loginController::$zaboravili_preduzece = $config->get_app_data('login_pravno')["$jezik"]['zaboravili'];
/*a ovde reči ukoliko je došlo do greške prilikom logovanja bilo preduzeća ili fizičkog lica */
loginController::$nije_pronadjen = $config->get_app_data('logovanjeProvera')["$jezik"]['nije_pronadjen'];
loginController::$pogresni_podaci = $config->get_app_data('logovanjeProvera')["$jezik"]['pogresni_podaci'];
