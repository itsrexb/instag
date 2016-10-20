<?php
namespace Cart;
class Cart {
	private $data = array();

	public function __construct($registry) {
		$this->config   = $registry->get('config');
		$this->currency = $registry->get('currency');
		$this->customer = $registry->get('customer');
		$this->db       = $registry->get('db');
		$this->session  = $registry->get('session');
		$this->tax      = $registry->get('tax');
	}

	public function getProduct($cart_id) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "cart` WHERE cart_id = '" . (int)$cart_id . "'");

		return ($query->row ? $this->getProductData($query->row) : false);
	}

	public function getProducts() {
		$product_data = array();

		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "cart` WHERE customer_id = '" . (int)$this->customer->getId() . "' AND session_id = '" . $this->db->escape($this->session->getId()) . "'");

		foreach ($query->rows as $cart) {
			$product = $this->getProductData($cart);

			if ($product) {
				$product_data[] = $product;
			} else {
				$this->remove($cart['cart_id']);
			}
		}

		return $product_data;
	}

	private function getProductData($cart) {
		$product_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "product_to_store` p2s LEFT JOIN `" . DB_PREFIX . "product` p ON (p2s.product_id = p.product_id) LEFT JOIN `" . DB_PREFIX . "product_description` pd ON (p.product_id = pd.product_id) WHERE p2s.store_id = '" . (int)$this->config->get('config_store_id') . "' AND p2s.product_id = '" . (int)$cart['product_id'] . "' AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p.date_available <= NOW() AND p.status = '1'");

		if ($product_query->num_rows && ($cart['quantity'] > 0)) {
			if ($cart['price'] >= 0) {
				$price = $cart['price'];
			} else {
				$price = $product_query->row['price'];

				// set pricing based on currency
				$product_price_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "product_price` WHERE product_id = '" . (int)$cart['product_id'] . "' AND currency_id = '" . (int)$this->currency->getId() . "'");

				if ($product_price_query->row && $product_price_query->row['price'] > 0) {
					$price = $product_price_query->row['price'];
				}

				// Product Specials
				$product_special_query = $this->db->query("SELECT price FROM `" . DB_PREFIX . "product_special` WHERE product_id = '" . (int)$cart['product_id'] . "' AND customer_group_id IN ('0','" . (int)$this->config->get('config_customer_group_id') . "') AND ((date_start = '0000-00-00' OR date_start < NOW()) AND (date_end = '0000-00-00' OR date_end > NOW())) ORDER BY priority ASC, price ASC LIMIT 1");

				if ($product_special_query->num_rows) {
					$price = $product_special_query->row['price'];
				}
			}

			// Reward Points
			$product_reward_query = $this->db->query("SELECT points FROM `" . DB_PREFIX . "product_reward` WHERE product_id = '" . (int)$cart['product_id'] . "' AND customer_group_id IN ('0','" . (int)$this->config->get('config_customer_group_id') . "') ORDER BY points DESC LIMIT 1");

			if ($product_reward_query->num_rows) {
				$reward = $product_reward_query->row['points'];
			} else {
				$reward = 0;
			}

			// Downloads
			$download_data = array();

			$download_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "product_to_download` p2d LEFT JOIN `" . DB_PREFIX . "download` d ON (p2d.download_id = d.download_id) LEFT JOIN `" . DB_PREFIX . "download_description` dd ON (d.download_id = dd.download_id) WHERE p2d.product_id = '" . (int)$cart['product_id'] . "' AND dd.language_id = '" . (int)$this->config->get('config_language_id') . "'");

			foreach ($download_query->rows as $download) {
				$download_data[] = array(
					'download_id' => $download['download_id'],
					'name'        => $download['name'],
					'filename'    => $download['filename'],
					'mask'        => $download['mask']
				);
			}

			$recurring_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "recurring` r LEFT JOIN `" . DB_PREFIX . "product_recurring` pr ON (r.recurring_id = pr.recurring_id) LEFT JOIN `" . DB_PREFIX . "recurring_description` rd ON (r.recurring_id = rd.recurring_id) WHERE r.recurring_id = '" . (int)$cart['recurring_id'] . "' AND pr.product_id = '" . (int)$cart['product_id'] . "' AND rd.language_id = " . (int)$this->config->get('config_language_id') . " AND r.status = 1 AND pr.customer_group_id IN ('0','" . (int)$this->config->get('config_customer_group_id') . "')");

			if ($recurring_query->row) {
				if ($recurring_query->row['price_type'] == 'P') {
					$recurring_price = $price * ($recurring_query->row['price'] / 100);
				} else {
					$recurring_price = $recurring_query->row['price'];
				}

				if ($recurring_query->row['trial_price_type'] == 'P') {
					$trial_price = $price * ($recurring_query->row['trial_price'] / 100);
				} else {
					$trial_price = $recurring_query->row['trial_price'];
				}

				$recurring = array(
					'recurring_id'    => $cart['recurring_id'],
					'price'           => $recurring_price,
					'cycle'           => $recurring_query->row['cycle'],
					'frequency'       => $recurring_query->row['frequency'],
					'duration'        => $recurring_query->row['duration'],
					'trial_status'    => $recurring_query->row['trial_status'],
					'trial_price'     => $trial_price,
					'trial_cycle'     => $recurring_query->row['trial_cycle'],
					'trial_frequency' => $recurring_query->row['trial_frequency'],
					'trial_duration'  => $recurring_query->row['trial_duration']
				);
			} else {
				$recurring = false;
			}

			$account_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "account` WHERE customer_id = '" . (int)$this->customer->getId() . "' AND account_id = '" . $this->db->escape($cart['account_id']) . "'");

			return array(
				'cart_id'                  => $cart['cart_id'],
				'product_id'               => $product_query->row['product_id'],
				'name'                     => $product_query->row['name'],
				'model'                    => $product_query->row['model'],
				'image'                    => $product_query->row['image'],
				'account_id'               => $cart['account_id'],
				'account_type'             => ($account_query->row ? $account_query->row['type'] : ''),
				'account_username'         => ($account_query->row ? $account_query->row['username'] : ''),
				'time_extension'           => $product_query->row['time_extension'],
				'time_extension_frequency' => $product_query->row['time_extension_frequency'],
				'download'                 => $download_data,
				'quantity'                 => $cart['quantity'],
				'minimum'                  => $product_query->row['minimum'],
				'price'                    => $price,
				'total'                    => $price * $cart['quantity'],
				'discount'                 => $cart['discount'],
				'coupon_id'                => $cart['coupon_id'],
				'reward'                   => $reward * $cart['quantity'],
				'points'                   => ($product_query->row['points'] ? $product_query->row['points'] * $cart['quantity'] : 0),
				'tax_class_id'             => $product_query->row['tax_class_id'],
				'recurring'                => $recurring
			);
		}

		return false;
	}

	public function add($product_id, $account_id, $quantity = 1, $recurring_id = 0, $price = false) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "cart` WHERE customer_id = '" . (int)$this->customer->getId() . "' AND session_id = '" . $this->db->escape($this->session->getId()) . "' AND product_id = '" . (int)$product_id . "' AND account_id = '" . $this->db->escape($account_id) . "' AND recurring_id = '" . (int)$recurring_id . "' AND price = '" . (float)($price !== false ? $price : -1) . "'");

		if (!$query->row['total']) {
			$this->db->query("INSERT INTO `" . DB_PREFIX . "cart` SET customer_id = '" . (int)$this->customer->getId() . "', session_id = '" . $this->db->escape($this->session->getId()) . "', product_id = '" . (int)$product_id . "', account_id = '" . $this->db->escape($account_id) . "', recurring_id = '" . (int)$recurring_id . "', quantity = '" . (int)$quantity . "', price = '" . (float)($price !== false ? $price : -1) . "', date_added = NOW()");
		} else {
			$this->db->query("UPDATE `" . DB_PREFIX . "cart` SET quantity = (quantity + " . (int)$quantity . ") WHERE customer_id = '" . (int)$this->customer->getId() . "' AND session_id = '" . $this->db->escape($this->session->getId()) . "' AND product_id = '" . (int)$product_id . "' AND account_id = '" . $this->db->escape($account_id) . "' AND recurring_id = '" . (int)$recurring_id . "' AND price = '" . (float)($price !== false ? $price : -1) . "'");
		}

		return $this->db->getLastId();
	}

	public function update($cart_id, $quantity) {
		$this->db->query("UPDATE `" . DB_PREFIX . "cart` SET quantity = '" . (int)$quantity . "' WHERE cart_id = '" . (int)$cart_id . "' AND customer_id = '" . (int)$this->customer->getId() . "' AND session_id = '" . $this->db->escape($this->session->getId()) . "'");
	}

	public function remove($cart_id) {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "cart` WHERE cart_id = '" . (int)$cart_id . "' AND customer_id = '" . (int)$this->customer->getId() . "' AND session_id = '" . $this->db->escape($this->session->getId()) . "'");
	}

	public function clear() {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "cart` WHERE customer_id = '" . (int)$this->customer->getId() . "' AND session_id = '" . $this->db->escape($this->session->getId()) . "'");
	}

	public function updateDiscount($cart_id, $discount, $coupon_id = 0) {
		$this->db->query("UPDATE `" . DB_PREFIX . "cart` SET discount = '" . (float)$discount . "', coupon_id = '" . (int)$coupon_id . "' WHERE cart_id = '" . (int)$cart_id . "' AND customer_id = '" . (int)$this->customer->getId() . "' AND session_id = '" . $this->db->escape($this->session->getId()) . "'");
	}

	public function getRecurringProducts() {
		$product_data = array();

		foreach ($this->getProducts() as $value) {
			if ($value['recurring']) {
				$product_data[] = $value;
			}
		}

		return $product_data;
	}

	public function getSubTotal() {
		$total = 0;

		foreach ($this->getProducts() as $product) {
			$total += $product['total'];
		}

		return $total;
	}

	public function getTaxes() {
		$tax_data = array();

		foreach ($this->getProducts() as $product) {
			if ($product['tax_class_id']) {
				$tax_rates = $this->tax->getRates($product['price'], $product['tax_class_id']);

				foreach ($tax_rates as $tax_rate) {
					if (!isset($tax_data[$tax_rate['tax_rate_id']])) {
						$tax_data[$tax_rate['tax_rate_id']] = ($tax_rate['amount'] * $product['quantity']);
					} else {
						$tax_data[$tax_rate['tax_rate_id']] += ($tax_rate['amount'] * $product['quantity']);
					}
				}
			}
		}

		return $tax_data;
	}

	public function getTotal() {
		$total = 0;

		foreach ($this->getProducts() as $product) {
			$total += $this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax')) * $product['quantity'];
		}

		return $total;
	}

	public function countProducts() {
		$product_total = 0;

		$products = $this->getProducts();

		foreach ($products as $product) {
			$product_total += $product['quantity'];
		}

		return $product_total;
	}

	public function hasProducts() {
		return count($this->getProducts());
	}

	public function hasRecurringProducts() {
		return count($this->getRecurringProducts());
	}

	public function hasDownload() {
		foreach ($this->getProducts() as $product) {
			if ($product['download']) {
				return true;
			}
		}

		return false;
	}
}