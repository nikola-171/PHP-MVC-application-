<?php

namespace app\Controllers;

use app\Core\Config;
use app\Core\Request;
use app\Models\Loby;
use app\Core\Logger_main;
use app\Core\Kontejner_jezik;


class welcomeController extends AbstractController{
	public static $ULOGOVAN = 'ulogovan';
	public static $ADMIN = 'admin';
	protected const NOVI_KORISNIK = 'neulogovan korisnik pristupa stranici loby';
	protected const FIZICKO_LICE = 'fizicko lice pristupa stranici loby'; 
	protected const PREDUZECE = 'preduzece pristupa stranici loby'; 
	protected const ADMINISTRATOR = 'admin pristupa stranici loby'; 

	/*sve reči uključene u ovoj stranici */
	public static $naslov = null;
	public static $sadrzaj = null;
	public static $pocetna = null;
	public static $jezik = null;
	public static $opste_reci = null;
	public static $admin_pocetna = null;
	public static $jezik_strana = null;
	public static $promeni = null;
	public static $preduzeca = null;
	public static $najava = null;
	public static $info = null;
	public static $stranica = null;
	public static $naslovStrana = null;
	public static $citat = null;
	public static $autor = null;
	public static $uloguj = null;
	public static $logovanje_admin = null;
	public static $stavka_1 = null;
	public static $stavka_2 = null;
	public static $info_stavka_1 = null;
	public static $info_stavka_2 = null;
	public static $info_stavka_3 = null;
	public static $sta = null;
	public static $cilj = null;
	public static $primer = null;



	/*ovde menjamo jezik aplikaciji */
	public function promeniJezik(){
		$request = new Request();
		$jezik = $request->getParams()->get('jezik');
		$request->setSession('jezik',$jezik);
		Header('Location: /');
	}
	
	/*ovo vraća vektor reči */
	public static function daj_reci(){
		$reci = self::$opste_reci;
		$reci += array('logovanje_admin' => self::$logovanje_admin,
		 'uloguj' => self::$uloguj,'citat' => self::$citat,
		 'autor'=> self::$autor,'pocetna'=> self::$pocetna,
		 'naslov' => self::$naslov, 'sadrzaj' => self::$sadrzaj,
		 'jezik' => self::$jezik, 'promeni' => self::$promeni,
		 'preduzeca' => self::$preduzeca, 'najava' => self::$najava,
		 'info' => self::$info, 'stranica' => self::$stranica,
		 'naslovStrana' => self::$naslovStrana,'logovanje_admin' => self::$logovanje_admin,
		 'admin_pocetna' => self::$admin_pocetna, 'stavka_1' => self::$stavka_1,
		 'stavka_2' => self::$stavka_2,
		 'info_stavka_1' => self::$info_stavka_1,
		 'info_stavka_2' => self::$info_stavka_2,
		 'info_stavka_3' => self::$info_stavka_3,
		 'sta' => self::$sta,
		 'cilj' => self::$cilj,
		 'primer' => self::$primer

		);

		 return $reci;
	}

	public function showAll(){
		$request = new Request();
		$reci = self::daj_reci();

		if($request->getSession()->has('user')){
			if($request->getSession()->get('user') == 'user'){
				Logger_main::log_info(self::FIZICKO_LICE);
			}else{
				Logger_main::log_info(self::PREDUZECE);

			}
			$reci += array(self::$ULOGOVAN => self::$ULOGOVAN);
			return $this->render('loby.twig', $reci); 
		}

		if($request->getSession()->has('admin')){
			Logger_main::log_info(self::ADMINISTRATOR);
			$reci += array(self::$ADMIN => self::$ADMIN);

			return $this->render('loby.twig', $reci); 
		}
		Logger_main::log_info(self::NOVI_KORISNIK);
		return $this->render('loby.twig', $reci);  
	}
	
}

$jezik = Kontejner_jezik::daj_jezik();
$config = Kontejner_jezik::daj_config();

welcomeController::$jezik = $jezik;

welcomeController::$naslov = $config->get_app_data('welcome')["$jezik"]['naslov'];
welcomeController::$sadrzaj = $config->get_app_data('welcome')["$jezik"]['sadrzaj'];
welcomeController::$pocetna = $config->get_app_data('welcome')["$jezik"]['pocetna'];
welcomeController::$jezik_strana = $config->get_app_data('welcome')["$jezik"]['jezik'];
welcomeController::$promeni = $config->get_app_data('welcome')["$jezik"]['promeni'];
welcomeController::$preduzeca = $config->get_app_data('welcome')["$jezik"]['preduzeca'];
welcomeController::$najava = $config->get_app_data('welcome')["$jezik"]['najava'];
welcomeController::$info = $config->get_app_data('welcome')["$jezik"]['info'];
welcomeController::$stranica = $config->get_app_data('welcome')["$jezik"]['stranica'];
welcomeController::$naslovStrana = $config->get_app_data('welcome')["$jezik"]['naslovStrana'];
welcomeController::$citat = $config->get_app_data('welcome')["$jezik"]['citat'];
welcomeController::$autor = $config->get_app_data('welcome')["$jezik"]['autor'];
welcomeController::$uloguj = $config->get_app_data('welcome')["$jezik"]['uloguj'];
welcomeController::$logovanje_admin = $config->get_app_data('welcome')["$jezik"]['logovanje_admin'];
welcomeController::$admin_pocetna = $config->get_app_data('welcome')["$jezik"]['admin_pocetna'];
welcomeController::$stavka_1 = $config->get_app_data('welcome')["$jezik"]['stavka_1'];
welcomeController::$stavka_2 = $config->get_app_data('welcome')["$jezik"]['stavka_2'];

welcomeController::$info_stavka_1 = $config->get_app_data('welcome')["$jezik"]['info_stavka_1'];
welcomeController::$info_stavka_2 = $config->get_app_data('welcome')["$jezik"]['info_stavka_2'];
welcomeController::$info_stavka_3 = $config->get_app_data('welcome')["$jezik"]['info_stavka_3'];
welcomeController::$sta = $config->get_app_data('welcome')["$jezik"]['sta'];
welcomeController::$cilj = $config->get_app_data('welcome')["$jezik"]['cilj'];
welcomeController::$primer = $config->get_app_data('welcome')["$jezik"]['primer'];


welcomeController::$opste_reci = Kontejner_jezik::daj_opste_reci();





