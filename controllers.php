<?php

Class Controllers extends Main {
	protected $cList,$locals;

	function __construct($req) {
		$this->getControllers();
		$this->parseQuery($req);
	}

	private function getControllers() {
        if (!file_exists(CONFIG_CONTROLLERS_JSON)) $this->error("Controllers","Controllers JSON file not found");
    		$json = file_get_contents(CONFIG_CONTROLLERS_JSON);
            $this-> cList = json_decode($json);
	}

	private function parseQuery($req) {
        $found = false;
        /// if pattern not sent, asume root ///
        if (!array_key_exists('pattern', $req)) $req['pattern'] = "/default"; 
        
        $pattern = $req['pattern'];
        $parameters_clean = explode("/", $req['pattern']);
        $parameters = $parameters_clean;
        $controller = $this->searchController($pattern);
        if ($controller) $found = true;
        else $found = false;

        /*************************************************************************
            Descend each level of the request until 
            controller pattern is found or send a 404
        **************************************************************************/

        while(!$controller) {
            $param = array_pop($parameters);
            $pattern = implode("/", $parameters);
            $controller = $this->searchController($pattern);
            if ($controller) $found = true;
            if (count($parameters) <= 0)  { $controller = true; $found=false; }
        }
        
        if ($found) {
            $this-> callController($controller,$req,$parameters_clean); $found = true; 
        } else {
            $this->__autoload("Template");
            print $this->Template->render("404",'{ "URL" : "'.$req["pattern"].'" }');
        }
    }

    private function callController($controller,$req,$parameters=null) {
        parse_str(file_get_contents("php://input"),$data);

        $path_to_file = CONFIG_CONTROLLERS_PATH . $this-> cList-> $controller -> run;
        if (!file_exists($path_to_file)) $this->error("Controllers", "Controller file not found");
        require $path_to_file;
        if (class_exists('Controller')) {
            $controller = new Controller();
        } else { 
            $this->error("Controllers","Controller not found in controller file");
        }
        if ($_SERVER['REQUEST_METHOD'] == "PUT" || $_SERVER['REQUEST_METHOD'] == "DELETE")
            $controller-> $_SERVER['REQUEST_METHOD'] ($data,$parameters);
        else
            $controller-> $_SERVER['REQUEST_METHOD'] ($req,$parameters);
    }

    private function searchController($pattern) {
        $found = false;
        foreach ($this -> cList as $key => $controller) {
            if (is_array($controller->pattern)) { 
                foreach ($controller->pattern as $contPat) {
                    if ($pattern == $contPat) {
                        $found = $key;
                    }
                }
            } else if ($pattern == $controller->pattern) {
                $found = $key;
            }
        }
        return $found;
    }
}
?>