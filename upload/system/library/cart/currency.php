<?php
namespace Cart;
class Currency {
	private $code;
	private $currencies = array();

	public function __construct($registry, $currency_code = '') {
		$this->config   = $registry->get('config');
		$this->db       = $registry->get('db');
		$this->language = $registry->get('language');
		$this->request  = $registry->get('request');

		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "currency`");

		foreach ($query->rows as $result) {
			$this->currencies[$result['code']] = array(
				'currency_id'   => $result['currency_id'],
				'title'         => $result['title'],
				'symbol_left'   => $result['symbol_left'],
				'symbol_right'  => $result['symbol_right'],
				'decimal_place' => $result['decimal_place'],
				'value'         => $result['value'],
				'locale'        => $result['locale'],
				'status'        => $result['status']
			);
		}

		if ($currency_code) {
			$this->set($currency_code);
		} else {
			// try to set currency based on visitor locale, otherwise set to store default
			if (!empty($this->request->server['HTTP_ACCEPT_LANGUAGE'])) {
				$http_accept_language_data = explode(';', strtolower($this->request->server['HTTP_ACCEPT_LANGUAGE']));

				if (!empty($http_accept_language_data[0])) {
					$locales = explode(',', $http_accept_language_data[0]);

					foreach ($locales as $locale) {
						foreach ($this->currencies as $currency_code => $currency) {
							if ($currency['status'] && strpos($currency['locale'], $locale) !== false) {
								$this->set($currency_code);
								return;
							}
						}
					}
				}
			}

			$this->set($this->config->get('config_currency'));
		}
	}

	public function set($currency) {
		$this->code = $currency;
	}

	public function format($number, $currency = '', $value = '', $format = true) {
		if ($currency && $this->has($currency)) {
			$symbol_left   = $this->currencies[$currency]['symbol_left'];
			$symbol_right  = $this->currencies[$currency]['symbol_right'];
			$decimal_place = $this->currencies[$currency]['decimal_place'];
		} else {
			$symbol_left   = $this->currencies[$this->code]['symbol_left'];
			$symbol_right  = $this->currencies[$this->code]['symbol_right'];
			$decimal_place = $this->currencies[$this->code]['decimal_place'];

			$currency = $this->code;
		}

		if (!$value) {
			$value = $this->currencies[$currency]['value'];
		}

		$amount = $value ? (float)$number * $value : (float)$number;

		$amount = sprintf('%0.' . (int)$decimal_place . 'f', $amount);

		if (!$format) {
			return $amount;
		}

		$string = '';

		if ($symbol_left) {
			$string .= $symbol_left;
		}

		$string .= number_format($amount, (int)$decimal_place, $this->language->get('decimal_point'), $this->language->get('thousand_point'));

		if ($symbol_right) {
			$string .= $symbol_right;
		}

		return $string;
	}

	public function convert($value, $from, $to) {
		if (isset($this->currencies[$from])) {
			$from = $this->currencies[$from]['value'];
		} else {
			$from = 1;
		}

		if (isset($this->currencies[$to])) {
			$to = $this->currencies[$to]['value'];
		} else {
			$to = 1;
		}

		return $value * ($to / $from);
	}

	public function getId($currency = '') {
		if (!$currency) {
			return $this->currencies[$this->code]['currency_id'];
		} elseif ($currency && isset($this->currencies[$currency])) {
			return $this->currencies[$currency]['currency_id'];
		} else {
			return 0;
		}
	}

	public function getSymbolLeft($currency = '') {
		if (!$currency) {
			return $this->currencies[$this->code]['symbol_left'];
		} elseif ($currency && isset($this->currencies[$currency])) {
			return $this->currencies[$currency]['symbol_left'];
		} else {
			return '';
		}
	}

	public function getSymbolRight($currency = '') {
		if (!$currency) {
			return $this->currencies[$this->code]['symbol_right'];
		} elseif ($currency && isset($this->currencies[$currency])) {
			return $this->currencies[$currency]['symbol_right'];
		} else {
			return '';
		}
	}

	public function getDecimalPlace($currency = '') {
		if (!$currency) {
			return $this->currencies[$this->code]['decimal_place'];
		} elseif ($currency && isset($this->currencies[$currency])) {
			return $this->currencies[$currency]['decimal_place'];
		} else {
			return 0;
		}
	}

	public function getCode() {
		return $this->code;
	}

	public function getValue($currency = '') {
		if (!$currency) {
			return $this->currencies[$this->code]['value'];
		} elseif ($currency && isset($this->currencies[$currency])) {
			return $this->currencies[$currency]['value'];
		} else {
			return 0;
		}
	}

	public function has($currency) {
		return isset($this->currencies[$currency]);
	}
}
