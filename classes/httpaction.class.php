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
	function getLink() {
		$ret = $this->url;
		if(count($this->params) > 0)
			$ret .= '?';
		$ret .= implode('&', $this->params);
		return $ret;
	}
	function wasCalled() {
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
			array_push($params, $val);
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
