<?php
final class Loader {
	protected $registry;

	public function __construct($registry) {
		$this->registry = $registry;
	}

	public function controller($route, $data = array()) {
		// Sanitize the call
		$route = str_replace('/[^a-zA-Z0-9_\/]/', '', (string)$route);

		$action = new Action($route);

		$output = $action->execute($this->registry, $data);

		if (!($output instanceof Exception)) {
			return $output;
		} else {
			return false;
		}
	}

	public function model($route) {
		// Sanitize the call
		$route = preg_replace('/[^a-zA-Z0-9_\/]/', '', (string)$route);
		$model_name = 'model_' . str_replace(array('/', '-', '.'), array('_', '', ''), $route);

		if ($this->registry->get($model_name) !== null) {
			return $this->registry->get($model_name);
		}

		$file  = DIR_APPLICATION . 'model/' . $route . '.php';
		$class = 'Model' . preg_replace('/[^a-zA-Z0-9]/', '', $route);

		if (is_file($file)) {
			include_once($file);

			$this->registry->set($model_name, new $class($this->registry));

			return $this->registry->get($model_name);
		} else {
			throw new \Exception('Error: Could not load model ' . $route . '!');
		}
	}

	public function view($route, $data = array()) {
		// Sanitize the call
		$route = str_replace('/[^a-zA-Z0-9_\/]/', '', (string)$route);

		// TODO: remove the preg_replace when all controllers have been updated to not include .tpl
		$file = preg_replace('/.tpl$/', '', $route) . '.tpl';

		if (!is_file(DIR_TEMPLATE . $file)) {
			if (is_file(DIR_TEMPLATE . $this->registry->get('config')->get('config_template') . '/template/' . $file)) {
				$file = $this->registry->get('config')->get('config_template') . '/template/' . $file;
			} else {
				$file = 'default/template/' . $file;
			}
		}

		$template = new Template('basic');

		foreach ($data as $key => $value) {
			$template->set($key, $value);
		}

		return $template->render($file);
	}

	public function library($route) {
		// Sanitize the call
		$route = preg_replace('/[^a-zA-Z0-9_\/]/', '', (string)$route);

		$file = DIR_SYSTEM . 'library/' . $route . '.php';
		$class = str_replace('/', '\\', $route);

		if (is_file($file)) {
			include_once($file);

			$this->registry->set(basename($route), new $class($this->registry));
		} else {
			throw new \Exception('Error: Could not load library ' . $route . '!');
		}
	}

	public function helper($route) {
		$file = DIR_SYSTEM . 'helper/' . str_replace('/[^a-zA-Z0-9_\/]/', '', (string)$route) . '.php';

		if (is_file($file)) {
			include_once($file);
		} else {
			throw new \Exception('Error: Could not load helper ' . $route . '!');
		}
	}

	public function vendor($route) {
		$file = DIR_SYSTEM . 'vendor/' . str_replace('/[^a-zA-Z0-9_\/]/', '', (string)$route) . '.php';

		if (is_file($file)) {
			include_once($file);
		} else {
			throw new \Exception('Error: Could not load vendor ' . $route . '!');
		}
	}

	public function config($route) {
		$this->registry->get('config')->load($route);
	}

	public function language($route) {
		return $this->registry->get('language')->load($route);
	}
}