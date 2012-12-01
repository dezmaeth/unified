<?
/* 
 * Author: Francisco Javier Henseleit Palma
 * Company: MCAST
 * Class Blog Nashira Framework
 */
class Blog extends Main {
    public $key;
	private $posts = array();
    function __construct() {
        parent::__autoload('Db'); 
        $this->getKey();
    }
    private function getKey() {
        $it = new DirectoryIterator(dirname(__FILE__));
        $this->key = file_get_contents($it->getPath()."/.key");
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

    private function _posts($cat) {
        if (!$cat) 
        $q = $this->Db->exeQ("SELECT * FROM  `blog` ORDER BY  `blog`.`DATE` DESC");     
         else
		$q = $this->Db->exeQ("SELECT * FROM  `blog` WHERE CATEGORY = '".$cat."' ORDER BY  `blog`.`DATE` DESC");	
		while ($r = $this->Db->fetch_array($q)) {
			$this->posts[]= $r;
		} return $this->posts;
    }

    public function jsonPosts($cat) {
        if (is_numeric($cat))
    	return json_encode($this->getHTML($this -> _posts($cat)));
        else json_encode($this->getHTML($this -> _posts(false)));
    }

    public function getPosts($cat) {
        if (is_numeric($cat))
       return $this->getHTML($this -> _posts($cat));
        else $this->getHTML($this -> _posts(false));
    }
    
    private function getHTML($arr) {
        $clean = array();
        for($i=0;$i<count($arr);$i++){ 
            $clean[$i]['BODY'] = base64_decode($arr[$i]['BODY']);
            $clean[$i]['TITLE'] = base64_decode($arr[$i]['TITLE']);
            $clean[$i]['pURL'] = $arr[$i]['pURL'];
            $clean[$i]['ID'] = $arr[$i]['ID'];
            $clean[$i]['DATE'] = $arr[$i]['DATE'];
            $clean[$i]['IMG'] = $arr[$i]['IMG'];
        }
        return $clean;
    }

    private function cleanQuery($arr) {
        for($i=0;$i<count($arr);$i++){ 
             $arr['BODY'] = base64_encode($arr['BODY']);
             $arr['TITLE'] = base64_encode($arr['TITLE']);
        }
        return $arr;
    }

    private function newPost($post) {
        $date = date("Y-m-d");
        $q = $this->Db->exeQ("INSERT INTO  `blog` (`CATEGORY`, `LANG`,`DATE` ,`pURL` ,`TITLE` ,`BODY` ,`IMG`)
                              VALUES ('".$post["CATEGORY"]."','".$post["LANG"]."','".$date."',  '".$post['pURL']."',  '".base64_encode(urldecode($post['TITLE']))."','".base64_encode(urldecode($post['BODY']))."', '".trim($post['IMG'])."')");
        return $q;
    }

    private function delPost($post) {
      if (is_numeric($post)) {
        $q = $this->Db->exeQ("SELECT  `blog`.`IMG` ,  `images`.`NAME` 
            FROM  `blog` 
            INNER JOIN  `images` ON  `blog`.`IMG` =  `images`.`ID` 
            WHERE  `blog`.`ID` ='".$post."'");
        $img = mysql_fetch_array($q);
         if (!is_null($img['IMG'])) {
          unlink($path = $this->Db->Config->get('IMG_PATH').$img['NAME']);
          $q = $this->Db->exeQ("DELETE FROM `images` WHERE `images`.`ID` ='".$img['IMG']."'");
         }
        $q = $this->Db->exeQ("DELETE FROM `blog` WHERE `blog`.`ID` ='".$post."'");
        return $q;
      }
    }

    private function editPost($post) {
        $q = $this->Db->exeQ("UPDATE `blog` SET `pURL` = '".$post['pURL']."',`BODY` = '".base64_encode(urldecode($post['BODY']))."', `TITLE` = '".base64_encode(urldecode($post['TITLE']))."', `IMG` = '".trim($post['IMG'])."' WHERE  `blog`.`ID` ='".$post['ID']."'");

        return $q;
    }

    public function install() {
        include ('install.php');
    }

    private function savePost($post) {
        $post = $this->cleanQuery($post);
        $q = $this->Db->exeQ("INSERT INTO `blog` (`DATE` ,`pURL` ,`TITLE` ,`BODY` ,`IMG`) VALUES 
                              ('".date()."',  '".$post['pURL']."','".$post['TITLE']."', '".$post['BODY']."')");
    }
}
?>