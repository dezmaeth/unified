<?php
/* 
 * Author: Francisco Javier Henseleit Palma
 * Company: MCAST
 * Configuration Class uSell Platform
 */
class Config extends Main {
  public $key, $CONTROLLERS_JSON,$CONTROLLERS_PATH,$VIEWS_PATH,$VIEWS_JSON,$USELL_PATH, $DB_SERVER,$DB_SERVER_USERNAME,$DB_SERVER_PASSWORD, $DB_DATABASE,
         $PPATH, $IMG_PATH, $DEBUG;
      
  function __construct() {
      $this->_setConfig();
  }

  private function _setConfig() {
    if (!file_exists(dirname ( __FILE__ ) ."/config.ini")) $this->error("Config","Config file not found");
    
    $config = parse_ini_file(dirname ( __FILE__ ) ."/config.ini");
    foreach ($config as $key => $value) {
      $this->$key = $value;
    }

    if (!defined('VIEWS_PATH')) define('VIEWS_PATH', $this->VIEWS_PATH);
    if (!defined('IMG_PATH')) define('IMG_PATH', $this->IMG_PATH);
    if (!defined('USELL_PATH')) define('USELL_PATH', $this->USELL_PATH);
    if (!defined('PATH')) define('PATH', "//".$_SERVER["SERVER_NAME"]."/");
  }
}
?>