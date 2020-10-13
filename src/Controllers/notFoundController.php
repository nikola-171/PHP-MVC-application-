<?php

namespace app\Controllers;

use app\Core\Config;
use app\Core\Request;
use app\Core\Kontejner_jezik;
use app\Core\Logger_main;

class notFoundController extends AbstractController{

    protected const info = 'nepostojeca stranica trazena';
    public static $msg = null;
    public static $pocetna = null;
    public static $opste_reci = null;

    public static function daj_vektor_reci(){
        $reci = self::$opste_reci;
        $reci += array('pocetna' => self::$pocetna,'msg' => self::$msg);
        return $reci;
    }

    public function show_error_message(){
        $reci = self::daj_vektor_reci();
        $request = new Request();

        if($request->getSession()->has('user')){
            if($request->getSession()->get('auth') == 'company'){
                Logger_main::log_info(self::info."id-company-".$request->getSession()->get('user'));
            }else{
                Logger_main::log_info(self::info."id-korisnik-".$request->getSession()->get('user'));
            }
        }elseif($request->getSession()->has('admin')){
            Logger_main::log_info(self::info." admin");
        }else{
            Logger_main::log_info(self::info);

        }
        return $this->render('not_Found.twig', $reci);
    }
}

$jezik = Kontejner_jezik::daj_jezik();
$config = Kontejner_jezik::daj_config();

notFoundController::$msg = $config->get_app_data('page_not_found')["$jezik"]['error_message'];
notFoundController::$pocetna = $config->get_app_data('page_not_found')["$jezik"]['pocetna'];
notFoundController::$opste_reci = Kontejner_jezik::daj_opste_reci();