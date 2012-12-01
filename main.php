<?php
/* Author: Francisco Javier Henseleit Palma
 * Company: MCAST
 * Nashira Framework Main Script
 */
require "controllers.php";

class Main {
  private $controllers;
  public $request, $it;

  private function setConfig() {
    if (!file_exists(dirname ( __FILE__ ) ."/config.ini")) $this->error("Config","Config file not found");
    
    $config_ini = parse_ini_file(dirname ( __FILE__ ) ."/config.ini");
    foreach ($config_ini as $key => $value) {
      define("CONFIG_".$key, $value);
    }

    if (!defined('PATH')) define('PATH', "//".$_SERVER["SERVER_NAME"]."/");
  }


  public function __construct() {
    $this->Config = new stdClass();
    $this->setConfig();
  }
	
  public function __autoload($class_name,$params=null) {
    $this-> it = new DirectoryIterator(dirname(__FILE__));
    $filename = $this->it -> getPath()."/modules/$class_name/$class_name.php";
    
    if (file_exists($filename)) {
      require_once $filename;
      $this-> $class_name = new $class_name ($params);
    } else print "ERROR: MODULE NOT FOUND";
  }

  public function checkSession() {
    if (array_key_exists("logged", $_COOKIE)) { 
      session_id($_COOKIE["logged"]);
      session_start();
      return true;
    }
    return false;
  }

  public function escape_string($value)
  {
    $search = array("\\",  "\x00", "\n",  "\r",  "'",  '"', "\x1a");
    $replace = array("\\\\","\\0","\\n", "\\r", "\'", '\"', "\\Z");

    return str_replace($search, $replace, $value);
  }
		
  private function cleanQuery($req) {
    setlocale(LC_ALL, 'es_ES.UTF8');
    $allClean = array();
    foreach ($req as $key => $str) {
  	  $clean = preg_replace("/[^a-zA-Z0-9\/_| -]/", '', $str);
  	  $clean = preg_replace("/[\/_| -]+/", '-', $clean);
  	  $allClean[$key] = $str;
  	}
  	return $req;
  }

  protected function encryptString($string) {
    $this-> it = new DirectoryIterator(dirname(__FILE__));
    $salt = md5(file_get_contents($this->it->getPath()."/.key"));
    $hash = crypt($string,'$6$rounds=5000$'.$salt.'$');
    $hash = explode('$', $hash);
    return end($hash);
  }

  public function error($module,$error) {
    if (CONFIG_DISPLAY_ERRORS) {
      header ("Content-Type: application/json");
      echo '{ "ERROR" : "'.$module.' module says: '.$error.'" }';
    }

    if (CONFIG_LOG_ERRORS) {
      $date = date("Y-m-d H:i:s");
      file_put_contents(CONFIG_ERROR_LOG_FILE,"ERROR ".$date.": ".$module." : ".$error."\n", FILE_APPEND);
    }

    die();
  }


  public function Query($req) {
    $this->request = $this->cleanQuery($req);
    $this->controllers = new Controllers($this->request);
  }
}
?>