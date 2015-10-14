<?php
/* @var $this StoreController */

$this->breadcrumbs=array(
	'Manage Store',
);
?>

<?php if(Yii::app()->user->hasFlash('success')):?>
<div class='flash-success'><?php echo Yii::app()->user->getFlash('success'); ?></div>
<?php endif; ?>
<?php if(Yii::app()->user->hasFlash('error')): ?>
 
<div class="flash-error">
    <?php echo Yii::app()->user->getFlash('error'); ?>
</div>
 
<?php endif; ?>

<?php 


$this->widget('zii.widgets.grid.CGridView', array(
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		array(
			'name'=>'store_id',
			'type'=>'raw',
		),
		array(
			'name'=>'store_name',
			'type'=>'raw',
		),
		array(
			'name'=>'seller_name',
			'type'=>'raw',
		),
		array(
			'name'=>'email',
			'type'=>'raw',
		),
		array(
			'name'=>'mobile_numbers',
			'type'=>'raw',
		),
		array(
			'name'=>'business_address',
			'type'=>'raw',
		),
		/*array(
			'name'=>'business_address_country',
			'type'=>'raw',
		),*/
		array(
			'name'=>'business_address_state',
			'type'=>'raw',
		),
		array(
			'name'=>'business_address_city',
			'type'=>'raw',
		),
		array(
			'name'=>'business_address_pincode',
			'type'=>'raw',
		),
		array(
            'name'=>'status',
	        'value'=>'Functions::getStatus($data->status)',
	        'filter'=>CHtml::listData(Functions::getStatuses(), 'value', 'label')
        ),
		/*array(
			'class'=>'CButtonColumn',
		),*/
		
		'link'=>array(
			'header'=>'Action',
			'type'=>'raw',
			'value'=> 'CHtml::button("Edit",array("onclick"=>"document.location.href=\'".Yii::app()->controller->createUrl("store/update",array("id"=>$data->store_id))."\'"))',
		),
	),
)); ?>

