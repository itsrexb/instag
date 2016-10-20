<?php
class ControllerAnalyticsFacebook extends Controller {
	public function index() {
		return html_entity_decode($this->config->get('facebook_code'), ENT_QUOTES, 'UTF-8');
	}
}