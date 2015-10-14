<?php
/* @var $this AdminUserController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'Admin Users',
);

$this->menu=array(
	array('label'=>'Create AdminUser', 'url'=>array('create')),
	array('label'=>'Manage AdminUser', 'url'=>array('admin')),
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
echo CHtml::button('Create User',
	array(
		'submit'=>array('user/create')));
?>

<?php

$this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'user-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'afterAjaxUpdate' => 'reinstallDatePicker',
	'columns'=>array(
		array(
			'header'=>'No.',
			'value'=>'$this->grid->dataProvider->pagination->currentPage * $this->grid->dataProvider->pagination->pageSize + ($row+1)',
		),
		'name',
		'username',
		'role',
		'status'=>array(
			'name'=>'status',
			'value'=>function($model){
				return Yii::app()->commonfunc->getKeyValue($model->status);
			},
			//'filter' => CHtml::listData( Yii::app()->commonfunc->getStatus()),
		),
		array(
			'name' => 'created_date',
			'filter' => $this->widget('zii.widgets.jui.CJuiDatePicker', array(
				'model'=>$model,
				'attribute'=>'created_date',
				'language' => 'en',
				// 'i18nScriptFile' => 'jquery.ui.datepicker-ja.js', (#2)
				'htmlOptions' => array(
					'id' => 'created_date',
					'size' => '10',
				),
				'defaultOptions' => array(  // (#3)
					'showOn' => 'focus',
					'dateFormat' => 'yy-mm-dd',
					'showOtherMonths' => true,
					'selectOtherMonths' => true,
					'changeMonth' => true,
					'changeYear' => true,
					'showButtonPanel' => true,
				)
			),
				true
			)),
		array(
			'class'=>'CButtonColumn',
		),
	),
));

Yii::app()->clientScript->registerScript('re-install-date-picker', "
function reinstallDatePicker(id, data) {
        //use the same parameters that you had set in your widget else the datepicker will be refreshed by default
    $('#created_date').datepicker(jQuery.extend({showMonthAfterYear:false},jQuery.datepicker.regional['en'],{'dateFormat':'yy-mm-dd'}));
}
");
?>