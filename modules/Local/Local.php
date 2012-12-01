<?php
/* 
 * Author: Francisco Javier Henseleit Palma
 * Company: MCAST
 * Class Template Nashira Framework
 */

class Local extends Main {
	public $values;
	function __construct() {
    	parent::__autoload('Config');
    	$this->getLocals();
    }

    private function getLocals() {
    	switch ($this->Config->STATIC_LOCALS_TYPE) {
    		case 'xml':
    			$this->getFromXML();
    			break;
    		case 'db':
    			$this->getFromDb();
    			break;
    	}
    	
    }

    private function getFromXML() {
    	$this->values = json_decode(json_encode(simplexml_load_file($_SERVER['DOCUMENT_ROOT'].$this->Config->STATIC_LOCALS)),1);
    }

    private function getFromDb() {
    	$this->__autoload("Db");
    	$this->values = $this->Db->queryDb("SELECT * FROM ".$this->Config->STATIC_LOCALS,true);
    }

}
?>