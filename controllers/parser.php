<?php


Class Controller extends Controllers {

	function __construct() {
		header("Content-Type: application/json");
	}

	function GET($r,$p) {
		echo '{"data": [ 0: 1] }';
	}
}
?>
