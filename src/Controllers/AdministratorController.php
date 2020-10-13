<?php

namespace app\Controllers;

use app\Core\Request;
use app\Models\Administrator;
use app\Core\SecurityMonitor;
use app\Core\Kontejner_jezik;
use app\Core\PDF_kreator;
use app\Core\Logger_main;
use FPDF;

use app\Controllers\logoutController;

class AdministratorController extends AbstractController{

    protected const nivo_pristupa = 'admin';
    protected const moze = 'moze';

    /*reči za početnu stranu administratora */
    public static $naslov = null;
    public static $izvestaj = null;
    public static $loger_info = null; 
    public static $loger_warning = null; 
    public static $loger_error = null; 
    public static $lista_fizickih = null;
    public static $lista_preduzeca = null; 
    public static $lista_objava = null;
    public static $prikaz_fizicka = null; 
    public static $prikaz_preduzeca = null; 
    public static $prikaz_objava = null;

    public static $opste_reci = null;

    public static $prazna_polja = null;
    public static $ne_poklapanje = null; 

    public static $naslov_logovanje = null;
    public static $naslov_registracija = null;

    public static $naslov_fizicka_lica = null;
    public static $naslov_preduzeca = null;
    public static $naslov_objava = null;

    /*logovanje */
    public static $logovanje = null;
    public static $admin_ime = null;
    public static $admin_lozinka = null;
    public static $nema_admina = null;

    public static $neispravno = null; 
    public static $nema = null; 


    /*registracija administratora */
    public static $registracija = null;
    public static $ime = null;
    public static $prezime = null;
    public static $email = null;
    public static $admin_ime_registracija = null;
    public static $lozinka = null;
    public static $lozinka_potvrda = null;

    /*reči u slučaju da je došlo do neke greške */
    public static $vec_postoji = null;
    public static $uspesno = null;
    public static $greska = null;

    /*reči za stranicu administrator prikaz preduzeća */
    public static $id = null;
    public static $naziv = null;
    public static $maticni = null;
    public static $sifra_drustva = null;
    public static $email_prikaz_preduezca = null;
    public static $obrisi = null;
    public static $greska_brisanja_preduzeca = null;

    /*reči za stranicu administrator prikaz fizičkih lica */
    public static $id_fizicko_lice = null;
    public static $ime_fizicko_lice = null;
    public static $prezime_fizicko_lice = null;
    public static $email_fizicko_lice = null;
    public static $datum_rodjenja_fizicko_lice = null;
    public static $korisnicko_ime_fizicko_lice = null;
    public static $greska_fizicko_lice = null;

    /*reči za stranicu administrator prikaz objava  */
    public static $id_objava = null;
    public static $datum_odrzavanja_objava = null;
    public static $naslov_prikaz_objava = null;
    public static $naziv_preduzeca_objava = null;
    public static $akcija_objava = null;
    public static $prikaz_objave = null;
    public static $greska_objava = null;
    public static $datum_postavljanja_objava = null;
    public static $liste = null;


    public static function daj_vektor_reci(){
        $reci = self::$opste_reci;
        $reci += array('prazna_polja' => self::$prazna_polja,
            'logovanje' => self::$logovanje,
            'admin_ime' => self::$admin_ime,
            'admin_lozinka' => self::$admin_lozinka,
            'nema_admina' => self::$nema_admina,
            'naslov' => self::$naslov_logovanje
        );
        return $reci;
    }

    public static function daj_vektor_reci_pocetna_strana(){
        $reci = self::$opste_reci;
        $reci += array('naslov' => self::$naslov,'izvestaj' => self::$izvestaj,
        'loger_info' => self::$loger_info,'loger_warning' => self::$loger_warning,
        'loger_error' => self::$loger_error,'lista_fizickih' => self::$lista_fizickih,
        'lista_preduzeca' => self::$lista_preduzeca,'lista_objava' => self::$lista_objava,
        'prikaz_fizicka' => self::$prikaz_fizicka,'prikaz_preduzeca' => self::$prikaz_preduzeca,
        'prikaz_objava' => self::$prikaz_objava, 'liste' => self::$liste);

        return $reci;
    }

    public static function daj_vektor_reci_prikaz_fizicka_lica(){
        $reci = self::$opste_reci;
        $reci += array('id' => self::$id_fizicko_lice,'ime' => self::$ime_fizicko_lice,
        'prezime' => self::$prezime_fizicko_lice,
        'email' => self::$email_fizicko_lice,
        'datum_rodjenja' => self::$datum_rodjenja_fizicko_lice,
        'korisnicko_ime' => self::$korisnicko_ime_fizicko_lice,'obrisi' => self::$obrisi,
        'naslov' => self::$naslov_fizicka_lica,'greska' => self::$greska_fizicko_lice);
        return $reci;
    }

    public static function daj_vektor_reci_prikaz_preduezca(){
        $reci = self::$opste_reci;
        $reci += array('id' => self::$id,'naziv' => self::$naziv,'maticni' => self::$maticni,
        'sifra_drustva' => self::$sifra_drustva,
        'email_prikaz_preduezca' => self::$email_prikaz_preduezca,'obrisi' => self::$obrisi,
        'naslov' => self::$naslov_preduzeca, 'greska' => self::$greska_brisanja_preduzeca);
        return $reci;
    }

    public static function daj_vektor_reci_prikaz_objava(){
        $reci = self::$opste_reci;
        $reci += array('id' => self::$id_objava,'datum_odrzavanja' => self::$datum_odrzavanja_objava,
        'naslov' => self::$naslov_prikaz_objava,'naziv_preduzeca' => self::$naziv_preduzeca_objava,
        'akcija' => self::$akcija_objava,'prikaz' => self::$prikaz_objave,
        'greska' => self::$greska_objava, 'datum_postavljanja' => self::$datum_postavljanja_objava,
        'obrisi' => self::$obrisi
    );
        return $reci;
    }

    public static function daj_vektor_reci_registracija(){
        $reci = self::$opste_reci;
        $reci += array('ne_poklapanje' => self::$ne_poklapanje,'prazna_polja' => self::$prazna_polja,
        'registracija' => self::$registracija,'ime' => self::$ime,'prezime' => self::$prezime,
        'email' => self::$email,'admin_ime' => self::$admin_ime_registracija,'lozinka' => self::$lozinka,
        'lozinka_potvrda' => self::$lozinka_potvrda, 'naslov' => self::$naslov_registracija);

        return $reci;
    }

    public function administrator_logout(){
        $request = new Request();
        $logout = new logoutController($this->di, $request);
        $logout->logout();
        Logger_main::log_info('admin se izlogovao');
    }

    public function stampaj_sadrzaj_logera_info(){
        $pdf = new PDF_kreator();
        $title = 'logger info';
        $pdf->SetTitle($title);
        $pdf->SetAuthor('');
        $pdf->PrintChapter(1,'Logger info','F:\projekat\src\Logger\logger_info.log');
        $pdf->Output();
    }

    public function stampaj_sadrzaj_logera_warning(){
        $pdf = new PDF_kreator();
        $title = 'logger warning';
        $pdf->SetTitle($title);
        $pdf->SetAuthor('');
        $pdf->PrintChapter(1,'Logger warning','F:\projekat\src\Logger\logger_warning.log');
        $pdf->Output();
    }

    public function stampaj_sadrzaj_logera_error(){
        $pdf = new PDF_kreator();
        $title = 'logger error';
        $pdf->SetTitle($title);
        $pdf->SetAuthor('');
        $pdf->PrintChapter(1,'Logger error','F:\projekat\src\Logger\logger_error.log');
        $pdf->Output();
    }

    public function stampaj_fizickih_lica(){
        $baza = new Administrator($this->db);
        $fizicka_lica = $baza->daj_sva_fizicka_lica();
        $tekst_za_stampu = "";

        foreach($fizicka_lica as $lice){
            $tekst_za_stampu .= <<<EOD
                 id : ${lice['id']}, 
                 ime : ${lice['ime']},
                 prezime : ${lice['prezime']},
                 datum rodjenja : ${lice['dan_rodjenja']}/${lice['mesec_rodjenja']}/${lice['godina_rodjenja']},
                 email : ${lice['email']}, 
                 korisnicko ime : ${lice['korisnicko_ime']}
                 *************************\n
            EOD;
            $tekst_za_stampu = mb_convert_encoding($tekst_za_stampu, 'UTF-8', 'iso-8859-2');
        }
        $pdf = new PDF_kreator();
        $pdf->SetTitle('fizicka lica');

        $pdf->SetAuthor('');
        $pdf->stampajTekst(1,'naslov',$tekst_za_stampu);
        $pdf->Output();

    }

    public function stampaj_preduzeca(){
        $baza = new Administrator($this->db);
        $preduzeca = $baza->daj_sva_preduzeca();
        $tekst_za_stampu = "";

        foreach($preduzeca as $lice){
            $tekst_za_stampu .= <<<EOD
                  id : ${lice['id']}, 
                  ime : ${lice['naziv']},
                  maticni broj : ${lice['maticni_broj']},
                  sifra privrednog drustva : ${lice['sifra_privrednog_drustva']}, 
                  email - ${lice['email']}
                  *************************\n
            EOD;
            $tekst_za_stampu = mb_convert_encoding($tekst_za_stampu, 'UTF-8', 'iso-8859-2');
        }
       $pdf = new PDF_kreator();
       $pdf->SetTitle('preduzeca');

       $pdf->SetAuthor('');
       $pdf->stampajTekst(1,'naslov',$tekst_za_stampu);
       $pdf->Output();
   

    }

    public function stampaj_objave(){
        $baza = new Administrator($this->db);
        $objave = $baza->daj_sve_objave();
        $tekst_za_stampu = "";

        foreach($objave as $lice){
            $tekst_za_stampu .= <<<EOD
                 id : ${lice['id']},
                 naslov : ${lice['naslov']}
                 preduzece : ${lice['naziv']},
                 datum odrzavanja : ${lice['datum']},
                 datum postavljanja : ${lice['datum_postavljanja']},
                 *********************\n
            EOD;
            $tekst_za_stampu = mb_convert_encoding($tekst_za_stampu, 'UTF-8', 'iso-8859-2');
        }
        $pdf = new PDF_kreator();
        $pdf->SetTitle('objave');

        $pdf->SetAuthor('');
        $pdf->stampajTekst(1,'naslov',$tekst_za_stampu);
        $pdf->Output();

    }

    public function izbrisi_fizicko_lice(){
        $request = new Request();
        $baza = new Administrator($this->db);
        if($request->isGet()){
            Header('Location: pocetna_strana_administratora');
        }
        $id = $request->getParams()->get('id');
        $row = $baza->izbrisi_fizicko_lice($id);
        if($row != 1){
            Logger_main::log_error('greska brisanja fizickog lica');

            echo 'greska';

        }else{
            Logger_main::log_info('izbrisali fizicko lice');
            echo 'uspeh';

        }
    }

    public function izbrisi_preduzece(){
        $request = new Request();
        $baza = new Administrator($this->db);
        if($request->isGet()){
            Header('Location: pocetna_strana_administratora');
        }
        $id = $request->getParams()->get('id');
        $row = $baza->izbrisi_preduzece($id);
        if($row != 1){
            Logger_main::log_error('greska brisanja preduzeca');

            echo 'greska';

        }else{
            Logger_main::log_info('izbrisali preduzece');
            echo 'uspeh';

        }

    }

    public function izbrisi_objavu(){
        $request = new Request();
        $baza = new Administrator($this->db);

        if($request->isGet()){
            Header('Location: pocetna_strana_administratora');
        }
        $id = $request->getParams()->get('id');
        $row = $baza->izbrisi_objavu($id);
        if($row != 1){
            echo 'greska';
        }else{
            echo 'uspeh';

        }
    }

    public function prikaz_objava(){
        $baza = new Administrator($this->db);

        $objave = $baza->daj_sve_objave();
        $reci = self::daj_vektor_reci_prikaz_objava();

        $reci += array('objave' => $objave, 'naslov_strane' => self::$naslov_objava);

        return $this->render('administrator_prikaz_objava.twig',$reci);
    }

    public function prikaz_preduzeca(){
        $baza = new Administrator($this->db);

        $preduzeca = $baza->daj_sva_preduzeca();
        $reci = self::daj_vektor_reci_prikaz_preduezca();
        $reci += array('preduzeca' => $preduzeca);
        return $this->render('administrator_prikaz_preduzeca.twig',$reci);
    }

    public function prikaz_fizickih_lica(){
        $baza = new Administrator($this->db);
        $fizicka_lica = $baza->daj_sva_fizicka_lica();
        $reci = self::daj_vektor_reci_prikaz_fizicka_lica();
        $reci += array('fizicka_lica' => $fizicka_lica);
   
        return $this->render('administrator_prikaz_fizickih_lica.twig',$reci);

    }

    public function prikaz_pocetne_strane_administrator(){
        
       $request = new Request();
       if($request->getSession()->has('user')){
        Header('Location: /');
       }
       if(!$request->getSession()->has('admin')){
           Header('Location: /administrator_logovanje');
       }
       $reci = self::daj_vektor_reci_pocetna_strana();

       return $this->render('administrator_pocetna_strana.twig', $reci);

   }

    public function postavi_sessiju(){
        $request = new Request();

        if($request->isGet()){
            Header('Location: /');
        }

        if(!$request->getParams()->has('administrator_ime')){
            die('Greska prililom prijavljivanja administratora');
        }

        $administrator_ime = $request->getParams()->get('administrator_ime');

        $request->setSession('admin', self::nivo_pristupa);
        
    }

    public function verifikacija_administratora(){
        $baza = new Administrator($this->db);
        $request = new Request();
        if(!$request->getParams()->has('administrator_ime') || !$request->getParams()->has('administrator_lozinka')){
            die('Greška prilikom verifikacije administratora');
        }else{
            if($baza->provera_da_li_postoji_admin()){
                $administrator_ime = $request->getParams()->get('administrator_ime');
                $administrator_lozinka = $request->getParams()->get('administrator_lozinka');
                
                $podaci_iz_baze = $baza->vadi_podatke_iz_baze($administrator_ime);
                if(empty($podaci_iz_baze)){
                    echo self::$neispravno;
                }else{
                    $administrator_lozinka_iz_baze = $podaci_iz_baze[0]['lozinka'];
                    if(SecurityMonitor::equals($administrator_lozinka, $administrator_lozinka_iz_baze)){
                        echo self::moze;
                    }else{
                        echo self::$neispravno;
                    }
                }
            }else{
                echo $nema;
            }
        }
    }

    public function registracija_administratora(){

        $baza = new Administrator($this->db);
        $request = new Request();

        if($request->isGet()){
            Header('Location: /');
        }

        if($baza->provera_da_li_postoji_admin()){
            die(self::$vec_postoji);
        }else{
            if(!$request->getParams()->has('ime') || !$request->getParams()->has('prezime') ||!$request->getParams()->has('email')
                || !$request->getParams()->has('administrator_ime') ||!$request->getParams()->has('administrator_lozinka')){
                    die(self::$greska);
            }

            $ime = $request->getParams()->get('ime');
            $prezime = $request->getParams()->get('prezime');
            $email = $request->getParams()->get('email');
            $administrator_ime = $request->getParams()->get('administrator_ime');
            $lozinka = $request->getParams()->get('administrator_lozinka');

            $administrator_lozinka = SecurityMonitor::encrypt_password($lozinka);

            $rezultat = $baza->ubaci_podatke_u_bazi($ime, $prezime, $email, $administrator_ime, $administrator_lozinka);
            echo self::$uspesno;
        }
    }

    public function prikaz_logovanje(){
        $request = new Request();
        if($request->getSession()->has('user')){
            Header('Location: /');
           }
        if($request->getSession()->has('admin')){
            Header('Location: /pocetna_strana_administratora');
        }
        $reci = self::$opste_reci;
        return $this->render('administrator_logovanje.twig', self::daj_vektor_reci());
    }

    public function prikaz_registracija(){
        $request = new Request();
        if($request->getSession()->has('user')){
            Header('Location: /');
           }
        if($request->getSession()->has('admin')){
            Header('Location: /pocetna_strana_administratora');
        }
        return $this->render('administrator_registracija.twig', self::daj_vektor_reci_registracija());
    }
}

$jezik = Kontejner_jezik::daj_jezik();
$config = Kontejner_jezik::daj_config();

AdministratorController::$opste_reci = Kontejner_jezik::daj_opste_reci();

AdministratorController::$logovanje = $config->get_app_data('administrator')["$jezik"]['logovanje'];
AdministratorController::$admin_ime = $config->get_app_data('administrator')["$jezik"]['admin_ime'];
AdministratorController::$admin_lozinka = $config->get_app_data('administrator')["$jezik"]['admin_lozinka'];
AdministratorController::$nema_admina = $config->get_app_data('administrator')["$jezik"]['nema_admina'];
AdministratorController::$neispravno = $config->get_app_data('administrator')["$jezik"]['neispravno'];
AdministratorController::$nema = $config->get_app_data('administrator')["$jezik"]['nema'];
AdministratorController::$prazna_polja = $config->get_app_data('administrator')["$jezik"]['prazna_polja'];
AdministratorController::$ne_poklapanje = $config->get_app_data('administrator')["$jezik"]['ne_poklapanje'];
AdministratorController::$naslov_logovanje = $config->get_app_data('administrator')["$jezik"]['naslov'];

AdministratorController::$naslov_fizicka_lica = $config->get_app_data('administrator')["$jezik"]['naslov_prikaz_fizicka'];
AdministratorController::$naslov_preduzeca = $config->get_app_data('administrator')["$jezik"]['naslov_prikaz_preduzeca'];
AdministratorController::$naslov_objava = $config->get_app_data('administrator')["$jezik"]['naslov_prikaz_objava'];

AdministratorController::$vec_postoji = $config->get_app_data('registracija_administratora')["$jezik"]['vec_postoji'];
AdministratorController::$uspesno = $config->get_app_data('registracija_administratora')["$jezik"]['uspesno'];
AdministratorController::$greska = $config->get_app_data('registracija_administratora')["$jezik"]['greska'];
AdministratorController::$naslov_registracija = $config->get_app_data('registracija_administratora')["$jezik"]['naslov'];

AdministratorController::$registracija = $config->get_app_data('registracija_administratora')["$jezik"]['registracija'];
AdministratorController::$ime = $config->get_app_data('registracija_administratora')["$jezik"]['ime'];
AdministratorController::$prezime = $config->get_app_data('registracija_administratora')["$jezik"]['prezime'];
AdministratorController::$email = $config->get_app_data('registracija_administratora')["$jezik"]['email'];
AdministratorController::$admin_ime_registracija = $config->get_app_data('registracija_administratora')["$jezik"]['admin_ime'];
AdministratorController::$lozinka = $config->get_app_data('registracija_administratora')["$jezik"]['lozinka'];
AdministratorController::$lozinka_potvrda = $config->get_app_data('registracija_administratora')["$jezik"]['lozinka_potvrda'];

AdministratorController::$naslov = $config->get_app_data('administraor_pocetna')["$jezik"]['naslov'];
AdministratorController::$izvestaj = $config->get_app_data('administraor_pocetna')["$jezik"]['izvestaj'];
AdministratorController::$loger_info = $config->get_app_data('administraor_pocetna')["$jezik"]['loger_info'];
AdministratorController::$loger_warning = $config->get_app_data('administraor_pocetna')["$jezik"]['loger_warning'];
AdministratorController::$loger_error = $config->get_app_data('administraor_pocetna')["$jezik"]['loger_error'];
AdministratorController::$lista_fizickih = $config->get_app_data('administraor_pocetna')["$jezik"]['lista_fizickih'];
AdministratorController::$lista_preduzeca = $config->get_app_data('administraor_pocetna')["$jezik"]['lista_preduzeca'];
AdministratorController::$lista_objava = $config->get_app_data('administraor_pocetna')["$jezik"]['lista_objava'];
AdministratorController::$prikaz_fizicka = $config->get_app_data('administraor_pocetna')["$jezik"]['prikaz_fizicka'];
AdministratorController::$prikaz_preduzeca = $config->get_app_data('administraor_pocetna')["$jezik"]['prikaz_preduzeca'];
AdministratorController::$prikaz_objava = $config->get_app_data('administraor_pocetna')["$jezik"]['prikaz_objava'];
AdministratorController::$liste = $config->get_app_data('administraor_pocetna')["$jezik"]['liste'];

AdministratorController::$id = $config->get_app_data('administrator_prikaz_preduzeca')["$jezik"]['id'];
AdministratorController::$naziv = $config->get_app_data('administrator_prikaz_preduzeca')["$jezik"]['naziv'];
AdministratorController::$maticni = $config->get_app_data('administrator_prikaz_preduzeca')["$jezik"]['maticni'];
AdministratorController::$sifra_drustva = $config->get_app_data('administrator_prikaz_preduzeca')["$jezik"]['sifra_drustva'];
AdministratorController::$email_prikaz_preduezca = $config->get_app_data('administrator_prikaz_preduzeca')["$jezik"]['email'];
AdministratorController::$obrisi = $config->get_app_data('administrator_prikaz_preduzeca')["$jezik"]['obrisi'];
AdministratorController::$greska_brisanja_preduzeca = $config->get_app_data('administrator_prikaz_preduzeca')["$jezik"]['greska'];

AdministratorController::$id_fizicko_lice = $config->get_app_data('administrator_prikaz_fizickih_lica')["$jezik"]['id'];
AdministratorController::$ime_fizicko_lice = $config->get_app_data('administrator_prikaz_fizickih_lica')["$jezik"]['ime'];
AdministratorController::$prezime_fizicko_lice = $config->get_app_data('administrator_prikaz_fizickih_lica')["$jezik"]['prezime'];
AdministratorController::$email_fizicko_lice = $config->get_app_data('administrator_prikaz_fizickih_lica')["$jezik"]['email'];
AdministratorController::$datum_rodjenja_fizicko_lice = $config->get_app_data('administrator_prikaz_fizickih_lica')["$jezik"]['datum_rodjenja'];
AdministratorController::$korisnicko_ime_fizicko_lice = $config->get_app_data('administrator_prikaz_fizickih_lica')["$jezik"]['korisnicko_ime'];
AdministratorController::$greska_fizicko_lice = $config->get_app_data('administrator_prikaz_fizickih_lica')["$jezik"]['greska'];

AdministratorController::$id_objava = $config->get_app_data('administrator_prikaz_objava')["$jezik"]['id'];
AdministratorController::$datum_odrzavanja_objava = $config->get_app_data('administrator_prikaz_objava')["$jezik"]['datum_odrzavanja'];
AdministratorController::$naslov_prikaz_objava = $config->get_app_data('administrator_prikaz_objava')["$jezik"]['naslov'];
AdministratorController::$naziv_preduzeca_objava = $config->get_app_data('administrator_prikaz_objava')["$jezik"]['naziv_preduzeca'];
AdministratorController::$akcija_objava = $config->get_app_data('administrator_prikaz_objava')["$jezik"]['akcija'];
AdministratorController::$prikaz_objave = $config->get_app_data('administrator_prikaz_objava')["$jezik"]['prikaz'];
AdministratorController::$greska_objava = $config->get_app_data('administrator_prikaz_objava')["$jezik"]['greska'];
AdministratorController::$datum_postavljanja_objava = $config->get_app_data('administrator_prikaz_objava')["$jezik"]['datum_postavljanja'];


