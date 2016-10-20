<?php
class DB {
	private $adaptor;
	private $time_zone;
	private $offset_sign   = '+';
	private $offset_hour   = '0';
	private $offset_minute = '00';

	public function __construct($adaptor, $hostname, $username, $password, $database, $port = NULL) {
		$class = 'DB\\' . $adaptor;

		if (class_exists($class)) {
			$this->adaptor = new $class($hostname, $username, $password, $database, $port);

			$this->query("SET time_zone = '+0:00'");
		} else {
			throw new \Exception('Error: Could not load database adaptor ' . $adaptor . '!');
		}
	}

	public function query($sql) {
		$results = $this->adaptor->query($sql);

		if ($this->time_zone) {
			if (!empty($results->rows)) {
				$time_zone = new \DateTimeZone($this->time_zone);

				foreach ($results->rows as $row_key => $row) {
					foreach ($row as $value_key => $value) {
						if (strpos($value_key, 'date_') === 0 && strlen($value) > 10 && strpos($value, '0000-00-00') === false) {
							$datetime = new \DateTime($value);
							$datetime->setTimezone($time_zone);

							$results->rows[$row_key][$value_key] = $datetime->format('Y-m-d H:i:s');
						} else if (strpos($value_key, 'hour') === 0) {
							if ($this->offset_sign == '+') {
								$value += $this->offset_hour;
							} else {
								$value -= $this->offset_hour;
							}

							$results->rows[$row_key][$value_key] = $value;
						}
					}
				}

				$results->row = $results->rows[0];
			}
		}

		return $results;
	}

	public function escape($value) {
		return $this->adaptor->escape(trim($value));
	}

	public function countAffected() {
		return $this->adaptor->countAffected();
	}

	public function getLastId() {
		return $this->adaptor->getLastId();
	}

	public function connected() {
		return $this->adaptor->connected();
	}

	public function setTimeZone($time_zone) {
		$this->time_zone = $time_zone;

		$now = new \DateTime();
		$now->setTimezone(new \DateTimeZone($time_zone));

		$this->offset_minute = $now->getOffset() / 60;

		$this->offset_sign    = ($this->offset_minute < 0 ? -1 : 1);
		$this->offset_minute  = abs($this->offset_minute);
		$this->offset_hour    = floor($this->offset_minute / 60);
		$this->offset_minute -= $this->offset_hour * 60;
	}

	/*
	@param $data ARRAY(table, fields, conditions)
	*/
	public function update($data) {
		if (is_array($data)) {
			$fields = array();

			foreach ($data['fields'] as $field => $value) {
				$fields[] = $field . " = '" . $this->escape($value) . "'";
			}

			$conditions = array();

			foreach ($data['conditions'] as $condition => $value) {
				$conditions[] = $condition . " = '" . $this->escape($value) . "'";
			}

			return $this->query("UPDATE `" . DB_PREFIX . $data['table'] . "` SET " . implode(", ", $fields) . " WHERE " . implode(" AND ", $conditions));
		} else {
			return $this->query($data);
		}
	}

	/*
	@param $data ARRAY(table, fields)
	*/
	public function insert($data) {
		if (is_array($data)) {
			$fields = array();

			foreach ($data['fields'] as $field => $value) {
				$fields[] = $field . " = '" . $this->escape($value) . "'";
			}

			return $this->query("INSERT INTO " . DB_PREFIX . $data['table'] . " SET " . implode(", ", $fields));
		} else {
			return $this->query($data);
		}
	}
}