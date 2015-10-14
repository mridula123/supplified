<section id="navigation-main">
<div class="navbar">
	<div class="navbar-inner">
        <div class="container" id="message-container" >
            <?php if(Yii::app()->user->hasFlash('success')):?>
                <div class="flash-success">
                    <?php echo Yii::app()->user->getFlash('success'); ?>
                </div>
            <?php endif; ?>
            <?php if(Yii::app()->user->hasFlash('error')):?>
                <div class="flash-error">
                    <?php echo Yii::app()->user->getFlash('error'); ?>
                </div>
            <?php endif; ?>
            <div class="nav-collapse">
			<?php
            $this->widget('zii.widgets.CMenu',array(
                    'htmlOptions'=>array('class'=>'nav'),
                    'submenuHtmlOptions'=>array('class'=>'dropdown-menu'),
					'itemCssClass'=>'item-test',
                    'encodeLabel'=>false,
                    'items'=>
                        Yii::app()->menu->getMenu()
                )); ?>
    	    </div>
        </div>
	</div>
</div>
</section><!-- /#navigation-main -->