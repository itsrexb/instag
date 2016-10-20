<?php
class Log {
	private $handle;

	public function __construct($registry) {
		$this->config = $registry->get('config');

		$this->handle = fopen(DIR_LOGS . $this->config->get('config_error_filename'), 'a');

		set_error_handler(array($this, 'error_handler'));
	}

	public function write($message) {
		fwrite($this->handle, date('Y-m-d G:i:s') . ' - ' . print_r($message, true) . "\n");
	}

	public function error_handler($code, $message, $file, $line) {
		// error suppressed with @
		if (error_reporting() === 0) {
			return false;
		}

		switch ($code) {
			case E_NOTICE:
			case E_USER_NOTICE:
				$error = 'Notice';
				break;
			case E_WARNING:
			case E_USER_WARNING:
				$error = 'Warning';
				break;
			case E_ERROR:
			case E_USER_ERROR:
				$error = 'Fatal Error';
				break;
			default:
				$error = 'Unknown';
				break;
		}

		if ($this->config->get('config_error_display')) {
			echo '<b>' . $error . '</b>: ' . $message . ' in <b>' . $file . '</b> on line <b>' . $line . '</b>';
		}

		if ($this->config->get('config_error_log')) {
			$this->write('PHP ' . $error . ':  ' . $message . ' in ' . $file . ' on line ' . $line);
		}

		return true;
	}

	public function __destruct() {
		fclose($this->handle);
	}
}