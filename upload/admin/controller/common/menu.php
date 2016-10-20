<?php
class ControllerCommonMenu extends Controller {
	public function index() {
		$data = $this->load->language('common/menu');

		$data['account'] = $this->url->link('customer/account', 'token=' . $this->session->data['token'], true);
		$data['account_tip'] = $this->url->link('catalog/account_tip', 'token=' . $this->session->data['token'], true);
		$data['affiliate'] = $this->url->link('affiliate/affiliate', 'token=' . $this->session->data['token'], true);
		$data['affiliate_group'] = $this->url->link('affiliate/affiliate_group', 'token=' . $this->session->data['token'], true);
		$data['analytics'] = $this->url->link('extension/analytics', 'token=' . $this->session->data['token'], true);
		$data['home'] = $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true);
		$data['api'] = $this->url->link('user/api', 'token=' . $this->session->data['token'], true);
		$data['backup'] = $this->url->link('tool/backup', 'token=' . $this->session->data['token'], true);
		$data['banner'] = $this->url->link('design/banner', 'token=' . $this->session->data['token'], true);
		$data['capability'] = $this->url->link('catalog/capability', 'token=' . $this->session->data['token'], true);
		$data['captcha'] = $this->url->link('extension/captcha', 'token=' . $this->session->data['token'], true);
		$data['category'] = $this->url->link('catalog/category', 'token=' . $this->session->data['token'], true);
		$data['source_interest'] = $this->url->link('catalog/source_interest', 'token=' . $this->session->data['token'], true);
		$data['country'] = $this->url->link('localisation/country', 'token=' . $this->session->data['token'], true);
		$data['conversion'] = $this->url->link('extension/conversion', 'token=' . $this->session->data['token'], true);
		$data['coupon'] = $this->url->link('marketing/coupon', 'token=' . $this->session->data['token'], true);
		$data['currency'] = $this->url->link('localisation/currency', 'token=' . $this->session->data['token'], true);
		$data['customer'] = $this->url->link('customer/customer', 'token=' . $this->session->data['token'], true);
		$data['customer_fields'] = $this->url->link('customer/customer_field', 'token=' . $this->session->data['token'], true);
		$data['customer_group'] = $this->url->link('customer/customer_group', 'token=' . $this->session->data['token'], true);
		$data['custom_field'] = $this->url->link('customer/custom_field', 'token=' . $this->session->data['token'], true);
		$data['download'] = $this->url->link('catalog/download', 'token=' . $this->session->data['token'], true);
		$data['error_log'] = $this->url->link('tool/error_log', 'token=' . $this->session->data['token'], true);
		$data['feed'] = $this->url->link('extension/feed', 'token=' . $this->session->data['token'], true);
		$data['fraud'] = $this->url->link('extension/fraud', 'token=' . $this->session->data['token'], true);
		$data['geo_zone'] = $this->url->link('localisation/geo_zone', 'token=' . $this->session->data['token'], true);
		$data['information'] = $this->url->link('catalog/information', 'token=' . $this->session->data['token'], true);
		$data['language'] = $this->url->link('localisation/language', 'token=' . $this->session->data['token'], true);
		$data['layout'] = $this->url->link('design/layout', 'token=' . $this->session->data['token'], true);
		$data['location'] = $this->url->link('localisation/location', 'token=' . $this->session->data['token'], true);
		$data['marketing'] = $this->url->link('marketing/marketing', 'token=' . $this->session->data['token'], true);
		$data['module'] = $this->url->link('extension/module', 'token=' . $this->session->data['token'], true);
		$data['marketing_extension'] = $this->url->link('extension/marketing', 'token=' . $this->session->data['token'], true);
		$data['order'] = $this->url->link('sale/order', 'token=' . $this->session->data['token'], true);
		$data['order_status'] = $this->url->link('localisation/order_status', 'token=' . $this->session->data['token'], true);
		$data['payment'] = $this->url->link('extension/payment', 'token=' . $this->session->data['token'], true);
		$data['product'] = $this->url->link('catalog/product', 'token=' . $this->session->data['token'], true);
		$data['recurring'] = $this->url->link('catalog/recurring', 'token=' . $this->session->data['token'], true);
		$data['recurring_order'] = $this->url->link('sale/recurring_order', 'token=' . $this->session->data['token'], true);
		$data['report_sale_profit'] = $this->url->link('report/sale_profit', 'token=' . $this->session->data['token'], true);
		$data['report_sale_order'] = $this->url->link('report/sale_order', 'token=' . $this->session->data['token'], true);
		$data['report_sale_recurring_order'] = $this->url->link('report/sale_recurring_order', 'token=' . $this->session->data['token'], true);
		$data['report_sale_tax'] = $this->url->link('report/sale_tax', 'token=' . $this->session->data['token'], true);
		$data['report_sale_coupon'] = $this->url->link('report/sale_coupon', 'token=' . $this->session->data['token'], true);
		$data['report_product_purchased'] = $this->url->link('report/product_purchased', 'token=' . $this->session->data['token'], true);
		$data['report_customer_activity'] = $this->url->link('report/customer_activity', 'token=' . $this->session->data['token'], true);
		$data['report_customer_online'] = $this->url->link('report/customer_online', 'token=' . $this->session->data['token'], true);
		$data['report_customer_order'] = $this->url->link('report/customer_order', 'token=' . $this->session->data['token'], true);
		$data['report_customer_reward'] = $this->url->link('report/customer_reward', 'token=' . $this->session->data['token'], true);
		$data['report_customer_credit'] = $this->url->link('report/customer_credit', 'token=' . $this->session->data['token'], true);
		$data['report_customer_profit'] = $this->url->link('report/customer_profit', 'token=' . $this->session->data['token'], true);
		$data['report_marketing'] = $this->url->link('report/marketing', 'token=' . $this->session->data['token'], true);
		$data['report_affiliate'] = $this->url->link('report/affiliate', 'token=' . $this->session->data['token'], true);
		$data['report_account_decline'] = $this->url->link('report/account_decline', 'token=' . $this->session->data['token'], true);
		$data['report_account_follower'] = $this->url->link('report/account_follower_growth', 'token=' . $this->session->data['token'], true);
		$data['report_affiliate_activity'] = $this->url->link('report/affiliate_activity', 'token=' . $this->session->data['token'], true);
		$data['report_account_source_interest'] = $this->url->link('report/account_source_interest', 'token=' . $this->session->data['token'], true);
		$data['setting'] = $this->url->link('setting/store', 'token=' . $this->session->data['token'], true);
		$data['tax_class'] = $this->url->link('localisation/tax_class', 'token=' . $this->session->data['token'], true);
		$data['tax_rate'] = $this->url->link('localisation/tax_rate', 'token=' . $this->session->data['token'], true);
		$data['total'] = $this->url->link('extension/total', 'token=' . $this->session->data['token'], true);
		$data['upload'] = $this->url->link('tool/upload', 'token=' . $this->session->data['token'], true);
		$data['url_alias'] = $this->url->link('setting/url_alias', 'token=' . $this->session->data['token'], true);
		$data['user'] = $this->url->link('user/user', 'token=' . $this->session->data['token'], true);
		$data['user_group'] = $this->url->link('user/user_permission', 'token=' . $this->session->data['token'], true);
		$data['voucher'] = $this->url->link('sale/voucher', 'token=' . $this->session->data['token'], true);
		$data['voucher_theme'] = $this->url->link('sale/voucher_theme', 'token=' . $this->session->data['token'], true);
		$data['zone'] = $this->url->link('localisation/zone', 'token=' . $this->session->data['token'], true);

		return $this->load->view('common/menu', $data);
	}
}