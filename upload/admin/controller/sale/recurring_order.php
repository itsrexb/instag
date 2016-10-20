<?php
class ControllerSaleRecurringOrder extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('sale/recurring_order');

		$this->load->model('sale/recurring_order');

		$this->getList();
	}

	public function info() {
		$this->load->language('sale/recurring_order');

		$this->load->model('sale/recurring_order');

		$this->getInfo();
	}

	public function cancel() {
		$this->load->language('sale/recurring_order');

		$this->load->model('sale/recurring_order');

		if ($this->validateCancel()) {
			$this->model_sale_recurring_order->cancelRecurringOrder($this->request->get['recurring_order_id']);

			$recurring_order_info = $this->model_sale_recurring_order->getRecurringOrder($this->request->get['recurring_order_id']);

			if ($recurring_order_info) {
				// update any enabled marketing extensions
				$this->load->model('extension/extension');
				$marketing_extensions = $this->model_extension_extension->getInstalled('marketing');

				foreach ($marketing_extensions as $marketing) {
					if ($this->config->get($marketing . '_status')) {
						$this->{$marketing} = new $marketing($this->registry);

						$this->load->model('extension/marketing/' . $marketing);
						$this->{'model_extension_marketing_' . $marketing}->updateCustomer($recurring_order_info['customer_id']);
					}
				}
			}

			$this->session->data['success'] = $this->language->get('success_cancel');

			$this->response->redirect($this->url->link('sale/recurring_order', $this->getUrl(), true));
		}

		$this->getList();
	}

	protected function getList() {
		$data = $this->language->all();

		$this->document->setTitle($this->language->get('heading_title'));

		if (isset($this->request->get['filter_recurring_order_id'])) {
			$filter_recurring_order_id = trim($this->request->get['filter_recurring_order_id']);
		} else {
			$filter_recurring_order_id = null;
		}

		if (isset($this->request->get['filter_customer'])) {
			$filter_customer = trim($this->request->get['filter_customer']);
		} else {
			$filter_customer = null;
		}

		if (isset($this->request->get['filter_account_username'])) {
			$filter_account_username = trim($this->request->get['filter_account_username']);
		} else {
			$filter_account_username = null;
		}

		if (isset($this->request->get['filter_email'])) {
			$filter_email = trim($this->request->get['filter_email']);
		} else {
			$filter_email = null;
		}

		if (isset($this->request->get['filter_account_type'])) {
			$filter_account_type = trim($this->request->get['filter_account_type']);
		} else {
			$filter_account_type = null;
		}

		

		if (isset($this->request->get['filter_active'])) {
			$filter_active = trim($this->request->get['filter_active']);
		} else {
			$filter_active = null;
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

		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'ro.recurring_order_id';
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

		$url = $this->getUrl();

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true),
		);

		$data['breadcrumbs'][] = array(
			'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('sale/recurring_order', $url, true),
		);

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

		$filter_data = array(
			'filter_recurring_order_id' => $filter_recurring_order_id,
			'filter_customer'           => $filter_customer,
			'filter_account_username'   => $filter_account_username,
			'filter_email'   			=> $filter_email,
			'filter_account_type'       => $filter_account_type,
			'filter_active'             => $filter_active,
			'filter_date_added_start'   => $filter_date_added_start,
			'filter_date_added_end'     => $filter_date_added_end,
			'order'                     => $order,
			'sort'                      => $sort,
			'start'                     => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit'                     => $this->config->get('config_limit_admin'),
		);

		$data['recurring_orders'] = array();

		$recurring_orders = $this->model_sale_recurring_order->getRecurringOrders($filter_data);
		$recurring_order_total = $this->model_sale_recurring_order->getTotalRecurringOrders($filter_data);
		
		foreach ($recurring_orders as $recurring_order) {
			$data['recurring_orders'][] = array(
				'recurring_order_id' => $recurring_order['recurring_order_id'],
				'customer'           => $recurring_order['customer'],
				'account_username'   => $recurring_order['account_username'],
				'email'  			 => $recurring_order['email'],
				'account_type'       => ($recurring_order['account_type'] ? $this->language->get('text_' . $recurring_order['account_type']) : ''),
				'status'             => ($recurring_order['active'] ? $this->language->get('text_active') : $this->language->get('text_inactive')),
				'date_added'         => date($this->language->get('datetime_format'), strtotime($recurring_order['date_added'])),
				'view'               => $this->url->link('sale/recurring_order/info', $url . '&recurring_order_id=' . $recurring_order['recurring_order_id'], true)
			);
		}

		$data['account_types'] = array(
			'*'         => $this->language->get('text_all_account_types'),
			'instagram' => $this->language->get('text_instagram')
		);

		$url = $this->getUrl(array('sort', 'order'));

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		$data['sort_recurring_order']  = $this->url->link('sale/recurring_order', $url . '&sort=ro.recurring_order_id', true);
		$data['sort_customer']         = $this->url->link('sale/recurring_order', $url . '&sort=customer', true);
		$data['sort_account_username'] = $this->url->link('sale/recurring_order', $url . '&sort=account_username', true);
		$data['sort_email'] 		   = $this->url->link('sale/recurring_order', $url . '&sort=c.email', true);
		$data['sort_account_type']     = $this->url->link('sale/recurring_order', $url . '&sort=ro.type', true);
		$data['sort_active']           = $this->url->link('sale/recurring_order', $url . '&sort=ro.active', true);
		$data['sort_date_added']       = $this->url->link('sale/recurring_order', $url . '&sort=ro.date_added', true);

		$url = $this->getUrl(array('page'));
	

		$pagination = new Pagination();
		$pagination->total = $recurring_order_total;
		$pagination->page  = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->text  = $this->language->get('text_pagination');
		$pagination->url   = $this->url->link('sale/recurring_order', $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();
		$data['results'] = sprintf($this->language->get('text_pagination'), ($recurring_order_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($recurring_order_total - $this->config->get('config_limit_admin'))) ? $recurring_order_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $recurring_order_total, ceil($recurring_order_total / $this->config->get('config_limit_admin')));

		$data['filter_recurring_order_id'] = $filter_recurring_order_id;
		$data['filter_customer']           = $filter_customer;
		$data['filter_account_username']   = $filter_account_username;
		$data['filter_email']   		   = $filter_email;
		$data['filter_account_type']       = $filter_account_type;
		$data['filter_active']             = $filter_active;
		$data['filter_date_added_start']   = $filter_date_added_start;
		$data['filter_date_added_end']     = $filter_date_added_end;

		$data['sort']  = $sort;
		$data['order'] = $order;

		$data['token'] = $this->session->data['token'];

		$data['header']      = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer']      = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('sale/recurring_order_list', $data));
	}

	protected function getInfo() {
		$data = $this->load->language('sale/recurring_order');

		if (isset($this->request->get['recurring_order_id'])) {
			$this->load->model('sale/recurring_order');

			$recurring_order_info = $this->model_sale_recurring_order->getRecurringOrder($this->request->get['recurring_order_id']);
		} else {
			$recurring_order_info = array();
		}

		if ($recurring_order_info) {
			$this->document->setTitle($this->language->get('heading_title'));

			$url = $this->getUrl();

			$data['breadcrumbs'] = array();

			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_home'),
				'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true)
			);

			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link('sale/recurring_order', $url, true)
			);

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

			$data['href_cancel'] = $this->url->link('sale/recurring_order', $url, true);

			$data['buttons'] = array();

			if ($recurring_order_info['active']) {
				$data['buttons'][] = array(
					'text' => $this->language->get('button_cancel_recurring_order'),
					'href' => $this->url->link('sale/recurring_order/cancel', $url . '&recurring_order_id=' . $recurring_order_info['recurring_order_id'], true)
				);
			}

			$data['recurring_order_id'] = $recurring_order_info['recurring_order_id'];

			if ($recurring_order_info['store_id']) {
				$this->load->model('setting/store');

				$store_info = $this->model_setting_store->getStore($recurring_order_info['store_id']);

				if ($store_info) {
					$data['store_name'] = $store_info['name'];
					$data['store_url']  = $store_info['url'];
				} else {
					$data['store_name'] = '';
					$data['store_url']  = '';
				}
			} else {
				$data['store_name'] = $this->config->get('config_name');
				$data['store_url']  = HTTP_CATALOG;
			}

			$data['date_added']     = date($this->language->get('date_format_short'), strtotime($recurring_order_info['date_added']));
			$data['payment_method'] = $recurring_order_info['payment_method'];
			$data['status']         = ($recurring_order_info['active'] ? $this->language->get('text_active') : $this->language->get('text_inactive'));
			$data['customer_name']  = $recurring_order_info['customer'];

			if ($recurring_order_info['customer_id']) {
				$data['customer_href'] = $this->url->link('customer/customer/edit', 'token=' . $this->session->data['token'] . '&customer_id=' . $recurring_order_info['customer_id'], true);

				$this->load->model('customer/customer');

				$customer_info = $this->model_customer_customer->getCustomer($recurring_order_info['customer_id']);

				if ($customer_info) {
					$data['customer_email']     = $customer_info['email'];
					$data['customer_telephone'] = $customer_info['telephone'];

					$this->load->model('customer/customer_group');

					$customer_group_info = $this->model_customer_customer_group->getCustomerGroup($customer_info['customer_group_id']);

					if ($customer_group_info) {
						$data['customer_group'] = $customer_group_info['name'];
					} else {
						$data['customer_group'] = '';
					}
				} else {
					$data['customer_group']     = '';
					$data['customer_email']     = '';
					$data['customer_telephone'] = '';
				}
			} else {
				$data['customer_href']      = '';
				$data['customer_group']     = '';
				$data['customer_email']     = '';
				$data['customer_telephone'] = '';
			}

			$data['product_name']     = $recurring_order_info['product_name'];
			$data['product_quantity'] = $recurring_order_info['product_quantity'];

			$data['date_next_recurring'] = '';

			if ($recurring_order_info['active']) {
				$this->load->model('sale/order');

				$order_data = $this->model_sale_order->getOrders(array(
					'filter_recurring_order_id' => $this->request->get['recurring_order_id'],
					'filter_date_added'         => date('Y-m-d', strtotime('-' . $this->config->get('config_recurring_order_days_before') . ' days', strtotime($recurring_order_info['date_next_recurring']))),
					'sort'  => 'o.date_added',
					'order' => 'DESC',
					'limit' => 1
				));

				if ($order_data) {
					$last_order_date_added = new DateTime($order_data[0]['date_added']);
				} else {
					$last_order_date_added = false;
				}

				if ($last_order_date_added >= new DateTime('today')) {
					$date_next_recurring = new DateTime('+ 1 day');
				} else {
					$date_next_recurring = max(new DateTime('today'), new DateTime($recurring_order_info['date_next_recurring']));
				}

				$data['date_next_recurring'] = $date_next_recurring->format($this->language->get('date_format_short'));
			}

			$data['account_type']        = $recurring_order_info['account_type'];
			$data['account_username']    = $recurring_order_info['account_username'];
			$data['account_href']        = $this->url->link('customer/account/edit', 'token=' . $this->session->data['token'] . '&account_id=' . $recurring_order_info['account_id'], true);
			$data['recurring_price']     = $this->currency->format($recurring_order_info['recurring_price']);
			$data['recurring_cycle']     = $recurring_order_info['recurring_cycle'];
			$data['recurring_frequency'] = $this->language->get('text_frequency_' . $recurring_order_info['recurring_frequency']);

			if (!$recurring_order_info['active']) {
				$data['recurring_duration'] = $this->language->get('text_cancelled');
			} else if (!$recurring_order_info['recurring_duration']) {
				$data['recurring_duration'] = $this->language->get('text_forever');
			} else {
				$data['recurring_duration'] = $recurring_order_info['recurring_duration'];
			}

			$data['trial_status']    = $recurring_order_info['trial_status'];
			$data['trial_price']     = $this->currency->format($recurring_order_info['trial_price']);
			$data['trial_cycle']     = $recurring_order_info['trial_cycle'];
			$data['trial_frequency'] = $recurring_order_info['trial_frequency'];
			$data['trial_duration']  = $recurring_order_info['trial_duration'];

			if ($recurring_order_info['coupon_id']) {
				$this->load->model('marketing/coupon');

				$coupon_info = $this->model_marketing_coupon->getCoupon($recurring_order_info['coupon_id']);

				if ($coupon_info) {
					$data['coupon_code'] = $coupon_info['code'];

					if ($recurring_order_info['coupon_remaining']) {
						$data['coupon_remaining'] = sprintf($this->language->get('text_coupon_remaining'), $recurring_order_info['coupon_remaining']);
					} else {
						$data['coupon_remaining'] = '';
					}
				} else {
					$data['coupon_code']      = '';
					$data['coupon_remaining'] = '';
				}
			} else {
				$data['coupon_code']      = '';
				$data['coupon_remaining'] = '';
			}

			$data['token'] = $this->request->get['token'];

			$data['header']      = $this->load->controller('common/header');
			$data['column_left'] = $this->load->controller('common/column_left');
			$data['footer']      = $this->load->controller('common/footer');

			$this->response->setOutput($this->load->view('sale/recurring_order_info', $data));
		} else {
			return new Action('error/not_found');
		}
	}

	protected function validateCancel() {
		if (!$this->user->hasPermission('modify', 'sale/recurring_order')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (empty($this->request->get['recurring_order_id'])) {
			$this->error['warning'] = $this->language->get('error_recurring_order_id');
		}

		return !$this->error;
	}

	protected function getUrl($blacklist = array()) {
		$url_data = array();

		$url_data['token'] = $this->session->data['token'];

		if (isset($this->request->get['filter_recurring_order_id']) && !in_array('recurring_order_id', $blacklist)) {
			$url_data['filter_recurring_order_id'] = $this->request->get['filter_recurring_order_id'];
		}

		if (isset($this->request->get['filter_customer']) && !in_array('customer', $blacklist)) {
			$url_data['filter_customer'] = html_entity_decode($this->request->get['filter_customer'], ENT_QUOTES, 'UTF-8');
		}

		if (isset($this->request->get['filter_account_username']) && !in_array('account_username', $blacklist)) {
			$url_data['filter_account_username'] = html_entity_decode($this->request->get['filter_account_username'], ENT_QUOTES, 'UTF-8');
		}

		if (isset($this->request->get['filter_account_type']) && !in_array('account_type', $blacklist)) {
			$url_data['filter_account_type'] = html_entity_decode($this->request->get['filter_account_type'], ENT_QUOTES, 'UTF-8');
		}

		if (isset($this->request->get['filter_active']) && !in_array('active', $blacklist)) {
			$url_data['filter_active'] = $this->request->get['filter_active'];
		}

		if (isset($this->request->get['filter_date_added_start']) && !in_array('date_added_start', $blacklist)) {
			$url_data['filter_date_added_start'] = $this->request->get['filter_date_added_start'];
		}

		if (isset($this->request->get['filter_date_added_end']) && !in_array('date_added_end', $blacklist)) {
			$url_data['filter_date_added_end'] = $this->request->get['filter_date_added_end'];
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

	public function transactions() {
		$data = $this->load->language('sale/recurring_order');

		if (isset($this->request->get['recurring_order_id'])) {
			$recurring_order_id = $this->request->get['recurring_order_id'];
		} else {
			$recurring_order_id = 0;
		}

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$data['transactions'] = array();

		if ($recurring_order_id) {
			$filter_order_status_ids = array(0);

			$this->load->model('localisation/order_status');

			$order_status_data = $this->model_localisation_order_status->getOrderStatuses();

			foreach ($order_status_data as $order_status) {
				$filter_order_status_ids[] = $order_status['order_status_id'];
			}

			$filter_data = array(
				'filter_order_status'       => implode(',', $filter_order_status_ids),
				'filter_recurring_order_id' => $recurring_order_id,
				'sort'                      => 'o.order_id',
				'order'                     => 'DESC',
				'start'                     => ($page - 1) * $this->config->get('config_limit_admin'),
				'limit'                     => $this->config->get('config_limit_admin')
			);

			$this->load->model('sale/order');

			$order_total = $this->model_sale_order->getTotalOrders($filter_data);

			$order_data = $this->model_sale_order->getOrders($filter_data);

			foreach ($order_data as $order) {
				$data['transactions'][] = array(
					'order_id'	 => $order['order_id'],
					'status'     => ($order['status'] ? $order['status'] : $this->language->get('text_failed')),
					'total'      => $this->currency->format($order['total'], $order['currency_code'], $order['currency_value']),
					'date_added' => date($this->language->get('date_format_short'), strtotime($order['date_added'])),
					'href'       => $this->url->link('sale/order/info', 'token=' . $this->session->data['token'] . '&order_id=' . $order['order_id'], true)
				);
			}

			$pagination = new Pagination();
			$pagination->total = $order_total;
			$pagination->page  = $page;
			$pagination->limit = $this->config->get('config_limit_admin');
			$pagination->url   = $this->url->link('sale/recurring_order/transactions', 'token=' . $this->session->data['token'] . '&recurring_order_id=' . $recurring_order_id . '&page={page}', true);

			$data['pagination'] = $pagination->render();

			$data['results'] = sprintf($this->language->get('text_pagination'), ($order_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($order_total - $this->config->get('config_limit_admin'))) ? $order_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $order_total, ceil($order_total / $this->config->get('config_limit_admin')));
		}

		$this->response->setOutput($this->load->view('sale/recurring_order_transactions', $data));
	}
}