<?php
class Url {
	private $http;
	private $https;
	private $rewrite = array();

	public function __construct($http, $https = '') {
		$this->http  = $http;
		$this->https = $https;
	}

	public function addRewrite($rewrite) {
		$this->rewrite[] = $rewrite;
	}

	public function link($route, $args = '', $secure = false) {
		if ($secure) {
			$url = $this->https;
		} else {
			$url = $this->http;
		}

		$url .= 'index.php?route=' . $route;

		if ($args) {
			if (is_array($args)) {
				$url .= '&' . http_build_query($args);
			} else {
				$url .= '&' . ltrim($args, '&');
			}
		}

		foreach ($this->rewrite as $rewrite) {
			$url = $rewrite->rewrite($url);
		}

		return $url;
	}
}