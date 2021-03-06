<?php

class UserController extends Controller
{
    /**
     * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
     * using two-column layout. See 'protected/views/layouts/column2.php'.
     */
    //public $layout='//layouts/column2';


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
            /*	array('allow',  // allow all users to perform 'index' and 'view' actions
                    'actions'=>$this->getActionList(),
                    'users'=>array('@'),
                ),*/
            array('allow', // allow authenticated user to perform 'create' and 'update' actions
                'actions' => array('forgotpassword','verify'),
                'users' => array('*'),
            ),
            array('allow', // allow admin user to perform 'admin' and 'delete' actions
                'actions' => array('create', 'update', 'index', 'delete', 'view'),
                'users' => array('admin'),
            ),
            array('deny',  // deny all users
                'users' => array('*'),
            ),
        );
    }

    /**
     * Displays a particular model.
     * @param integer $id the ID of the model to be displayed
     */
    public function actionView($id)
    {
        $this->render('view', array(
            'model' => $this->loadModel($id),
        ));
    }

    /**
     * Creates a new model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     */
    public function actionCreate()
    {

        $model = new AdminUser;
        $model->scenario = 'createuser';

        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);

        if (isset($_POST['AdminUser'])) {
            $model->attributes = $_POST['AdminUser'];
            if ($model->validate()) {
                $model->save();
                $this->redirect(array('view', 'id' => $model->id));
            }
        }

        $this->render('create', array(
            'model' => $model,
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
        $model->scenario = 'updateuser';
        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);

        if (isset($_POST['AdminUser'])) {
            $model->attributes = $_POST['AdminUser'];
            if ($model->password != '')
                $model->scenario = 'changePassword';
            else
                unset($model->password);

            if ($model->validate()) {
                $model->save();
                $this->redirect(array('view', 'id' => $model->id));
            }
        }

        $this->render('update', array(
            'model' => $model,
        ));
    }

    /**
     * Deletes a particular model.
     * If deletion is successful, the browser will be redirected to the 'admin' page.
     * @param integer $id the ID of the model to be deleted
     */
    public function actionDelete($id)
    {
        $model = $this->loadModel($id);
        $model->status = 2;
        $model->save();
        // if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
        if (!isset($_GET['ajax']))
            $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
    }

    /**
     * Lists all models.
     */
    public function actionIndex()
    {
        $model = new AdminUser('search');
        if (isset($_GET['AdminUser']))
            $model->attributes = $_GET['AdminUser'];

        $this->render('index', array(
            'model' => $model,
        ));
    }

    /**
     * Manages all models.
     */
    public function actionAdmin()
    {
        $model = new AdminUser('search');
        $model->unsetAttributes();  // clear any default values
        if (isset($_GET['AdminUser']))
            $model->attributes = $_GET['AdminUser'];

        $this->render('admin', array(
            'model' => $model,
        ));
    }

    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     * @param integer $id the ID of the model to be loaded
     * @return AdminUser the loaded model
     * @throws CHttpException
     */
    public function loadModel($id)
    {
        $model = AdminUser::model()->findByPk($id);
        if ($model === null)
            throw new CHttpException(404, 'The requested page does not exist.');
        return $model;
    }

    /**
     * Performs the AJAX validation.
     * @param AdminUser $model the model to be validated
     */
    protected function performAjaxValidation($model)
    {
        if (isset($_POST['ajax']) && $_POST['ajax'] === 'admin-user-form') {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
    }

    public function actionForgotpassword()
    {
        $model = new AdminUser();

        if (isset($_POST['AdminUser'])) {
            $userEmail = $_POST['AdminUser']['email'];
            $model= AdminUser::model()->findByAttributes(array('email'=>$userEmail));
            $userEmail = AdminUser::model()->findByAttributes(array('email' => $userEmail));
            if(isset($userEmail)) {
                $getToken = rand(0, 99999);
                $getTime = date("H:i:s");
                $model->auth_key = md5($getToken . $getTime);
                $adminName = "Supplified Team";
                $adminEmail = "info@supplified.com";
                $subject = "Reset Password";
                $content = "Please click the link to<br/>'.CHtml::link('Click Here to Reset Password', array('user/verify',
                                         'token'=>'.$model->auth_key.'), array('class' => ''))";
                if ($model->validate()) {
                    $name = '=?UTF-8?B?' . base64_encode($adminName) . '?=';
                    $subject = '=?UTF-8?B?' . base64_encode($subject) . '?=';
                    $headers = "From: $name <{$adminEmail}>\r\n" .
                        "Reply-To: {$adminEmail}\r\n" .
                        "MIME-Version: 1.0\r\n" .
                        "Content-type: text/html; charset=UTF-8";
                    $model->save();
                    Yii::app()->user->setFlash('success', 'Link to reset your password has been sent to your email');
                    // mail($userEmail, $subject, $content, $headers);
                    $this->redirect(array('site/login'));
                }
            }
        }
        $this->render('forgotpassword', array(
            'model' => $model,
        ));
    }

    public function getToken($token)
    {
        $model=AdminUser::model()->findByAttributes(array('auth_key'=>$token));
        if($model===null)
            throw new CHttpException(404,'The requested page does not exist.');
        return $model;
    }

    public function actionVerify($token)
    {
        $model=$this->getToken($token);
        $model->scenario = 'changePassword';
        if(isset($_POST['AdminUser']))
        {
            if($model->auth_key==$token){
                $model->attributes = $_POST['AdminUser'];

                $model->auth_key="";
                if($model->save()){
                    Yii::app()->user->setFlash('success', '<b>Password has been successfully changed! please login</b>');
                    $this->redirect('?r=site/login');
                }
            }
        }
        $this->render('verify',array(
            'model'=>$model,
        ));
    }

}
