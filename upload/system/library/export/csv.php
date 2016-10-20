<?php
namespace Export;
final class CSV {
	private $connection;

	private $header = array();

	public function __construct($filename = 'export') {
		header('Content-Type: text/csv');
		header('Content-Disposition: attachment; filename=' . $filename . '.csv');
		header('Expires: 0');
		header('Cache-Control: must-revalidate');
		header('Pragma: public');

		$this->connection = fopen('php://output', 'w');
	}

	public function addHeader($data) {
		$this->write($data);

		$this->header = array_fill_keys($data, '');
	}

	public function write($data) {
		if ($this->header) {
			$data = array_merge($this->header, $data);
		}

		fputcsv($this->connection, $data);
	}

	public function close() {
		fclose($this->connection);
		exit;
	}

	public function hasHeader() {
		return ($this->header ? true : false);
	}

	public function __destruct() {
		fclose($this->connection);
	}
}