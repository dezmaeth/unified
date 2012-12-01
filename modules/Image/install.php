<?php
// Database Local
$querys = array();
$querys[0] = 'CREATE TABLE IF NOT EXISTS`images` (
`ID` VARCHAR( 32 ) NOT NULL ,
`NAME` VARCHAR( 32 ) NOT NULL ,
`TYPE` VARCHAR( 20 ) NOT NULL ,
`REAL_NAME` VARCHAR( 32 ) NOT NULL ,
`DATE` DATE NOT NULL
) ENGINE = INNODB;';


foreach($querys as $q) {
	$this->Db->Update($q);
}

//image path index modification

$dir = $this->Db->Config->IMG_PATH;
$f = fopen($dir."index.php","w+");
$data = file_get_contents(dirname(__FILE__)."/driver.txt");
fwrite($f, $data);
fclose($f);
return true;
?>