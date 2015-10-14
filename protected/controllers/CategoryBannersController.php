<?php

class CategoryBannersController extends Controller {

    public $status;

    /**
    * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
    * using two-column layout. See 'protected/views/layouts/column2.php'.
    */
    public $layout = '//layouts/column2';

    /**
    * @return array action filters
    */
    public function filters() {
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
    public function accessRules() {
        return array(
        array('allow', // allow all users to perform 'index' and 'view' actions
        	'actions'=>array(''),
			'users'=>array('*'),
        ),
        array('allow', // allow authenticated user to perform 'create' and 'update' actions
        'actions' => array('create', 'update','admin','AjaxCities'),
        'users' => array('@'),
        ),
        array('allow', // allow admin user to perform 'admin' and 'delete' actions
        'actions' => array('admin', 'delete','create','AjaxCities'),
        'users' => array('admin'),
        ),
        array('deny', // deny all users
        'users' => array('*'),
        ),
        );
    }

    /**
    * Displays a particular model.
    * @param integer $id the ID of the model to be displayed
    */
    public function actionView() {
        $this->render('view', array(
        'model' => $this->loadModel(),
        ));
    }

    /**
    * Creates a new model.
    * If creation is successful, the browser will be redirected to the 'view' page.
    */

    /**
    * Updates a particular model.
    * If update is successful, the browser will be redirected to the 'view' page.
    * @param integer $id the ID of the model to be updated
    */
    public function actionUpdate($id) {       

        $model = $this->loadModel($id);

        if (isset($_POST['StoreFront'])&& isset($id) ) { 

            $model->attributes = $_POST['StoreFront'];    
            $model->save();
            Yii::app()->user->setFlash('success', 'Updated Successfully !.' );            
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

    /**
    * Lists all models.
    */
    public function actionIndex() {
        $id = Yii::app()->session['store_id'];
        $getrecord = new UserStore();
        $record = $getrecord->getRecordById($id);
        $this->render('_view', array(
        'myVariable' => $record,
        ));
    }

    /**
    * Manages all models.
    */
    public function actionAdmin() {



        $model = new CategoryBanners('search'); 

        $model->unsetAttributes();  // clear any default values
        if (isset($_GET['CategoryBanners']))
            $model->attributes = $_GET['CategoryBanners'];

        $this->render('admin', array(
        'model' => $model,

        ));  

    }


    public function actionCreate() {
        $model = new CategoryBanners();   
        $level2='';
        // $level3='';
        $level2_id='';
        $level1_id='';

        if(isset($_POST['CatLevel1']) && ($_POST['CatLevel1']>0) )
        {
            $level1_id=$_POST['CatLevel1'];
            $level2=Category::model()->getcatbyparrentid($_POST['CatLevel1']); 
        }

        if(isset($_POST['CatLevel2']) && ($_POST['CatLevel2']>0) )
        {
            $level2_id=$_POST['CatLevel2'];
            $level3=Category::model()->getcatbyparrentid($_POST['CatLevel2']); 
        } 

        $cat_id = 0;
        if(isset($_POST['CatLevel1']) && ($_POST['CatLevel1']>0)){
            $cat_id = $_POST['CatLevel1'];
        }
        if(isset($_POST['CatLevel2']) && ($_POST['CatLevel2']>0)){
            $cat_id = $_POST['CatLevel2'];
        }

        //if (isset($_POST['CategoryBanners']))
        if(isset($_POST['yt0']))
        {
            $model1 = new FrontCSV;
            //.........................forcategory......//   
            /*if(isset($_POST['CatLevel3']))
            $cat_id=$_POST['CatLevel3'];         */
            //...........................end.................//

            //.................................banner1..............................................//
            $banner1 = CUploadedFile::getInstancesByName('Banner_1_Image'); 
            if(isset($banner1) && count($banner1) > 0)
            {
                $type = 'Cat_Header_1';
                $this->createImage($banner1,$type,$cat_id);
            }
            if(isset($_POST['link1']) && !empty($_POST['link1']))
            {
                $type = 'Cat_Header_1';
                $this->createLink($_POST['link1'],$type,$cat_id);
            }
            if(isset($_POST['Status1']))
            {
                $type = 'Cat_Header_1';
                $this->createPost($_POST['Status1'],$type,$cat_id);
            }
            if(isset($_POST['Title1']) && !empty($_POST['Title1']))
            {
                $type = 'Cat_Header_1';
                $this->createTitle($_POST['Title1'],$type,$cat_id);
            }
            //.......................end............................//    

            //.................................banner2..............................................//
            $banner2 = CUploadedFile::getInstancesByName('Banner_2_Image'); 

            if(isset($banner2) && count($banner2) > 0)
            {
                $type='Cat_Header_2';
                $this->createImage($banner2,$type,$cat_id);
            }
            if(isset($_POST['link2']) && !empty($_POST['link2']))
            {
                $type='Cat_Header_2';
                $this->createLink($_POST['link2'],$type,$cat_id);
            }
            if(isset($_POST['Status2']))
            {
                $type='Cat_Header_2';
                $this->createPost($_POST['Status2'],$type,$cat_id);
            }
            if(isset($_POST['Title2']) && !empty($_POST['Title2']))
            {
                $type='Cat_Header_2';
                $this->createTitle($_POST['Title2'],$type,$cat_id);
            }
            //.......................end............................//    

            //.................................banner3..............................................//
            if(!empty($_FILES['img_3_1']['tmp_name']) )
            {
                $type='Cat_Header_3_img_1';
                $this->createImage1($_FILES['img_3_1'],$type,$cat_id);
            }
            if(isset($_POST['link3_1']) && !empty($_POST['link3_1']))
            {
                $type='Cat_Header_3_img_1';
                $this->createLink($_POST['link3_1'],$type,$cat_id);
            }
            if(isset($_POST['Status3_1']))
            {
                $type='Cat_Header_3_img_1';
                $this->createPost($_POST['Status3_1'],$type,$cat_id);
            }
            if(isset($_POST['Title3_1']) && !empty($_POST['Title3_1']))
            {
                $type='Cat_Header_3_img_1';
                $this->createTitle($_POST['Title3_1'],$type,$cat_id);
            }
            //.......................end............................//    

            //.................................banner3.2..............................................//
            if(!empty($_FILES['img_3_2']['tmp_name']))
            {
                $type='Cat_Header_3_img_2';
                $this->createImage1($_FILES['img_3_2'],$type,$cat_id);
            }
            if(isset($_POST['link3_2']) && !empty($_POST['link3_2']))
            {
                $type='Cat_Header_3_img_2';
                $this->createLink($_POST['link3_2'],$type,$cat_id);
            }
            if(isset($_POST['Status3_2']))
            {
                $type='Cat_Header_3_img_2';
                $this->createPost($_POST['Status3_2'],$type,$cat_id);
            }
            if(isset($_POST['Title3_2']) && !empty($_POST['Title3_2']))
            {
                $type='Cat_Header_3_img_2';
                $this->createTitle($_POST['Title3_2'],$type,$cat_id);
            }
            //.......................end............................//    

            //.................................banner3.3..............................................//
            if(!empty($_FILES['img_3_3']['tmp_name']))
            {
                $type='Cat_Header_3_img_3';
                $this->createImage1($_FILES['img_3_3'],$type,$cat_id);
            }
            if(isset($_POST['link3_3']) && !empty($_POST['link3_3']))
            {
                $type='Cat_Header_3_img_3';
                $this->createLink($_POST['link3_3'],$type,$cat_id);
            }
            if(isset($_POST['Status3_3']))
            {
                $type='Cat_Header_3_img_3';
                $this->createPost($_POST['Status3_3'],$type,$cat_id);
            }
            if(isset($_POST['Title3_3']) && !empty($_POST['Title3_3']))
            {
                $type='Cat_Header_3_img_3';
                $this->createTitle($_POST['Title3_3'],$type,$cat_id);
            }
            //.......................end............................//    

            //.................................banner3.4..............................................//
            if(!empty($_FILES['img_3_4']['tmp_name']))
            {
                $type='Cat_Header_3_img_4';
                $this->createImage1($_FILES['img_3_4'],$type,$cat_id);
            }
            if(isset($_POST['link3_4']) && !empty($_POST['link3_4']))
            {
                $type='Cat_Header_3_img_4';
                $this->createLink($_POST['link3_4'],$type,$cat_id);
            }
            if(isset($_POST['Status3_4']))
            {
                $type='Cat_Header_3_img_4';
                $this->createPost($_POST['Status3_4'],$type,$cat_id);
            }
            if(isset($_POST['Title3_4']) && !empty($_POST['Title3_4']))
            {
                $type='Cat_Header_3_img_4';
                $this->createTitle($_POST['Title3_4'],$type,$cat_id);
            }
            //.......................end............................//    

            //.................................banner3.5..............................................//
            if(!empty($_FILES['img_3_5']['tmp_name']))
            {
                $type='Cat_Header_3_img_5';
                $this->createImage1($_FILES['img_3_5'],$type,$cat_id);
            }
            if(isset($_POST['link3_5']) && !empty($_POST['link3_5']))
            {
                $type='Cat_Header_3_img_5';
                $this->createLink($_POST['link3_5'],$type,$cat_id);
            }
            if(isset($_POST['Status3_5']))
            {
                $type='Cat_Header_3_img_5';
                $this->createPost($_POST['Status3_5'],$type,$cat_id);
            }
            if(isset($_POST['Title3_5']) && !empty($_POST['Title3_5']))
            {
                $type='Cat_Header_3_img_5';
                $this->createTitle($_POST['Title3_5'],$type,$cat_id);
            }
            //.......................end............................//    
        }

        //.....................get image dedai....................//
        $bannerinfo='';
        if($cat_id!=0)
        {
            $bannerinfo=$model->getbanners($cat_id);  
        }
        //......................end.................................//

        $this->render('create', array(
        'model' => $model,
        'level2' => $level2,
        'level1_id' => $level1_id,
        'bannerinfo'  =>$bannerinfo,
        /* 'level3' => $level3,  */
        'level2_id' => $level2_id, 
        ));  
    }

    /**
    * Returns the data model based on the primary key given in the GET variable.
    * If the data model is not found, an HTTP exception will be raised.
    * @param integer $id the ID of the model to be loaded
    * @return Store the loaded model
    * @throws CHttpException
    */
    public function loadModel($id) {
        $model = StoreFront::model()->findByPk($id);
        if ($model === null)
            throw new CHttpException(404, 'The requested page does not exist.');
        return $model;
    }

    /**
    * Performs the AJAX validation.
    * @param Store $model the model to be validated
    */
    protected function performAjaxValidation($model) {
        if (isset($_POST['ajax']) && $_POST['ajax'] === 'store-form') {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
    }

    public function createImage($banner,$type,$cat_id)
    {
        foreach ($banner as $image => $pic) 
        {
            $pic->saveAs(UPLOAD_MEDIA_PATH.$pic->name); 
            $base_img_name  =$type.uniqid();   
            $file1 = $base_img_name;
            $baseDir = BANNER_IMG_PATH;

            $media_url_dir=$baseDir;
            $content_medai_img=@file_get_contents(UPLOAD_MEDIA_PATH.$pic->name);
            $media_main=$media_url_dir.$base_img_name.'.jpg'; //name
            $media_main1=$base_img_name.'.jpg'; //name   
            @mkdir($media_url_dir, 0777, true);    
            $success = file_put_contents($media_main, $content_medai_img); 

            $sqldelban1 = "DELETE FROM banners WHERE type ='".$type."' and cat_id='".$cat_id."'";

            $connection = Yii::app() -> db;
            $command = $connection -> createCommand($sqldelban1);
            $command -> execute();

            $sqlinsban1 = "INSERT INTO banners (cat_id,image_url,type) VALUES('".$cat_id."','".$media_main1."', '".$type."')";

            $connection = Yii::app() -> db;
            $command = $connection -> createCommand($sqlinsban1);
            $command -> execute();                        
        }
    }
    public function createImage1($banner,$type,$cat_id)
    {
        $tmp_name = $banner["tmp_name"];
        $name = $banner["name"];
        move_uploaded_file($tmp_name, UPLOAD_MEDIA_PATH."$name");

        $base_img_name  =$type.uniqid();   
        $file1 = $base_img_name;
        $baseDir = BANNER_IMG_PATH;

        $media_url_dir=$baseDir;
        $content_medai_img=@file_get_contents(UPLOAD_MEDIA_PATH.$name);
        $media_main1=$base_img_name.'.jpg'; //name
        $media_main=$media_url_dir.$base_img_name.'.jpg'; //name    
        @mkdir($media_url_dir, 0777, true);    
        $success = file_put_contents($media_main, $content_medai_img); 

        $sqldelban1 = "DELETE FROM banners WHERE type ='".$type."' and cat_id='".$cat_id."'";
        $connection = Yii::app() -> db;
        $command = $connection -> createCommand($sqldelban1);
        $command -> execute();

        $sqlinsban1 = "INSERT INTO banners (cat_id,image_url,type) VALUES('".$cat_id."','".$media_main1."', '".$type."')";
        $connection = Yii::app() -> db;
        $command = $connection -> createCommand($sqlinsban1);
        $command -> execute();
    }

    public function createLink($link,$type,$cat_id)
    {
        $sqlinsbanLink1 = "Update banners set link='".addslashes($link)."' WHERE type ='".$type."' and cat_id='".$cat_id."'";
        $connection = Yii::app() -> db;
        $command = $connection -> createCommand($sqlinsbanLink1);
        $command -> execute();     
    }

    public function createPost($post,$type,$cat_id)
    {
        $sqlinsbanPost = "Update banners set status='".$post."' WHERE type ='".$type."' and cat_id='".$cat_id."'";
        $connection = Yii::app() -> db;
        $command = $connection -> createCommand($sqlinsbanPost);
        $command -> execute();     
    }

    public function createTitle($title,$type,$cat_id)
    {
        $sqlinsbanPost = "Update banners set title='".addslashes($title)."' WHERE type ='".$type."' and cat_id='".$cat_id."'";
        $connection = Yii::app() -> db;
        $command = $connection -> createCommand($sqlinsbanPost);
        $command -> execute();     
    }
}