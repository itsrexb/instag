<?php
class Export {
	private $adaptor;
	private $filename;

	public function __construct($adaptor, $filename = 'export') {
		$class = 'Export\\' . $adaptor;

		if (class_exists($class)) {
			$this->adaptor = new $class($filename);
		} else {
			throw new \Exception('Error: Could not load export adaptor ' . $adaptor . '!');
		}
	}

	public function addHeader($data) {
		return $this->adaptor->addHeader($data);
	}

	public function write($data) {
		return $this->adaptor->write($data);
	}

	public function close() {
		return $this->adaptor->close();
	}

	public function hasHeader() {
		return $this->adaptor->hasHeader();
	}
}