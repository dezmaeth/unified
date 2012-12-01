<?php
/* 
 *Author: Francisco Javier Henseleit Palma
 * Company: MCAST
 * Local Image Manager Module Internal Storage Nashira Framework
 */
class Image extends Main {
	
	public $request;
	public function __construct() 
	{
		
		parent::__autoload('Config');
		parent::__autoload('Db');
	}
	
	public function getImage($id) {
		$path = $this->Config->get('IMG_PATH');
		$query = $this->Db->queryDb("SELECT * FROM `images` WHERE `ID` = '".$id."'");
		$img = $this->Db->fetch_array($query);
		if (file_exists($path.$img['NAME'])) {
			return readfile($path.$img['NAME']);
		} else {
		 die("imagen no existe");
		}
	}

	public function getMIME($file) {
		$file = explode(".", $file);
		$ext = end($file);
		switch ($ext) {
			case 'jpg':
				return "image/jpg";
			break;

			case 'png':
				return "image/png";
			break;

			case 'gif':
				return "image/gif";
			break;
			
			default:
				return "text/plain";
				break;
		}
	}

	public function getImageList() {
		$arr = array();
		$q = $this->Db->queryDb("SELECT ID,REAL_NAME FROM `images`");
		while($img = $this->Db->fetch_array($q)) {
			$arr[] = array("ID"=>$img['ID'],"NAME"=>$img['REAL_NAME']);
		}
		return $arr;
	}

	public function getJsonImageList () {
		$arr = array();
		$q = $this->Db->queryDb("SELECT ID,REAL_NAME FROM `images`");
		while($img = $this->Db->fetch_array($q)) {
			$arr[] = array("ID"=>$img['ID'],"NAME"=>$img['REAL_NAME']);
		}
		return json_encode($arr,true);	
	}

	public function displayImage($id) {
		$path = $this->Config->IMG_PATH;
		$query = $this->Db->queryDb("SELECT * FROM `images` WHERE `ID` = '".$id."'");
		$img = $this->Db->fetch_array($query);
		if (file_exists($path.$img['NAME']) && $img['NAME']) {
			header('Content-Type: '.$img['TYPE']);
			ob_clean();
			flush();
			readfile($path.$img['NAME']);
		} else {
			header('Content-Type: image/gif');
			ob_clean();
			flush();
			$it = new DirectoryIterator(dirname(__FILE__));
			readfile($it->getPath().'/wrong.gif');
		}
	}

	protected function parseQuery($req) {
        if (is_array($req)) {
        $this->request = $req;
        $act = array_keys($this->request);
        return $this->{$act[0]}($this->request{$act[0]});
        } else {
            return $this->$req();
        }
    }

    public function saveImage($url) {
    	$img_path = $this->Config->IMG_PATH;
    	$image_data = file_get_contents($url);
    	$md5 = md5(uniqid(" ",true).$url);
    	$file_name = md5(uniqid()."_".$url);
    	$target_path = $img_path.$file_name;
    	$q = $this->Db->queryDb("INSERT INTO  `images` (`ID` ,`TYPE` ,`NAME`,`REAL_NAME` ,`DATE`) 
							 VALUES ('".$md5."',  '". $this->getMIME($url) ."',  '".$file_name."', '".$url."', '".date("Y-m-d")."');");	
		if ($q) {
			file_put_contents($target_path, $image_data);
			return $md5; 
		} else {
			// save to database failed
			return false;
			unlink($target_path);
		}
    }

    public function deleteImage($id) {
    	/* remove image */
    	
    }

	public function uploadImage() {
		$target_path = $this->Config->IMG_PATH;
		$name = basename($_FILES['Filedata']['name']);
		$type = $_FILES['Filedata']['type'];
		$md5 = md5(uniqid(" ",true).$name);
    	$fName = md5(uniqid()."_".$name);
		$target_path = $target_path .$fName;
		if(move_uploaded_file($_FILES['Filedata']['tmp_name'], $target_path)) {
			$q = $this->Db->queryDb("INSERT INTO  `images` (`ID` ,`TYPE` ,`NAME`,`REAL_NAME` ,`DATE`) 
							 VALUES ('".$md5."',  '".$type."',  '".$fName."', '".$name."', '".date("Y-m-d")."');");	
			if ($q) return $md5; else {
				// save to database failed
				return false; unlink($target_path);
			}
		} else {
			//file save failed
			return false;
		}
	}

	public function install() {
        include ('install.php');
    }
}
?>