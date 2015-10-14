<?php
/* @var $this StoreController */
/* @var $model Store */
/* @var $form CActiveForm */
?>

<div class="wide form">

<?php 
$form=$this->beginWidget('CActiveForm', array(
'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
));

?>
    
	<div class="row">
		<?php echo $form->label($model,'store_id'); ?>
		<?php echo $form->textField($model,'store_id',array('size'=>10,'maxlength'=>10)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'getit_store_id'); ?>
		<?php echo $form->textField($model,'getit_store_id',array('size'=>10,'maxlength'=>10)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'username'); ?>
		<?php echo $form->textField($model,'username',array('size'=>60,'maxlength'=>255)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'password'); ?>
		<?php echo $form->textField($model,'password',array('size'=>10,'maxlength'=>10)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'store_code'); ?>
		<?php echo $form->textField($model,'store_code'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'store_name'); ?>
		<?php echo $form->textField($model,'store_name',array('size'=>60,'maxlength'=>255)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'store_details'); ?>
		<?php echo $form->textField($model,'store_details'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'store_logo'); ?>
		<?php echo $form->textField($model,'store_logo'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'seller_name'); ?>
		<?php echo $form->textField($model,'seller_name'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'business_address'); ?>
		<?php echo $form->textField($model,'business_address'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'business_address_country'); ?>
		<?php echo $form->textField($model,'business_address_country'); ?>
	</div>
    
	<div class="row">
		<?php echo $form->label($model,'business_address_state'); ?>
		<?php echo $form->textField($model,'business_address_state'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'business_address_city'); ?>
		<?php echo $form->textField($model,'business_address_city'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'business_address_pincode'); ?>
		<?php echo $form->textField($model,'business_address_pincode'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'mobile_numbers'); ?>
		<?php echo $form->textField($model,'mobile_numbers'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'telephone_numbers'); ?>
		<?php echo $form->textField($model,'telephone_numbers'); ?>
	</div>
    
    <div class="row">
		<?php echo $form->label($model,'visible'); ?>
		<?php echo $form->textField($model,'visible'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'meta_title'); ?>
		<?php echo $form->textField($model,'meta_title'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'meta_keywords'); ?>
		<?php echo $form->textField($model,'meta_keywords'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'is_active_valid'); ?>
		<?php echo $form->textField($model,'is_active_valid'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'store_shipping_charge'); ?>
		<?php echo $form->textField($model,'store_shipping_charge'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton('Search'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- search-form -->