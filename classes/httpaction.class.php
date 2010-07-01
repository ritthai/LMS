<?php
class HttpAction {
	public $url, $method, $params, $perms;
	private $ACCEPTED_METHODS = array('get', 'post');
	function __construct($url, $method, $params, $perms='any') {
		$method = strtolower($method);
		if(!in_array($method, $this->ACCEPTED_METHODS)) {
			Error::generate('fatal', 'Invalid HTTP method.');
			return;
		}
		$this->url = $url;
		$this->method = $method;
		$this->params = $params;
		$this->perms = $perms;
	}
	function getLink($params=array()) {
		$ret = $this->url;
		if(count($params) > 0)
			$ret .= '?';
		foreach($params as $k=>$v)
			$params[$k] = "$k=$v";
		$ret .= implode('&', $params);
		return $ret;
	}
	function wasCalled() {
		//echo $cur_uri."<br/>";
		//echo $req_uri."<br/>";
		$req_uri = $_SERVER['REQUEST_URI'];
		$qpos = strrpos($req_uri, '?');
		if($qpos) $req_uri = substr($req_uri, 0, $qpos);
		$cur_uri = $this->getLink();
		$req_uri = rtrim($req_uri, " /\r\n");
		$cur_uri = rtrim($cur_uri, " /\r\n");
		$req_uri = ltrim($req_uri, " /\r\n");
		$cur_uri = ltrim($cur_uri, " /\r\n");
		$req_uri = explode('/', $req_uri);
		$cur_uri = explode('/', $cur_uri);
		if(count($req_uri) > count($cur_uri))
			$req_uri = array_slice($req_uri, count($cur_uri));
		else if(count($req_uri) < count($cur_uri))
			$cur_uri = array_slice($cur_uri, count($req_uri));
		$req_uri = implode('/', $req_uri);
		$cur_uri = implode('/', $cur_uri);
		//echo $req_uri."<br/>";
		if($req_uri != $cur_uri) return false;
		//echo $req_uri."-<br/>";
		
		$parr = strcmp($this->method,'get')==0 ? $_GET : $_POST;
		$params = array();
		foreach($parr as $key=>$val)
			array_push($params, $key);
		if(count($params) == 0) return false;
		return count(array_intersect($params, $this->params))==count($this->params);
	}
	function checkPerms($perms) {
		if($this->perms == 'any') return true;
		return in_array($perms, $this->perms);
	}
	function getParams() {
		$parr = strcmp($this->method,'get')==0 ? $_GET : $_POST;
		$params = array();
		foreach($parr as $key=>$val)
			$params[$key] = $val;
		return $params;
	}
	function FORM_BEGIN() {
		echo "<form action=\"$this->url\" method=\"$this->method\">";
	}
	function FORM_END() {
		echo "</form>";
	}
}
?>
