<?php

class StoreController extends Controller {

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
        'actions' => array('create', 'update','export'),
        'users' => array('@'),
        ),
        array('allow', // allow admin user to perform 'admin' and 'delete' actions
        'actions' => array('admin', 'delete','export'),
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
    public function actionView($id) {
        $this->render('view', array(
        'model' => $this->loadModel($id),
        ));
    }

    /**
    * Creates a new model.
    * If creation is successful, the browser will be redirected to the 'view' page.
    */
    public function actionCreate() {
        $model = new Store;

        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);

        if (isset($_POST['Store'])) {
            $model->attributes = $_POST['Store'];
			$model->store_logo=CUploadedFile::getInstance($model,'store_logo');  
			 if(empty($model->store_logo))
			    unset($model['store_logo']);
            if ($model->save())
			{
			if(isset($model->store_logo))
				{
				
			   $model->store_logo->saveAs(STORE_LOGO_PATH.$model->store_logo); 
                
                        $base_img_name               = uniqid();   
                       //$path  = pathinfo($media);
                        $file1 = $base_img_name;
                        $baseDir = STORE_LOGO_PATH;
                        if ($file1[0]) {
                            $baseDir .= $file1[0] . '/';
                        }

                        if ($file1[1]) {
                            $baseDir .= $file1[1] . '/';
                        } else {
                            $baseDir .= '_/';
                        }      
                       $media_url_dir=$baseDir;
                       $content_medai_img=@file_get_contents(STORE_LOGO_PATH.$model->store_logo);
                       $media_main=$media_url_dir.$base_img_name.'.jpg'; //name
                       @mkdir($media_url_dir, 0777, true);    
                       $success = file_put_contents($media_main, $content_medai_img); 
                       
                            $baseThumbPath =STORE_LOGO_THUMB_PATH;
                            @mkdir($baseThumbPath, 0777, true); 
                            
                            $baseDir = $baseThumbPath;
                            if ($file1[0]) {
                                $baseDir .= $file1[0] . '/';
                            }

                            if ($file1[1]) {
                                $baseDir .= $file1[1] . '/';
                            } else {
                                $baseDir .= '_/';
                            }
                             $thumb_url_dir=$baseDir;
                             $media_thumb_url = $thumb_url_dir.$base_img_name.'.jpg';
                            @mkdir($thumb_url_dir, 0777, true);
                            $width = 150; $height = 150;
                            $image = $this->createImage(STORE_LOGO_PATH.$model->store_logo,$width,$height,$media_thumb_url); 
							}
                $this->redirect(array('admin', 'id' => $model->store_id));
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
    public function actionUpdate($id) {
       // $id = Yii::app()->session['store_id'];
        $model = $this->loadModel($id);
        $status= $model->status;


        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);

        if (isset($_POST['Store'])&& isset($id) ) { 
            
            $model->attributes = $_POST['Store'];
			
            $model->store_logo=CUploadedFile::getInstance($model,'store_logo');            
            unset($model['password']); 
            if(empty($model->store_logo))
			    unset($model['store_logo']);
            if ($model->save())
			{
			    if(isset($model->store_logo))
				{
				
                $model->store_logo->saveAs(STORE_LOGO_PATH.$model->store_logo); 
                
                        $base_img_name               = uniqid();   
                       //$path  = pathinfo($media);
                        $file1 = $base_img_name;
                        $baseDir = STORE_LOGO_PATH;
                        if ($file1[0]) {
                            $baseDir .= $file1[0] . '/';
                        }

                        if ($file1[1]) {
                            $baseDir .= $file1[1] . '/';
                        } else {
                            $baseDir .= '_/';
                        }      
                       $media_url_dir=$baseDir;
                       $content_medai_img=@file_get_contents(STORE_LOGO_PATH.$model->store_logo);
                       $media_main=$media_url_dir.$base_img_name.'.jpg'; //name
                       @mkdir($media_url_dir, 0777, true);    
                       $success = file_put_contents($media_main, $content_medai_img); 
                       
                            $baseThumbPath =STORE_LOGO_THUMB_PATH;
                            @mkdir($baseThumbPath, 0777, true); 
                            
                            $baseDir = $baseThumbPath;
                            if ($file1[0]) {
                                $baseDir .= $file1[0] . '/';
                            }

                            if ($file1[1]) {
                                $baseDir .= $file1[1] . '/';
                            } else {
                                $baseDir .= '_/';
                            }
                             $thumb_url_dir=$baseDir;
                            $media_thumb_url = $thumb_url_dir.$base_img_name.'.jpg';
                            @mkdir($thumb_url_dir, 0777, true);
                            $width = 150; $height = 150;
                            $image = $this->createImage(STORE_LOGO_PATH.$model->store_logo,$width,$height,$media_thumb_url); 
                           // unlink(BASE_IMG_LOG_CSV);
                        }
						//.......................solor backloag.................//
                         $solrBackLog = new SolrBackLog();
                         //$is_deleted =  ($model->status == 1) ? 0 : 1;
						 $is_deleted = '0';
                         $solrBackLog->insertByStoreId($id,$is_deleted); 
					   //.........................end.....................................//
					   
              }     $this->redirect(array('update', 'id' => $model->store_id));
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
    public function actionDelete($id) {
        $this->loadModel($id)->delete();

        // if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
        if (!isset($_GET['ajax']))
            $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
    }

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

        $id = Yii::app()->session['store_id'];

        if($id==1)
        {
            $model = new Store('search'); 
            $model->unsetAttributes();  
            if(isset($_GET['Store'])) {
                $model->attributes=$_GET['Store'];
            }
            $model->setAttribute('is_deleted','=0');      
        }
        else
        {
            $model = new UserStore();
            $record = $model->getRecordById($id);
            $model->unsetAttributes();
            $model->attributes=$record[0];

        }


        $this->render('admin', array(
        'model' => $model,

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
	
        $model = Store::model()->findByPk($id);
		
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
    
    
   public function createImage($url , $width = 150, $height = 150, $savePath)
    {
        // The file
        $filename = $url;
        $pathInfo = pathinfo($filename);
        $savePath = $savePath;

        // Set a maximum height and width
        $width = $width;
        $height = $height;

        $size = getimagesize($filename);

        // Get new dimensions
        list($width_orig, $height_orig) = getimagesize($filename);

        $ratio_orig = $width_orig/$height_orig;

        if ($width/$height > $ratio_orig) {
            $width = $height*$ratio_orig;
        } else {
            $height = $width/$ratio_orig;
        }

        // Resample
        $image_p = imagecreatetruecolor($width, $height);

        if($size['mime'] == "image/jpeg")
            $image = imagecreatefromjpeg($filename);
        elseif($size['mime'] == "image/png")
            $image = imagecreatefrompng($filename);
        elseif($size['mime'] == "image/gif")
            $image = imagecreatefromgif($filename);
        else
            $image = imagecreatefromjpeg($filename);

        /*handle transparency for gif and png images*/
        if($size['mime'] == "image/png") {
            imagesavealpha($image_p, true);
            $color = imagecolorallocatealpha($image_p,0,0,0,127);
            imagefill($image_p, 0, 0, $color);
        } 

        imagecopyresampled($image_p, $image, 0, 0, 0, 0, $width, $height, $width_orig, $height_orig);

        if($size['mime'] == "image/jpeg")
            imagejpeg($image_p, $savePath, 100);
        elseif($size['mime'] == "image/png")
            imagepng($image_p, $savePath, 9);
        elseif($size['mime'] == "image/gif")
            imagegif($image_p, $savePath);
        else
            imagejpeg($image_p, $savePath, 100);

        // Free up memory
        imagedestroy($image_p);

        return true;
    }
	public function actionExport($id){	
	$connection = Yii::app()->db;
	$sqlchksubsid = "SELECT sp.`subscribed_product_id`,sp.`base_product_id`,sp.`store_id`,s.store_name,bs.title,bs.color,sp.`store_price`,sp.`store_offer_price`,sp.`weight`,sp.`length`,sp.`width`,sp.`height`,sp.`status`,sp.sku,sp.`quantity`,sp.`is_cod`,sp.`created_date`,sp.`modified_date` FROM `subscribed_product` sp join store as s on s.store_id=sp.`store_id` join base_product as bs on bs.`base_product_id`=sp.`base_product_id` WHERE sp.`store_id`=".$id." group by sp.`base_product_id`";   
	$command1 = $connection->createCommand($sqlchksubsid);
    $command1->execute();
    $assocDataArray=$command1->queryAll();	
	$fileName="Store_id_".$id.".csv"; 
	ob_clean();
    header('Pragma: public');
    header('Expires: 0');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Cache-Control: private', false);
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment;filename=' . $fileName);    
    if(isset($assocDataArray['0'])){
        $fp = fopen('php://output', 'w');
        fputcsv($fp, array_keys($assocDataArray['0']));
        foreach($assocDataArray AS $values){
            fputcsv($fp, $values);
        }
        fclose($fp);
    }
    ob_flush();
}

}
