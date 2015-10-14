<?php
/* @var $this ResponsibilitiesController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'Responsibilities',
);

$this->menu=array(
	array('label'=>'Create Responsibilities', 'url'=>array('create')),
	array('label'=>'Manage Responsibilities', 'url'=>array('admin')),
);
?>

<h1>Responsibilities</h1>

<?php
/* @var $this AdminUserController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'Admin Users',
);

$this->menu=array(
	array('label'=>'Create Responsibilities', 'url'=>array('create')),
	array('label'=>'Manage Responsibilities', 'url'=>array('admin')),
);
?>

	<h1>Admin Users</h1>

<?php
/* @var $this AdminUserController */
/* @var $data AdminUser */
?>

	<div class="search-form" style="display:none">
		<?php $this->renderPartial('_search',array(
			'model'=>$model,
		)); ?>
	</div><!-- search-form -->
<?php
echo CHtml::button('Create Responsibilities',
	array(
		'submit'=>array('responsibilities/create')));
?>

<?php
$this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'responsibilities-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		array(
			'header'=>'No.',
			'value'=>'$this->grid->dataProvider->pagination->currentPage * $this->grid->dataProvider->pagination->pageSize + ($row+1)',
		),
		array(
			'name'=>'Users.name',
			'value'=>'$data->Users->name',
			'filter' => true
		),
		array(
			'name'=>'Users.role',
			'value'=>'$data->Users->role',
		),
		'status'=>array(
			'name'=>'status',
			'value'=>function($model){
				return Yii::app()->commonfunc->getKeyValue($model->status);
			},
			'filter' => false
		),
		array
		(
			'class'=>'CButtonColumn',
			'template'=>'{update}',
			'buttons'=>array(
				'update' => array(
					'url'=>'Yii::app()->createUrl("responsibilities/update", array("id"=>$data->user_id))',
				),
			),
		),
	),
));

?>