<?php
$biller_company_name = "";
$biller_registration_number = "";
$biller_vat_number = "";
if(is_wc_endpoint_url('order-pay')) {
    global $wp;
    $order_id = absint($wp->query_vars['order-pay']);
    $order = new WC_Order($order_id);
    $biller_company_name =$order->get_meta('biller_company_name');
    $biller_registration_number =$order->get_meta('biller_registration_number');
    $biller_vat_number =$order->get_meta('biller_vat_number');
}
?>

<div class="form-row form-row-wide">
	<label> <?php esc_html_e( 'Company name', 'biller-business-invoice' ); ?><span class="required">*</span></label>
	<input id="biller_company_name" class="input-text" name ="biller_company_name" type="text" autocomplete="off" value="<?php esc_html_e($biller_company_name)?>">
	<label> <?php esc_html_e( 'Registration number', 'biller-business-invoice' ); ?>
		<span>(<?php esc_html_e( 'recommended', 'biller-business-invoice' ); ?>)</span></label>
	<input id="biller_registration_number" class="input-text" name ="biller_registration_number" type="text" autocomplete="off" value="<?php esc_html_e($biller_registration_number)?>">
	<label> <?php esc_html_e( 'VAT number', 'biller-business-invoice' ); ?>
		<span>(<?php esc_html_e( 'optional', 'biller-business-invoice' ); ?>)</span></label>
	<input id="biller_vat_number" class="input-text" name ="biller_vat_number" type="text" autocomplete="off" value="<?php esc_html_e($biller_vat_number)?>">
</div>
