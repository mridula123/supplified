<?php
/* @var $this StoreController */
/* @var $model Store */

$this->breadcrumbs=array(
	'Stores'=>array('index'),
	$model->store_id,
);

$this->menu=array(
	array('label'=>'List Store', 'url'=>array('index')),
	array('label'=>'Create Store', 'url'=>array('create')),
	array('label'=>'Update Store', 'url'=>array('update', 'id'=>$model->store_id)),
	array('label'=>'Delete Store', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->store_id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage Store', 'url'=>array('admin')),
);
?>

<h1>View Store #<?php echo $model->store_id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'store_front_name', 
        'store_front_api_key',
        'store_front_api_password',
        'store_front_api_token',
	),
)); ?>
