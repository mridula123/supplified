<?php
/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base class.
 */
class Controller extends CController
{
	/**
	 * @var string the default layout for the controller view. Defaults to '//layouts/column1',
	 * meaning using a single column layout. See 'protected/views/layouts/column1.php'.
	 */
	public $layout='//layouts/column1';

	/**
	 * @var array context menu items. This property will be assigned to {@link CMenu::items}.
	 */
	public $menu=array();
	/**
	 * @var array the breadcrumbs of the current page. The value of this property will
	 * be assigned to {@link CBreadcrumbs::links}. Please refer to {@link CBreadcrumbs::links}
	 * for more details on how to specify this property.
	 */
	public $breadcrumbs=array();

	public function beforeAction($action) {
        if(Yii::app()->user->isGuest){
            
        }
		return true;
	}

    public function getActionList(){
        $actionList = array();
        if(!Yii::app()->user->isGuest){
            if(Yii::app()->user->role!='SUPERADMIN') {
                $userFunction = Responsibilities::model()->with('UserFunction')->findAll(array('condition' => 'controller_name = "' . Yii::app()->controller->id . '" AND user_id = ' . Yii::app()->user->id . ' AND status=1 '));
                foreach ($userFunction as $row) {
                    $actionList[] = $row->UserFunction->action_name;
                }
            }
        }
        return $actionList;
    }
}