<?php
/* @var $this ResponsibilitiesController */
/* @var $model Responsibilities */

$this->breadcrumbs=array(
	'Responsibilities'=>array('index'),
	$model->id=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'List Responsibilities', 'url'=>array('index')),
	array('label'=>'Create Responsibilities', 'url'=>array('create')),
	array('label'=>'View Responsibilities', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage Responsibilities', 'url'=>array('admin')),
);
?>

<h1>Update Responsibilities <?php echo $model->id; ?></h1>

<?php $this->renderPartial('_form', array('model'=>$model,'data'=>$treeList,'userList'=>$userList,'selectedFunctionList'=>$selectedFunctionList)); ?>