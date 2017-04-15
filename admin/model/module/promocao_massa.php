<?php
/*
  Model promoções em massa
  Criado por Marlon em 03/02/2015
*/
class ModelModulePromocaoMassa extends Model {
	public function getProductsByCategoryId($category_id) {
		$query = $this->db->query("SELECT p.product_id, pd.name, p.price FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) LEFT JOIN " . DB_PREFIX . "product_to_category p2c ON (p.product_id = p2c.product_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p2c.category_id = '" . (int)$category_id . "' ORDER BY pd.name ASC");

		return $query->rows;
	}

	public function addSpecial($data) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "product_special SET product_id = '" . (int)$data['product_id'] . "', customer_group_id = '" . (int)$data['customer_group_id'] . "', priority = '1', price = '" . (float)$data['price'] . "', date_start = '" . $this->db->escape($data['date_start']) . "', date_end = '" . $this->db->escape($data['date_end']) . "'");
	}
}
?>
