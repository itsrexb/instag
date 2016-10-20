<?php
class ControllerSaleOrder extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('sale/order');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('sale/order');

		$this->getList();
	}

	protected function getList() {
		$data = $this->language->all();

		if (isset($this->request->get['filter_order_id'])) {
			$filter_order_id = trim($this->request->get['filter_order_id']);
		} else {
			$filter_order_id = null;
		}

		if (isset($this->request->get['filter_customer'])) {
			$filter_customer = trim($this->request->get['filter_customer']);
		} else {
			$filter_customer = null;
		}

		if (isset($this->request->get['filter_order_status'])) {
			$filter_order_status = trim($this->request->get['filter_order_status']);
		} else {
			$filter_order_status = null;
		}

		if (isset($this->request->get['filter_recurring_order'])) {
			$filter_recurring_order = trim($this->request->get['filter_recurring_order']);
		} else {
			$filter_recurring_order = null;
		}

		if (isset($this->request->get['filter_total'])) {
			$filter_total = trim($this->request->get['filter_total']);
		} else {
			$filter_total = null;
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
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'o.order_id';
		}

		if (isset($this->request->get['order'])) {
			$order = trim($this->request->get['order']);
		} else {
			$order = 'DESC';
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
			'href' => $this->url->link('sale/order', $url, true)
		);

		$data['invoice'] = $this->url->link('sale/order/invoice', 'token=' . $this->session->data['token'], true);

		$data['orders'] = array();

		$filter_data = array(
			'filter_order_id'         => $filter_order_id,
			'filter_customer'         => $filter_customer,
			'filter_order_status'     => $filter_order_status,
			'filter_recurring_order'  => $filter_recurring_order,
			'filter_total'            => $filter_total,
			'filter_has_affiliate'    => $filter_has_affiliate,
			'filter_has_ext_aff_id'   => $filter_has_ext_aff_id,
			'filter_date_added'       => $filter_date_added,
			'filter_date_added_start' => $filter_date_added_start,
			'filter_date_added_end'   => $filter_date_added_end,
			'sort'                    => $sort,
			'order'                   => $order,
			'start'                   => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit'                   => $this->config->get('config_limit_admin')
		);

		$order_total = $this->model_sale_order->getTotalOrders($filter_data);

		$results = $this->model_sale_order->getOrders($filter_data);
		
		foreach ($results as $result) {
					$data['orders'][] = array(
				'order_id'        => $result['order_id'],
				'customer'        => $result['customer'],
				'customer_id'     => $result['customer_id'],
				'status'          => $result['status'],
				'recurring_order' => ($result['recurring_order'] ? $this->language->get('text_yes') : $this->language->get('text_no')),
				'total'           => $this->currency->format($result['total'], $result['currency_code'], $result['currency_value']),
				'date_added'      => date($this->language->get('datetime_format'), strtotime($result['date_added'])),
				'view'            => $this->url->link('sale/order/info', $url . '&order_id=' . $result['order_id'], true),
				'resend_link'     => HTTPS_CATALOG . 'index.php?route=api/order/send_receipt&order_id=' . $result['order_id']
			);
		}
	
		$data['token'] = $this->session->data['token'];

		if (isset($this->request->post['selected'])) {
			$data['selected'] = (array)$this->request->post['selected'];
		} else {
			$data['selected'] = array();
		}

		$url = $this->getUrl(array('sort', 'order'));

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		$data['sort_order']           = $this->url->link('sale/order', $url . '&sort=o.order_id', true);
		$data['sort_customer']        = $this->url->link('sale/order', $url . '&sort=customer', true);
		$data['sort_status']          = $this->url->link('sale/order', $url . '&sort=status', true);
		$data['sort_recurring_order'] = $this->url->link('sale/order', $url . '&sort=o.recurring_order', true);
		$data['sort_total']           = $this->url->link('sale/order', $url . '&sort=o.total', true);
		$data['sort_date_added']      = $this->url->link('sale/order', $url . '&sort=o.date_added', true);

		$url = $this->getUrl(array('page'));

		$pagination = new Pagination();
		$pagination->total = $order_total;
		$pagination->page  = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url   = $this->url->link('sale/order', $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($order_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($order_total - $this->config->get('config_limit_admin'))) ? $order_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $order_total, ceil($order_total / $this->config->get('config_limit_admin')));

		$data['filter_order_id']         = $filter_order_id;
		$data['filter_customer']         = $filter_customer;
		$data['filter_order_status']     = $filter_order_status;
		$data['filter_recurring_order']  = $filter_recurring_order;
		$data['filter_total']            = $filter_total;
		$data['filter_date_added']       = $filter_date_added;
		$data['filter_date_added_start'] = $filter_date_added_start;
		$data['filter_date_added_end']   = $filter_date_added_end;

		$this->load->model('localisation/order_status');

		$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

		$data['sort']  = $sort;
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

		$data['header']      = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer']      = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('sale/order_list', $data));
	}

	public function info() {
		$this->load->model('sale/order');

		if (isset($this->request->get['order_id'])) {
			$order_id = $this->request->get['order_id'];
		} else {
			$order_id = 0;
		}

		$order_info = $this->model_sale_order->getOrder($order_id);

		if ($order_info) {
			$data = $this->load->language('sale/order');

			$this->document->setTitle($this->language->get('heading_title'));

			$data['heading_title'] = $this->language->get('heading_title');
			//resend order
			$data['text_resend_success'] = $this->language->get('text_resend_success');
			$data['text_resend_error'] = $this->language->get('text_resend_error');
			$data['text_resend'] = $this->language->get('text_resend');
			//resend order
			$data['text_ip_add'] = sprintf($this->language->get('text_ip_add'), $this->request->server['REMOTE_ADDR']);
			$data['text_order']  = sprintf($this->language->get('text_order'), $this->request->get['order_id']);

			$data['token'] = $this->session->data['token'];

			$url = $this->getUrl();

			$data['breadcrumbs'] = array();

			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_home'),
				'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true)
			);

			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link('sale/order', $url, true)
			);

			$data['invoice'] = $this->url->link('sale/order/invoice', 'token=' . $this->session->data['token'] . '&order_id=' . (int)$this->request->get['order_id'], true);
			$data['edit']    = $this->url->link('sale/order/edit', 'token=' . $this->session->data['token'] . '&order_id=' . (int)$this->request->get['order_id'], true);
			$data['cancel']  = $this->url->link('sale/order', $url, true);

			$data['order_id']    = $this->request->get['order_id'];
			$data['resend_link'] = HTTPS_CATALOG . 'index.php?route=api/order/send_receipt&order_id=' . $this->request->get['order_id'];

			$data['store_name'] = $order_info['store_name'];
			$data['store_url']  = $order_info['store_url'];

			if ($order_info['store_id']) {
				$this->load->model('setting/setting');

				$data['store_api_url'] = $this->model_setting_setting->getSetting('config', 'config_ssl', $order_info['store_id']);

				if (!$data['store_api_url']) {
					$data['store_api_url'] = $this->model_setting_setting->getSetting('config', 'config_url', $order_info['store_id']);
				}
			} else {
				$data['store_api_url'] = HTTPS_CATALOG;
			}

			if ($order_info['invoice_no']) {
				$data['invoice_no'] = $order_info['invoice_prefix'] . $order_info['invoice_no'];
			} else {
				$data['invoice_no'] = '';
			}

			$data['date_added'] = date($this->language->get('date_format_short'), strtotime($order_info['date_added']));

			$data['firstname']   = $order_info['firstname'];
			$data['lastname']    = $order_info['lastname'];
			$data['customer_id'] = $order_info['customer_id'];

			$this->load->model('customer/customer');

			if ($order_info['customer_id']) {
				$data['customer'] = $this->url->link('customer/customer/edit', 'token=' . $this->session->data['token'] . '&customer_id=' . $order_info['customer_id'], true);

				$customer_info = $this->model_customer_customer->getCustomer($order_info['customer_id']);

				if ($customer_info && $customer_info['affiliate_id'] && strtotime($customer_info['date_affiliate']) <= strtotime($order_info['date_added'])) {
					$this->load->model('affiliate/affiliate');

					$affiliate_info = $this->model_affiliate_affiliate->getAffiliate($customer_info['affiliate_id']);

					if ($affiliate_info) {
						if ($affiliate_info['path']) {
							$data['affiliate'] = $affiliate_info['path'] . '&nbsp;&nbsp;&gt;&nbsp;&nbsp;' . $affiliate_info['firstname'] . ' ' . $affiliate_info['lastname'];
						} else {
							$data['affiliate'] = $affiliate_info['firstname'] . ' ' . $affiliate_info['lastname'];
						}

						$data['href_affiliate'] = $this->url->link('affiliate/affiliate/edit', 'token=' . $this->session->data['token'] . '&affiliate_id=' . $customer_info['affiliate_id'], true);
					} else {
						$data['affiliate'] = '';
					}
				} else {
					$data['affiliate'] = '';
				}
			} else {
				$data['customer']  = '';
				$data['affiliate'] = '';
			}

			$this->load->model('customer/customer_group');

			$customer_group_info = $this->model_customer_customer_group->getCustomerGroup($order_info['customer_group_id']);

			if ($customer_group_info) {
				$data['customer_group'] = $customer_group_info['name'];
			} else {
				$data['customer_group'] = '';
			}

			$data['email'] = $order_info['email'];

			$data['telephone'] = $order_info['telephone'];

			$data['payment_method'] = $order_info['payment_method'];

			// Uploaded files
			$this->load->model('tool/upload');

			$data['products'] = array();

			$products = $this->model_sale_order->getOrderProducts($this->request->get['order_id']);

			foreach ($products as $product) {
				$data['products'][] = array(
					'order_product_id' => $product['order_product_id'],
					'product_id'       => $product['product_id'],
					'name'             => $product['name'],
					'model'            => $product['model'],
					'account_type'     => $product['account_type'],
					'account_username' => $product['account_username'],
					'quantity'         => $product['quantity'],
					'price'            => $this->currency->format($product['price'] + ($this->config->get('config_tax') ? $product['tax'] : 0), $order_info['currency_code'], $order_info['currency_value']),
					'total'            => $this->currency->format($product['total'] + ($this->config->get('config_tax') ? ($product['tax'] * $product['quantity']) : 0), $order_info['currency_code'], $order_info['currency_value']),
					'href'             => $this->url->link('catalog/product/edit', 'token=' . $this->session->data['token'] . '&product_id=' . $product['product_id'], true),
					'href_account'    => $this->url->link('customer/account/edit', 'token=' . $this->session->data['token'] . '&account_id=' . $product['account_id'], true)
				);
			}

			$data['vouchers'] = array();

			$vouchers = $this->model_sale_order->getOrderVouchers($this->request->get['order_id']);

			foreach ($vouchers as $voucher) {
				$data['vouchers'][] = array(
					'description' => $voucher['description'],
					'amount'      => $this->currency->format($voucher['amount'], $order_info['currency_code'], $order_info['currency_value']),
					'href'        => $this->url->link('sale/voucher/edit', 'token=' . $this->session->data['token'] . '&voucher_id=' . $voucher['voucher_id'], true)
				);
			}

			$data['totals'] = array();

			$totals = $this->model_sale_order->getOrderTotals($this->request->get['order_id']);

			foreach ($totals as $total) {
				$data['totals'][] = array(
					'title' => $total['title'],
					'text'  => $this->currency->format($total['value'], $order_info['currency_code'], $order_info['currency_value']),
				);
			}

			$data['comment'] = nl2br($order_info['comment']);

			$data['reward'] = $order_info['reward'];

			$data['reward_total'] = $this->model_customer_customer->getTotalCustomerRewardsByOrderId($this->request->get['order_id']);

			$this->load->model('localisation/order_status');

			$order_status_info = $this->model_localisation_order_status->getOrderStatus($order_info['order_status_id']);

			if ($order_status_info) {
				$data['order_status'] = $order_status_info['name'];
			} else {
				$data['order_status'] = '';
			}

			$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

			$data['order_status_id'] = $order_info['order_status_id'];

			$data['account_custom_field'] = $order_info['custom_field'];

			// Custom Fields
			$this->load->model('customer/custom_field');

			$data['account_custom_fields'] = array();

			$filter_data = array(
				'sort'  => 'cf.sort_order',
				'order' => 'ASC',
			);

			$custom_fields = $this->model_customer_custom_field->getCustomFields($filter_data);

			foreach ($custom_fields as $custom_field) {
				if ($custom_field['location'] == 'account' && isset($order_info['custom_field'][$custom_field['custom_field_id']])) {
					if ($custom_field['type'] == 'select' || $custom_field['type'] == 'radio') {
						$custom_field_value_info = $this->model_customer_custom_field->getCustomFieldValue($order_info['custom_field'][$custom_field['custom_field_id']]);

						if ($custom_field_value_info) {
							$data['account_custom_fields'][] = array(
								'name'  => $custom_field['name'],
								'value' => $custom_field_value_info['name']
							);
						}
					}

					if ($custom_field['type'] == 'checkbox' && is_array($order_info['custom_field'][$custom_field['custom_field_id']])) {
						foreach ($order_info['custom_field'][$custom_field['custom_field_id']] as $custom_field_value_id) {
							$custom_field_value_info = $this->model_customer_custom_field->getCustomFieldValue($custom_field_value_id);

							if ($custom_field_value_info) {
								$data['account_custom_fields'][] = array(
									'name'  => $custom_field['name'],
									'value' => $custom_field_value_info['name']
								);
							}
						}
					}

					if ($custom_field['type'] == 'text' || $custom_field['type'] == 'textarea' || $custom_field['type'] == 'file' || $custom_field['type'] == 'date' || $custom_field['type'] == 'datetime' || $custom_field['type'] == 'time') {
						$data['account_custom_fields'][] = array(
							'name'  => $custom_field['name'],
							'value' => $order_info['custom_field'][$custom_field['custom_field_id']]
						);
					}

					if ($custom_field['type'] == 'file') {
						$upload_info = $this->model_tool_upload->getUploadByCode($order_info['custom_field'][$custom_field['custom_field_id']]);

						if ($upload_info) {
							$data['account_custom_fields'][] = array(
								'name'  => $custom_field['name'],
								'value' => $upload_info['name']
							);
						}
					}
				}
			}

			$data['ip']              = $order_info['ip'];
			$data['forwarded_ip']    = $order_info['forwarded_ip'];
			$data['user_agent']      = $order_info['user_agent'];
			$data['accept_language'] = $order_info['accept_language'];

			// Additional Tabs
			$data['tabs'] = array();

			$this->load->model('extension/extension');

			$content = $this->load->controller('payment/' . $order_info['payment_code'] . '/order');

			if ($content) {
				$this->load->language('payment/' . $order_info['payment_code']);

				$data['tabs'][] = array(
					'code'    => $order_info['payment_code'],
					'title'   => $this->language->get('heading_title'),
					'content' => $content
				);
			}

			$extensions = $this->model_extension_extension->getInstalled('fraud');

			foreach ($extensions as $extension) {
				if ($this->config->get($extension . '_status')) {
					$this->load->language('fraud/' . $extension);

					$content = $this->load->controller('fraud/' . $extension . '/order');

					if ($content) {
						$data['tabs'][] = array(
							'code'    => $extension,
							'title'   => $this->language->get('heading_title'),
							'content' => $content
						);
					}
				}
			}

			// API login
			$this->load->model('user/api');

			$api_info = $this->model_user_api->getApi($this->config->get('config_api_id'));

			if ($api_info) {
				$data['api_id'] = $api_info['api_id'];
				$data['api_key'] = $api_info['key'];
				$data['api_ip'] = $this->request->server['REMOTE_ADDR'];
			} else {
				$data['api_id'] = '';
				$data['api_key'] = '';
				$data['api_ip'] = '';
			}

			$data['permission_access_transaction'] = $this->user->hasPermission('access', 'sale/order_transaction');

			//Get User
			$this->load->model('user/user');
			$user = $this->model_user_user->getUser($this->user->getId());

			$data['text_amount'] = $this->language->get('text_amount');
			$data['done_by'] = $this->language->get('text_done_by').' '.$user['firstname'].' '.$user['lastname'];

			$data['header'] = $this->load->controller('common/header');
			$data['column_left'] = $this->load->controller('common/column_left');
			$data['footer'] = $this->load->controller('common/footer');
			$this->response->setOutput($this->load->view('sale/order_info', $data));
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

	protected function getUrl($blacklist = array()) {
		$url_data = array();

		$url_data['token'] = $this->session->data['token'];

		if (isset($this->request->get['filter_order_id']) && !in_array('order_id', $blacklist)) {
			$url_data['filter_order_id'] = $this->request->get['filter_order_id'];
		}

		if (isset($this->request->get['filter_customer']) && !in_array('customer', $blacklist)) {
			$url_data['filter_customer'] = html_entity_decode($this->request->get['filter_customer'], ENT_QUOTES, 'UTF-8');
		}

		if (isset($this->request->get['filter_order_status']) && !in_array('order_status', $blacklist)) {
			$url_data['filter_order_status'] = $this->request->get['filter_order_status'];
		}

		if (isset($this->request->get['filter_recurring_order']) && !in_array('recurring_order', $blacklist)) {
			$url_data['filter_recurring_order'] = $this->request->get['filter_recurring_order'];
		}

		if (isset($this->request->get['filter_total']) && !in_array('total', $blacklist)) {
			$url_data['filter_total'] = $this->request->get['filter_total'];
		}
		
		if (isset($this->request->get['filter_date_added_start']) && !in_array('date_added_start', $blacklist)) {
			$url_data['filter_date_added_start'] = $this->request->get['filter_date_added_start'];
		}

		if (isset($this->request->get['filter_date_added_end']) && !in_array('date_added_end', $blacklist)) {
			$url_data['filter_date_added_end'] = $this->request->get['filter_date_added_end'];
		}
		
		if (isset($this->request->get['filter_date_added']) && !in_array('date_added', $blacklist)) {
			$url_data['filter_date_added'] = $this->request->get['filter_date_added'];
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

	public function createInvoiceNo() {
		$this->load->language('sale/order');

		$json = array();

		if (!$this->user->hasPermission('modify', 'sale/order')) {
			$json['error'] = $this->language->get('error_permission');
		} elseif (isset($this->request->get['order_id'])) {
			if (isset($this->request->get['order_id'])) {
				$order_id = $this->request->get['order_id'];
			} else {
				$order_id = 0;
			}

			$this->load->model('sale/order');

			$invoice_no = $this->model_sale_order->createInvoiceNo($order_id);

			if ($invoice_no) {
				$json['invoice_no'] = $invoice_no;
			} else {
				$json['error'] = $this->language->get('error_action');
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function addReward() {
		$this->load->language('sale/order');

		$json = array();

		if (!$this->user->hasPermission('modify', 'sale/order')) {
			$json['error'] = $this->language->get('error_permission');
		} else {
			if (isset($this->request->get['order_id'])) {
				$order_id = $this->request->get['order_id'];
			} else {
				$order_id = 0;
			}

			$this->load->model('sale/order');

			$order_info = $this->model_sale_order->getOrder($order_id);

			if ($order_info && $order_info['customer_id'] && ($order_info['reward'] > 0)) {
				$this->load->model('customer/customer');

				$reward_total = $this->model_customer_customer->getTotalCustomerRewardsByOrderId($order_id);

				if (!$reward_total) {
					$this->model_customer_customer->addReward($order_info['customer_id'], $this->language->get('text_order_id') . ' #' . $order_id, $order_info['reward'], $order_id);
				}
			}

			$json['success'] = $this->language->get('text_reward_added');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function removeReward() {
		$this->load->language('sale/order');

		$json = array();

		if (!$this->user->hasPermission('modify', 'sale/order')) {
			$json['error'] = $this->language->get('error_permission');
		} else {
			if (isset($this->request->get['order_id'])) {
				$order_id = $this->request->get['order_id'];
			} else {
				$order_id = 0;
			}

			$this->load->model('sale/order');

			$order_info = $this->model_sale_order->getOrder($order_id);

			if ($order_info) {
				$this->load->model('customer/customer');

				$this->model_customer_customer->deleteReward($order_id);
			}

			$json['success'] = $this->language->get('text_reward_removed');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function history() {
		$data = $this->load->language('sale/order');

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$data['histories'] = array();

		$this->load->model('sale/order');

		$results = $this->model_sale_order->getOrderHistories($this->request->get['order_id'], ($page - 1) * 10, 10);

		foreach ($results as $result) {
			$data['histories'][] = array(
				'notify'     => $result['notify'] ? $this->language->get('text_yes') : $this->language->get('text_no'),
				'status'     => $result['status'],
				'comment'    => nl2br($result['comment']),
				'date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added']))
			);
		}

		$history_total = $this->model_sale_order->getTotalOrderHistories($this->request->get['order_id']);

		$pagination = new Pagination();
		$pagination->total = $history_total;
		$pagination->page  = $page;
		$pagination->limit = 10;
		$pagination->url   = $this->url->link('sale/order/history', 'token=' . $this->session->data['token'] . '&order_id=' . $this->request->get['order_id'] . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($history_total) ? (($page - 1) * 10) + 1 : 0, ((($page - 1) * 10) > ($history_total - 10)) ? $history_total : ((($page - 1) * 10) + 10), $history_total, ceil($history_total / 10));

		$this->response->setOutput($this->load->view('sale/order_history', $data));
	}

	public function invoice() {
		$data = $this->load->language('sale/order');

		$data['title'] = $this->language->get('text_invoice');

		if ($this->request->server['HTTPS']) {
			$data['base'] = HTTPS_SERVER;
		} else {
			$data['base'] = HTTP_SERVER;
		}

		$data['lang']     = $this->language->get('code');
		$data['base_url'] =  $this->config->get('config_url');

		$this->load->model('sale/order');
		$this->load->model('setting/setting');

		$data['orders'] = array();

		$orders = array();

		if (isset($this->request->post['selected'])) {
			$orders = $this->request->post['selected'];
		} elseif (isset($this->request->get['order_id'])) {
			$orders[] = $this->request->get['order_id'];
		}

		foreach ($orders as $order_id) {
			$order_info = $this->model_sale_order->getOrder($order_id);

			if ($order_info) {
				$store_info = $this->model_setting_setting->getSettings('config', $order_info['store_id']);

				if ($store_info) {
					$store_address   = $store_info['config_address'];
					$store_email     = $store_info['config_email'];
					$store_telephone = $store_info['config_telephone'];
				} else {
					$store_address   = $this->config->get('config_address');
					$store_email     = $this->config->get('config_email');
					$store_telephone = $this->config->get('config_telephone');
				}

				if ($order_info['invoice_no']) {
					$invoice_no = $order_info['invoice_prefix'] . $order_info['invoice_no'];
				} else {
					$invoice_no = '';
				}

				$this->load->model('tool/upload');

				$product_data = array();

				$products = $this->model_sale_order->getOrderProducts($order_id);

				foreach ($products as $product) {
					$product_data[] = array(
						'name'             => $product['name'],
						'model'            => $product['model'],
						'type'             => $product['account_type'],
						'account_username' => $product['account_username'],
						'quantity'         => $product['quantity'],
						'price'            => $this->currency->format($product['price'] + ($this->config->get('config_tax') ? $product['tax'] : 0), $order_info['currency_code'], $order_info['currency_value']),
						'total'            => $this->currency->format($product['total'] + ($this->config->get('config_tax') ? ($product['tax'] * $product['quantity']) : 0), $order_info['currency_code'], $order_info['currency_value'])
					);
				}

				$voucher_data = array();

				$vouchers = $this->model_sale_order->getOrderVouchers($order_id);

				foreach ($vouchers as $voucher) {
					$voucher_data[] = array(
						'description' => $voucher['description'],
						'amount'      => $this->currency->format($voucher['amount'], $order_info['currency_code'], $order_info['currency_value'])
					);
				}

				$total_data = array();

				$totals = $this->model_sale_order->getOrderTotals($order_id);

				foreach ($totals as $total) {
					$total_data[] = array(
						'title' => $total['title'],
						'text'  => $this->currency->format($total['value'], $order_info['currency_code'], $order_info['currency_value']),
					);
				}

				$data['orders'][] = array(
					'order_id'        => $order_id,
					'invoice_no'      => $invoice_no,
					'date_added'      => date($this->language->get('date_format_short'), strtotime($order_info['date_added'])),
					'store_name'      => $order_info['store_name'],
					'store_url'       => rtrim($order_info['store_url'], '/'),
					'store_address'   => nl2br($store_address),
					'store_email'     => $store_email,
					'store_telephone' => $store_telephone,
					'email'           => $order_info['email'],
					'telephone'       => $order_info['telephone'],
					'payment_method'  => $order_info['payment_method'],
					'product'         => $product_data,
					'voucher'         => $voucher_data,
					'total'           => $total_data,
					'comment'         => nl2br($order_info['comment'])
				);
			}
		}

		$this->response->setOutput($this->load->view('sale/order_invoice', $data));
	}
}