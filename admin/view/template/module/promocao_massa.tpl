<?php echo $header; ?>
<div id="content">
  <div class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
    <?php } ?>
  </div>
  <?php if ($error_warning) { ?>
  <div class="warning"><?php echo $error_warning; ?></div>
  <?php } ?>
  <div class="box">
    <div class="heading">
      <h1><img src="view/image/module.png" alt="" /> <?php echo $heading_title; ?></h1>
      <div class="buttons"><a onclick="$('#form').submit();" class="button"><?php echo $button_save; ?></a><a href="<?php echo $cancel; ?>" class="button"><?php echo $button_cancel; ?></a></div>
    </div>
    <div class="content">
      <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
        <table class="form">
          <tr>
            <td>Departamento:</td>
            <td>
              <select name="category_id">
                <option value="">Selecione...</option>
                <?php foreach ($categories as $category) { ?>
                <?php if ($category['category_id'] == $category_id) { ?>
                <option value="<?php echo $category['category_id']; ?>" selected="selected"><?php echo $category['name']; ?></option>
                <?php } else { ?>
                <option value="<?php echo $category['category_id']; ?>"><?php echo $category['name']; ?></option>
                <?php } ?>
                <?php } ?>
              </select>
              <?php if (isset($error['error_category'])) { ?>
              <span class="error"><?php echo $error['error_category']; ?></span>
              <?php } ?>
            </td>
          </tr>
          <tr>
            <td>Grupo de clientes:</td>
            <td>
              <select name="customer_group_id">
                <?php foreach ($customer_groups as $customer_group) { ?>
                <?php if ($customer_group['customer_group_id'] == $customer_group_id) { ?>
                <option value="<?php echo $customer_group['customer_group_id']; ?>" selected="selected"><?php echo $customer_group['name']; ?></option>
                <?php } else { ?>
                <option value="<?php echo $customer_group['customer_group_id']; ?>"><?php echo $customer_group['name']; ?></option>
                <?php } ?>
                <?php } ?>
              </select>
              <?php if (isset($error['error_customer_group'])) { ?>
              <span class="error"><?php echo $error['error_customer_group']; ?></span>
              <?php } ?>
            </td>
          </tr>
          <tr>
            <td>
              <label>
                <?php if ($opcao) { ?>
                <input type="radio" name="opcao" value="0">
                <?php } else { ?>
                <input type="radio" name="opcao" value="0" checked="checked">
                <?php } ?>
                Desconto em porcentagem:
              </label>
            </td>
            <td>
              <input type="text" name="porcentagem" value="<?php echo $porcentagem; ?>" class="float" /> %</td>
              <?php if (isset($error['error_porcentagem'])) { ?>
              <span class="error"><?php echo $error['error_porcentagem']; ?></span>
              <?php } ?>
          </tr>
          <tr>
            <td>
              <label>
                <?php if ($opcao) { ?>
                <input type="radio" name="opcao" value="1" checked="checked">
                <?php } else { ?>
                <input type="radio" name="opcao" value="1">
                <?php } ?>
                Promoção com preço fixo:
              </label>
            </td>
            <td><input type="text" name="price" value="<?php echo $price; ?>" class="float" />
              <?php if (isset($error['error_price'])) { ?>
              <span class="error"><?php echo $error['error_price']; ?></span>
              <?php } ?></td>
          </tr>
          <tr>
            <td>Data inicial:</td>
            <td><input type="text" name="date_start" value="<?php echo $date_start; ?>" class="date" /></td>
          </tr>
          <tr>
            <td>Data final:</td>
            <td><input type="text" name="date_end" value="<?php echo $date_end; ?>" class="date" /></td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td><a class="button" onclick="visualizar();">Visualizar alterações</a></td>
          </tr>
        </table>
      </form>
      <table id="produtos" class="list" style="display:none;">
        <thead>
          <tr>
            <td class="left">Produto</td>
            <td class="right">Preço atual</td>
            <td class="right">Preço promocional</td>
          </tr>
        </thead>
        <tbody>
        </tbody>
      </table>
    </div>
  </div>
</div>
<script type="text/javascript"><!--
$('.date').datepicker({dateFormat: 'yy-mm-dd'});
function visualizar() {
	$.ajax({
		url: 'index.php?route=module/promocao_massa/produtos&token=<?php echo $token; ?>',
    data: $('#form input[type="text"], #form input[type="radio"]:checked, #form select'),
    type: 'post',
		dataType: 'json',
		success: function(json) {
      console.log(json);
      if (json.mensagem) {
        window.alert(json.mensagem);
      }
			if (json.produtos) {
        $('#produtos').show();
        removeProducts();
        var i;
        for (i = 0; i < json.produtos.length; i++) {
          addProduct(json.produtos[i].name, json.produtos[i].price, json.produtos[i].new_price);
        }
      } else {
        $('#produtos').hide();
      }
		},
    error: function(e) {
      console.log(e);
    }
	});
}
function removeProducts() {
  $('#produtos tbody').html('');
}
function addProduct(name, price, new_price) {
  html = '<tr>';
  html += '  <td class="left">' + name + '</td>';
  html += '  <td class="right">' + price + '</td>';
  html += '  <td class="right">' + new_price + '</td>';
  html += '</tr>';

  $('#produtos tbody').append(html);
}
//--></script>
<script type="text/javascript"><!--
function prepararFloat(el) {
  el.value = el.value.replace(/\,/g, '.');
  var posDecimal = el.value.lastIndexOf('.');
  if (posDecimal > 0) {
    var r = el.value.substr(0, posDecimal);
    r = r.replace(/\./g, '');
    r += el.value.substr(posDecimal);
    el.value = r;
  }
}
$('input.float').on('blur', function() {prepararFloat(this);});
//--></script>
<?php echo $footer; ?>