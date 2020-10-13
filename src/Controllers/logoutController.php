<?php

namespace app\Controllers;

use app\Core\Request;
use app\Core\Logger_main;

class logoutController extends AbstractController{

    protected const logout = 'korisnik se izlogovao';
    protected const logout_preduzece = 'preduzece se izlogovalo';

    public function logout(){
        $request = new Request();
        $jezik = 'srp';
        if($request->getSession()->has('jezik')){
			if($request->getSession()->get('jezik') != ''){
				$jezik = $request->getSession()->get('jezik');
			}
        }
        if($request->getSession()->get('auth') == 'company'){
            Logger_main::log_info(self::logout_preduzece.' '.$request->getSession()->get('user'));
        }else{
            Logger_main::log_info(self::logout.' '.$request->getSession()->get('user'));
        }
        //session_destroy();
        $_SESSION = array();
        $request = new Request();
        $request->setSession('jezik', $jezik);

        Header('Location: /');
    }
}