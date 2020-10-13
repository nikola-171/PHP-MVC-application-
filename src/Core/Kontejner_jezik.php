<?php
namespace app\Core;
use app\Core\Config;

/*klasa koja nam daje opšte reči koje se koriste na stranicama kao i tekući jezik koji je
izabran od strane korisnika, preko ove klase mozemo lako da dodamo nove reci koje ce se pojaviti u skoro
svim stranicama */
class Kontejner_jezik{
    public static $odjava = null;
    public static $registruj_se = null;
    public static $ulogujte_se = null;
    public static $pretraga = null;
    public static $placeholder = null;
    public static $prosledi = null;
    public static $nazad = null;
    public static $postavi = null;
    public static $obavezno = null;
    public static $jezik = null;
    public static $prikaz = null;
    public static $prazna = null;

    public static $reci = null;

    public static $config = null;

    public static function daj_opste_reci(){
        return self::$reci;
    }

    public static function daj_config(){
        return self::$config;
    }
    public static function daj_jezik(){
        $request = new Request();

        $jezik = 'srp';
        if($request->getSession()->has('jezik')){
           if($request->getSession()->get('jezik') != ''){
                $jezik = $request->getSession()->get('jezik');
            }
        }

        return $jezik;
  }
}
Kontejner_jezik::$config = new Config();

$jezik = Kontejner_jezik::daj_jezik();
$config = Kontejner_jezik::daj_config();

Kontejner_jezik::$odjava = $config->get_app_data('opste')["$jezik"]['odjava'];
Kontejner_jezik::$registruj_se = $config->get_app_data('opste')["$jezik"]['registruj_se'];
Kontejner_jezik::$ulogujte_se = $config->get_app_data('opste')["$jezik"]['ulogujte_se'];
Kontejner_jezik::$pretraga = $config->get_app_data('opste')["$jezik"]['pretraga'];
Kontejner_jezik::$placeholder = $config->get_app_data('opste')["$jezik"]['placeholder'];
Kontejner_jezik::$prosledi = $config->get_app_data('opste')["$jezik"]['prosledi'];
Kontejner_jezik::$nazad = $config->get_app_data('opste')["$jezik"]['nazad'];
Kontejner_jezik::$postavi = $config->get_app_data('opste')["$jezik"]['postavi'];
Kontejner_jezik::$obavezno = $config->get_app_data('opste')["$jezik"]['obavezno'];
Kontejner_jezik::$prikaz = $config->get_app_data('opste')["$jezik"]['prikaz'];
Kontejner_jezik::$prazna = $config->get_app_data('opste')["$jezik"]['prazna'];





Kontejner_jezik::$reci = array('odjava' => Kontejner_jezik::$odjava,
'registruj_se' => Kontejner_jezik::$registruj_se,'ulogujte_se' =>Kontejner_jezik::$ulogujte_se,
'pretraga' => Kontejner_jezik::$pretraga,
'placeholder' => Kontejner_jezik::$placeholder, 'prosledi' => Kontejner_jezik::$prosledi,
'nazad' => Kontejner_jezik::$nazad, 'postavi' => Kontejner_jezik::$postavi,
'obavezno' => Kontejner_jezik::$obavezno, 'jezik' => Kontejner_jezik::daj_jezik(),
'prikaz' => Kontejner_jezik::$prikaz, 'prazna' => Kontejner_jezik::$prazna);
