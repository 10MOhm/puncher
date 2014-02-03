<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class NavItems {
	
	public static function LoggedIn() {
	    return array(
    		'/' => 'Pointeuse',
            '/account' => 'Compte',
            '/info' => 'Informations',
            '/auth/logout/' => 'Déconnexion'
    	);
	}
	public static function NotLoggedIn() {
	    return array(
    		'/auth/login' => 'Connexion',
            '/info' => 'Informations'
    	);
	}
}
