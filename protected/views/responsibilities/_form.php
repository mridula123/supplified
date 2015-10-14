<?php
/* @var $this ResponsibilitiesController */
/* @var $model Responsibilities */
/* @var $form CActiveForm */
?>

<div class="form">

	<?php
	$form=$this->beginWidget('CActiveForm', array(
		'id'=>'responsibilities-form',
		// Please note: When you enable ajax validation, make sure the corresponding
		// controller action is handling ajax validation correctly.
		// There is a call to performAjaxValidation() commented in generated controller code.
		// See class documentation of CActiveForm for details on this.
		'enableAjaxValidation'=>false,
	)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'user_id'); ?>
		<?php echo $form->dropDownList($model, 'user_id',CHtml::listData($userList,
			'id', 'email'),
			array('prompt'=>'--Select User--',
			'disabled' => $model->isNewRecord=='false'?false:true
			)
		); ?>
		<?php echo $form->error($model,'user_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'admin_func_id'); ?>
		<?php
		Yii::import("application.extensions.AIOTree.*");
		$this->Widget('AIOTree',array(
			'model'=>$model,
			'attribute'=>'functionList',
			'data'=>$data,
			'type'=>'checkbox',
			'parentShow'=>true,
			'parentTag'=>'div',
			'parentId'=>'aiotree_id',
			'selectParent'=>true,
			'controlTag'=>'div',
			//'controlClass'=>'CC',
			//'controlId'=>'CId',
			//'controlStyle'=>'color:red;',
			//'controlDivider'=>' <=> ',
			//'controlLabel'=>array('collapse'=>' COLLAPSE '),
			'controlHtmlOptions'=>array(
				//  'id'=>'control_id',
				//'class'=>'control_class',
				//'style'=>'color:blue;',
			),
			'liHtmlOptions'=>array(
				'class'=>'link-class',
			),
		));
		?>
		<?php echo $form->error($model,'admin_func_id'); ?>
	</div>

	<!--<div class="row">
		<?php /*echo $form->labelEx($model,'status'); */?>
		<?php /*echo $form->dropDownList($model, 'status',Yii::app()->commonfunc->getStatus(),
			array('prompt'=>'--Select Status--')
		);
		*/?>
		<?php /*echo $form->error($model,'status'); */?>
	</div>-->

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
        <?php echo CHtml::button('Cancel',
            array(
                'submit'=>array('responsibilities/index'))); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->
<script>
	<?php
		foreach($selectedFunctionList as $row){
	?>
			$("input:checkbox[value='<?php echo $row->admin_func_id?>']").attr("checked", true);
	<?php
		}
    ?>

</script>