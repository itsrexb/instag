<?php
class ModelAffiliateAffiliate extends Model {
	public function addAffiliate($data) {
		$this->db->query("INSERT INTO `" . DB_PREFIX . "affiliate` SET affiliate_group_id = '" . (int)$data['affiliate_group_id'] . "', parent_id = '" . (int)$data['parent_id'] . "', firstname = '" . $this->db->escape($data['firstname']) . "', lastname = '" . $this->db->escape($data['lastname']) . "', email = '" . $this->db->escape($data['email']) . "', timezone = '".$this->config->get('config_timezone')."', telephone = '" . $this->db->escape($data['telephone']) . "', salt = '" . $this->db->escape($salt = token(9)) . "', password = '" . $this->db->escape(sha1($salt . sha1($salt . sha1($data['password'])))) . "', company = '" . $this->db->escape($data['company']) . "', website = '" . $this->db->escape($data['website']) . "', address_1 = '" . $this->db->escape($data['address_1']) . "', address_2 = '" . $this->db->escape($data['address_2']) . "', city = '" . $this->db->escape($data['city']) . "', postcode = '" . $this->db->escape($data['postcode']) . "', country_id = '" . (int)$data['country_id'] . "', zone_id = '" . (int)$data['zone_id'] . "', code = '" . $this->db->escape($data['code']) . "', account_fee = '" . (float)$data['account_fee'] . "', tax = '" . $this->db->escape($data['tax']) . "', payment = '" . $this->db->escape($data['payment']) . "', payment_data = '" . $this->db->escape(json_encode($data['payment_data'])) . "', status = '" . (int)$data['status'] . "', approved = '" . (int)!$this->config->get('config_affiliate_approval') . "', date_added = NOW(), braintree_customer_id =  '" . $this->db->escape($data['braintree_customer_id']) . "' ");

		$affiliate_id = $this->db->getLastId();

		$level = 0;

		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "affiliate_path` WHERE affiliate_id = '" . (int)$data['parent_id'] . "' ORDER BY `level` ASC");

		foreach ($query->rows as $result) {
			$this->db->query("INSERT INTO `" . DB_PREFIX . "affiliate_path` SET affiliate_id = '" . (int)$affiliate_id . "', path_id = '" . (int)$result['path_id'] . "', level = '" . (int)$level . "'");

			$level++;
		}

		$this->db->query("INSERT INTO `" . DB_PREFIX . "affiliate_path` SET affiliate_id = '" . (int)$affiliate_id . "', path_id = '" . (int)$affiliate_id . "', level = '" . (int)$level . "'");

		return $affiliate_id;
	}

	public function editAffiliate($affiliate_id, $data) {
		$this->db->query("UPDATE `" . DB_PREFIX . "affiliate` SET affiliate_group_id = '" . (int)$data['affiliate_group_id'] . "', parent_id = '" . (int)$data['parent_id'] . "', firstname = '" . $this->db->escape($data['firstname']) . "', lastname = '" . $this->db->escape($data['lastname']) . "', email = '" . $this->db->escape($data['email']) . "', telephone = '" . $this->db->escape($data['telephone']) . "', company = '" . $this->db->escape($data['company']) . "', website = '" . $this->db->escape($data['website']) . "', address_1 = '" . $this->db->escape($data['address_1']) . "', address_2 = '" . $this->db->escape($data['address_2']) . "', city = '" . $this->db->escape($data['city']) . "', postcode = '" . $this->db->escape($data['postcode']) . "', country_id = '" . (int)$data['country_id'] . "', zone_id = '" . (int)$data['zone_id'] . "', code = '" . $this->db->escape($data['code']) . "', account_fee = '" . (float)$data['account_fee'] . "', tax = '" . $this->db->escape($data['tax']) . "', payment = '" . $this->db->escape($data['payment']) . "', payment_data = '" . $this->db->escape(json_encode($data['payment_data'])) . "', status = '" . (int)$data['status'] . "', braintree_customer_id =  '" . $this->db->escape($data['braintree_customer_id']) . "' WHERE affiliate_id = '" . (int)$affiliate_id . "'");

		if ($data['password']) {
			$this->db->query("UPDATE `" . DB_PREFIX . "affiliate` SET salt = '" . $this->db->escape($salt = token(9)) . "', password = '" . $this->db->escape(sha1($salt . sha1($salt . sha1($data['password'])))) . "' WHERE affiliate_id = '" . (int)$affiliate_id . "'");
		}

		$path_parent = array();

		// Get the nodes new parents
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "affiliate_path` WHERE affiliate_id = '" . (int)$data['parent_id'] . "' ORDER BY level ASC");

		foreach ($query->rows as $result) {
			$path_parent[] = $result['path_id'];
		}

		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "affiliate_path` WHERE path_id = '" . (int)$affiliate_id . "' ORDER BY level ASC");

		if ($query->rows) {
			foreach ($query->rows as $affiliate_path) {
				// Delete the path below the current one
				$this->db->query("DELETE FROM `" . DB_PREFIX . "affiliate_path` WHERE affiliate_id = '" . (int)$affiliate_path['affiliate_id'] . "' AND level < '" . (int)$affiliate_path['level'] . "'");

				$path = $path_parent;

				// Get whats left of the nodes current path
				$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "affiliate_path` WHERE affiliate_id = '" . (int)$affiliate_path['affiliate_id'] . "' ORDER BY level ASC");

				foreach ($query->rows as $result) {
					$path[] = $result['path_id'];
				}

				// Combine the paths with a new level
				$level = 0;

				foreach ($path as $path_id) {
					$this->db->query("REPLACE INTO `" . DB_PREFIX . "affiliate_path` SET affiliate_id = '" . (int)$affiliate_path['affiliate_id'] . "', path_id = '" . (int)$path_id . "', level = '" . (int)$level . "'");

					$level++;
				}
			}
		} else {
			// Delete the path
			$this->db->query("DELETE FROM `" . DB_PREFIX . "affiliate_path` WHERE affiliate_id = '" . (int)$affiliate_id . "'");

			$path = $path_parent;

			// Fix for records with no paths
			$level = 0;

			foreach ($path as $path_id) {
				$this->db->query("INSERT INTO `" . DB_PREFIX . "affiliate_path` SET affiliate_id = '" . (int)$affiliate_id . "', path_id = '" . (int)$path_id . "', level = '" . (int)$level . "'");

				$level++;
			}

			$this->db->query("REPLACE INTO `" . DB_PREFIX . "affiliate_path` SET affiliate_id = '" . (int)$affiliate_id . "', path_id = '" . (int)$affiliate_id . "', level = '" . (int)$level . "'");
		}
	}

	public function deleteAffiliate($affiliate_id) {
		$affiliate_info = $this->getAffiliate($affiliate_id);

		$this->db->query("UPDATE `" . DB_PREFIX . "affiliate` SET parent_id = '" . (int)$affiliate_info['parent_id'] . "' WHERE parent_id = '" . (int)$affiliate_id . "'");

		$this->db->query("DELETE FROM `" . DB_PREFIX . "affiliate_path` WHERE affiliate_id = '" . (int)$affiliate_id . "'");

		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "affiliate_path` WHERE path_id = '" . (int)$affiliate_id . "'");

		foreach ($query->rows as $result) {
			$this->db->query("UPDATE `" . DB_PREFIX . "affiliate_path` SET level = (level - 1) WHERE affiliate_id = '" . (int)$result['affiliate_id'] . "' AND level > '" . (int)$result['level'] . "'");
		}

		$this->db->query("DELETE FROM `" . DB_PREFIX . "affiliate_path` WHERE path_id = '" . (int)$affiliate_id . "'");
		$this->db->query("DELETE FROM `" . DB_PREFIX . "affiliate` WHERE affiliate_id = '" . (int)$affiliate_id . "'");
		$this->db->query("DELETE FROM `" . DB_PREFIX . "affiliate_activity` WHERE affiliate_id = '" . (int)$affiliate_id . "'");
		$this->db->query("DELETE FROM `" . DB_PREFIX . "affiliate_transaction` WHERE affiliate_id = '" . (int)$affiliate_id . "'");

		$this->db->query("UPDATE `" . DB_PREFIX . "customer` SET affiliate_id = '0' WHERE affiliate_id = '" . (int)$affiliate_id . "'");
	}

	public function getAffiliate($affiliate_id) {
		$query = $this->db->query("SELECT DISTINCT *, (SELECT GROUP_CONCAT(CONCAT(a2.firstname, ' ', a2.lastname) ORDER BY level SEPARATOR '&nbsp;&nbsp;&gt;&nbsp;&nbsp;') FROM `" . DB_PREFIX . "affiliate_path` ap LEFT JOIN `" . DB_PREFIX . "affiliate` a2 ON (a2.affiliate_id = ap.path_id AND ap.affiliate_id != ap.path_id) WHERE ap.affiliate_id = a.affiliate_id GROUP BY ap.affiliate_id) AS path FROM `" . DB_PREFIX . "affiliate` a WHERE a.affiliate_id = '" . (int)$affiliate_id . "'");

		if ($query->row) {
			$affiliate_data = $query->row;

			$affiliate_data['payment_data'] = json_decode($affiliate_data['payment_data'], true);
		} else {
			$affiliate_data = array();
		}

		return $affiliate_data;
	}

	public function getAffiliateByEmail($email) {
		$query = $this->db->query("SELECT DISTINCT * FROM `" . DB_PREFIX . "affiliate` WHERE LCASE(email) = '" . $this->db->escape(utf8_strtolower($email)) . "'");

		return $query->row;
	}

	public function getAffiliates($data = array()) {
		$sql = "SELECT *, (SELECT GROUP_CONCAT(CONCAT(a2.firstname, ' ', a2.lastname) ORDER BY level SEPARATOR '  >  ') FROM `" . DB_PREFIX . "affiliate_path` ap LEFT JOIN `" . DB_PREFIX . "affiliate` a2 ON (a2.affiliate_id = ap.path_id) WHERE ap.affiliate_id = a.affiliate_id GROUP BY ap.affiliate_id) AS name, (SELECT agd.name FROM `" . DB_PREFIX . "affiliate_group_description` agd WHERE agd.affiliate_group_id = a.affiliate_group_id AND agd.language_id = '1') AS affiliate_group, (SELECT COUNT(DISTINCT c.customer_id) FROM `" . DB_PREFIX . "customer` c WHERE c.affiliate_id = a.affiliate_id) AS total_customers, (SELECT COUNT(DISTINCT c.customer_id) FROM `" . DB_PREFIX . "customer` c INNER JOIN `" . DB_PREFIX . "recurring_order` ro ON (ro.customer_id  = c.customer_id) WHERE c.affiliate_id = a.affiliate_id AND ro.active = '1') AS total_active_customers, (SELECT SUM(at.amount) FROM `" . DB_PREFIX . "affiliate_transaction` at WHERE at.affiliate_id = a.affiliate_id GROUP BY at.affiliate_id) AS balance FROM `" . DB_PREFIX . "affiliate` a";

		$implode = array();

		if (!empty($data['filter_affiliate_group_id'])) {
			$implode[] = "a.affiliate_group_id = '" . $this->db->escape($data['filter_affiliate_group_id']) . "'";
		}

		if (!empty($data['filter_name'])) {
			$implode[] = "CONCAT(a.firstname, ' ', a.lastname) LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
		}

		if (!empty($data['filter_email'])) {
			$implode[] = "a.email LIKE '" . $this->db->escape($data['filter_email']) . "%'";
		}

		if (!empty($data['filter_code'])) {
			$implode[] = "a.code = '" . $this->db->escape($data['filter_code']) . "'";
		}

		if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
			$implode[] = "a.status = '" . (int)$data['filter_status'] . "'";
		}

		if (isset($data['filter_approved']) && !is_null($data['filter_approved'])) {
			$implode[] = "a.approved = '" . (int)$data['filter_approved'] . "'";
		}

		if (!empty($data['filter_date_added'])) {
			$filter_date_added_start = new \DateTime($data['filter_date_added'] . ' 00:00:00', new \DateTimeZone($this->config->get('config_timezone')));
			$filter_date_added_start->setTimezone(new \DateTimeZone('UTC'));

			$filter_date_added_end = new \DateTime($data['filter_date_added'] . ' 23:59:59', new \DateTimeZone($this->config->get('config_timezone')));
			$filter_date_added_end->setTimezone(new \DateTimeZone('UTC'));

			$implode[] = "a.date_added >= '" . $this->db->escape($filter_date_added_start->format('Y-m-d H:i:s')) . "'";
			$implode[] = "a.date_added <= '" . $this->db->escape($filter_date_added_end->format('Y-m-d H:i:s')) . "'";
		}

		if ($implode) {
			$sql .= " WHERE " . implode(" AND ", $implode);
		}

		$sort_data = array(
			'name',
			'a.email',
			'affiliate_group',
			'total_customers',
			'total_active_customers',
			'a.code',
			'a.status',
			'a.approved',
			'a.date_added'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY name";
		}

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC";
		} else {
			$sql .= " ASC";
		}

		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}

		$query = $this->db->query($sql);

		$store_timezone = new DateTimeZone($this->config->get('config_timezone'));

		$affiliate_data = array();

		foreach ($query->rows as $affiliate) {
			$affiliate['payment_data'] = json_decode($affiliate['payment_data'], true);

			$affiliate_data[] = $affiliate;
		}

		return $affiliate_data;
	}

	public function approve($affiliate_id) {
		$affiliate_info = $this->getAffiliate($affiliate_id);

		if ($affiliate_info) {
			$this->db->query("UPDATE `" . DB_PREFIX . "affiliate` SET approved = '1' WHERE affiliate_id = '" . (int)$affiliate_id . "'");

			$this->load->language('mail/affiliate');

			$message  = sprintf($this->language->get('text_approve_welcome'), html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8')) . "\n\n";
			$message .= $this->language->get('text_approve_login') . "\n";
			$message .= HTTP_CATALOG . 'index.php?route=affiliate/login' . "\n\n";
			$message .= $this->language->get('text_approve_services') . "\n\n";
			$message .= $this->language->get('text_approve_thanks') . "\n";
			$message .= html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8');

			$mail = new Mail();
			$mail->protocol = $this->config->get('config_mail_protocol');
			$mail->parameter = $this->config->get('config_mail_parameter');
			$mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
			$mail->smtp_username = $this->config->get('config_mail_smtp_username');
			$mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
			$mail->smtp_port = $this->config->get('config_mail_smtp_port');
			$mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');

			$mail->setTo($affiliate_info['email']);
			$mail->setFrom($this->config->get('config_email'));
			$mail->setSender(html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8'));
			$mail->setSubject(sprintf($this->language->get('text_approve_subject'), html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8')));
			$mail->setText($message);
			$mail->send();
		}
	}

	public function getAffiliatesByNewsletter() {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "affiliate` WHERE newsletter = '1' ORDER BY firstname, lastname, email");

		return $query->rows;
	}

	public function getTotalAffiliates($data = array()) {
		$sql = "SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "affiliate`";

		$implode = array();

		if (!empty($data['filter_affiliate_group_id'])) {
			$implode[] = "affiliate_group_id = '" . $this->db->escape($data['filter_affiliate_group_id']) . "'";
		}

		if (!empty($data['filter_name'])) {
			$implode[] = "CONCAT(firstname, ' ', lastname) LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
		}

		if (!empty($data['filter_email'])) {
			$implode[] = "email LIKE '" . $this->db->escape($data['filter_email']) . "%'";
		}

		if (!empty($data['filter_code'])) {
			$implode[] = "code = '" . $this->db->escape($data['filter_code']) . "'";
		}

		if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
			$implode[] = "status = '" . (int)$data['filter_status'] . "'";
		}

		if (isset($data['filter_approved']) && !is_null($data['filter_approved'])) {
			$implode[] = "approved = '" . (int)$data['filter_approved'] . "'";
		}

		if (!empty($data['filter_date_added'])) {
			$filter_date_added_start = new \DateTime($data['filter_date_added'] . ' 00:00:00', new \DateTimeZone($this->config->get('config_timezone')));
			$filter_date_added_start->setTimezone(new \DateTimeZone('UTC'));

			$filter_date_added_end = new \DateTime($data['filter_date_added'] . ' 23:59:59', new \DateTimeZone($this->config->get('config_timezone')));
			$filter_date_added_end->setTimezone(new \DateTimeZone('UTC'));

			$implode[] = "date_added >= '" . $this->db->escape($filter_date_added_start->format('Y-m-d H:i:s')) . "'";
			$implode[] = "date_added <= '" . $this->db->escape($filter_date_added_end->format('Y-m-d H:i:s')) . "'";
		}

		if ($implode) {
			$sql .= " WHERE " . implode(" AND ", $implode);
		}

		$query = $this->db->query($sql);

		return $query->row['total'];
	}

	public function getTotalAffiliatesAwaitingApproval() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "affiliate` WHERE status = '0' OR approved = '0'");

		return $query->row['total'];
	}

	public function getTotalAffiliatesByAffiliateGroupId($affiliate_group_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "affiliate` WHERE affiliate_group_id = '" . (int)$affiliate_group_id . "'");

		return $query->row['total'];
	}

	public function getTotalAffiliatesByCountryId($country_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "affiliate` WHERE country_id = '" . (int)$country_id . "'");

		return $query->row['total'];
	}

	public function getTotalAffiliatesByZoneId($zone_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "affiliate` WHERE zone_id = '" . (int)$zone_id . "'");

		return $query->row['total'];
	}

	public function addTransaction($affiliate_id, $description = '', $amount = '') {
		$affiliate_info = $this->getAffiliate($affiliate_id);

		if ($affiliate_info) {
			$this->db->query("INSERT INTO `" . DB_PREFIX . "affiliate_transaction` SET affiliate_id = '" . (int)$affiliate_id . "', description = '" . $this->db->escape($description) . "', amount = '" . (float)$amount . "', date_added = NOW()");

			$affiliate_transaction_id = $this->db->getLastId();

			$this->load->language('mail/affiliate');

			$message  = sprintf($this->language->get('text_transaction_received'), $this->currency->format($amount, $this->config->get('config_currency'))) . "\n\n";
			$message .= sprintf($this->language->get('text_transaction_total'), $this->currency->format($this->getTransactionTotal($affiliate_id), $this->config->get('config_currency')));

			$mail = new Mail();
			$mail->protocol = $this->config->get('config_mail_protocol');
			$mail->parameter = $this->config->get('config_mail_parameter');
			$mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
			$mail->smtp_username = $this->config->get('config_mail_smtp_username');
			$mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
			$mail->smtp_port = $this->config->get('config_mail_smtp_port');
			$mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');

			$mail->setTo($affiliate_info['email']);
			$mail->setFrom($this->config->get('config_email'));
			$mail->setSender(html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8'));
			$mail->setSubject(sprintf($this->language->get('text_transaction_subject'), html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8')));
			$mail->setText($message);
			$mail->send();

			return $affiliate_transaction_id;
		}
	}

	public function deleteTransaction($affiliate_transaction_id) {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "affiliate_transaction` WHERE affiliate_transaction_id = '" . (int)$affiliate_transaction_id . "'");
	}

	public function getTransactions($affiliate_id, $start = 0, $limit = 10) {
		if ($start < 0) {
			$start = 0;
		}

		if ($limit < 1) {
			$limit = 10;
		}

		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "affiliate_transaction` WHERE affiliate_id = '" . (int)$affiliate_id . "' ORDER BY date_added DESC, affiliate_transaction_id DESC LIMIT " . (int)$start . "," . (int)$limit);

		return $query->rows;
	}

	public function getTotalTransactions($affiliate_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total  FROM `" . DB_PREFIX . "affiliate_transaction` WHERE affiliate_id = '" . (int)$affiliate_id . "'");

		return $query->row['total'];
	}

	public function getTransactionTotal($affiliate_id) {
		$query = $this->db->query("SELECT SUM(amount) AS total FROM `" . DB_PREFIX . "affiliate_transaction` WHERE affiliate_id = '" . (int)$affiliate_id . "'");

		return $query->row['total'];
	}

	public function getTotalLoginAttempts($email) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "affiliate_login` WHERE `email` = '" . $this->db->escape($email) . "'");

		return $query->row;
	}

	public function deleteLoginAttempts($email) {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "affiliate_login` WHERE `email` = '" . $this->db->escape($email) . "'");
	}
	
}
