<?php
class ModelAffiliateAffiliate extends Model {
	public function addAffiliate($data) {
		$code = uniqid();

		$this->db->query("INSERT INTO `" . DB_PREFIX . "affiliate` SET affiliate_group_id = '" . (int)$this->config->get('config_affiliate_group_id') . "', parent_id = '" . (int)$data['parent_id'] . "', firstname = '" . $this->db->escape($data['firstname']) . "', lastname = '" . $this->db->escape($data['lastname']) . "', email = '" . $this->db->escape($data['email']) . "', timezone = '".$this->config->get('config_timezone')."', telephone = '" . $this->db->escape($data['telephone']) . "', salt = '" . $this->db->escape($salt = token(9)) . "', password = '" . $this->db->escape(sha1($salt . sha1($salt . sha1($data['password'])))) . "', company = '" . $this->db->escape($data['company']) . "', website = '" . $this->db->escape($data['website']) . "', address_1 = '" . $this->db->escape($data['address_1']) . "', address_2 = '" . $this->db->escape($data['address_2']) . "', city = '" . $this->db->escape($data['city']) . "', postcode = '" . $this->db->escape($data['postcode']) . "', country_id = '" . (int)$data['country_id'] . "', zone_id = '" . (int)$data['zone_id'] . "', code = '" . $this->db->escape($code) . "', tax = '" . $this->db->escape($data['tax']) . "', payment = '" . $this->db->escape($data['payment']) . "', payment_data = '" . $this->db->escape(json_encode($data['payment_data'])) . "', status = '1', approved = '" . (int)!$this->config->get('config_affiliate_approval') . "', date_added = NOW()");

		$affiliate_id = $this->db->getLastId();

		$level = 0;

		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "affiliate_path` WHERE affiliate_id = '" . (int)$data['parent_id'] . "' ORDER BY `level` ASC");

		foreach ($query->rows as $result) {
			$this->db->query("INSERT INTO `" . DB_PREFIX . "affiliate_path` SET affiliate_id = '" . (int)$affiliate_id . "', path_id = '" . (int)$result['path_id'] . "', level = '" . (int)$level . "'");

			$level++;
		}

		$this->db->query("INSERT INTO `" . DB_PREFIX . "affiliate_path` SET affiliate_id = '" . (int)$affiliate_id . "', path_id = '" . (int)$affiliate_id . "', level = '" . (int)$level . "'");

		// send out affiliate welcome email
		$this->load->model('localisation/language');
		$language_info = $this->model_localisation_language->getLanguage($this->config->get('config_language_id'));

		if ($language_info) {
			$language_code      = $language_info['code'];
			$language_directory = $language_info['directory'];
		} else {
			$language_code      = '';
			$language_directory = '';
		}

		$language = new Language($language_directory);
		$language->load($language_directory);

		$mail_data = $language->load('mail/affiliate');

		$mail_data['text_commission'] = sprintf($language->get('text_commission'), $this->config->get('config_affiliate_commission'));
		$mail_data['text_footer']     = sprintf($language->get('text_footer'), date('Y'), $this->config->get('config_name'));
		$mail_data['text_welcome']    = sprintf($language->get('text_welcome'), $this->config->get('config_name'));

		$mail_data['href_dashboard'] = $this->url->link('affiliate/dashboard', '', true);
		$mail_data['href_home']      = $this->url->link('common/home');
		$mail_data['href_tracking']  = $this->url->link('common/home', 'tracking=' . $code, true);

		$mail_data['logo']      = $this->config->get('config_url') . 'image/' . $this->config->get('config_email_logo');
		$mail_data['site_name'] = $this->config->get('config_name');

		$mail_data['firstname'] = $data['firstname'];
		$mail_data['lastname']  = $data['lastname'];

		$mail_data['show_approval'] = $this->config->get('config_affiliate_approval');

		$mail = new Mail();
		$mail->protocol      = $this->config->get('config_mail_protocol');
		$mail->parameter     = $this->config->get('config_mail_parameter');
		$mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
		$mail->smtp_username = $this->config->get('config_mail_smtp_username');
		$mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
		$mail->smtp_port     = $this->config->get('config_mail_smtp_port');
		$mail->smtp_timeout  = $this->config->get('config_mail_smtp_timeout');

		$mail->setFrom($this->config->get('config_email'));
		$mail->setSender(html_entity_decode($mail_data['site_name'], ENT_QUOTES, 'UTF-8'));
		$mail->setTo($data['email']);
		$mail->setSubject(html_entity_decode(sprintf($language->get('subject_register'), $this->config->get('config_name')), ENT_QUOTES, 'UTF-8'));
		$mail->setHtml($this->load->view('mail/affiliate_register', $mail_data));
		$mail->setText($this->load->view('mail/affiliate_register_text', array_map('strip_tags', $mail_data)));

		$mail->send();

		// Send to main admin email if new affiliate email is enabled
		if ($this->config->get('config_affiliate_mail')) {
			$message  = $this->language->get('text_signup') . "\n\n";
			$message .= $this->language->get('text_firstname') . ' ' . $data['firstname'] . "\n";
			$message .= $this->language->get('text_lastname') . ' ' . $data['lastname'] . "\n";

			if ($data['website']) {
				$message .= $this->language->get('text_website') . ' ' . $data['website'] . "\n";
			}

			if ($data['company']) {
				$message .= $this->language->get('text_company') . ' '  . $data['company'] . "\n";
			}

			$message .= $this->language->get('text_email') . ' '  .  $data['email'] . "\n";
			$message .= $this->language->get('text_telephone') . ' ' . $data['telephone'] . "\n";

			$mail->setTo($this->config->get('config_email'));
			$mail->setSubject(html_entity_decode($this->language->get('subject_register_admin'), ENT_QUOTES, 'UTF-8'));
			$mail->setHtml('');
			$mail->setText($message);
			$mail->send();

			// Send to additional alert emails if new affiliate email is enabled
			$emails = explode(',', $this->config->get('config_mail_alert'));

			foreach ($emails as $email) {
				if (utf8_strlen($email) > 0 && filter_var($email, FILTER_VALIDATE_EMAIL)) {
					$mail->setTo($email);
					$mail->send();
				}
			}
		}

		return $affiliate_id;
	}

	public function editAffiliate($data) {
		$affiliate_id = $this->affiliate->getId();

		$this->db->query("UPDATE `" . DB_PREFIX . "affiliate` SET firstname = '" . $this->db->escape($data['firstname']) . "', lastname = '" . $this->db->escape($data['lastname']) . "', email = '" . $this->db->escape($data['email']) . "', timezone = '" . $this->db->escape($data['timezone']) . "', telephone = '" . $this->db->escape($data['telephone']) . "', company = '" . $this->db->escape($data['company']) . "', website = '" . $this->db->escape($data['website']) . "', address_1 = '" . $this->db->escape($data['address_1']) . "', address_2 = '" . $this->db->escape($data['address_2']) . "', city = '" . $this->db->escape($data['city']) . "', postcode = '" . $this->db->escape($data['postcode']) . "', country_id = '" . (int)$data['country_id'] . "', zone_id = '" . (int)$data['zone_id'] . "' WHERE affiliate_id = '" . (int)$affiliate_id . "'");
	}

	public function editPayment($data) {
		$affiliate_id = $this->affiliate->getId();

		$this->db->query("UPDATE `" . DB_PREFIX . "affiliate` SET tax = '" . $this->db->escape($data['tax']) . "', payment = '" . $this->db->escape($data['payment']) . "', payment_data = '" . $this->db->escape(json_encode($data['payment_data'])) . "' WHERE affiliate_id = '" . (int)$affiliate_id . "'");
	}

	public function editPassword($email, $password) {
		$affiliate_id = $this->affiliate->getId();

		$this->db->query("UPDATE `" . DB_PREFIX . "affiliate` SET salt = '" . $this->db->escape($salt = token(9)) . "', password = '" . $this->db->escape(sha1($salt . sha1($salt . sha1($password)))) . "' WHERE LOWER(email) = '" . $this->db->escape(utf8_strtolower($email)) . "'");
	}

	public function getAffiliate($affiliate_id) {
		$query = $this->db->query("SELECT *, (SELECT GROUP_CONCAT(CONCAT(a2.firstname, ' ', a2.lastname) ORDER BY level SEPARATOR '&nbsp;&nbsp;&gt;&nbsp;&nbsp;') FROM `" . DB_PREFIX . "affiliate_path` ap LEFT JOIN `" . DB_PREFIX . "affiliate` a2 ON (a2.affiliate_id = ap.path_id AND ap.affiliate_id != ap.path_id) WHERE ap.affiliate_id = a.affiliate_id GROUP BY ap.affiliate_id) AS path FROM `" . DB_PREFIX . "affiliate` a WHERE a.affiliate_id = '" . (int)$affiliate_id . "'");

		if ($query->row) {
			$affiliate_data = $query->row;

			$affiliate_data['payment_data'] = json_decode($affiliate_data['payment_data'], true);
		} else {
			$affiliate_data = array();
		}

		return $affiliate_data;
	}

	public function getAffiliateByEmail($email) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "affiliate` WHERE LOWER(email) = '" . $this->db->escape(utf8_strtolower($email)) . "'");

		return $query->row;
	}

	public function getAffiliateByCode($code) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "affiliate` WHERE code = '" . $this->db->escape($code) . "'");

		return $query->row;
	}

	public function getAffiliatePath($affiliate_id) {
		$affiliate_path_data = array();

		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "affiliate_path` WHERE affiliate_id = '" . (int)$affiliate_id . "' ORDER BY level ASC");

		foreach ($query->rows as $affiliate_path) {
			$affiliate_path_data[] = $affiliate_path['path_id'];
		}

		return $affiliate_path_data;
	}

	public function getTotalAffiliatesByEmail($email) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "affiliate` WHERE LOWER(email) = '" . $this->db->escape(utf8_strtolower($email)) . "'");

		return $query->row['total'];
	}
}