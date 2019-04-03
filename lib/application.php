<?php 

namespace lib;

class Application {

protected 	$routes  = [];
protected 	$matches = null;
protected   $vars = [];

 
public function addGet($url, $callback, $isSecure = false){
		$this->routes[] = [
				'url' => $url,
				'callback' => $callback,
				'isSecure' => $isSecure,
				'method' => 'GET',
		];
	}

public	function addPOST($url, $callback, $isSecure = false){
		$this->routes[] = [
				'url' => $url,
				'callback' => $callback,
				'isSecure' => $isSecure,
				'method' => 'POST',
		];
	}

public	function addXHttp($url, $callback, $isSecure = false){
		// Content-type: application/x-www-form-urlencoded		
		// X-Requested-With: XMLHttpRequest
		
		$this->routes[] = [
				'url' => $url,
				'callback' => $callback,
				'isSecure' => $isSecure,
				'method' => 'PUT',
		];
	}

public function redirect( $url) {
				header('Location: ' . $url);
			echo 'redirect to ' . $url;
			die();
}

public	function Run() {
		session_start();
		$isNotFound = true;

		foreach ($this->routes as $routeItem) {
						
			if ($this->checkUri($routeItem['url']) &&
			    $routeItem['method'] == $_SERVER['REQUEST_METHOD'] && 
			 	$this->checkSecure($routeItem)) {

					$routeItem['callback'](); 
					$isNotFound = false;
					break;
			} 
		}

		if ($isNotFound) {
			header('Location: /404.php');
			echo 'redirect to 404';
			die();
		}
	}

public function init($template) {
	$this->template = $template;	
}

private	function checkUri($uri) {

	if ($uri == $_SERVER['REQUEST_URI']) {
		return true;
	}

	$url = $_SERVER['REQUEST_URI'].'/';		
	$parsed_uri = [];
	
	
	while($tok = strtok($url, '/')){
		$parsed_uri[] = $tok;		
		$url = str_replace($tok.'/', '', $url);
	}
	

	$parsed_tpl = [];
	$url = $uri.'/';
	while($tok = strtok($url, '/')){
		$parsed_tpl[] = $tok;		
		$url = str_replace($tok.'/', '', $url);
	}

	
	$res = preg_match_all("%{=?\w+(?=})%", $uri, $matches);
	if ($res) {
		$var = $matches[0];
		$this->vars = str_replace('{', '', $var);
	}



	$this->vars = array_flip($this->vars);
	
	$result = false;
	foreach ($parsed_tpl as $key => $tok) {
		
		if ( $tok[0] =='{' ) {
			$varname = str_replace(['{','}'], ['',''], $tok);
			
			if (isset($this->vars[$varname]) && isset($parsed_uri[$key])  ) {
				$this->vars[$varname] = $parsed_uri[$key];
				$result =  true;				
			} else {
				$result =  false; break;
			}
		} else {
			if ( isset($parsed_uri[$key]) && $tok == $parsed_uri[$key]) {
				$result =  true;				
			} else {
				$result =  false; break;
			}
		}
	}

	return $result;
	}


public function getVar($name) {		
	return isset($this->vars[$name]) ? $this->vars[$name] : null;
}


private	function checkSecure(array $routeItem) {
		if (!isset($routeItem['isSecure']) ||
		           $routeItem['isSecure'] == false) {
			return true;
		}

		if ($routeItem['isSecure'] && isset( $_SESSION['isAuth']) && $_SESSION['isAuth'] ) return true;

		return false;
	}
}
