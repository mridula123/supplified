<?php
    /* @var $this StoreController */
    /* @var $model Store */

    $this->breadcrumbs=array(
    'Brand'=>array('admin'),
    'create',
    );

    
    
   $this->menu=array(
     array('label'=>'Create Brand', 'url'=>array('brand/create'))
    );
?>        
<h1>Create Brand</h1>



<div class="form">

    <?php $form = $this->beginWidget(
        'CActiveForm',
        array(
        'id' => 'upload-form',
        'enableAjaxValidation' => false,
        'htmlOptions' => array('enctype' => 'multipart/form-data'),
        )
        ); ?>

    <p class="note">Fields with <span class="required">*</span> are required.</p>

    <?php echo CHtml::errorSummary($model); ?>
	 <?php if(Yii::app()->user->hasFlash('success')):?>
   <div class="Csv" style="color:green;">
       <?php echo Yii::app()->user->getFlash('success'); ?>
   </div>
<?php endif; ?>

     <div class="row">
        <?php echo $form->labelEx($model,'Brand Name'); ?>
        <?php echo $form->textField($model,'store_front_name',array('size'=>40,'maxlength'=>255)); ?>
        <?php echo $form->error($model,'store_front_name'); ?>
    </div>
	<div class="row">
        <?php echo $form->labelEx($model,'Brand Code'); ?>
        <?php echo $form->textField($model,'redirect_url',array('size'=>40,'maxlength'=>255,'allowEmpty'=>false)); ?>
        <?php echo $form->error($model,'redirect_url'); ?>
    </div>
	
	
    <div class="row buttons">
        <?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
    </div>

    <?php $this->endWidget(); ?>

</div><!-- form -->
