<?php


Class Controller extends Controllers {

	function __construct() {
		header("Content-Type: application/json");
	}

	function GET($r,$p) {
		$this->__autoload("Db");
		$list = $this->Db->exeQ("SELECT * FROM LISTADO_CONSULTORIOS");
		
		print json_encode($list);

	}
}
?>
