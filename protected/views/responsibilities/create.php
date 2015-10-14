<?php
/* @var $this ResponsibilitiesController */
/* @var $model Responsibilities */

$this->breadcrumbs=array(
	'Responsibilities'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List Responsibilities', 'url'=>array('index')),
	array('label'=>'Manage Responsibilities', 'url'=>array('admin')),
);
?>

<h1>Create Responsibilities</h1>

<?php $this->renderPartial('_form', array('model'=>$model,'userList'=>$userlist,'data'=>$treeList,'selectedFunctionList'=>array())); ?>