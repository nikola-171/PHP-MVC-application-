<?php

namespace app\Controllers;

use app\Core\Config;
use app\Core\Request;
use app\Core\Kontejner_jezik;
use app\Core\Logger_main;

class infoController extends AbstractController{
   
    protected const strana = 'info.twig';
    protected const log_korisnik = 'korisnik je pristupio stranici info.twig';
    protected const log_admin = 'admin je pristupio stranici';
    protected const log = 'pristup stranici info.twig nelogovan korisnik';

    public static $niz = null;
    public static $naslov = null;
    public static $par_1 = null;
    public static $par_2 = null;
    public static $par_3 = null;
    public static $par_4 = null;
    public static $par_5 = null;

    public static function daj_reci(){
        $reci = self::$niz;
        $reci += array('naslov' => self::$naslov, 'par_1' => self::$par_1,
        'par_2' => self::$par_2, 'par_3' => self::$par_3, 'par_4' => self::$par_4,
        'par_5' => self::$par_5,);
        return $reci;
    }

    
    public function showAll(){
        $request = new Request();
        $reci = self::daj_reci();
        if($request->getSession()->has('user')){
            $reci += array("ulogovan" => "ulogovan");
            Logger_main::log_info(self::log_korisnik.'id-'.$request->getSession()->get('user'));
			return $this->render('info.twig', $reci); 
        }

        if($request->getSession()->has('admin')){
            $reci += array("admin" => "admin");
            Logger_main::log_info(self::log_admin.$request->getSession()->get('admin'));

			return $this->render('info.twig', $reci); 
        }
        Logger_main::log_info(self::log);

        return $this->render('info.twig', $reci);
    }
}
$jezik = Kontejner_jezik::daj_jezik();
$config = Kontejner_jezik::daj_config();

infoController::$niz = Kontejner_jezik::daj_opste_reci();
infoController::$naslov = $config->get_app_data('info')["$jezik"]['naslov'];
infoController::$par_1 = $config->get_app_data('info')["$jezik"]['par_1'];
infoController::$par_2 = $config->get_app_data('info')["$jezik"]['par_2'];
infoController::$par_3 = $config->get_app_data('info')["$jezik"]['par_3'];
infoController::$par_4 = $config->get_app_data('info')["$jezik"]['par_4'];
infoController::$par_5 = $config->get_app_data('info')["$jezik"]['par_5'];



