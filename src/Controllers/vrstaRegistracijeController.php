<?php

namespace app\Controllers;

use app\Core\Config;
use app\Core\Request;
use app\Core\Kontejner_jezik;
use app\Core\Logger_main;


class vrstaRegistracijeController extends AbstractController{
    /**reči koje se koriste na stranici vrstaregistracije.twig */
    public static $fizicko = null;
    public static $pravno = null;
    public static $registracija = null;
    public static $naslov = null;

    protected const admin_greska = 'admin pristupa stranici za vrstu registracije';
    protected const ulogovan = 'ulogovan korisnik pristupa stranici za vrstu registracije';
    protected const pristup = 'pristup stranici za vrstu registracije';

    /*opšte reči - one koje se javljaju na svim stranicama */
    public static $opste_reci = null;

    public function showAll(){
        /*ovde prikazujemo stranicu vrstaregistracije.twig ali zabranjujemo pristup
        ako je fizičko lice ili preduzeće ili administrator ulogovan */

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
        $reci += array('fizicko' => self::$fizicko, 'pravno' => self::$pravno,
                        'registracija' => self::$registracija, 'naslov' => self::$naslov);

        Logger_main::log_info(self::pristup);
        return $this->render('vrstaRegistracije.twig', $reci);
    }
}

$jezik = Kontejner_jezik::daj_jezik();
$config = Kontejner_jezik::daj_config();

/*ovde uzimamo opšte reči */
vrstaRegistracijeController::$opste_reci = Kontejner_jezik::daj_opste_reci();
/*i dopunjujemo ih rečima koje se javljaju na neku konkretnu stranicu, u ovom slučaju
vrstaRegistracije.twig*/

vrstaRegistracijeController::$fizicko = $config->get_app_data('vrstaRegistracije')["$jezik"]['fizickoLice'];
vrstaRegistracijeController::$pravno = $config->get_app_data('vrstaRegistracije')["$jezik"]['pravnoLice'];
vrstaRegistracijeController::$registracija = $config->get_app_data('vrstaRegistracije')["$jezik"]['registracija'];
vrstaRegistracijeController::$naslov = $config->get_app_data('vrstaRegistracije')["$jezik"]['naslov'];

