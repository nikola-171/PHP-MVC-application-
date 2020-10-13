<?php

namespace app\Controllers;

use app\Core\Config;
use app\Core\Request;
use app\Controllers\notFoundController;
use app\Models\Login;
use app\Models\PocetnaStrana;
use app\Core\Kontejner_jezik;
use app\Core\Logger_main;
use app\Core\SecurityMonitor;


class pocetnaStranaController extends AbstractController{
    protected const admin_pristup_pocetnoj_strani = 'admin pristupa pocetnoj strani, biva preusmeren na : /';
    protected const neulogovan_korisnik = 'nelogovan korisnik pristpa pocetnoj stranici, biva preusmeren na : /';
    protected const drugaciji_id = 'logovan korisnik salje get metodom id razlicit od svog';
    protected const korisnik_pristpa_pocetnoj_stranici = 'korisnik pristupa pocetnoj stranici';
    protected const postavljanje_komentara_pristup_get_metodom = 'postavljanje objave GET metodom';
    protected const postavljanje_komentara_neulogovan_korisnik = 'postavljanje objave od strane neulogovanog korisnika';
    protected const nivo_pristupa = 'ne postoji nivo pristupa prilikom postavljanja objave';
    protected const nivo_pristupa_fizicko_lice = 'fizicko lice pokusava da postavi objavu';
    protected const postavljena_objava = 'objava postavljena';
    protected const nevalidna_objava = 'objava ne sadrzi naslov ili sadrzaj';
    protected const greska_brisanja_objave = 'greska brisanja objave';
    protected const uspesno_obrisana_objava = 'objava uspesno obrisana';

    public static $opste_reci = null;

    public static $filter_naslov_sekcije = null;
    public static $filter_datum = null;
    public static $filter_datum_pre = null;
    public static $filter_datum_posle = null;
    public static $filter_naziv = null;
    public static $filter_naslov = null;
    public static $filter_sadrzi = null;
    public static $dogadjaj_naslov_sekcije = null;
    public static $dogadjaj_naslov = null;
    public static $dogadjaj_tekst = null;
    public static $dogadjaj_datum = null;
    public static $dogadjaj_slika = null;
    public static $citat = null;
    public static $citat_autor = null;
    public static $objava_obrisi = null;
    public static $ucitaj = null;
    public static $pocetna_strana = null;
    public static $nema = null;
    public static $objava_ne_postoji = null;

    public static function daj_reci(){
        $reci = self::$opste_reci;
        $reci += array( 
        'filter_naslov_sekcije' => self::$filter_naslov_sekcije,
        'filter_datum' => self::$filter_datum,
        'filter_datum_pre' => self::$filter_datum_pre,
        'filter_datum_posle' => self::$filter_datum_posle,
        'filter_naziv' => self::$filter_naziv,
        'filter_naslov' => self::$filter_naslov,
        'filter_sadrzi' => self::$filter_sadrzi,
        'dogadjaj_naslov_sekcije' => self::$dogadjaj_naslov_sekcije,
        'dogadjaj_naslov' => self::$dogadjaj_naslov,
        'dogadjaj_tekst' => self::$dogadjaj_tekst,
        'dogadjaj_naslov' => self::$dogadjaj_naslov,
        'dogadjaj_datum' => self::$dogadjaj_datum,
        'dogadjaj_slika' => self::$dogadjaj_slika,
        'citat' => self::$citat,
        'citat_autor' => self::$citat_autor,
        'objava_obrisi' => self::$objava_obrisi,
        'ucitaj' => self::$ucitaj,'pocetna_strana' => self::$pocetna_strana,
        'nema' => self::$nema);
        return $reci;
    }

    public function prikaz_objave(){
        $request = new Request();
        $baza = new PocetnaStrana($this->db);
        if(!$request->getParams()->has('id_objave')){
            Header('Location: /');
        }
        if($request->getSession()->has('admin')){
            Header('Location: /');
        }
        if(!$request->getSession()->has('user') && !$request->getSession()->has('auth')){
            Header('Location: /');
        }
        $reci = self::$opste_reci;
     
        $id = $request->getParams()->get('id_objave');
        $objava = $baza->daj_info_objava($id);
        if(empty($objava)){
            $reci += array('error' => self::$objava_ne_postoji);
        }else{
            $reci += array('naslov' => $objava[0]['naslov'], 'sadrzaj' => $objava[0]['tekst'],
                           'datum' => $objava[0]['datum'],'datum_postavljanja' => $objava[0]['datum_postavljanja'],
                        'putanja_slike' => $objava[0]['putanja_slike'],'orijentacija' => $objava[0]['orijentacija']);

        }

        //var_dump($objava); 
        return $this->render('prikaz_objave.twig', $reci);
    }

    public function showAll($id){
        $config = new Config();

        $request = new Request();

        if($request->getSession()->has('admin')){
            Logger_main::log_info(self::admin_pristup_pocetnoj_strani);
            Header('Location: /');
        }
        $datum = '';
        $datumParam = '=';
        $naziv = '';
        $naslov = '';
        $sadrzi = '';

        $jezik = 'srp';
		if($request->getSession()->has('jezik')){
			if($request->getSession()->get('jezik') != ''){
				$jezik = $request->getSession()->get('jezik');
			}
		}
		$jezik = strval($jezik);

        /* mehanizam filtera */
        if($request->getParams()->has('datum')){
            if($request->getParams()->get('datum') != ''){
                $datum = $request->getParams()->get('datum');
            }
        }
        if($request->getParams()->has('datumParam')){
            $datumParam = $request->getParams()->get('datumParam');
        }
        if($request->getParams()->has('naziv')){
            if($request->getParams()->get('naziv') != ''){
                $naziv = $request->getParams()->get('naziv');
            }
        }
        if($request->getParams()->has('naslov')){
            if($request->getParams()->get('naslov') != ''){
                $naslov = $request->getParams()->get('naslov');
            }
        }
        if($request->getParams()->has('sadrzi')){
            if($request->getParams()->has('sadrzi') != ''){
                $sadrzi = $request->getParams()->get('sadrzi');
            }
        }
        
        /*ovde zabranjujemo da kada se ulogujemo da u url-u unesemo drugi id kako bi nam pokazao početnu stranicu za drugog 
         korisnika */
        if(!$request->getSession()->has('user')){
            Logger_main::log_warning(self::neulogovan_korisnik);
            Header('Location: /');
        }
         
        if($request->getSession()->get('user') != $id){
            Logger_main::log_warning(self::drugaciji_id);
            Header('Location: /');
        }

        $loginModel = new Login($this->db);
        $pocetnaStrana = new PocetnaStrana($this->db);
        $sadrzaj = $pocetnaStrana->getContent($datum,$datumParam,$naziv,$naslov,$sadrzi);
        $reci = self::daj_reci();
        $reci += array('sadrzi' => $sadrzi, 'naslov' => $naslov, 'naziv' => $naziv, 
        'datumParam'=> $datumParam, 'datum' => $datum, 
        'korisnik' => $request->getSession()->get('user'), 'auth' => $request->getSession()->get('auth'));

        if($sadrzaj != false){
            $reci += array('id_zadnjeg' => $sadrzaj['id_zadnjeg'],'sadrzaj' => $sadrzaj['sadrzaj']);
        }else{
            $reci += array('id_zadnjeg' => '0','sadrzaj' => '');
        }
        $id = $request->getSession()->get('user');
        $nivo_pristupa = $request->getSession()->get('auth');
        Logger_main::log_info(self::korisnik_pristpa_pocetnoj_stranici." [id-${id},auth-${nivo_pristupa}]");
        return $this->render('pocetna_strana.twig', $reci);
    }

    

    public function postaviKomentar(){

        $pocetnaStrana = new PocetnaStrana($this->db);
        $request = new Request();

        if($request->isGet()){
            Logger_main::log_warning(self::postavljanje_komentara_pristup_get_metodom);
            Header('Location: /');
        }

        if(!$request->getSession()->has('user')){
            Logger_main::log_warning(self::postavljanje_komentara_neulogovan_korisnik);
            Header('Location: /');
        }

        if(!$request->getSession()->has('auth')){
            Logger_main::log_warning(self::nivo_pristupa);
            Header('Location: /');
        }

        if(!$request->getParams()->has('naslov') || !$request->getParams()->has('sadrzaj')){
            Logger_main::log_warning(self::nevalidna_objava);
            Header('Location: logovanje');
        }

        if($request->getSession()->get('auth') != 'company'){
            Logger_main::log_warning(self::nivo_pristupa_fizicko_lice);
            Header('Location: logovanjePravno');
        }

        $id = $request->getSession()->get('user');
        $sadrzaj = $request->getParams()->get('sadrzaj');
        $naslov = $request->getParams()->get('naslov');
        $datum = $request->getParams()->get('datum');
        if($naslov == ''){
            Header('Location: logovanje');
        }
        if($sadrzaj == ''){
            Header('Location: logovanje');
        }
        if($datum == ''){
            Header('Location: logovanje');
        }
        
        $id_komentara = $pocetnaStrana->postaviKomentar(SecurityMonitor::sanitaraizeString($sadrzaj),
                        SecurityMonitor::sanitaraizeString($naslov),
                        SecurityMonitor::sanitaraizeString($datum));
       
         $saveto = '';
         $orijentacija = '';
         if(isset($_FILES['image']['name']) && !empty($_FILES['image']['name'])){
            
            if(!file_exists('profile_images/'))
            {
                mkdir('profile_images/', 0777, true);
            }
            $saveto = "profile_images/$id_komentara.jpg";
            move_uploaded_file($_FILES['image']['tmp_name'], $saveto);
            switch($_FILES['image']['type'])
            {
            case "image/gif":
                $src = imagecreatefromgif($saveto);
                break;
            case "image/jpeg":
            case "image/jpg":
                $src = imagecreatefromjpeg($saveto);
                break;
            case "image/png":
                $src = imagecreatefrompng($saveto);
                break;
            default:
                die('greska u ekstenziji slike');
            }
            list($w, $h) = getimagesize($saveto);
            if($w > $h){
                $orijentacija = 's';
            }else{
                $orijentacija = 'v';
            }
            $max = 1200;
            $tw = $w;
            $th = $h;
            if($w > $h && $w > $max)
            {
                $th = $max * $h / $w;
                $tw = $max;
            }
            elseif($h > $w && $h > $max)
            {
                $tw = $max * $w / $h;
                $th = $max;
            }
            else
            {
                $th = $tw = $max;
            }
             // 3) Kreiranje nove slicice sa zadatim dimenzijama ($tw x $th)
             $tmp = imagecreatetruecolor($tw, $th);
             imagecopyresampled($tmp, $src, 0, 0, 0, 0, $tw, $th, $w, $h);
             imageconvolution($tmp, array(array(-1, -1, -1), array(-1, 16, -1),
                 array(-1, -1, -1)), 8, 0);
             imagejpeg($tmp, $saveto);
             imagedestroy($src);
             imagedestroy($tmp);
             Logger_main::log_info(self::postavljena_objava." id-${id}");
             $pocetnaStrana->ubaci_sliku_za_komentar($id_komentara, $saveto, $orijentacija);
         }
        Header('Location: logovanje');
    }

    public function izbrisiObjavu(){
        $request = new Request();
        if($request->isGet()){
            Logger_main::log_warning(self::greska_brisanja_objave.' pristup get metodom');
            Header('Location: /');
        }
        if(!$request->getSession()->has('user')){
            Logger_main::log_warning(self::greska_brisanja_objave.' neulogovani korisnik');
            Header('Location: /');
        }

        if(!$request->getSession()->has('auth')){
            Logger_main::log_warning(self::greska_brisanja_objave.' ne postoji nivo pristupa');

            Header('Location: /');
        }

        if($request->getSession()->get('auth') != 'company'){
            $id = $request->getSession()->get('user');
            Logger_main::log_warning(self::greska_brisanja_objave." fizicko lice pokusava da obrise objavu (id-${id})");

            Header('Location:/');
        }

        if(!$request->getParams()->has('id')){
            Logger_main::log_warning(self::greska_brisanja_objave.' ne postoji id');

            Header('Location: logovanje');
        }

        $pocetnaStrana = new PocetnaStrana($this->db);
        $id = $request->getParams()->get('id');
        $id_preduzeca = $request->getSession()->get('user');
        $pocetnaStrana->izbrisiObjavu($id);
        Logger_main::log_info(self::uspesno_obrisana_objava ." (id-${$id_preduzeca})");
        Header('Location: logovanje');
    }

    public function ucitajObjave(){
        $request = new Request();
        if($request->isGet()){
            Header('Location: /');
        }
        $pocetnaStrana = new PocetnaStrana($this->db);
        $datum = '';
        $datumParam = '=';
        $naziv = '';
        $naslov = '';
        $sadrzi = '';

        if(!$request->getParams()->has('id')){
            die('Došlo je do greške prilikom slanja podataka između ajax funkcije i php skripta');
        }

        $id = $request->getParams()->get('id');
        $datum = $request->getParams()->get('datum');
        $datumParam = $request->getParams()->get('datumParam');
        $naziv = $request->getParams()->get('naziv');
        $naslov = $request->getParams()->get('naslov');
        $sadrzi = $request->getParams()->get('sadrzi');

        $sadrzaj = $pocetnaStrana->ucitaj_jos_objava($id, $datum, $datumParam, $naziv, $naslov, $sadrzi);
        echo json_encode($sadrzaj);
     
    }
}

$jezik = Kontejner_jezik::daj_jezik();
$config = Kontejner_jezik::daj_config();

pocetnaStranaController::$filter_naslov_sekcije = $config->get_app_data('pocetna_strana')["$jezik"]['filter_naslov_sekcije'];
pocetnaStranaController::$filter_datum = $config->get_app_data('pocetna_strana')["$jezik"]['filter_datum'];
pocetnaStranaController::$filter_datum_pre = $config->get_app_data('pocetna_strana')["$jezik"]['filter_datum_pre'];
pocetnaStranaController::$filter_datum_posle = $config->get_app_data('pocetna_strana')["$jezik"]['filter_datum_posle'];
pocetnaStranaController::$filter_naziv = $config->get_app_data('pocetna_strana')["$jezik"]['filter_naziv'];
pocetnaStranaController::$filter_naslov = $config->get_app_data('pocetna_strana')["$jezik"]['filter_naslov'];
pocetnaStranaController::$filter_sadrzi = $config->get_app_data('pocetna_strana')["$jezik"]['filter_sadrzi'];
pocetnaStranaController::$dogadjaj_naslov_sekcije = $config->get_app_data('pocetna_strana')["$jezik"]['dogadjaj_naslov_sekcije'];
pocetnaStranaController::$dogadjaj_naslov = $config->get_app_data('pocetna_strana')["$jezik"]['dogadjaj_naslov'];
pocetnaStranaController::$dogadjaj_tekst = $config->get_app_data('pocetna_strana')["$jezik"]['dogadjaj_tekst'];
pocetnaStranaController::$dogadjaj_datum = $config->get_app_data('pocetna_strana')["$jezik"]['dogadjaj_datum'];
pocetnaStranaController::$dogadjaj_slika = $config->get_app_data('pocetna_strana')["$jezik"]['dogadjaj_slika'];
pocetnaStranaController::$citat = $config->get_app_data('pocetna_strana')["$jezik"]['citat'];
pocetnaStranaController::$citat_autor = $config->get_app_data('pocetna_strana')["$jezik"]['citat_autor'];
pocetnaStranaController::$objava_obrisi = $config->get_app_data('pocetna_strana')["$jezik"]['objava_obrisi'];
pocetnaStranaController::$ucitaj = $config->get_app_data('pocetna_strana')["$jezik"]['ucitaj'];
pocetnaStranaController::$pocetna_strana = $config->get_app_data('pocetna_strana')["$jezik"]['pocetna_strana'];
pocetnaStranaController::$nema = $config->get_app_data('pocetna_strana')["$jezik"]['nema'];

pocetnaStranaController::$objava_ne_postoji = $config->get_app_data('prikaz_objave')["$jezik"]['ne_postoji'];

pocetnaStranaController::$opste_reci = kontejner_jezik::daj_opste_reci();
