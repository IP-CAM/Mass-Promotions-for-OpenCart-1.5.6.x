<?php
/*
	Controller promoções em massa
	Criado por Marlon em 03/02/2015
*/
class ControllerModulePromocaoMassa extends Controller {
	private $error = array();
	protected $title = 'Promoções em massa';

	public function index() {
		$this->document->setTitle($this->title);

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			if (!empty($this->request->post['opcao'])) {
				$opcao = 1;
			} else {
				$opcao = 0;
			}

			$this->load->model('module/promocao_massa');

			$produtos = $this->model_module_promocao_massa->getProductsByCategoryId($this->request->post['category_id']);

			foreach ($produtos as $produto) {
				$data = array(
					'product_id' => $produto['product_id'],
					'customer_group_id' => $this->request->post['customer_group_id'],
					'date_start' => $this->request->post['date_start'],
					'date_end' => $this->request->post['date_end']
				);
				if ($opcao) {
					$data['price'] = $this->request->post['price'];
				} else {
					$data['price'] = $produto['price'] - ($produto['price'] * ($this->request->post['porcentagem'] / 100));
				}
				$this->model_module_promocao_massa->addSpecial($data);
			}

			$this->session->data['success'] = 'Promoções salvas.';

			if ($this->user->getGroupId() == 1) {
				$this->redirect($this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'));
			} else {
				$this->redirect($this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'));
			}
		}

		// Widgets de data em PT-BR
		$this->document->addScript('view/javascript/jquery/ui/i18n/jquery.ui.datepicker-pt-BR.js');

		$this->data['heading_title'] = $this->title;

		$this->data['button_save'] = $this->language->get('button_save');
		$this->data['button_cancel'] = $this->language->get('button_cancel');

		$this->data['breadcrumbs'] = array();

		$this->data['breadcrumbs'][] = array(
			'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
			'separator' => false
		);

		$this->data['breadcrumbs'][] = array(
			'text'      => 'Módulos',
			'href'      => $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'),
			'separator' => ' :: '
		);

		$this->data['breadcrumbs'][] = array(
			'text'      => $this->title,
			'href'      => $this->url->link('module/promocao_massa', 'token=' . $this->session->data['token'], 'SSL'),
			'separator' => ' :: '
		);

		$this->data['action'] = $this->url->link('module/promocao_massa', 'token=' . $this->session->data['token'], 'SSL');

		if ($this->user->getGroupId() == 1) {
			$this->data['cancel'] = $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL');
		} else {
			$this->data['cancel'] = $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL');
		}

		$this->data['error'] = $this->error;

		if (!empty($this->error['error_warning'])) {
			$this->data['error_warning'] = $this->error['error_warning'];
		} else {
			$this->data['error_warning'] = '';
		}

		$this->data['token'] = $this->session->data['token'];

		if (isset($this->request->post['opcao'])) {
			$this->data['opcao'] = $this->request->post['opcao'];
		} else {
			$this->data['opcao'] = 0;
		}

		if (isset($this->request->post['price'])) {
			$this->data['price'] = $this->request->post['price'];
		} else {
			$this->data['price'] = '';
		}

		if (isset($this->request->post['porcentagem'])) {
			$this->data['porcentagem'] = $this->request->post['porcentagem'];
		} else {
			$this->data['porcentagem'] = '';
		}

		if (isset($this->request->post['category_id'])) {
			$this->data['category_id'] = $this->request->post['category_id'];
		} else {
			$this->data['category_id'] = '';
		}

		if (isset($this->request->post['customer_group_id'])) {
			$this->data['customer_group_id'] = $this->request->post['customer_group_id'];
		} else {
			$this->data['customer_group_id'] = $this->config->get('config_customer_group_id');
		}

		if (isset($this->request->post['date_start'])) {
			$this->data['date_start'] = $this->request->post['date_start'];
		} else {
			$this->data['date_start'] = date('Y-m-d');
		}

		if (isset($this->request->post['date_end'])) {
			$this->data['date_end'] = $this->request->post['date_end'];
		} else {
			$this->data['date_end'] = '';
		}

		$this->load->model('catalog/category');

		$this->data['categories'] = array();

		$categories = $this->model_catalog_category->getCategories(array());

		foreach ($categories as $category) {
			$this->data['categories'][] = array(
				'category_id' => $category['category_id'],
				'name'        => $category['name']
			);
		}

		$this->load->model('sale/customer_group');

		$this->data['customer_groups'] = $this->model_sale_customer_group->getCustomerGroups();

		$this->data['is_admin'] = ($this->user->getGroupId() == 1);

		$this->template = 'module/promocao_massa.tpl';
		$this->children = array(
			'common/header',
			'common/footer'
		);

		$this->response->setOutput($this->render());
	}

	public function produtos() {
		$this->load->model('module/promocao_massa');

		$json = array();

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$produtos = $this->model_module_promocao_massa->getProductsByCategoryId($this->request->post['category_id']);

			$json['produtos'] = array();

			if (!empty($this->request->post['opcao'])) {
				$opcao = 1;
			} else {
				$opcao = 0;
			}

			foreach ($produtos as $produto) {
				if ($opcao) {
					$new_price = $this->request->post['price'];
				} else {
					$new_price = $produto['price'] - ($produto['price'] * ($this->request->post['porcentagem'] / 100));
				}

				$json['produtos'][] = array(
					'name' => $produto['name'],
					'price' => $this->currency->format($produto['price']),
					'new_price' => $this->currency->format($new_price)
				);
			}

			if (!$produtos) {
				$json['mensagem'] = 'Não há produtos neste departamento.';
			}
		}

		if ($this->error) {
			$json['mensagem'] = array_shift($this->error);
		}

		$this->response->setOutput(json_encode($json));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'module/promocao_massa')) {
			$this->error['warning'] = 'Você não tem permissão para modificar este módulo.';
		}

		if (empty($this->request->post['category_id'])) {
			$this->error['error_category'] = 'Selecione um departamento.';
		}

		if (empty($this->request->post['customer_group_id'])) {
			$this->error['error_customer_group'] = 'Selecione um grupo de clientes.';
		}

		if (!empty($this->request->post['opcao']) && !isset($this->request->post['price'])) {
			$this->error['error_price'] = 'Digite um preço fixo.';
		}

		if (empty($this->request->post['opcao']) && empty($this->request->post['porcentagem'])) {
			$this->error['error_porcentagem'] = 'Digite um valor de porcentagem.';
		}

		if (empty($this->request->post['date_start'])) {
			$this->error['error_date_start'] = 'Informe a data inicial da promoção.';
		}

		if (empty($this->request->post['date_end'])) {
			$this->error['error_date_end'] = 'Informe a data final da promoção.';
		}

		if (!$this->error) {
			return true;
		} else {
			return false;
		}	
	}
}
?>