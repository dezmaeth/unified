<?php
/* 
 *Author: Francisco Javier Henseleit Palma
 * Company: MCAST
 * Auth Manager Nashira Framework
 */
class Auth extends Main {
	public $request,$cookie,$username,$access_lv;
	public function __construct($cookie="Auth") 
	{
		parent::__autoload('Db');
		$this->cookie = $cookie;
	}

	public function login($username,$password) {
		$this->__autoload("Db");
		$res = $this->Db->exeQ("SELECT * from auth 
								WHERE username = '".$username."' 
								AND password='".$this->encryptString(addslashes($password))."'");
		if (count($res) >0)	{
			session_start();
			setcookie("Auth",session_id(), time() + 3600 * 24);
			return true;
		} else {
			return false; 
		}
	}


	public function checkSession() {
	    if (array_key_exists("Auth", $_COOKIE)) {
		    session_id($_COOKIE["Auth"]);
		    session_start();
		    return 1;
	    } else 
    		return 0;
  	}

	public function logout(){
		session_start();
		setcookie("Auth", "", time() - 3600 * 24);
		session_write_close();
		session_destroy();
		return true;
	}
}

?>