<?php
Class Controller extends Main {
	function __construct() {

	}

	function GET() {
		$this->__autoload("Template");
		$this->Template->getLayout("layout");
		print $this->Template->render("home");
	}
}
?>