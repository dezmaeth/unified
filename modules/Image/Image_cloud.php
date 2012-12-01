<?php
/* Author: Francisco Javier Henseleit Palma
 * Company: MCAST
 * Cloud Image Manager uSell Module 
 */

class Image extends Main {
	public function __construct() 
	{
		$this->getKey();
	}
	private function getKey() {
		$it = new DirectoryIterator(dirname(__FILE__));
        $this->key = file_get_contents($it->getPath()."/.key");
    }
	
	private function getImage() {
		include "puente.php";
		$pt = new puente();
		echo $pt->ask_selee("selee=image&action=LoadCustom&amp;ID=$id&amp;");		
	}

	public function install() {
		
	}
}
?>