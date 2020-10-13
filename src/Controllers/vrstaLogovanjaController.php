<?php

namespace app\Controllers;

use app\Core\Request;
use app\Core\Kontejner_jezik;
use app\Core\Logger_main;

class vrstaLogovanjaController extends AbstractController{
    /*reči koje se koriste na ovoj stranici */
    public static $logovanje = null;
    public static $fizicko = null;
    public static $pravno = null;
    public static $naslov = null;

    protected const admin_greska = 'admin pristupa stranici za vrstu logovanja';
    protected const ulogovan = 'ulogovan korisnik pristupa stranici za vrstu logovanja';
    protected const pristup = 'pristup stranici za vrstu logovanja';


    /*opšte reči - one koje se ponavljaju na svakom strani, zato sam ih stavio u 
    klasi Kontejner_jezik da se neki delovi koda ne bi ponavljali u svakom Controller-u
    Za svaki kontroler uzmem opšte reči i one koje su specifične samo za tu određenu
    stranicu */

    public static $opste_reci = null;

    public function showAll(){
        /*prikazujemo stranicu vrstaLogovanja.twig i prosleđujemo joj rečnik 
        ako je neki korisnik ulogovan - ili fizičko lice ili preduzeće ili je ulogovan admin
        onda ne dozvoljavamo da se ova stranica prikaže*/

        $request = new Request();
        if($request->getSession()->has('admin')){
            Logger_main::log_warning(self::admin_greska);
            Header('Location: /');
        }
        
        if($request->getSession()->has('user')){
            $user = $request->getSession()->get('user');
            $nivo_pristupa = $request->getSession()->get('auth');
            Logger_main::log_warning(self::ulogovan." (id-${user},auth-${nivo_pristupa})");

            Header("Location: /logovanje/pocetnaStrana/$user");
        }
        $reci = self::$opste_reci;
        $reci += array('logovanje' => self::$logovanje,'fizicko' => self::$fizicko,
                       'pravno' => self::$pravno, 'naslov' => self::$naslov);
                       
        Logger_main::log_info(self::pristup);
        return $this->render('vrstaLogovanja.twig', $reci);
    }

} 

$jezik = Kontejner_jezik::daj_jezik();
$config = Kontejner_jezik::daj_config();

vrstaLogovanjaController::$logovanje = $config->get_app_data('vrstaLogovanja')["$jezik"]['logovanje'];
vrstaLogovanjaController::$fizicko = $config->get_app_data('vrstaLogovanja')["$jezik"]['fizickoLice'];
vrstaLogovanjaController::$pravno = $config->get_app_data('vrstaLogovanja')["$jezik"]['pravnoLice'];
vrstaLogovanjaController::$naslov = $config->get_app_data('vrstaLogovanja')["$jezik"]['naslov'];

vrstaLogovanjaController::$opste_reci = Kontejner_jezik::daj_opste_reci();