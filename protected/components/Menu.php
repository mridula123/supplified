<?php

/**
 * Created by PhpStorm.
 * User: MridulaSingh
 * Date: 10/12/2015
 * Time: 1:15 PM
 */
class Menu extends CApplicationComponent
{
    private static $menuTree = array();

    public static function getMenu() {
        if(!Yii::app()->user->isGuest) {
            if(Yii::app()->user->role=='SUPERADMIN'){

                self::$menuTree[] = array('label'=>'User Manager <span class="caret"></span>', 'url'=>array('#'), 'visible'=>!Yii::app()->user->isGuest,'itemOptions'=>array('class'=>'dropdown','tabindex'=>"-1"),'linkOptions'=>array('class'=>'dropdown-toggle','data-toggle'=>"dropdown"),
                    'items'=>array(
                        array('label'=>'Create User', 'url'=>array('/user/index')),
                    ));

                self::$menuTree[] = array('label'=>'Manage Responsibilities', 'url'=>array('/responsibilities/index'), 'visible'=>!Yii::app()->user->isGuest,'linkOptions'=>array("data-description"=>""));
            }else {
                if (empty(self::$menuTree)) {
                    $menuList = Responsibilities::model()->with('UserFunction')->findAll(
                        array(
                            'condition' => 'user_id = ' . Yii::app()->user->id . ' AND is_menu=1 AND parent_id=0'
                        )
                    );
                    foreach ($menuList as $item) {
                        $chump = self::getMenuItems($item);
                        if(count($chump)>0){
                            self::$menuTree[] = array('label'=>''.$item->UserFunction->title.' <span class="caret"></span>', 'items' => $chump, 'url' => Yii::app()->createUrl('' . $item->UserFunction->controller_name . '/' . $item->UserFunction->action_name),'itemOptions'=>array('class'=>'dropdown','tabindex'=>"-1"),'linkOptions'=>array('class'=>'dropdown-toggle','data-toggle'=>"dropdown"));
                        }else{
                            self::$menuTree[] = array('label'=>''.$item->UserFunction->title.'', 'url' => Yii::app()->createUrl('' . $item->UserFunction->controller_name . '/' . $item->UserFunction->action_name), 'visible'=>!Yii::app()->user->isGuest,'linkOptions'=>array("data-description"=>""));
                        }
                    }
                }
            }
        }
      //  self::$menuTree[] = array('label'=>'Login', 'url'=>array('/site/login'), 'visible'=>Yii::app()->user->isGuest,'linkOptions'=>array("data-description"=>"member area"));
        self::$menuTree[] = array('label'=>'Logout ('.Yii::app()->user->name.')', 'url'=>array('/site/logout'), 'visible'=>!Yii::app()->user->isGuest,'linkOptions'=>array("data-description"=>"member area"));

        return self::$menuTree;
    }

    private static function getMenuItems($modelRow) {
        $childItems = array();
        if (!$modelRow)
            return;
        $childList =  Responsibilities::model()->with('UserFunction')->findAll(
            array('condition'=>'parent_id = '.$modelRow->UserFunction->id.' AND user_id = ' . Yii::app()->user->id . ' AND is_menu=1')
        );


        if (!isset($childList) && count($childList)==0) {
            return [];
        }else{
            foreach($childList as $row){
                $childItems[] = array('label' => $row->UserFunction->title, 'url' => Yii::app()->createUrl(''.$row->UserFunction->controller_name.'/'.$row->UserFunction->action_name));
            }
        }
        return $childItems;
    }
}