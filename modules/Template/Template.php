<?php
/* 
 * Author: Francisco Javier Henseleit Palma
 * Company: MCAST
 * Class Template Nashira Framework
 */

class Template extends Main {
	public $views, $layout,$views_path,$html = array();
    private $static_locals,$locals_id,$buffer;
    function __construct($locals_id=null) {
        $this->locals_id = $locals_id;
        $this->loadViews();
    }

    private function loadViews() {
        if (file_exists(CONFIG_VIEWS_JSON)) {
            $json = file_get_contents(CONFIG_VIEWS_JSON);
            $this->views = json_decode($json);
            if (is_null($this->views)) {
                $this->error("Template","Views Config File is invalid");   
            }
        } else $this->error("Template","Views Config File not Found");
    }

    public function getLayout($layout) {
        if (property_exists($this->views, $layout)) {
            $this-> layout = $this-> views -> $layout;
            $this-> layout = file_get_contents(CONFIG_VIEWS_PATH
                                                .$this-> layout-> path
                                                .$this-> layout-> run);
        } else 
        $this->error("Template","Layout not found"); 
    }

    public function render($view_name,$locals=false) {        
            if (property_exists($this->views,$view_name)) {
                $view = $this->views->$view_name;
                $view_file = CONFIG_VIEWS_PATH . $view->path . $view->run;
                if (file_exists($view_file)) 
                    $view = file_get_contents( $view_file );
                else $this->error("Template","View '$view_name' run file not found"); 
            } else {
                $this->error("Template","View '$view_name' not declared"); 
            }
            
            if (!is_null($this->layout)) {
                $reply = preg_replace("/{BODY}/", $view, $this->layout);
            } else {
                $reply = $view; 
            }
            
            if ($locals && !is_array($locals))
                 $locals = json_decode($locals,true);

            if (CONFIG_LOAD_STATIC_LOCALS && !is_null($this->locals_id)) {
                $this->__autoload("Local");
                $locals = array_merge($locals,$this->Local->values[$this->locals_id]); 
                $locals["locals_id"] =  $this->locals_id;
            }
            
            if (!is_bool($locals)) {

                if (!is_null($locals)) {

                    ob_start();
                    eval(' ?>'.$reply.'<?php ');
                    $reply = ob_get_contents();
                    ob_end_clean();

                    $reply = $this->foreach_replace($reply,$locals);
                    $reply = $this->if_replace($reply,$locals);
                
                    foreach($locals as $key => $val) {
                        if (!is_array($val))
                        $reply = preg_replace("/{".$key."}/", $val, $reply);
                    }
                } else {
                    $this->error("Template","Invalid locals sent to view ".$view_name);
                }

            }
        // path variable replacement 
        $reply = preg_replace("/{path}/", "//".$_SERVER["SERVER_NAME"]."/", $reply);
        if (CONFIG_MINIFY_OUTPUT) {
            $reply = $this->sanitize_output($reply);
        }
        
        return $reply;
    }

    private function foreach_replace($html,$locals) {
        preg_match_all("/((\{foreach:.*\})(?s:.*?)\{foreach:end\})/", $html, $matches);
        
        $tags = $matches[2];
        foreach ($matches[0] as $i => $match) {
            $match_clean = str_replace($tags[$i],'',$match);
            $match_clean = str_replace("{foreach:end}",'',$match_clean);
            $tag = preg_replace("/\{foreach:/", '', $tags[$i]);
            $tag = preg_replace("/\}/", '', $tag);
            $html = str_replace($match, $this->foreach_execute($match,$match_clean,$tag, $locals), $html);
        }
        return $html;
    }

    private function foreach_execute($match, $match_clean, $tag ,$locals) {
        $reply = ""; $match_replaced = $match_clean;
        if (array_key_exists($tag, $locals) && is_array($locals[$tag])) {
            $local = array_values($locals[$tag]);
            foreach ($local as $key => $local_child) {
                foreach ($local_child as $key => $value) {
                    $match_replaced = preg_replace("/\{".$key."\}/", $value, $match_replaced);     
                }
                $reply .= $match_replaced;
                $match_replaced = $match_clean;
            };
        }
        return $reply;
    }

    private function if_replace($html,$replacement) {
        preg_match_all("/(\{if:(.*)\}(.*)\{if:end\})/msU", $html, $matches);
        
        $data = $matches[1];
        $conditions = $matches[2];
        $toReplace = $matches[3];
        foreach($conditions as $i => $cond) {
            $html = str_replace($data[$i],$this->if_execute($data[$i],$cond,$toReplace[$i],$replacement), $html);
        }
       return $html;
    }

    private function if_execute($match, $cond,$toReplace,$locals) {
        $match_clean = $match;
        $cond_clean = $cond;
        preg_match("/\((.*)\)/", $cond,$var);
        $variable = $var[1];
        
        foreach($locals as $key => $value) {
            if ($variable == $key) {
                $cond_final = str_replace("(".$key.")", $value, $cond);
                if (eval("return $cond_final;")) {

                   return $toReplace;
                }
            }
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

    private function sanitize_output($buffer)
    {
        //javascript closing tags
        $buffer = preg_replace('/(\}\n)/msU', '};', $buffer);

        parent::__autoload("Packer");
        preg_match_all("/(\{JS_PACKER:START\}(.*)\{JS_PACKER:END\})/msU", $buffer, $JS_TAGS);
        /*var_dump($JS_TAGS[2]);
        //print $buffer;
        die();*/

        $buffer = preg_replace('/(?<!\S)\/\/\s*[^\r\n]*/', '', $buffer);

        // Newlines and trailing spaces
        $buffer = preg_replace('/\n\s*\n/', "\n", $buffer);
        
        // multiline comments
        $buffer = preg_replace('/(\/\*(.*)\*\/)/msU', '', $buffer);

        $buffer = preg_replace(array('/ {2,}/','/<!--.*?-->|\t|(?:\r?\n[ \t]*)+/s'),array(' ',''),$buffer);

        return $buffer;
    }
}
?>