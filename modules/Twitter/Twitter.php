<?php
/* 
 * Author: Francisco Javier Henseleit Palma
 * Company: MCAST
 * Class Twitter Nashira Framework
 */
ini_set('allow_url_fopen', 'on');

class Twitter extends Main {
	private 
        $feed_string = "https://api.twitter.com/1/statuses/user_timeline.json?include_entities=true&include_rts=true&count=2&screen_name=",
        $re_tweet_string = "http://api.twitter.com/1/statuses/retweet/",
        $xml,$twitter_config,$twitter_username;

    function __construct() {
        	parent::__autoload('Config');
        	$this->loadConfig();
    }

	private function setUsername($username) {
		$this->twitter_username = $username;
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

    private function getData($url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_REFERER, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

    public function retweet($req) {
        $reply = file_get_contents(
                    $this -> re_tweet_string .
                    $req["id"].
                    ".json");
        var_dump($reply);
    }


    public function getTweets($type=".json") {
        $url = $this -> feed_string . $this-> twitter_config['username'];
        $reply = $this->getData($url);
        return $reply;
    }
}

?>