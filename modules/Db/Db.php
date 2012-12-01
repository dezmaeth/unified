<?php
/* 
 * Author: Francisco Javier Henseleit Palma
 * Company: MCAST
 * Class Db Mysql Driver Nashira Framework
 */
class Db extends Main {
    public $key;
    function __construct() {
    }

    public function conectara_bd() {
        $con = mysql_connect(
        CONFIG_DB_SERVER,
        CONFIG_DB_SERVER_USERNAME,
        CONFIG_DB_SERVER_PASSWORD
        ) or $this->error("DB","COULD NOT CONNECT TO DATABASE");
        mysql_set_charset('utf8',$con);
        mysql_query("SET character_set_results = 'utf8', character_set_client = 'utf8', character_set_connection = 'utf8', character_set_database = 'utf8', character_set_server = 'utf8'", $con); 
        mysql_select_db(CONFIG_DB_DATABASE,$con) or $this->error("DB","DATABASE NOT FOUND");
        return $con;
    }

    public function desconectar_db()
    {
        mysql_close();
    }

    public function fetch_array($array)   
    {
        return mysql_fetch_array($array);
    }

    public function fetch_assoc($array) 
    {
      return mysql_fetch_assoc($array);   
    }

    public function query($query) 
    {
        return mysql_query($query);   
    }

    public function escape_string($string) 
    {
        return mysql_escape_string($string);
    }

    public function insert_id() {
        return mysql_insert_id();   
    }

    public function close() 
    {
        return mysql_close();  
    }

    private function convert_to_array($mysql_array) {
        $array = array();
        while ($row = mysql_fetch_array($mysql_array)) {
            $array[] = $row;
        }
        return $array;
    }
    private function convert_to_assoc_array($mysql_array) {
        $array = array();
        while ($row = mysql_fetch_assoc($mysql_array)) {
            $array[$row["id"]] = $row;
        }
        return $array;
    }

    public function connect() 
    {
        return mysql_connect(CONFIG_DB_SERVER,CONFIG_DB_SERVER_USERNAME,CONFIG_DB_SERVER_PASSWORD) or $this->error("DB","COULD NOT CONNECT TO DATABASE");
    }

    public function select_db($CON) 
    {
        return mysql_select_db(CONFIG_DB_SERVER,$CON);
    }

    public function errno() {
        return mysql_errno();
    }

    public function Update($querry) {
            
            $this->conectara_bd();
            $a = $this->query($querry) or $this->errno();
            $this->desconectar_db();
            
            return $a;
    }


    public function queryDb($querry,$assoc= false) {
            $this->conectara_bd();
            $a = $this->query($querry) or $this->error("DB","COULD NOT EXECUTE QUERY code.".$this->errno()."\n ".mysql_error());
            $this->desconectar_db();
            
            if ($assoc) $a = $this->convert_to_assoc_array($a);

            return $a;
    }

    public function insert($query) {
         $this->conectara_bd();
         $a = $this->query($query) or $this->error("DB","COULD NOT EXECUTE INSERT QUERY code.".mysql_errno()."\n ".mysql_error());
         $id = $this->insert_id();
         $this->desconectar_db();
         return  $id;
    }

	public function exeQ($querry,$json=false) {

        try {
            $this->conectara_bd();
            $a = $this->query($querry) or $this->error("DB","COULD NOT EXECUTE QUERY code.".$this->errno()."\n ".mysql_error());
            $this->desconectar_db();
            if ($json)
                return json_encode($this->convert_to_array($a));
            else
                return $this->convert_to_array($a);
        } catch( Exception $e ) { 
            echo( $e->getMessage());
            return false;
        }    
    }
}
?>