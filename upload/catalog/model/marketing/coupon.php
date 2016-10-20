<?php
class ModelMarketingCoupon extends Model {
	public function getCoupon($coupon_id) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "coupon` WHERE coupon_id = '" . (int)$coupon_id . "' AND status = '1'");

		return $query->row;
	}
}