<?php

class ModelSettingUrlAlias extends Model {
  public function updateUrlAliases($data) {
    $this->db->query("DELETE FROM `" . DB_PREFIX . "url_alias` WHERE query NOT LIKE '%_id=%'");

    foreach ($data as $url_alias) {
      $this->db->query("INSERT INTO `" . DB_PREFIX . "url_alias` SET query = '" . $this->db->escape($url_alias['route']) . "', keyword = '" . $this->db->escape($url_alias['keyword']) . "'");
    }
  }

  public function getUrlAliases() {
    $url_alias_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "url_alias` WHERE query NOT LIKE '%_id=%'");

    $url_alias_data = array();

    foreach ($url_alias_query->rows as $url_alias) {
      $url_alias_data[] = array(
        'route'   => str_replace('route=', '', $url_alias['query']),
        'keyword' => $url_alias['keyword']
      );
    }

    return $url_alias_data;
  }
}
