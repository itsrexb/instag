<?php
class ControllerConversionCustomer extends Controller {
	public function index() {
		return html_entity_decode($this->config->get('conversion_customer_code'), ENT_QUOTES, 'UTF-8');
	}
}