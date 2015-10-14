<?php
/* @var $this StoreController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'Stores',
);

$this->menu=array(
	//array('label'=>'Create Store', 'url'=>array('create')),
	array('label'=>'Manage Store', 'url'=>array('admin')),
);
?>

<h1>Stores</h1>
<p>
You may optionally enter a comparison operator (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b>
or <b>=</b>) at the beginning of each of your search values to specify how the comparison should be done.
</p>
<br>

<div class="view">
        <?php foreach($myVariable as $key=>$data) {?>
	<b><?php echo CHtml::encode(UserStore::model()->getAttributeLabel('store_id')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data['store_id']), array('view', 'id'=>$data['store_id'])); ?>
	<br />

	<b><?php echo CHtml::encode(UserStore::model()->getAttributeLabel('getit_store_id')); ?>:</b>
	<?php echo CHtml::encode($data['getit_store_id']); ?>
	<br />
        
        <b><?php echo CHtml::encode(UserStore::model()->getAttributeLabel('username')); ?>:</b>
	<?php echo CHtml::encode($data['username']); ?>
	<br />

	<b><?php echo CHtml::encode(UserStore::model()->getAttributeLabel('password')); ?>:</b>
	<?php echo CHtml::encode($data['password']); ?>
	<br />

	<b><?php echo CHtml::encode(UserStore::model()->getAttributeLabel('store_code')); ?>:</b>
	<?php echo CHtml::encode($data['store_code']); ?>
	<br />

	<b><?php echo CHtml::encode(UserStore::model()->getAttributeLabel('store_name')); ?>:</b>
	<?php echo CHtml::encode($data['store_name']); ?>
	<br />

	<b><?php echo CHtml::encode(UserStore::model()->getAttributeLabel('store_details')); ?>:</b>
	<?php echo CHtml::encode($data['store_details']); ?>
	<br />

	<b><?php echo CHtml::encode(UserStore::model()->getAttributeLabel('store_logo')); ?>:</b>
	<?php echo CHtml::encode($data['store_logo']); ?>
	<br />

	<b><?php echo CHtml::encode(UserStore::model()->getAttributeLabel('seller_name')); ?>:</b>
	<?php echo CHtml::encode($data['seller_name']); ?>
	<br />
        
        <b><?php echo'</br>';?></b>

	
    <?php }?>

</div>