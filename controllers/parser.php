<?php


Class Controller extends Controllers {

	function __construct() {
		
	}

	function GET($r,$p) {

		if (isset($p[2])) {
			$this->__autoload("Db");
                	$list = (object) $this->Db->exeQ("SELECT * FROM `LISTADO_CONSULTORIOS`");

			switch($p[2]) {

			case "xml":
				//header("Content-type: text/xml");
				$xml = new SimpleXMLElement('<root/>');
				array_walk_recursive($list, array ($xml, 'addChild'));
				print $xml->asXML();

				//print $this->array2xml($list);
			break;

			case "dump":
				header("Content-Type: text/plain");
				print_r($list);
			break;

			case "json":
				header("Content-type: application/json");
				print json_encode($list);
			break;
			}			


		}
		
	}

	function POST($r,$p) {
		

	}
}
?>
