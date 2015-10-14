<?php
/* @var $this ResponsibilitiesController */
/* @var $model Responsibilities */

$this->breadcrumbs=array(
	'Responsibilities'=>array('index'),
	$model->id,
);

$this->menu=array(
	array('label'=>'List Responsibilities', 'url'=>array('index')),
	array('label'=>'Create Responsibilities', 'url'=>array('create')),
	array('label'=>'Update Responsibilities', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete Responsibilities', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage Responsibilities', 'url'=>array('admin')),
);
?>

<h1>View Responsibilities #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'user_id',
		'admin_func_id',
		'status',
	),
)); ?>
