<?php

class ResponsibilitiesController extends Controller
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
//	public $layout='//layouts/column2';

	/**
	 * @return array action filters
	 */
	public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
			'postOnly + delete', // we only allow deletion via POST request
		);
	}

	/**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	public function accessRules()
	{
		return array(
			array('allow',  // allow all users to perform 'index' and 'view' actions
                'actions'=>array('create','update','view','index'),
				'users'=>array('admin'),
			),
			/*array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('create','update'),
				'users'=>array('@'),
			),
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array('admin','delete'),
				'users'=>array('admin'),
			),*/
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView($id)
	{
		$this->render('view',array(
			'model'=>$this->loadModel($id),
		));
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model = new Responsibilities;
		$treeList = $this->getTreeList();
		$criteria = new CDbCriteria;
		$criteria->select = 't.id, t.email'; // select fields which you want in output
		$criteria->condition = 't.status = 1 AND role!="SUPERADMIN" AND id NOT IN (SELECT DISTINCT user_id FROM responsibilities)';
		$userList = AdminUser::model()->findAll($criteria);
		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);
		if(isset($_POST['Responsibilities']))
		{
			foreach($_POST['Responsibilities']['functionList'] as $row){
				$model = new Responsibilities();
				$model->user_id = $_POST['Responsibilities']['user_id'];
				$model->admin_func_id = $row;
				$model->status = 1;
				$model->save();
			}
			$this->redirect(array('index'));
		}

		$this->render('create',array(
			'model'=>$model,
			'userlist'=>$userList,
			'treeList' => $treeList
		));
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id)
	{

		$model = $this->loadModel($id);
		$treeList = $this->getTreeList();
		$criteria = new CDbCriteria;
		$criteria->select = 't.id, t.email'; // select fields which you want in output
		$criteria->condition = 't.status = 1 AND role!="SUPERADMIN"';
		$userList = AdminUser::model()->findAll($criteria);

		$selectedFunctionList = Responsibilities::model()->findAll(array('condition'=>'user_id = '.$id.' AND status=1'));
		$selectedArray = array();
		foreach($selectedFunctionList as $row){
			$selectedArray[] = $row->admin_func_id;
		}

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Responsibilities']))
		{
			Responsibilities::model()->updateAll(array( 'status' => 2 ), 'user_id = '.$id.' AND status=1' );

			foreach($_POST['Responsibilities']['functionList'] as $row){

			  if(in_array($row,$selectedArray)){
				  Responsibilities::model()->updateAll(array( 'status' => 1 ), 'admin_func_id = '.$row.'' );
			  }else{
				  $model = new Responsibilities();
				  $model->user_id = $id;
				  $model->admin_func_id = $row;
				  $model->status = 1;
				  $model->save();
			  }
			}
				$this->redirect(array('index'));
		}


		$this->render('update',array(
			'model'=>$model,
			'userList'=>$userList,
			'treeList'=>$treeList,
			'selectedFunctionList' => $selectedFunctionList
		));
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id)
	{
		$this->loadModel($id)->delete();

		// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
		if(!isset($_GET['ajax']))
			$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		$model = new Responsibilities('search');
		if (isset($_GET['AdminUser']))
			$model->attributes = $_GET['AdminUser'];
		$this->render('index',array(
			'model'=>$model,
		));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new Responsibilities('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Responsibilities']))
			$model->attributes=$_GET['Responsibilities'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return Responsibilities the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model=Responsibilities::model()->findByAttributes(array(
            'user_id' => $id,
        ));
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param Responsibilities $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='responsibilities-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}

	protected function getTreeList(){
		$parentFunction = AdminFunctions::model()->findAll();
		// BUILDING ADMIN FUNCTION LIST TO DISPLAY IN TREE VIEW
		$treeList = array();
		foreach($parentFunction as $key=>$row){
			$treeList[$row['id']] = array('parentid'=>$row['parent_id'],'text'=>$row['title']);
		}
		return $treeList;
	}
}
