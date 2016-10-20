<?php
namespace Cart;
class Customer {
	private $customer_id;
	private $firstname;
	private $lastname;
	private $customer_group_id;
	private $email;
	private $telephone;
	private $newsletter;
	private $country_id;
	private $discount;
	private $timezone;
	private $currency_code;
	private $language_code;

	public function __construct($registry) {
		$this->db      = $registry->get('db');
		$this->request = $registry->get('request');
		$this->session = $registry->get('session');
		$this->user    = $registry->get('user');

		if (isset($this->session->data['customer_id'])) {
			if ($this->user->isLogged()) {
				$customer_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "customer` WHERE customer_id = '" . (int)$this->session->data['customer_id'] . "'");
			} else {
				$customer_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "customer` WHERE customer_id = '" . (int)$this->session->data['customer_id'] . "' AND status = '1'");
			}

			if ($customer_query->num_rows) {
				$this->customer_id       = $customer_query->row['customer_id'];
				$this->firstname         = $customer_query->row['firstname'];
				$this->lastname          = $customer_query->row['lastname'];
				$this->customer_group_id = $customer_query->row['customer_group_id'];
				$this->email             = $customer_query->row['email'];
				$this->telephone         = $customer_query->row['telephone'];
				$this->newsletter        = $customer_query->row['newsletter'];
				$this->country_id        = $customer_query->row['country_id'];
				$this->discount          = $customer_query->row['discount'];
				$this->timezone          = $customer_query->row['timezone'];
				$this->currency_code     = $customer_query->row['currency_code'];
				$this->language_code     = $customer_query->row['language_code'];

				if (isset($this->request->server['REMOTE_ADDR'])) {
					$this->db->query("UPDATE `" . DB_PREFIX . "customer` SET ip = '" . $this->db->escape($this->request->server['REMOTE_ADDR']) . "' WHERE customer_id = '" . (int)$this->customer_id . "'");

					$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "customer_ip` WHERE customer_id = '" . (int)$this->session->data['customer_id'] . "' AND ip = '" . $this->db->escape($this->request->server['REMOTE_ADDR']) . "'");

					if (!$query->num_rows) {
						$this->db->query("INSERT INTO `" . DB_PREFIX . "customer_ip` SET customer_id = '" . (int)$this->session->data['customer_id'] . "', ip = '" . $this->db->escape($this->request->server['REMOTE_ADDR']) . "', date_added = NOW()");
					}
				}
			} else {
				$this->logout();
			}
		}
	}

	public function login($email, $password, $override = false) {
		if ($override) {
			$customer_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "customer` WHERE LOWER(email) = '" . $this->db->escape(utf8_strtolower($email)) . "'");
		} else {
			$customer_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "customer` WHERE LOWER(email) = '" . $this->db->escape(utf8_strtolower($email)) . "' AND (password = SHA1(CONCAT(salt, SHA1(CONCAT(salt, SHA1('" . $this->db->escape($password) . "'))))) OR password = '" . $this->db->escape(md5($password)) . "') AND status = '1' AND approved = '1'");
		}

		if ($customer_query->num_rows) {
			$this->session->data['customer_id'] = $customer_query->row['customer_id'];

			$this->customer_id       = $customer_query->row['customer_id'];
			$this->firstname         = $customer_query->row['firstname'];
			$this->lastname          = $customer_query->row['lastname'];
			$this->customer_group_id = $customer_query->row['customer_group_id'];
			$this->email             = $customer_query->row['email'];
			$this->telephone         = $customer_query->row['telephone'];
			$this->newsletter        = $customer_query->row['newsletter'];
			$this->country_id        = $customer_query->row['country_id'];
			$this->discount          = $customer_query->row['discount'];
			$this->timezone          = $customer_query->row['timezone'];
			$this->currency_code     = $customer_query->row['currency_code'];
			$this->language_code     = $customer_query->row['language_code'];

			if (isset($this->request->server['REMOTE_ADDR'])) {
				$this->db->query("UPDATE `" . DB_PREFIX . "customer` SET ip = '" . $this->db->escape($this->request->server['REMOTE_ADDR']) . "' WHERE customer_id = '" . (int)$this->customer_id . "'");
			}

			return true;
		} else {
			return false;
		}
	}

	public function logout() {
		unset($this->session->data['customer_id']);

		$this->customer_id       = '';
		$this->firstname         = '';
		$this->lastname          = '';
		$this->customer_group_id = '';
		$this->email             = '';
		$this->telephone         = '';
		$this->newsletter        = '';
		$this->country_id        = '';
		$this->discount          = '';
		$this->timezone          = '';
		$this->currency_code     = '';
		$this->language_code     = '';
	}

	public function isLogged() {
		return $this->customer_id;
	}

	public function getId() {
		return $this->customer_id;
	}

	public function getFirstName() {
		return $this->firstname;
	}

	public function getLastName() {
		return $this->lastname;
	}

	public function getGroupId() {
		return $this->customer_group_id;
	}

	public function getEmail() {
		return $this->email;
	}

	public function getTelephone() {
		return $this->telephone;
	}

	public function getNewsletter() {
		return $this->newsletter;
	}

	public function getCountryId() {
		return $this->country_id;
	}

	public function getDiscount() {
		if ($this->discount == 0) {
			$query = $this->db->query("SELECT discount FROM `" . DB_PREFIX . "customer_group` WHERE customer_group_id = '" . (int)$this->customer_group_id . "'");

			if ($query->row) {
				$this->discount = $query->row['discount'];
			}
		}

		return $this->discount;
	}

	public function getTimeZone() {
		return $this->timezone;
	}

	public function getCurrencyCode() {
		return $this->currency_code;
	}

	public function getLanguageCode() {
		return $this->language_code;
	}

	public function getBalance() {
		$query = $this->db->query("SELECT SUM(amount) AS total FROM `" . DB_PREFIX . "customer_transaction` WHERE customer_id = '" . (int)$this->customer_id . "'");

		return $query->row['total'];
	}

	public function getRewardPoints() {
		$query = $this->db->query("SELECT SUM(points) AS total FROM `" . DB_PREFIX . "customer_reward` WHERE customer_id = '" . (int)$this->customer_id . "'");

		return $query->row['total'];
	}
}