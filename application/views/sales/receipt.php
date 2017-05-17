<?php $this->load->view("partial/header"); ?>
<?php
$is_integrated_credit_sale = is_sale_integrated_cc_processing();
if (isset($error_message))
{
  echo '<h1 style="text-align: center;">'.$error_message.'</h1>';
  exit;
}
?>

<?php echo form_open('http://localhost/loans/loans', array('id' => 'external_loan_form', 'target' => '_blank')); ?>

<div id="receipt_wrapper" class="receipt_<?php echo $this->config->item('receipt_text_size');?>">
  <div id="receipt_header">
    <?php if($this->config->item('company_logo')) {?>
    <div id="company_logo"><?php echo img(array('src' => $this->Appconfig->get_logo_image())); ?></div>
    <?php } ?>
    <div id="company_address">Dirección. <?php echo nl2br($this->Location->get_info_for_key('address')); ?></div>
    <div id="company_phone">Telf. <?php echo $this->Location->get_info_for_key('phone'); ?></div>
    <?php if($this->config->item('website')) { ?>
      <div id="website"><?php echo $this->config->item('website'); ?></div>
    <?php } ?>
    <div id="sale_receipt"><?php echo "NOTA DE REMISIÓN"; ?></div>
    <div id="sale_time"><?php echo $transaction_time ?></div>
    <div class="pull-right"><button class="btn btn-primary text-white hidden-print" id="new_sale_button_1" onclick="window.location='<?php echo site_url('sales'); ?>'" > <?php echo lang('sales_new_sale'); ?> </button></div>
  </div>
  <div id="receipt_general_info">
    <?php if(isset($customer))
    {
    ?>
    <input type="hidden" id="customer_id" name="customer_id" value="<?= $customer_id; ?>" />
    <input type="hidden" id="customer_name" name="customer_name" value="<?= $customer_name; ?>" />

      <div id="customer"><?php echo lang('customers_customer').": ".$customer; ?></div>
      <?php if(!empty($customer_address_1)){ ?><div><?php echo lang('common_address'); ?> : <?php echo $customer_address_1; ?></div><?php } ?>
      <?php if (!empty($customer_city)) { echo $customer_city;} ?>
      <?php if(!empty($customer_phone)){ ?><div><?php echo lang('common_phone_number'); ?> : <?php echo $customer_phone; ?></div><?php } ?>
      <?php if(!empty($customer_email)){ ?><div><?php echo lang('common_email'); ?> : <?php echo $customer_email; ?></div><?php } ?>
    <?php
    }
    ?>
    <div id="sale_id"><?php echo "Nota de Remisión : ".$sale_id; ?></div>
    <input type="hidden" id="sale_id" name="sale_id" value="<?= $sale_id; ?>" />
    <input type="hidden" id="sale_id_raw" name="sale_id_raw" value="<?= $sale_id_raw; ?>" />
    <?php if (isset($sale_type)) { ?>
      <div id="sale_type"><?php echo $sale_type; ?></div>
      <input type="hidden" id="sale_type" name="sale_type" value="<?= $sale_type; ?>" />
    <?php } ?>
    
    <?php
    if ($register_name)
    {
    ?>
      <div id="sale_register_used"><?php echo 'CAJA: '.$register_name; ?></div>    
    <?php
    }
    ?>
    
    
    <?php
    if ($tier)
    {
    ?>
      <div id="tier"><?php echo lang('items_tier_name').': '.$tier; ?></div>    
    <?php
    }
    ?>
    
    <div id="employee"><?php echo lang('employees_employee').": ".$employee; ?></div>
    <?php 
    if($this->Location->get_info_for_key('enable_credit_card_processing'))
    {
      echo '<div id="mercahnt_id">'.lang('config_merchant_id').': '.$this->Location->get_info_for_key('merchant_id').'</div>';
    }
    ?>
    
  </div>
  <table id="receipt_items">
  <tr style="line-height:8px;">
  <th class="left_text_align" style="width:<?php echo $discount_exists ? "33%" : "49%"; ?>;"><?php echo lang('items_item'); ?></th>
  <th class="gift_receipt_element left_text_align" style="width:20%;"><?php echo lang('common_price'); ?></th>
  <th class="left_text_align" style="width:15%;">Cuota Inicial</th>
  <?php if($discount_exists) 
    {
  ?>
  <th class="gift_receipt_element left_text_align" style="width:16%;"><?php echo lang('sales_discount'); ?></th>
  <?php
  }
  ?>
  <th  class="gift_receipt_element right_text_align" style="width:16%;"><?php echo lang('sales_total'); ?></th>
  </tr>
  <?php
  foreach(array_reverse($cart, true) as $line=>$item)
  {
  ?>
    <tr style="line-height:8px;">
    <td class="left_text_align"><?php echo $item['name']; ?><?php if ($item['size']){ ?> (<?php echo $item['size']; ?>)<?php } ?></td>
    <td class="gift_receipt_element left_text_align"><?php echo to_currency($item['price']); ?></td>
    <td class="left_text_align"><?php echo to_currency($item['cuotainicial']); ?></td>
    <?php if($discount_exists) 
    {
    ?>
    <td class="gift_receipt_element left_text_align"><?php echo to_currency($item['discount']); ?></td>
    <?php
    }
    ?>
    <td class="gift_receipt_element right_text_align"><?php echo to_currency($item['price']*$item['quantity']-$item['discount']-$item['cuotainicial']); ?></td>
    </tr>

      <tr>
      <td colspan="3" align="left"><?php echo $item['description']; ?></td>
    <td colspan="1" ><?php echo isset($item['serialnumber']) ? $item['serialnumber'] : ''; ?></td>
    
    <?php if($discount_exists) {?>
    <td colspan="1"><?php echo '&nbsp;'; ?></td>
    <?php } ?>
      </tr>

  <?php
  }
  ?>
  <tr class="gift_receipt_element">
  <td class="right_text_align" colspan="<?php echo $discount_exists ? '4' : '3'; ?>" style='border-top:2px solid #000000;'><?php echo lang('sales_sub_total'); ?></td>
  <td class="right_text_align" colspan="1" style='border-top:2px solid #000000;'><?php echo to_currency($subtotal); ?></td>
  </tr>

  <?php if ($this->config->item('group_all_taxes_on_receipt')) { ?>
    <?php 
    $total_tax = 0;
    foreach($taxes as $name=>$value) 
    {
      $total_tax+=$value;
     }
    ?>  
    <tr class="gift_receipt_element">
      <td class="right_text_align" colspan="<?php echo $discount_exists ? '4' : '3'; ?>"><?php echo lang('reports_tax'); ?>:</td>
      <td class="right_text_align" colspan="1"><?php echo to_currency($total_tax); ?></td>
    </tr>
    
  <?php }else {?>
    <?php foreach($taxes as $name=>$value) { ?>
      <tr class="gift_receipt_element">
        <td class="right_text_align" colspan="<?php echo $discount_exists ? '4' : '3'; ?>"><?php echo $name; ?>:</td>
        <td class="right_text_align" colspan="1"><?php echo to_currency($value); ?></td>
      </tr>
    <?php }; ?>
  <?php } ?>

  <tr  style="line-height:8px;" class="gift_receipt_element">
  <td class="right_text_align "colspan="<?php echo $discount_exists ? '4' : '3'; ?>"><?php echo lang('sales_total'); ?></td>
  <td class="right_text_align" colspan="1"><?php echo $this->config->item('round_cash_on_sales') && $is_sale_cash_payment ?  to_currency(round_to_nearest_05($total)) : to_currency($total); ?></td>
    <input type="hidden" id="loan_amount" name="loan_amount" value="<?= ($total); ?>" />
  </tr>

    <tr  style="line-height:8px;"><td colspan="<?php echo $discount_exists ? '5' : '4'; ?>">&nbsp;</td></tr>

  <?php
    foreach($payments as $payment_id=>$payment)
  { ?>
    <tr class="gift_receipt_element">
    <td class="right_text_align" colspan="<?php echo $discount_exists ? '3' : '2'; ?>"><?php echo (isset($show_payment_times) && $show_payment_times) ?  date(get_date_format().' '.get_time_format(), strtotime($payment['payment_date'])) : lang('sales_payment'); ?></td>
    
    <?php if ($is_integrated_credit_sale || sale_has_partial_credit_card_payment()) { ?>
      <td class="right_text_align" colspan="1"><?php $splitpayment=explode(':',$payment['payment_type']); echo $splitpayment[0]; ?>: <?php echo $payment['card_issuer']. ' '.$payment['truncated_card']; ?></td>
    <?php } else { ?>
      <td class="right_text_align" colspan="1"><?php $splitpayment=explode(':',$payment['payment_type']); echo $splitpayment[0]; ?> </td>                      
    <?php } ?>
    <td class="right_text_align" colspan="1"><?php echo $this->config->item('round_cash_on_sales') && $payment['payment_type'] == lang('sales_cash') ?  to_currency(round_to_nearest_05($payment['payment_amount'])) : to_currency($payment['payment_amount']); ?>  </td>
    </tr>
  <?php
  }
  ?>  
    <tr><td colspan="<?php echo $discount_exists ? '5' : '4'; ?>">&nbsp;</td></tr>

  <?php foreach($payments as $payment) {?>
    <?php if (strpos($payment['payment_type'], lang('sales_giftcard'))!== FALSE) {?>
  <tr  style="line-height:8px;" class="gift_receipt_element">
    <td class="right_text_align" colspan="<?php echo $discount_exists ? '3' : '2'; ?>"><?php echo lang('sales_giftcard_balance'); ?></td>
    <td class="right_text_align" colspan="1"><?php echo $payment['payment_type'];?> </td>
    <?php $giftcard_payment_row = explode(':', $payment['payment_type']); ?>
    <td class="right_text_align" colspan="1"><?php echo to_currency($this->Giftcard->get_giftcard_value(end($giftcard_payment_row))); ?></td>
  </tr>
    <?php }?>
  <?php }?>
  
  <?php if ($amount_change >= 0) {?>
  <tr  style="line-height:8px;" class="gift_receipt_element">
    <td class="right_text_align" colspan="<?php echo $discount_exists ? '4' : '3'; ?>"><?php echo lang('sales_change_due'); ?></td>
    <td class="right_text_align" colspan="1">
    <?php echo $this->config->item('round_cash_on_sales')  && $is_sale_cash_payment ?  to_currency(round_to_nearest_05($amount_change)) : to_currency($amount_change); ?> </td>
  </tr>
  <?php
  }
  else
  {
  ?>
  <tr>
    <td class="right_text_align" colspan="<?php echo $discount_exists ? '4' : '3'; ?>"><?php echo lang('sales_amount_due'); ?></td>
    <td class="right_text_align" colspan="1"><?php echo $this->config->item('round_cash_on_sales')  && $is_sale_cash_payment ?  to_currency(round_to_nearest_05($amount_change * -1)) : to_currency($amount_change * -1); ?></td>
  </tr>  
  <?php
  } 
  ?>
  <?php if (isset($customer_balance_for_sale) && $customer_balance_for_sale !== FALSE) {?>
  <tr>
    <td class="right_text_align" colspan="<?php echo $discount_exists ? '4' : '3'; ?>"><?php echo lang('sales_customer_account_balance'); ?></td>
    <td class="right_text_align" colspan="1">
    <?php echo to_currency($customer_balance_for_sale); ?> </td>
  </tr>
  <?php
  }
  ?>
  
  <?php
  if ($ref_no)
  {
  ?>
  <tr>
    <td class="right_text_align" colspan="<?php echo $discount_exists ? '4' : '3'; ?>"><?php echo lang('sales_ref_no'); ?></td>
    <td class="right_text_align" colspan="1"><?php echo $ref_no; ?></td>
  </tr>  
  <?php
  }
  if (isset($auth_code) && $auth_code)
  {
  ?>
  <tr>
    <td class="right_text_align" colspan="<?php echo $discount_exists ? '4' : '3'; ?>"><?php echo lang('sales_auth_code'); ?></td>
    <td class="right_text_align" colspan="1"><?php echo $auth_code; ?></td>
  </tr>  
  <?php
  }
  ?>
  
  <tr>
    <td colspan="<?php echo $discount_exists ? '5' : '4'; ?>" align="right">
    <?php if($show_comment_on_receipt==1)
      {
        echo $comment ;
      }
    ?>
    </td>
  </tr>
  </table>

  <div id="sale_return_policy">
  <?php echo nl2br($this->config->item('return_policy')); ?>
   <br />   

  </div>
  
  <?php if (!$this->config->item('hide_barcode_on_sales_and_recv_receipt')) {?>
    <div id='barcode'>
    <?php echo "<img src='".site_url('barcode')."?barcode=$sale_id&text=$sale_id' />"; ?>
    </div>
  <?php } ?>
  <?php if(!$this->config->item('hide_signature')) { ?>
  
  <div id="signature">
  
  <?php foreach($payments as $payment) {?>
    <?php if (strpos($payment['payment_type'], lang('sales_credit'))!== FALSE) {?>
      <?php echo lang('sales_signature'); ?> --------------------------------- <br />  
      <?php 
      echo lang('sales_card_statement');
      break;
      ?>
  
    <?php }?>
  <?php }?>
  
  </div>
  <?php } ?>
  
  <?php 
   if (!$store_account_payment && $this->Employee->has_module_action_permission('sales', 'edit_sale', $this->Employee->get_logged_in_employee_info()->person_id)){

   $edit_sale_url = (isset($sale_type) && ($sale_type == lang('sales_layaway') || $sale_type == lang('sales_estimate'))) ? 'unsuspend' : 'change_sale';
  echo form_open("sales/$edit_sale_url/".$sale_id_raw,array('id'=>'sales_change_form')); ?>
  <button class="btn btn-primary text-white hidden-print" id="edit_sale" onclick="submit()" > <?php echo lang('sales_edit'); ?> </button>

  <?php }  ?>
  </form>

<button class="btn btn-primary text-white hidden-print" id="print_button" onclick="print_receipt()" > <?php echo lang('sales_print'); ?> </button>
  
<span class="hidden-print">
  <?php
  echo form_checkbox(array(
    'name'        => 'print_duplicate_receipt',
    'id'          => 'print_duplicate_receipt',
    'value'       => '1',
  )).'&nbsp;'.lang('sales_duplicate_receipt');
    ?>
</span>

<br />

<button class="btn btn-primary text-white hidden-print" id="fufillment_sheet_button" onclick="window.open('<?php echo site_url("sales/fulfillment/$sale_id_raw"); ?>', 'blank');" > <?php echo lang('sales_fulfillment_sheet'); ?></button>
<br />

<button class="btn btn-primary text-white hidden-print gift_receipt" id="gift_receipt_button" onclick="toggle_gift_receipt()" > <?php echo lang('sales_gift_receipt'); ?> </button>
<br />

  <?php if (!empty($customer_email)) { ?>
    <?php echo anchor('sales/email_receipt/'.$sale_id_raw, lang('sales_email_receipt'), array('id' => 'email_receipt','class' => 'btn btn-primary hidden-print'));?>
    <br />
  <?php }?>

<button class="btn btn-primary text-white hidden-print" id="new_sale_button_2" onclick="window.location='<?php echo site_url('sales'); ?>'" > <?php echo lang('sales_new_sale'); ?> </button>

    <?php
    echo form_submit(
            array(
                'name' => 'submit',
                'id' => 'btn-submit',
                'value' => 'Préstamo',
                'class' => 'btn btn-primary hidden-print'
            )
    );
    ?>

<?php
echo form_close();
?>

</div>

<div id="duplicate_receipt_holder">
  
</div>
<?php $this->load->view("partial/footer"); ?>

<?php if ($this->config->item('print_after_sale'))
{
?>
<script type="text/javascript">
$(window).bind("load", function() {
  window.print();
});
</script>
<?php }  ?>

<script type="text/javascript">

$(document).ready(function(){
  $("#email_receipt").click(function()
  {
    $.get($(this).attr('href'), function()
    {
      gritter(<?php echo json_encode(lang('common_success')); ?>,'<?php echo lang('sales_receipt_sent'); ?>','gritter-item-success',false,false);
      
    });
    
    return false;
  });
});

$('#print_duplicate_receipt').click(function()
{
  if ($('#print_duplicate_receipt').prop('checked'))
  {
     var receipt = $('#receipt_wrapper').clone();
     $('#duplicate_receipt_holder').html(receipt);
    $("#duplicate_receipt_holder").addClass('active');
  }
  else
  {
    $("#duplicate_receipt_holder").empty();
    $("#duplicate_receipt_holder").removeClass('active');
  }
});

function print_receipt()
 {
   window.print();
 }
 
 function toggle_gift_receipt()
 {
   var gift_receipt_text = <?php echo json_encode(lang('sales_gift_receipt')); ?>;
   var regular_receipt_text = <?php echo json_encode(lang('sales_regular_receipt')); ?>;
   
   if ($("#gift_receipt_button").hasClass('regular_receipt'))
   {
     $('#gift_receipt_button').addClass('gift_receipt');     
     $('#gift_receipt_button').removeClass('regular_receipt');
     $("#gift_receipt_button").text(gift_receipt_text);  
     $('.gift_receipt_element').show();  
   }
   else
   {
     $('#gift_receipt_button').removeClass('gift_receipt');     
     $('#gift_receipt_button').addClass('regular_receipt');
     $("#gift_receipt_button").text(regular_receipt_text);
     $('.gift_receipt_element').hide();  
   }
   
 }
</script>

<?php if($is_integrated_credit_sale && $is_sale) { ?>
<script type="text/javascript">
gritter(<?php echo json_encode(lang('common_success')); ?>, <?php echo json_encode(lang('sales_credit_card_processing_success'))?>, 'gritter-item-success',false,false);
</script>
<?php } ?>

<!-- This is used for mobile apps to print receipt-->
<script type="text/print" id="print_output"><?php echo $this->config->item('company'); ?>

<?php echo $this->Location->get_info_for_key('address'); ?>

<?php echo $this->Location->get_info_for_key('phone'); ?>

<?php if($this->config->item('website')) { ?>
  <?php echo $this->config->item('website'); ?>
<?php } ?>

<?php echo $receipt_title; ?>

<?php echo $transaction_time; ?>

<?php if(isset($customer))
{
?>
<?php echo lang('customers_customer').": ".$customer; ?>

<?php if(!empty($customer_address_1)){ ?><?php echo lang('common_address'); ?>: <?php echo $customer_address_1. ' '.$customer_address_2; ?>
  
<?php } ?>
<?php if (!empty($customer_city)) { echo $customer_city.' '.$customer_state.', '.$customer_zip; ?>

<?php } ?>
<?php if (!empty($customer_country)) { echo $customer_country; ?>
  
<?php } ?>
<?php if(!empty($customer_phone)){ ?><?php echo lang('common_phone_number'); ?> : <?php echo $customer_phone; ?>
  
<?php } ?>
<?php if(!empty($customer_email)){ ?><?php echo lang('common_email'); ?> : <?php echo $customer_email; ?><?php } ?>

<?php
}
?>
<?php echo lang('sales_id').": ".$sale_id; ?>

<?php if (isset($sale_type)) { ?>
<?php echo $sale_type; ?>
<?php } ?>

<?php echo lang('employees_employee').": ".$employee; ?>

<?php 
if($this->Location->get_info_for_key('enable_credit_card_processing'))
{
  echo lang('config_merchant_id').': '.$this->Location->get_info_for_key('merchant_id');
}
?>

<?php echo lang('items_item'); ?>            <?php echo lang('common_price'); ?> <?php echo lang('sales_quantity'); ?><?php if($discount_exists){echo ' '.lang('sales_discount');}?> <?php echo lang('sales_total'); ?>

---------------------------------------
<?php
foreach(array_reverse($cart, true) as $line=>$item)
{
?>
<?php echo character_limiter($item['name'], 14,'...'); ?><?php echo strlen($item['name']) < 14 ? str_repeat(' ', 14 - strlen($item['name'])) : ''; ?> <?php echo str_replace('&#8209;', '-', to_currency($item['price'])); ?> <?php echo to_quantity($item['quantity']); ?><?php if($discount_exists){echo ' '.$item['discount'];}?> <?php echo str_replace('&#8209;', '-', to_currency($item['price']*$item['quantity']-$item['price']*$item['quantity']*$item['discount']/100)); ?>

  <?php echo $item['description']; ?>  <?php echo isset($item['serialnumber']) ? $item['serialnumber'] : ''; ?>
  

<?php
}
?>

<?php echo lang('sales_sub_total'); ?>: <?php echo str_replace('&#8209;', '-', to_currency($subtotal)); ?>


<?php foreach($taxes as $name=>$value) { ?>
<?php echo $name; ?>: <?php echo str_replace('&#8209;', '-', to_currency($value)); ?>

<?php }; ?>

<?php echo lang('sales_total'); ?>: <?php echo $this->config->item('round_cash_on_sales') && $is_sale_cash_payment ?  str_replace('&#8209;', '-', to_currency(round_to_nearest_05($total))) : str_replace('&#8209;', '-', to_currency($total)); ?>

<?php
  foreach($payments as $payment_id=>$payment)
{ ?>

<?php echo (isset($show_payment_times) && $show_payment_times) ?  date(get_date_format().' '.get_time_format(), strtotime($payment['payment_date'])) : lang('sales_payment'); ?>  <?php if ($is_integrated_credit_sale || sale_has_partial_credit_card_payment()) { ?><?php $splitpayment=explode(':',$payment['payment_type']);echo $splitpayment[0]; ?>: <?php echo $payment['card_issuer']. ' '.$payment['truncated_card']; ?> <?php } else { ?><?php $splitpayment=explode(':',$payment['payment_type']); echo $splitpayment[0]; ?> <?php } ?><?php echo $this->config->item('round_cash_on_sales') && $payment['payment_type'] == lang('sales_cash') ?  str_replace('&#8209;', '-', to_currency(round_to_nearest_05($payment['payment_amount']))) : str_replace('&#8209;', '-', to_currency($payment['payment_amount'])); ?>
<?php
}
?>  

<?php foreach($payments as $payment) { $giftcard_payment_row = explode(':', $payment['payment_type']);?>
<?php if (strpos($payment['payment_type'], lang('sales_giftcard'))!== FALSE) {?><?php echo lang('sales_giftcard_balance'); ?>  <?php echo $payment['payment_type'];?>: <?php echo str_replace('&#8209;', '-', to_currency($this->Giftcard->get_giftcard_value(end($giftcard_payment_row)))); ?>
  <?php }?>
<?php }?>

<?php if ($amount_change >= 0) {?>
<?php echo lang('sales_change_due'); ?>: <?php echo $this->config->item('round_cash_on_sales')  && $is_sale_cash_payment ?  str_replace('&#8209;', '-', to_currency(round_to_nearest_05($amount_change))) : str_replace('&#8209;', '-', to_currency($amount_change)); ?>
<?php
}
else
{
?>
<?php echo lang('sales_amount_due'); ?>: <?php echo $this->config->item('round_cash_on_sales')  && $is_sale_cash_payment ?  str_replace('&#8209;', '-', to_currency(round_to_nearest_05($amount_change * -1))) : str_replace('&#8209;', '-', to_currency($amount_change * -1)); ?>
<?php
} 
?>
<?php if (isset($customer_balance_for_sale) && $customer_balance_for_sale !== FALSE) {?>
  
<?php echo lang('sales_customer_account_balance'); ?>: <?php echo to_currency($customer_balance_for_sale); ?>
<?php
}
?>
<?php
if ($ref_no)
{
?>

<?php echo lang('sales_ref_no'); ?>: <?php echo $ref_no; ?>
<?php
}
if (isset($auth_code) && $auth_code)
{
?>

<?php echo lang('sales_auth_code'); ?>: <?php echo $auth_code; ?>
<?php
}
?>
<?php if($show_comment_on_receipt==1){echo $comment;} ?>

<?php $this->config->item('return_policy'); ?>

<?php if(!$this->config->item('hide_signature')) { ?>
<?php foreach($payments as $payment) {?>
  <?php if (strpos($payment['payment_type'], lang('sales_credit'))!== FALSE) {?>
    
  <?php echo lang('sales_signature'); ?>: 
---------------------------------------
<?php 
echo lang('sales_card_statement');
break;
?><?php }?><?php }?><?php } ?></script>
