<?php
    /* @var $this CategoryController */
    /* @var $model Category */

    $this->breadcrumbs=array(
    'Brand'=>array('admin'),
    'Manage',
    );

    $this->menu=array(
    //array('label'=>'List Store', 'url'=>array('index')),
    //array('label'=>'Creat Store', 'url'=>array('create')),
    );

    Yii::app()->clientScript->registerScript('search', "
    $('.search-button').click(function(){
    $('.search-form').toggle();
    return false;
    });
    $('.search-form form').submit(function(){
    $('#category-grid').yiiGridView('update', {
    data: $(this).serialize()
    });
    return false;
    });
    ");
	$this->menu=array(
	  array('label'=>'Create Brand', 'url'=>array('brand/create'))
    );
?>


<h1>Manage Brand</h1>



<?php 
    
    ///if($_SESSION['checkAccess']=='Admin')
   // {
        $this->widget('zii.widgets.grid.CGridView', array(
        'dataProvider'=>$model->search(),
        'filter'=>$model,
        'columns'=>array(
		array(
        'name'=>'store_front_id',
        'type'=>'raw',
        ),
		
        array(
        'name'=>'store_front_name',
        'type'=>'raw',
        ),
       
		array(
        'name'=>'redirect_url',
        'type'=>'raw',
        ),
		
         'link'=>array(
        			'header'=>'Action',
                	'type'=>'raw',
                	'value'=> 'CHtml::button("Edit",array("onclick"=>"document.location.href=\'".Yii::app()->controller->createUrl("Brand/update",array("id"=>$data->store_front_id))."\'"))',
         ),
		 'link1'=>array(
        			'header'=>'Delete',
                	'type'=>'raw',
                	'value'=> 'CHtml::button("Delete",array("onclick"=>"document.location.href=\'".Yii::app()->controller->createUrl("Brand/delete",array("id"=>$data->store_front_id))."\'"))',
         ),
        ),
		
        ));  
    
  //  }
?>