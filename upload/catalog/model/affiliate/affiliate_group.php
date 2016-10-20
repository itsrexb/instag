<?php
class ModelAffiliateAffiliateGroup extends Model {
	public function getAffiliateGroupCommissions($affiliate_group_id) {
		$affiliate_group_commission_data = array();

		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "affiliate_group_commission` WHERE affiliate_group_id = '" . (int)$affiliate_group_id . "' ORDER BY level DESC");

		foreach ($query->rows as $result) {
			if (isset($affiliate_group_commission_data[$result['commission_affiliate_group_id']])) {
				$affiliate_group_commission_data[$result['commission_affiliate_group_id']][$result['level']] = $result['commission'];
			} else {
				$affiliate_group_commission_data[$result['commission_affiliate_group_id']] = array($result['level'] => $result['commission']);
			}
		}

		return $affiliate_group_commission_data;
	}
}