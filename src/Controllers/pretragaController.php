<?php

namespace app\Controllers;

use app\Core\Config;
use app\Core\Request;
use app\Models\Login;
use app\Models\Pretraga;
use app\Core\Kontejner_jezik;
use app\Core\Logger_main;

class pretragaController extends AbstractController{
    
    protected const nema_parametra_pretraga = 'ne postoji parametar pretraga';
    protected const pretraga_kratka = 'kriterijum za pretragu je prazan ili je kraci od 4 karaktera';
    protected const prazna_pretraga = 'pretraga nije nista pronasla';
    protected const pretraga = 'pretraga je pronasla podudaranja';



    public static $reci = null;

    public static $nedozvoljeno = null;
    public static $prazno = null;
    public static $pretraga_sajta= null;

    public function prikaz_rezultata(){

        $request = new Request();
        $baza = new Pretraga($this->db);
        $config = Kontejner_jezik::daj_config();
        $jezik = Kontejner_jezik::daj_jezik();

        $recnik = self::$reci;
        $recnik += array('pretraga_sajta' => self::$pretraga_sajta);

        if(!$request->getParams()->has('pretraga')){
            Logger_main::log_warning(self::nema_parametra_pretraga);
            Header('Location: /');
        }
        $kriterijum = $request->getParams()->get('pretraga');
        if(is_null($kriterijum) || strlen($kriterijum) < 4){
            Logger_main::log_warning(self::pretraga_kratka);
            $recnik += array('nedozvoljeno' => self::$nedozvoljeno);
        }else{
            $rezultat = $baza->pretrazi_stranice($request->getParams()->get('pretraga'));
            $reci_prevedene = array();
    
            if(empty($rezultat)){
                Logger_main::log_info(self::prazna_pretraga);
                $recnik += array('prazno' => self::$prazno);
            }else{
                Logger_main::log_info(self::pretraga);
                foreach($rezultat as $item){
                    $reci_prevedene[] = array('naslov' => $config->get_app_data('pretraga')["$jezik"][$item['naslov']], 'link' => $item['link']);
                }
                $recnik += array('rezultat' => $reci_prevedene);
            }
        }
        
        if($request->getSession()->has('user')){
            $recnik += array('ulogovan' => 'ulogovan');
            return $this->render('pretrazivanje.twig', $recnik);
        }

        if($request->getSession()->has('admin')){
            $recnik += array('admin' => 'admin');

            return $this->render('pretrazivanje.twig', $recnik);
        }
        
        return $this->render('pretrazivanje.twig', $recnik);
    }
}

$jezik = Kontejner_jezik::daj_jezik();
$config = Kontejner_jezik::daj_config();

pretragaController::$reci = Kontejner_jezik::daj_opste_reci();
pretragaController::$nedozvoljeno = $config->get_app_data('pretraga')["$jezik"]['nedozvoljeno'];
pretragaController::$prazno = $config->get_app_data('pretraga')["$jezik"]['prazno'];
pretragaController::$pretraga_sajta = $config->get_app_data('pretraga')["$jezik"]['pretraga_sajta'];



