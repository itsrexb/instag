<?php
use GeoIp2\Database\Reader;

class ControllerCustomerCustomer extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('customer/customer');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('customer/customer');

		$this->getList();
	}

	public function add() {
		$this->load->language('customer/customer');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('customer/customer');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$customer_id = $this->model_customer_customer->addCustomer($this->request->post);

			// update any enabled marketing extensions
			$this->load->model('extension/extension');
			$marketing_extensions = $this->model_extension_extension->getInstalled('marketing');

			foreach ($marketing_extensions as $marketing) {
				if ($this->config->get($marketing . '_status')) {
					$this->{$marketing} = new $marketing($this->registry);

					$this->load->model('extension/marketing/' . $marketing);
					$this->{'model_extension_marketing_' . $marketing}->updateCustomer($customer_id, $this->request->post);
				}
			}

			$this->session->data['success'] = $this->language->get('success_insert');

			$this->response->redirect($this->url->link('customer/customer', $this->getUrl(), true));
		}

		$this->getForm();
	}

	public function edit() {
		$this->load->language('customer/customer');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('customer/customer');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_customer_customer->editCustomer($this->request->get['customer_id'], $this->request->post);

			// update any enabled marketing extensions
			$this->load->model('extension/extension');
			$marketing_extensions = $this->model_extension_extension->getInstalled('marketing');

			foreach ($marketing_extensions as $marketing) {
				if ($this->config->get($marketing . '_status')) {
					$this->{$marketing} = new $marketing($this->registry);

					$this->load->model('extension/marketing/' . $marketing);
					$this->{'model_extension_marketing_' . $marketing}->updateCustomer($this->request->get['customer_id'], $this->request->post);
				}
			}

			$this->session->data['success'] = $this->language->get('success_update');

			$this->response->redirect($this->url->link('customer/customer', $this->getUrl(), true));
		}

		$this->getForm();
	}

	public function delete() {
		$this->load->language('customer/customer');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('customer/customer');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $customer_id) {
				$this->model_customer_customer->deleteCustomer($customer_id);
			}

			$this->session->data['success'] = sprintf($this->language->get('success_delete'), count($this->request->post['selected']));

			$this->response->redirect($this->url->link('customer/customer', $this->getUrl(), true));
		}

		$this->getList();
	}

	public function approve() {
		$this->load->language('customer/customer');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('customer/customer');

		$customers = array();

		if (isset($this->request->post['selected'])) {
			$customers = $this->request->post['selected'];
		} else if (isset($this->request->get['customer_id'])) {
			$customers[] = $this->request->get['customer_id'];
		}

		if ($customers && $this->validateApprove()) {
			$this->model_customer_customer->approve($this->request->get['customer_id']);

			$this->session->data['success'] = $this->language->get('success_approve');

			$this->response->redirect($this->url->link('customer/customer', $this->getUrl(), true));
		}

		$this->getList();
	}

	public function unlock() {
		$this->load->language('customer/customer');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('customer/customer');

		if (isset($this->request->get['email']) && $this->validateUnlock()) {
			$this->model_customer_customer->deleteLoginAttempts($this->request->get['email']);

			$this->session->data['success'] = $this->language->get('success_unlock');

			$this->response->redirect($this->url->link('customer/customer', $this->getUrl(), true));
		}

		$this->getList();
	}

	public function export() {
		$this->load->language('customer/customer');

		if ($this->validateExport()) {
			$this->load->model('customer/customer');

			$limit = 5000;
			$page  = 0;

			$export = new Export('csv', 'customers-export');

			do {
				$results = $this->model_customer_customer->getCustomers(array(
					'filter_name'              => (isset($this->request->get['filter_name']) ? $this->request->get['filter_name'] : null),
					'filter_email'             => (isset($this->request->get['filter_email']) ? $this->request->get['filter_email'] : null),
					'filter_country'           => (isset($this->request->get['filter_country']) ? $this->request->get['filter_country'] : null),
					'filter_account_status'    => (isset($this->request->get['filter_account_status']) ? $this->request->get['filter_account_status'] : null),
					'filter_customer_group_id' => (isset($this->request->get['filter_customer_group_id']) ? $this->request->get['filter_customer_group_id'] : null),
					'filter_affiliate_id'      => (isset($this->request->get['filter_affiliate_id']) ? $this->request->get['filter_affiliate_id'] : null),
					'filter_ext_aff_id'        => (isset($this->request->get['filter_ext_aff_id']) ? $this->request->get['filter_ext_aff_id'] : null),
					'filter_date_added_start'  => (isset($this->request->get['filter_date_added_start']) ? $this->request->get['filter_date_added_start'] : null),
					'filter_date_added_end'    => (isset($this->request->get['filter_date_added_end']) ? $this->request->get['filter_date_added_end'] : null),
					'sort'                     => (isset($this->request->get['sort']) ? $this->request->get['sort'] : 'name'),
					'order'                    => (isset($this->request->get['order']) ? $this->request->get['order'] : 'ASC'),
					'start'                    => $page * $limit,
					'limit'                    => $limit
				));

				if ($results) {
					foreach ($results as $result) {
						if ($result['total_accounts']) {
							if ($result['active_accounts']) {
								// active_accounts > 0
								if ($result['active_recurring_accounts']) {
									// active_recurring_accounts > 0
									$account_status = $this->language->get('text_active');
								} else {
									// active_recurring_accounts = 0
									if ($result['inactive_recurring_accounts']) {
										// inactive_recurring_accounts > 0
										$account_status = $this->language->get('text_inactive');
									} else {
										// inactive_recurring_accounts = 0
										$account_status = $this->language->get('text_free_trial');
									}
								}
							} else {
								// active_accounts = 0
								if ($result['kickoff_accounts']) {
									// kickoff_accounts > 0
									$account_status = $this->language->get('text_kickoff');
								} else {
									// kickoff_accounts = 0
									if ($result['total_revenue']) {
										$account_status = $this->language->get('text_expired_paid');
									} else {
										$account_status = $this->language->get('text_expired');
									}
								}
							}
						} else {
							// total_accounts = 0
							if ($result['deleted_accounts']) {
								// deleted_accounts > 0
								if ($result['total_revenue']) {
									$account_status = $this->language->get('text_deleted_paid');
								} else {
									$account_status = $this->language->get('text_deleted');
								}
							} else {
								// deleted_accounts = 0
								$account_status = $this->language->get('text_new');
							}
						}

						$data = array(
							'customer_id'      => $result['customer_id'],
							'firstname'        => $result['firstname'],
							'lastname'         => $result['lastname'],
							'email'            => $result['email'],
							'telephone'        => $result['telephone'],
							'country'          => $result['country'],
							'language_code'    => $result['language_code'],
							'currency_code'    => $result['currency_code'],
							'timezone'         => $result['timezone'],
							'account_status'   => $account_status,
							'total_accounts'   => $result['total_accounts'],
							'deleted_accounts' => $result['deleted_accounts'],
							'total_revenue'    => $this->currency->format($result['total_revenue']),
							'plan'             => $result['plan'],
							'customer_group'   => $result['customer_group'],
							'affiliate'        => str_replace(array('&nbsp;', '&gt;'), array(' ', '>'), $result['affiliate']),
							'date_affiliate'   => $result['date_affiliate'],
							'managed_billing'  => $result['managed_billing'],
							'tracking'         => $result['tracking'],
							'ext_aff_id'       => $result['ext_aff_id'],
							'discount'         => $result['discount'],
							'custom_field'     => $result['custom_field'],
							'ip'               => $result['ip'],
							'status'           => $result['status'],
							'date_added'       => $result['date_added']
						);

						if (!$export->hasHeader()) {
							$export->addHeader(array_keys($data));
						}

						$export->write($data);
					}
				}

				$page++;
			} while (count($results) == $limit);

			$export->close();
		}

		$this->response->redirect($this->url->link('customer/customer', $this->getUrl(), true));
	}

	protected function getList() {
		$data = $this->language->all();

		if (isset($this->request->get['filter_name'])) {
			$filter_name = trim($this->request->get['filter_name']);
		} else {
			$filter_name = null;
		}

		if (isset($this->request->get['filter_email'])) {
			$filter_email = trim($this->request->get['filter_email']);
		} else {
			$filter_email = null;
		}

		if (isset($this->request->get['filter_country'])) {
			$filter_country = trim($this->request->get['filter_country']);
		} else {
			$filter_country = null;
		}

		if (isset($this->request->get['filter_account_status'])) {
			$filter_account_status = trim($this->request->get['filter_account_status']);
		} else {
			$filter_account_status = null;
		}

		if (isset($this->request->get['filter_customer_group_id'])) {
			$filter_customer_group_id = trim($this->request->get['filter_customer_group_id']);
		} else {
			$filter_customer_group_id = null;
		}

		if (isset($this->request->get['filter_affiliate_id'])) {
			$filter_affiliate_id = trim($this->request->get['filter_affiliate_id']);

			$this->load->model('affiliate/affiliate');

			$affiliate_info = $this->model_affiliate_affiliate->getAffiliate($filter_affiliate_id);

			if ($affiliate_info) {
				if ($affiliate_info['path']) {
					$filter_affiliate = $affiliate_info['path'] . '&nbsp;&nbsp;&gt;&nbsp;&nbsp;' . $affiliate_info['firstname'] . ' ' . $affiliate_info['lastname'];
				} else {
					$filter_affiliate = $affiliate_info['firstname'] . ' ' . $affiliate_info['lastname'];
				}
			} else {
				$filter_affiliate = null;
			}
		} else {
			$filter_affiliate_id = null;
			$filter_affiliate    = null;
		}

		if (isset($this->request->get['filter_ext_aff_id'])) {
			$filter_ext_aff_id = trim($this->request->get['filter_ext_aff_id']);
		} else {
			$filter_ext_aff_id = null;
		}
		
		if (isset($this->request->get['filter_has_affiliate'])) {
			$filter_has_affiliate = trim($this->request->get['filter_has_affiliate']);
		} else {
			$filter_has_affiliate = null;
		}		

		if (isset($this->request->get['filter_has_ext_aff_id'])) {
			$filter_has_ext_aff_id = trim($this->request->get['filter_has_ext_aff_id']);
		} else {
			$filter_has_ext_aff_id = null;
		}
		
		if (isset($this->request->get['filter_date_added_start'])) {
			$filter_date_added_start = trim($this->request->get['filter_date_added_start']);
		} else {
			$filter_date_added_start = null;
		}

		if (isset($this->request->get['filter_date_added_end'])) {
			$filter_date_added_end = trim($this->request->get['filter_date_added_end']);
		} else {
			$filter_date_added_end = null;
		}
		
		if (isset($this->request->get['filter_date_added'])) {
			$filter_date_added = trim($this->request->get['filter_date_added']);
		} else {
			$filter_date_added = null;
		}

		if (isset($this->request->get['sort'])) {
			$sort = trim($this->request->get['sort']);
		} else {
			$sort = 'name';
		}

		if (isset($this->request->get['order'])) {
			$order = trim($this->request->get['order']);
		} else {
			$order = 'ASC';
		}

		if (isset($this->request->get['page'])) {
			$page = trim($this->request->get['page']);
		} else {
			$page = 1;
		}

		$url = $this->getUrl();

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('customer/customer', $url, true)
		);

		$data['add']    = $this->url->link('customer/customer/add', $url, true);
		$data['delete'] = $this->url->link('customer/customer/delete', $url, true);

		if ($this->user->hasPermission('access', 'common/export')) {
			$data['export'] = $this->url->link('customer/customer/export', $url, true);
		} else {
			$data['export'] = '';
		}

		$data['token'] = $this->session->data['token'];

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		if (isset($this->request->post['selected'])) {
			$data['selected'] = (array)$this->request->post['selected'];
		} else {
			$data['selected'] = array();
		}

		$data['customers'] = array();

		$filter_data = array(
			'filter_name'              => $filter_name,
			'filter_email'             => $filter_email,
			'filter_country'           => $filter_country,
			'filter_account_status'    => $filter_account_status,
			'filter_customer_group_id' => $filter_customer_group_id,
			'filter_affiliate_id'      => $filter_affiliate_id,
			'filter_ext_aff_id'        => $filter_ext_aff_id,
			'filter_has_affiliate'     => $filter_has_affiliate,
			'filter_has_ext_aff_id'    => $filter_has_ext_aff_id,
			'filter_date_added'        => $filter_date_added,
			'filter_date_added_start'  => $filter_date_added_start,
			'filter_date_added_end'    => $filter_date_added_end,			
			'sort'                     => $sort,
			'order'                    => $order,
			'start'                    => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit'                    => $this->config->get('config_limit_admin')
		);

		$customer_total = $this->model_customer_customer->getTotalCustomers($filter_data);

		$results = $this->model_customer_customer->getCustomers($filter_data);

		foreach ($results as $result) {
			$login_info = $this->model_customer_customer->getTotalLoginAttempts($result['email']);

			if ($login_info && $login_info['total'] >= $this->config->get('config_login_attempts')) {
				$unlock = $this->url->link('customer/customer/unlock', $url . '&email=' . $result['email'], true);
			} else {
				$unlock = '';
			}

			if ($result['total_accounts']) {
				if ($result['active_accounts']) {
					// active_accounts > 0
					if ($result['active_recurring_accounts']) {
						// active_recurring_accounts > 0
						$account_status = $this->language->get('text_active');
					} else {
						// active_recurring_accounts = 0
						if ($result['inactive_recurring_accounts']) {
							// inactive_recurring_accounts > 0
							$account_status = $this->language->get('text_inactive');
						} else {
							// inactive_recurring_accounts = 0
							$account_status = $this->language->get('text_free_trial');
						}
					}
				} else {
					// active_accounts = 0
					if ($result['kickoff_accounts']) {
						// kickoff_accounts > 0
						$account_status = $this->language->get('text_kickoff');
					} else {
						// kickoff_accounts = 0
						if ($result['total_revenue']) {
							$account_status = $this->language->get('text_expired_paid');
						} else {
							$account_status = $this->language->get('text_expired');
						}
					}
				}
			} else {
				// total_accounts = 0
				if ($result['deleted_accounts']) {
					// deleted_accounts > 0
					if ($result['total_revenue']) {
						$account_status = $this->language->get('text_deleted_paid');
					} else {
						$account_status = $this->language->get('text_deleted');
					}
				} else {
					// deleted_accounts = 0
					$account_status = $this->language->get('text_new');
				}
			}

			$data['customers'][] = array(
				'customer_id'      => $result['customer_id'],
				'name'             => $result['name'],
				'email'            => $result['email'],
				'country'          => $result['country'],
				'account_status'   => $account_status,
				'total_accounts'   => $result['total_accounts'],
				'deleted_accounts' => $result['deleted_accounts'],
				'total_revenue'    => $this->currency->format($result['total_revenue']),
				'plan'             => $result['plan'],
				'customer_group'   => $result['customer_group'],
				'affiliate'        => $result['affiliate'],
				'ext_aff_id'       => $result['ext_aff_id'],
				'date_added'       => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
				'unlock'           => $unlock,
				'edit'             => $this->url->link('customer/customer/edit', $url . '&customer_id=' . $result['customer_id'], true),
				'href_accounts'    => $this->url->link('customer/account', 'token=' . $this->session->data['token'] . '&filter_customer_id=' . $result['customer_id'], true)
			);
		}

		$data['account_statuses'] = array(
			'new'        => $this->language->get('text_new'),
			'kickoff'    => $this->language->get('text_kickoff'),
			'free_trial' => $this->language->get('text_free_trial'),
			'active'     => $this->language->get('text_active'),
			'inactive'   => $this->language->get('text_inactive'),
			'expired'    => $this->language->get('text_expired'),
			'deleted'    => $this->language->get('text_deleted')
		);

		$url = $this->getUrl(array('sort', 'order'));

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		$data['sort_name']             = $this->url->link('customer/customer', $url . '&sort=name', true);
		$data['sort_email']            = $this->url->link('customer/customer', $url . '&sort=c.email', true);
		$data['sort_country']          = $this->url->link('customer/customer', $url . '&sort=country', true);
		$data['sort_customer_group']   = $this->url->link('customer/customer', $url . '&sort=customer_group', true);
		$data['sort_total_accounts']   = $this->url->link('customer/customer', $url . '&sort=total_accounts', true);
		$data['sort_deleted_accounts'] = $this->url->link('customer/customer', $url . '&sort=deleted_accounts', true);
		$data['sort_total_revenue']    = $this->url->link('customer/customer', $url . '&sort=total_revenue', true);
		$data['sort_plan']             = $this->url->link('customer/customer', $url . '&sort=plan', true);
		$data['sort_affiliate']        = $this->url->link('customer/customer', $url . '&sort=affiliate', true);
		$data['sort_ext_aff_id']       = $this->url->link('customer/customer', $url . '&sort=ext_aff_id', true);
		$data['sort_ip']               = $this->url->link('customer/customer', $url . '&sort=c.ip', true);
		$data['sort_date_added']       = $this->url->link('customer/customer', $url . '&sort=c.date_added', true);

		$url = $this->getUrl(array('page'));

		$pagination = new Pagination();
		$pagination->total = $customer_total;
		$pagination->page  = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url   = $this->url->link('customer/customer', $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($customer_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($customer_total - $this->config->get('config_limit_admin'))) ? $customer_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $customer_total, ceil($customer_total / $this->config->get('config_limit_admin')));

		$data['filter_name']              = $filter_name;
		$data['filter_email']             = $filter_email;
		$data['filter_country']           = $filter_country;
		$data['filter_account_status']    = $filter_account_status;
		$data['filter_customer_group_id'] = $filter_customer_group_id;
		$data['filter_affiliate_id']      = $filter_affiliate_id;
		$data['filter_affiliate']         = $filter_affiliate;
		$data['filter_ext_aff_id']        = $filter_ext_aff_id;
		$data['filter_date_added']        = $filter_date_added;
		$data['filter_date_added_start']  = $filter_date_added_start;
		$data['filter_date_added_end']    = $filter_date_added_end;

		$this->load->model('customer/customer_group');

		$data['customer_groups'] = $this->model_customer_customer_group->getCustomerGroups();

		$this->load->model('setting/store');

		$data['stores'] = $this->model_setting_store->getStores();

		$data['sort']  = $sort;
		$data['order'] = $order;

		$data['header']      = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer']      = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('customer/customer_list', $data));
	}

	protected function getForm() {
		$data = $this->language->all();

		$data['text_form'] = !isset($this->request->get['customer_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');

		$data['token'] = $this->session->data['token'];

		if (isset($this->request->get['customer_id'])) {
			$data['customer_id'] = $this->request->get['customer_id'];
		} else {
			$data['customer_id'] = 0;
		}

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['firstname'])) {
			$data['error_firstname'] = $this->error['firstname'];
		} else {
			$data['error_firstname'] = '';
		}

		if (isset($this->error['lastname'])) {
			$data['error_lastname'] = $this->error['lastname'];
		} else {
			$data['error_lastname'] = '';
		}

		if (isset($this->error['email'])) {
			$data['error_email'] = $this->error['email'];
		} else {
			$data['error_email'] = '';
		}

		if (isset($this->error['password'])) {
			$data['error_password'] = $this->error['password'];
		} else {
			$data['error_password'] = '';
		}

		if (isset($this->error['confirm'])) {
			$data['error_confirm'] = $this->error['confirm'];
		} else {
			$data['error_confirm'] = '';
		}

		if (isset($this->error['custom_field'])) {
			$data['error_custom_field'] = $this->error['custom_field'];
		} else {
			$data['error_custom_field'] = array();
		}

		$url = $this->getUrl();

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('customer/customer', $url, true)
		);

		$data['text_system_default_affiliate_commission_count'] = sprintf($this->language->get('text_system_default'), (int)$this->config->get('config_affiliate_commission_count'));

		if (!isset($this->request->get['customer_id'])) {
			$data['action'] = $this->url->link('customer/customer/add', $url, true);
		} else {
			$data['action'] = $this->url->link('customer/customer/edit', $url . '&customer_id=' . $this->request->get['customer_id'], true);
		}

		$data['cancel'] = $this->url->link('customer/customer', $url, true);

		if (isset($this->request->get['customer_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$customer_info = $this->model_customer_customer->getCustomer($this->request->get['customer_id']);

			// sync local customer with any installed marketing extensions
			$this->load->model('extension/extension');
			$marketing_extensions = $this->model_extension_extension->getInstalled('marketing');

			foreach ($marketing_extensions as $marketing) {
				if ($this->config->get($marketing . '_status')) {
					$this->{$marketing} = new $marketing($this->registry);

					$this->load->model('extension/marketing/' . $marketing);
					$this->{'model_extension_marketing_' . $marketing}->localSync($this->request->get['customer_id'], $customer_info);
				}
			}
		}

		$this->load->model('customer/customer_group');

		$data['customer_groups'] = $this->model_customer_customer_group->getCustomerGroups();

		if (isset($this->request->post['customer_group_id'])) {
			$data['customer_group_id'] = $this->request->post['customer_group_id'];
		} else if (!empty($customer_info)) {
			$data['customer_group_id'] = $customer_info['customer_group_id'];
		} else {
			$data['customer_group_id'] = $this->config->get('config_customer_group_id');
		}

		if (isset($this->request->post['firstname'])) {
			$data['firstname'] = $this->request->post['firstname'];
		} else if (!empty($customer_info)) {
			$data['firstname'] = $customer_info['firstname'];
		} else {
			$data['firstname'] = '';
		}

		if (isset($this->request->post['lastname'])) {
			$data['lastname'] = $this->request->post['lastname'];
		} else if (!empty($customer_info)) {
			$data['lastname'] = $customer_info['lastname'];
		} else {
			$data['lastname'] = '';
		}

		if (isset($this->request->post['email'])) {
			$data['email'] = $this->request->post['email'];
		} else if (!empty($customer_info)) {
			$data['email'] = $customer_info['email'];
		} else {
			$data['email'] = '';
		}

		if (isset($this->request->post['telephone'])) {
			$data['telephone'] = $this->request->post['telephone'];
		} else if (!empty($customer_info)) {
			$data['telephone'] = $customer_info['telephone'];
		} else {
			$data['telephone'] = '';
		}

		// Custom Fields
		$this->load->model('customer/custom_field');

		$data['custom_fields'] = array();

		$filter_data = array(
			'sort'  => 'cf.sort_order',
			'order' => 'ASC'
		);

		$custom_fields = $this->model_customer_custom_field->getCustomFields($filter_data);

		foreach ($custom_fields as $custom_field) {
			$data['custom_fields'][] = array(
				'custom_field_id'    => $custom_field['custom_field_id'],
				'custom_field_value' => $this->model_customer_custom_field->getCustomFieldValues($custom_field['custom_field_id']),
				'name'               => $custom_field['name'],
				'value'              => $custom_field['value'],
				'type'               => $custom_field['type'],
				'location'           => $custom_field['location'],
				'sort_order'         => $custom_field['sort_order']
			);
		}

		if (isset($this->request->post['custom_field'])) {
			$data['account_custom_field'] = $this->request->post['custom_field'];
		} else if (!empty($customer_info)) {
			$data['account_custom_field'] = json_decode($customer_info['custom_field'], true);
		} else {
			$data['account_custom_field'] = array();
		}

		if (isset($this->request->post['discount'])) {
			$data['discount'] = $this->request->post['discount'];
		} else if (!empty($customer_info)) {
			$data['discount'] = $customer_info['discount'];
		} else {
			$data['discount'] = '';
		}

		if (isset($this->request->post['newsletter'])) {
			$data['newsletter'] = $this->request->post['newsletter'];
		} else if (!empty($customer_info)) {
			$data['newsletter'] = $customer_info['newsletter'];
		} else {
			$data['newsletter'] = '';
		}

		if (isset($this->request->post['managed_billing'])) {
			$data['managed_billing'] = $this->request->post['managed_billing'];
		} else if (!empty($customer_info)) {
			$data['managed_billing'] = $customer_info['managed_billing'];
		} else {
			$data['managed_billing'] = '';
		}

		$this->load->model('localisation/country');

		$data['countries'] = $this->model_localisation_country->getCountries();

		if (isset($this->request->post['country_id'])) {
			$data['country_id'] = $this->request->post['country_id'];
		} else if (!empty($customer_info)) {
			$data['country_id'] = $customer_info['country_id'];
		} else {
			$data['country_id'] = '';
		}

		$this->load->model('localisation/language');

		$language = $this->model_localisation_language->getLanguages();
		foreach($language as $key => $lang){
			$data['language'][] = array('language_id'=>$lang['language_id'], 'code' => $lang['code'],'name'=> $lang['name']);
		}

		if (isset($this->request->post['language_code'])) {
			$data['language_code'] = $this->request->post['language_code'];
		} else if (!empty($customer_info)) {
			$data['language_code'] = $customer_info['language_code'];
		} else {
			$data['language_code'] = '';
		}

		$this->load->model('localisation/currency');

		$data['currencies'] = $this->model_localisation_currency->getCurrencies();

		if (isset($this->request->post['currency_code'])) {
			$data['currency_code'] = $this->request->post['currency_code'];
		} else if (!empty($customer_info)) {
			$data['currency_code'] = $customer_info['currency_code'];
		} else {
			$data['currency_code'] = '';
		}

		if (isset($this->request->post['affiliate'])) {
			$data['affiliate'] = $this->request->post['affiliate'];
		} else if (!empty($customer_info)) {
			$data['affiliate'] = $customer_info['affiliate'];
		} else {
			$data['affiliate'] = '';
		}

		if (isset($this->request->post['affiliate_id'])) {
			$data['affiliate_id'] = $this->request->post['affiliate_id'];
		} else if (!empty($customer_info)) {
			$data['affiliate_id'] = $customer_info['affiliate_id'];
		} else {
			$data['affiliate_id'] = 0;
		}

		if (isset($this->request->post['affiliate_commission_count'])) {
			$data['affiliate_commission_count'] = $this->request->post['affiliate_commission_count'];
		} else if (!empty($customer_info)) {
			$data['affiliate_commission_count'] = $customer_info['affiliate_commission_count'];
		} else {
			$data['affiliate_commission_count'] = -1;
		}

		if (isset($this->request->post['ext_aff_id'])) {
			$data['ext_aff_id'] = $this->request->post['ext_aff_id'];
		} else if (!empty($customer_info)) {
			$data['ext_aff_id'] = $customer_info['ext_aff_id'];
		} else {
			$data['ext_aff_id'] = '';
		}

		if (isset($this->request->post['status'])) {
			$data['status'] = $this->request->post['status'];
		} else if (!empty($customer_info)) {
			$data['status'] = $customer_info['status'];
		} else {
			$data['status'] = true;
		}

		if (isset($this->request->post['approved'])) {
			$data['approved'] = $this->request->post['approved'];
		} else if (!empty($customer_info)) {
			$data['approved'] = $customer_info['approved'];
		} else {
			$data['approved'] = true;
		}

		if (isset($this->request->post['safe'])) {
			$data['safe'] = $this->request->post['safe'];
		} else if (!empty($customer_info)) {
			$data['safe'] = $customer_info['safe'];
		} else {
			$data['safe'] = 0;
		}

		if (isset($this->request->post['password'])) {
			$data['password'] = $this->request->post['password'];
		} else {
			$data['password'] = '';
		}

		if (isset($this->request->post['confirm'])) {
			$data['confirm'] = $this->request->post['confirm'];
		} else {
			$data['confirm'] = '';
		}

		if (isset($this->request->post['date_added'])) {
			$data['date_added'] = $this->request->post['date_added'];
		} else if (!empty($customer_info)) {
			$data['date_added'] = date($this->language->get('datetime_format'), strtotime($customer_info['date_added']));
		} else {
			$data['date_added'] = '';
		}

		$this->document->addStyle('view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.css');
		$this->document->addScript('view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.js');

		$data['header']      = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer']      = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('customer/customer_form', $data));
	}

	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'customer/customer')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		/*if ($this->config->get('config_instaghive_environment') == 'staging' && !isset($this->request->get['customer_id'])) {
			$this->error['warning'] = $this->language->get('error_disabled');
		}*/

		if ((utf8_strlen($this->request->post['firstname']) < 1) || (utf8_strlen(trim($this->request->post['firstname'])) > 32)) {
			$this->error['firstname'] = $this->language->get('error_firstname');
		}

		if ((utf8_strlen($this->request->post['lastname']) < 1) || (utf8_strlen(trim($this->request->post['lastname'])) > 32)) {
			$this->error['lastname'] = $this->language->get('error_lastname');
		}

		if ((utf8_strlen($this->request->post['email']) > 96) || !filter_var($this->request->post['email'], FILTER_VALIDATE_EMAIL)) {
			$this->error['email'] = $this->language->get('error_email');
		}

		$customer_info = $this->model_customer_customer->getCustomerByEmail($this->request->post['email']);

		if (!isset($this->request->get['customer_id'])) {
			if ($customer_info) {
				$this->error['warning'] = $this->language->get('error_exists');
			}
		} else {
			if ($customer_info && ($this->request->get['customer_id'] != $customer_info['customer_id'])) {
				$this->error['warning'] = $this->language->get('error_exists');
			}
		}

		// Custom field validation
		$this->load->model('customer/custom_field');

		$custom_fields = $this->model_customer_custom_field->getCustomFields(array('filter_customer_group_id' => $this->request->post['customer_group_id']));

		foreach ($custom_fields as $custom_field) {
			if (($custom_field['location'] == 'account') && $custom_field['required'] && empty($this->request->post['custom_field'][$custom_field['custom_field_id']])) {
				$this->error['custom_field'][$custom_field['custom_field_id']] = sprintf($this->language->get('error_custom_field'), $custom_field['name']);
			}
		}

		if ($this->request->post['password'] || (!isset($this->request->get['customer_id']))) {
			if ((utf8_strlen($this->request->post['password']) < 4) || (utf8_strlen($this->request->post['password']) > 20)) {
				$this->error['password'] = $this->language->get('error_password');
			}

			if ($this->request->post['password'] != $this->request->post['confirm']) {
				$this->error['confirm'] = $this->language->get('error_confirm');
			}
		}

		if ($this->error && !isset($this->error['warning'])) {
			$this->error['warning'] = $this->language->get('error_warning');
		}

		return !$this->error;
	}

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'customer/customer')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	protected function validateApprove() {
		if (!$this->user->hasPermission('modify', 'customer/customer')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	protected function validateUnlock() {
		if (!$this->user->hasPermission('modify', 'customer/customer')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	protected function validateExport() {
		if (!$this->user->hasPermission('access', 'common/export')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	protected function getUrl($blacklist = array()) {
		$url_data = array();

		$url_data['token'] = $this->session->data['token'];

		if (isset($this->request->get['filter_name']) && !in_array('name', $blacklist)) {
			$url_data['filter_name'] = html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8');
		}

		if (isset($this->request->get['filter_email']) && !in_array('email', $blacklist)) {
			$url_data['filter_email'] = html_entity_decode($this->request->get['filter_email'], ENT_QUOTES, 'UTF-8');
		}

		if (isset($this->request->get['filter_country']) && !in_array('country', $blacklist)) {
			$url_data['filter_country'] = html_entity_decode($this->request->get['filter_country'], ENT_QUOTES, 'UTF-8');
		}

		if (isset($this->request->get['filter_account_status']) && !in_array('account_status', $blacklist)) {
			$url_data['filter_account_status'] = html_entity_decode($this->request->get['filter_account_status'], ENT_QUOTES, 'UTF-8');
		}

		if (isset($this->request->get['filter_customer_group_id']) && !in_array('customer_group_id', $blacklist)) {
			$url_data['filter_customer_group_id'] = html_entity_decode($this->request->get['filter_customer_group_id'], ENT_QUOTES, 'UTF-8');
		}

		if (isset($this->request->get['filter_affiliate_id']) && !in_array('affiliate_id', $blacklist)) {
			$url_data['filter_affiliate_id'] = html_entity_decode($this->request->get['filter_affiliate_id'], ENT_QUOTES, 'UTF-8');
		}

		if (isset($this->request->get['filter_ext_aff_id']) && !in_array('ext_aff_id', $blacklist)) {
			$url_data['filter_ext_aff_id'] = html_entity_decode($this->request->get['filter_ext_aff_id'], ENT_QUOTES, 'UTF-8');
		}

		if (isset($this->request->get['filter_date_added_start']) && !in_array('date_added_start', $blacklist)) {
			$url_data['filter_date_added_start'] = html_entity_decode($this->request->get['filter_date_added_start'], ENT_QUOTES, 'UTF-8');
		}

		if (isset($this->request->get['filter_date_added_end']) && !in_array('date_added_end', $blacklist)) {
			$url_data['filter_date_added_end'] = html_entity_decode($this->request->get['filter_date_added_end'], ENT_QUOTES, 'UTF-8');
		}

		if (isset($this->request->get['filter_date_added']) && !in_array('date_added', $blacklist)) {
			$url_data['filter_date_added'] = html_entity_decode($this->request->get['filter_date_added'], ENT_QUOTES, 'UTF-8');
		}

		if (isset($this->request->get['sort']) && !in_array('sort', $blacklist)) {
			$url_data['sort'] = $this->request->get['sort'];
		}

		if (isset($this->request->get['order']) && !in_array('order', $blacklist)) {
			$url_data['order'] = $this->request->get['order'];
		}

		if (isset($this->request->get['page']) && !in_array('page', $blacklist)) {
			$url_data['page'] = $this->request->get['page'];
		}

		return http_build_query($url_data);
	}

	public function login() {
		if (isset($this->request->get['customer_id'])) {
			$customer_id = $this->request->get['customer_id'];
		} else {
			$customer_id = 0;
		}

		$this->load->model('customer/customer');

		$customer_info = $this->model_customer_customer->getCustomer($customer_id);

		if ($customer_info) {
			// Create token to login with
			$token = token(64);

			$this->model_customer_customer->editToken($customer_id, $token);

			if (isset($this->request->get['store_id'])) {
				$this->load->model('setting/store');
				$store_info = $this->model_setting_store->getStore($this->request->get['store_id']);
			} else {
				$store_info = array();
			}

			if ($store_info) {
				$this->response->redirect($store_info['url'] . 'index.php?route=customer/login&token=' . $token);
			} else {
				$this->response->redirect(HTTP_CATALOG . 'index.php?route=customer/login&token=' . $token);
			}
		} else {
			$this->load->language('error/not_found');

			$this->document->setTitle($this->language->get('heading_title'));

			$data['heading_title'] = $this->language->get('heading_title');

			$data['text_not_found'] = $this->language->get('text_not_found');

			$data['breadcrumbs'] = array();

			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_home'),
				'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true)
			);

			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link('error/not_found', 'token=' . $this->session->data['token'], true)
			);

			$data['header'] = $this->load->controller('common/header');
			$data['column_left'] = $this->load->controller('common/column_left');
			$data['footer'] = $this->load->controller('common/footer');

			$this->response->setOutput($this->load->view('error/not_found', $data));
		}
	}

	public function history() {
		$this->load->language('customer/customer');

		$this->load->model('customer/customer');

		$data['text_no_results'] = $this->language->get('text_no_results');

		$data['column_date_added'] = $this->language->get('column_date_added');
		$data['column_comment'] = $this->language->get('column_comment');

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$data['histories'] = array();

		$results = $this->model_customer_customer->getHistories($this->request->get['customer_id'], ($page - 1) * 10, 10);

		foreach ($results as $result) {
			$data['histories'][] = array(
				'comment'    => $result['comment'],
				'date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added']))
			);
		}

		$history_total = $this->model_customer_customer->getTotalHistories($this->request->get['customer_id']);

		$pagination = new Pagination();
		$pagination->total = $history_total;
		$pagination->page = $page;
		$pagination->limit = 10;
		$pagination->url = $this->url->link('customer/customer/history', 'token=' . $this->session->data['token'] . '&customer_id=' . $this->request->get['customer_id'] . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($history_total) ? (($page - 1) * 10) + 1 : 0, ((($page - 1) * 10) > ($history_total - 10)) ? $history_total : ((($page - 1) * 10) + 10), $history_total, ceil($history_total / 10));

		$this->response->setOutput($this->load->view('customer/customer_history', $data));
	}

	public function addHistory() {
		$this->load->language('customer/customer');

		$json = array();

		if (!$this->user->hasPermission('modify', 'customer/customer')) {
			$json['error'] = $this->language->get('error_permission');
		} else {
			$this->load->model('customer/customer');

			$this->model_customer_customer->addHistory($this->request->get['customer_id'], $this->request->post['comment']);

			$json['success'] = $this->language->get('success_history');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function transaction() {
		$this->load->language('customer/customer');

		$this->load->model('customer/customer');

		$data['text_no_results'] = $this->language->get('text_no_results');
		$data['text_balance'] = $this->language->get('text_balance');

		$data['column_date_added'] = $this->language->get('column_date_added');
		$data['column_description'] = $this->language->get('column_description');
		$data['column_amount'] = $this->language->get('column_amount');

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$data['transactions'] = array();

		$results = $this->model_customer_customer->getTransactions($this->request->get['customer_id'], ($page - 1) * 10, 10);

		foreach ($results as $result) {
			$data['transactions'][] = array(
				'amount'      => $this->currency->format($result['amount'], $this->config->get('config_currency')),
				'description' => $result['description'],
				'date_added'  => date($this->language->get('date_format_short'), strtotime($result['date_added']))
			);
		}

		$data['balance'] = $this->currency->format($this->model_customer_customer->getTransactionTotal($this->request->get['customer_id']), $this->config->get('config_currency'));

		$transaction_total = $this->model_customer_customer->getTotalTransactions($this->request->get['customer_id']);

		$pagination = new Pagination();
		$pagination->total = $transaction_total;
		$pagination->page = $page;
		$pagination->limit = 10;
		$pagination->url = $this->url->link('customer/customer/transaction', 'token=' . $this->session->data['token'] . '&customer_id=' . $this->request->get['customer_id'] . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($transaction_total) ? (($page - 1) * 10) + 1 : 0, ((($page - 1) * 10) > ($transaction_total - 10)) ? $transaction_total : ((($page - 1) * 10) + 10), $transaction_total, ceil($transaction_total / 10));

		$this->response->setOutput($this->load->view('customer/customer_transaction', $data));
	}

	public function addTransaction() {
		$this->load->language('customer/customer');

		$json = array();

		if (!$this->user->hasPermission('modify', 'customer/customer')) {
			$json['error'] = $this->language->get('error_permission');
		} else {
			$this->load->model('customer/customer');

			$this->model_customer_customer->addTransaction($this->request->get['customer_id'], $this->request->post['description'], $this->request->post['amount']);

			$json['success'] = $this->language->get('success_transaction');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function reward() {
		$this->load->language('customer/customer');

		$this->load->model('customer/customer');

		$data['text_no_results'] = $this->language->get('text_no_results');
		$data['text_balance'] = $this->language->get('text_balance');

		$data['column_date_added'] = $this->language->get('column_date_added');
		$data['column_description'] = $this->language->get('column_description');
		$data['column_points'] = $this->language->get('column_points');

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$data['rewards'] = array();

		$results = $this->model_customer_customer->getRewards($this->request->get['customer_id'], ($page - 1) * 10, 10);

		foreach ($results as $result) {
			$data['rewards'][] = array(
				'points'      => $result['points'],
				'description' => $result['description'],
				'date_added'  => date($this->language->get('date_format_short'), strtotime($result['date_added']))
			);
		}

		$data['balance'] = $this->model_customer_customer->getRewardTotal($this->request->get['customer_id']);

		$reward_total = $this->model_customer_customer->getTotalRewards($this->request->get['customer_id']);

		$pagination = new Pagination();
		$pagination->total = $reward_total;
		$pagination->page = $page;
		$pagination->limit = 10;
		$pagination->url = $this->url->link('customer/customer/reward', 'token=' . $this->session->data['token'] . '&customer_id=' . $this->request->get['customer_id'] . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($reward_total) ? (($page - 1) * 10) + 1 : 0, ((($page - 1) * 10) > ($reward_total - 10)) ? $reward_total : ((($page - 1) * 10) + 10), $reward_total, ceil($reward_total / 10));

		$this->response->setOutput($this->load->view('customer/customer_reward', $data));
	}

	public function addReward() {
		$this->load->language('customer/customer');

		$json = array();

		if (!$this->user->hasPermission('modify', 'customer/customer')) {
			$json['error'] = $this->language->get('error_permission');
		} else {
			$this->load->model('customer/customer');

			$this->model_customer_customer->addReward($this->request->get['customer_id'], $this->request->post['description'], $this->request->post['points']);

			$json['success'] = $this->language->get('success_reward');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function ip() {
		$data = $this->load->language('customer/customer');

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$data['ips'] = array();

		$this->load->model('customer/customer');

		$ip_total = $this->model_customer_customer->getTotalIps($this->request->get['customer_id']);

		$results = $this->model_customer_customer->getIps($this->request->get['customer_id'], ($page - 1) * 10, 10);

		$reader = new Reader(DIR_SYSTEM . '/vendor/GeoLite2-Country.mmdb');
	
		foreach ($results as $result) {
			try {
				$record = $reader->country($result['ip']);
			} catch (GeoIp2\Exception\AddressNotFoundException $e) {
				$record = false;
			}

			if ($record) {
				$iso_code = strtolower($record->country->isoCode);
			} else {
				$iso_code = '';
			}

			$data['ips'][] = array(
				'ip'         => $result['ip'],
				'iso_code'   => $iso_code,
				'total'      => $this->model_customer_customer->getTotalCustomersByIp($result['ip']),
				'date_added' => date('d/m/y', strtotime($result['date_added'])),
				'filter_ip'  => $this->url->link('customer/customer', 'token=' . $this->session->data['token'] . '&filter_ip=' . $result['ip'], true)
			);
		}

		// TODO: update customer country_id if country_id = 0

		$pagination = new Pagination();
		$pagination->total = $ip_total;
		$pagination->page  = $page;
		$pagination->limit = 10;
		$pagination->url   = $this->url->link('customer/customer/ip', 'token=' . $this->session->data['token'] . '&customer_id=' . $this->request->get['customer_id'] . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($ip_total) ? (($page - 1) * 10) + 1 : 0, ((($page - 1) * 10) > ($ip_total - 10)) ? $ip_total : ((($page - 1) * 10) + 10), $ip_total, ceil($ip_total / 10));

		$this->response->setOutput($this->load->view('customer/customer_ip', $data));
	}

	public function account() {
		$data = $this->load->language('customer/customer');

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$data['accounts'] = array();

		$this->load->model('customer/customer');

		$account_total = $this->model_customer_customer->getTotalAccounts($this->request->get['customer_id']);

		$account_data = $this->model_customer_customer->getAccounts($this->request->get['customer_id'], ($page - 1) * 10, 10);

		foreach ($account_data as $account) {
			$data['accounts'][] = array(
				'username'     => $account['username'],
				'type'         => $account['type'],
				'date_expires' => (($account['date_expires'] != '0000-00-00 00:00:00') ? date($this->language->get('datetime_format'), strtotime($account['date_expires'])) : ''),
				'date_added'   => date($this->language->get('datetime_format'), strtotime($account['date_added'])),
				'edit'         => $this->url->link('customer/account/edit', 'token=' . $this->session->data['token'] . '&account_id=' . $account['account_id'], true),
			);
		}

		$pagination = new Pagination();
		$pagination->total = $account_total;
		$pagination->page  = $page;
		$pagination->limit = 10;
		$pagination->url   = $this->url->link('customer/customer/account', 'token=' . $this->session->data['token'] . '&customer_id=' . $this->request->get['customer_id'] . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($account_total) ? (($page - 1) * 10) + 1 : 0, ((($page - 1) * 10) > ($account_total - 10)) ? $account_total : ((($page - 1) * 10) + 10), $account_total, ceil($account_total / 10));

		$this->response->setOutput($this->load->view('customer/customer_account', $data));
	}

	public function autocomplete() {
		$json = array();

		if (isset($this->request->get['filter_name']) || isset($this->request->get['filter_email'])) {
			if (isset($this->request->get['filter_name'])) {
				$filter_name = $this->request->get['filter_name'];
			} else {
				$filter_name = '';
			}

			if (isset($this->request->get['filter_email'])) {
				$filter_email = $this->request->get['filter_email'];
			} else {
				$filter_email = '';
			}

			$this->load->model('customer/customer');

			$filter_data = array(
				'filter_name'  => $filter_name,
				'filter_email' => $filter_email,
				'start'        => 0,
				'limit'        => 5
			);

			if(isset($this->request->get['implode'])){
					$filter_data['implode'] = $this->request->get['implode'];
			}

			$results = $this->model_customer_customer->getCustomers($filter_data);

			foreach ($results as $result) {
				$json[] = array(
					'customer_id'       => $result['customer_id'],
					'customer_group_id' => $result['customer_group_id'],
					'name'              => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8')),
					'customer_group'    => $result['customer_group'],
					'firstname'         => $result['firstname'],
					'lastname'          => $result['lastname'],
					'email'             => $result['email'],
					'telephone'         => $result['telephone'],
					'custom_field'      => json_decode($result['custom_field'], true)
				);
			}
		}

		$sort_order = array();

		foreach ($json as $key => $value) {
			$sort_order[$key] = $value['name'];
		}

		array_multisort($sort_order, SORT_ASC, $json);

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function customfield() {
		$json = array();

		$this->load->model('customer/custom_field');

		// Customer Group
		if (isset($this->request->get['customer_group_id'])) {
			$customer_group_id = $this->request->get['customer_group_id'];
		} else {
			$customer_group_id = $this->config->get('config_customer_group_id');
		}

		$custom_fields = $this->model_customer_custom_field->getCustomFields(array('filter_customer_group_id' => $customer_group_id));

		foreach ($custom_fields as $custom_field) {
			$json[] = array(
				'custom_field_id' => $custom_field['custom_field_id'],
				'required'        => empty($custom_field['required']) || $custom_field['required'] == 0 ? false : true
			);
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function order() {
		$data = $this->load->language('sale/order');

		if (isset($this->request->get['filter_order_id'])) {
			$filter_order_id = $this->request->get['filter_order_id'];
		} else {
			$filter_order_id = null;
		}

		if (isset($this->request->get['filter_customer_id'])) {
			$filter_customer_id = $this->request->get['filter_customer_id'];
		} else {
			$filter_customer_id = null;
		}

		if (isset($this->request->get['filter_order_status'])) {
			$filter_order_status = $this->request->get['filter_order_status'];
		} else {
			$filter_order_status = null;
		}

		if (isset($this->request->get['filter_total'])) {
			$filter_total = $this->request->get['filter_total'];
		} else {
			$filter_total = null;
		}

		if (isset($this->request->get['filter_date_added'])) {
			$filter_date_added = $this->request->get['filter_date_added'];
		} else {
			$filter_date_added = null;
		}

		if (isset($this->request->get['filter_date_modified'])) {
			$filter_date_modified = $this->request->get['filter_date_modified'];
		} else {
			$filter_date_modified = null;
		}

		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'o.order_id';
		}

		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'DESC';
		}

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$url = '';

		if (isset($this->request->get['filter_order_id'])) {
			$url .= '&filter_order_id=' . $this->request->get['filter_order_id'];
		}

		if (isset($this->request->get['filter_customer_id'])) {
			$url .= '&filter_customer_id=' . $this->request->get['filter_customer_id'];
		}

		if (isset($this->request->get['filter_order_status'])) {
			$url .= '&filter_order_status=' . $this->request->get['filter_order_status'];
		}

		if (isset($this->request->get['filter_total'])) {
			$url .= '&filter_total=' . $this->request->get['filter_total'];
		}

		if (isset($this->request->get['filter_date_added'])) {
			$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
		}

		if (isset($this->request->get['filter_date_modified'])) {
			$url .= '&filter_date_modified=' . $this->request->get['filter_date_modified'];
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['invoice'] = $this->url->link('sale/order/invoice', 'token=' . $this->session->data['token'], true);
		$data['add'] = $this->url->link('sale/order/add', 'token=' . $this->session->data['token'], true);

		$data['orders'] = array();

		$filter_data = array(
			'filter_order_id'      => $filter_order_id,
			'filter_customer_id'	 => $filter_customer_id,
			'filter_order_status'  => $filter_order_status,
			'filter_total'         => $filter_total,
			'filter_date_added'    => $filter_date_added,
			'filter_date_modified' => $filter_date_modified,
			'sort'                 => $sort,
			'order'                => $order,
			'start'                => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit'                => $this->config->get('config_limit_admin')
		);

		$this->load->model('sale/order');

		$order_total = $this->model_sale_order->getTotalOrders($filter_data);

		$results = $this->model_sale_order->getOrders($filter_data);
		
		foreach ($results as $result) {
			$data['orders'][] = array(
				'order_id'      => $result['order_id'],
				'status'        => $result['status'],
				'total'         => $this->currency->format($result['total'], $result['currency_code'], $result['currency_value']),
				'date_added'    => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
				'date_modified' => date($this->language->get('date_format_short'), strtotime($result['date_modified'])),
				'view'          => $this->url->link('sale/order/info', 'token=' . $this->session->data['token'] . '&order_id=' . $result['order_id'] . $url, true),
				'edit'          => $this->url->link('sale/order/edit', 'token=' . $this->session->data['token'] . '&order_id=' . $result['order_id'] . $url, true),
			);
		}

		$data['token'] = $this->session->data['token'];

		if (isset($this->request->post['selected'])) {
			$data['selected'] = (array)$this->request->post['selected'];
		} else {
			$data['selected'] = array();
		}

		$url = '';

		if (isset($this->request->get['filter_order_id'])) {
			$url .= '&filter_order_id=' . $this->request->get['filter_order_id'];
		}

		if (isset($this->request->get['filter_customer_id'])) {
			$url .= '&filter_customer_id=' . $this->request->get['filter_customer_id'];
		}

		if (isset($this->request->get['filter_order_status'])) {
			$url .= '&filter_order_status=' . $this->request->get['filter_order_status'];
		}

		if (isset($this->request->get['filter_total'])) {
			$url .= '&filter_total=' . $this->request->get['filter_total'];
		}

		if (isset($this->request->get['filter_date_added'])) {
			$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
		}

		if (isset($this->request->get['filter_date_modified'])) {
			$url .= '&filter_date_modified=' . $this->request->get['filter_date_modified'];
		}

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['sort_order']         = $this->url->link('customer/customer/order', 'token=' . $this->session->data['token'] . '&sort=o.order_id' . $url, true);
		$data['sort_status']        = $this->url->link('customer/customer/order', 'token=' . $this->session->data['token'] . '&sort=status' . $url, true);
		$data['sort_total']         = $this->url->link('customer/customer/order', 'token=' . $this->session->data['token'] . '&sort=o.total' . $url, true);
		$data['sort_date_added']    = $this->url->link('customer/customer/order', 'token=' . $this->session->data['token'] . '&sort=o.date_added' . $url, true);
		$data['sort_date_modified'] = $this->url->link('customer/customer/order', 'token=' . $this->session->data['token'] . '&sort=o.date_modified' . $url, true);

		$url = '';

		if (isset($this->request->get['filter_order_id'])) {
			$url .= '&filter_order_id=' . $this->request->get['filter_order_id'];
		}

		if (isset($this->request->get['filter_customer_id'])) {
			$url .= '&filter_customer_id=' . $this->request->get['filter_customer_id'];
		}

		if (isset($this->request->get['filter_order_status'])) {
			$url .= '&filter_order_status=' . $this->request->get['filter_order_status'];
		}

		if (isset($this->request->get['filter_total'])) {
			$url .= '&filter_total=' . $this->request->get['filter_total'];
		}

		if (isset($this->request->get['filter_date_added'])) {
			$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
		}

		if (isset($this->request->get['filter_date_modified'])) {
			$url .= '&filter_date_modified=' . $this->request->get['filter_date_modified'];
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $order_total;
		$pagination->page  = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url   = $this->url->link('customer/customer/order', 'token=' . $this->session->data['token'] . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($order_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($order_total - $this->config->get('config_limit_admin'))) ? $order_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $order_total, ceil($order_total / $this->config->get('config_limit_admin')));

		$data['filter_order_id']      = $filter_order_id;
		$data['filter_customer_id']   = $filter_customer_id;
		$data['filter_order_status']  = $filter_order_status;
		$data['filter_total']         = $filter_total;
		$data['filter_date_added']    = $filter_date_added;
		$data['filter_date_modified'] = $filter_date_modified;

		$this->load->model('localisation/order_status');

		$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

		$data['sort'] = $sort;
		$data['order'] = $order;

		$data['store'] = HTTPS_CATALOG;

		// API login
		$this->load->model('user/api');

		$api_info = $this->model_user_api->getApi($this->config->get('config_api_id'));

		if ($api_info) {
			$data['api_id']  = $api_info['api_id'];
			$data['api_key'] = $api_info['key'];
			$data['api_ip']  = $this->request->server['REMOTE_ADDR'];
		} else {
			$data['api_id']  = '';
			$data['api_key'] = '';
			$data['api_ip']  = '';
		}

		$this->response->setOutput($this->load->view('customer/customer_order_list', $data));
	}
}