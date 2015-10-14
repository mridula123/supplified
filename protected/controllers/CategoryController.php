<?php
error_reporting(0);
class CategoryController extends Controller {

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
        'actions' => array(''),
        'users' => array('*'),
        ),
        array('allow', // allow authenticated user to perform 'create' and 'update' actions
        'actions' => array('create','index','update', 'tree','BulkUpload','CreateFileDownload','Export','secondlabel','thirdlabel','dataexport','dataexportbig'),
        'users' => array('@'),
        ),
        array('allow', // allow admin user to perform 'admin' and 'delete' actions
        'actions' => array('create','admin', 'delete', 'tree','BulkUpload','CreateFileDownload','Export','secondlabel','thirdlabel','deletecat','dataexport','dataexportbig'),
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
    public function actionCreate($category_id = null) {

        $model = new Category;
        
        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);
        if ($category_id == null) {
            $category_id = $model->getDefaultCategoryId();
        }
        
        //echo'<pre>';print_r($_POST['Category']['category_name']);die;
        if (isset($_POST['Category'])) {
        	
        	$category_name=$_POST['Category']['category_name'];
        	$shipping_charge=$_POST['Category']['category_shipping_charge'];
        	$selMaincat=$_POST['Category']['selMaincat'];
        	$selMainsecond=$_POST['selMainsecond'];
        	

            if(isset($_POST['aiotree']['category_id']))
            {
                $val= explode("-",$_POST['aiotree']['category_id'][0]);
                //echo'<pre>';print_r($val);die;
                $connection = Yii::app()->db;
                $sql = "select category_id,category_name,parent_category_id,level,path from category where category_id=$val[3]";
                $command = $connection->createCommand($sql);
                $row = $command->queryAll();

                $level=$row[0]['level']+1;

                $model->attributes = $_POST['Category'];
                $category_name = $_POST['Category']['category_name'];
                $connection = Yii::app()->db;

                $sql = "INSERT INTO category(category_name, parent_category_id, level) VALUES('".$category_name."', '".$row[0]['category_id']."', '".$level."')";
                $command = $connection->createCommand($sql);
                $command->execute();
                $id = Yii::app()->db->getLastInsertID();

                $mysql = "Update category set path='".$row[0]['path']."/".$id."' where category_id=$id";
                $command = $connection->createCommand($mysql);
                $command->execute();

            }
            else {
            	$model->attributes = $_POST['Category'];
                $category_name = $_POST['Category']['category_name'];
                $connection = Yii::app()->db;
                if($selMaincat =='' && $selMainsecond ==''){
                	$sql = "INSERT INTO category(category_name, parent_category_id, level) VALUES('".$category_name."', '2', '2')";
                }
                
                if($selMaincat !='' && $selMainsecond ==''){
                	$sql = "INSERT INTO category(category_name, parent_category_id, level) VALUES('".$category_name."', '".$selMaincat."', '3')";
                }
                
                if($selMaincat !='' && $selMainsecond !=''){
                	$sql = "INSERT INTO category(category_name, parent_category_id, level) VALUES('".$category_name."', '".$selMainsecond."', '4')";
                }
                
                $command = $connection->createCommand($sql);
                $command->execute();
                $id = Yii::app()->db->getLastInsertID();
                if($selMaincat =='' && $selMainsecond ==''){
                	$mysql = "Update category set path='1/2/$id' where category_id=$id";
                }
                if($selMaincat !='' && $selMainsecond ==''){
                	$mysql = "Update category set path='1/2/$id' where category_id=$id";
                }
                if($selMaincat !='' && $selMainsecond !=''){
                	$mysql = "Update category set path='1/2/$selMaincat/$id' where category_id=$id";
                }
                $command = $connection->createCommand($mysql);
                $command->execute();
                
                
                
                //Start Images
                if($_FILES['Category']['name']['cat_banner_img'] !=''){
                	$CatbannerImg 		  = $_FILES['Category']['name']['cat_banner_img'];
                	$CatbannerImgName     = pathinfo($CatbannerImg, PATHINFO_FILENAME);
                	$CatbannerImgExt      = pathinfo($CatbannerImg, PATHINFO_EXTENSION);
                	$CatbannerImgnewName  = date("mdYhis", time())."-".$id.'.'.$CatbannerImgExt;
                	$model->cat_banner_img = CUploadedFile::getInstance($model,'cat_banner_img');
                }
                if($_FILES['Category']['name']['cat_pro_img'] !=''){
                	$CatProImg 			  = $_FILES['Category']['name']['cat_pro_img'];
                	$CatProImgName   	  = pathinfo($CatProImg, PATHINFO_FILENAME);
                	$CatProImgExt    	  = pathinfo($CatProImg, PATHINFO_EXTENSION);
                	$CatProImgnewName     = date("mdYhis", time())."-".$id.'.'.$CatProImgExt;
                	$model->cat_pro_img = CUploadedFile::getInstance($model,'cat_pro_img');
                }
                
                
                if($_FILES['Category']['name']['cat_banner_img'] !=''){
                	$fullImgSource = Yii::getPathOfAlias('webroot').'/banner/'.$CatbannerImgnewName;
                	$model->cat_banner_img->saveAs($fullImgSource);
                	$model->cat_banner_img = $CatbannerImgnewName;
                	$mysql0 = "Update category set cat_banner_img='$CatbannerImgnewName'  where category_id=$id";
                	$command = $connection->createCommand($mysql0);
                	$command->execute();
                }
                if($_FILES['Category']['name']['cat_pro_img'] !=''){
                	$fullImgSourcecatpro = Yii::getPathOfAlias('webroot').'/catproductimages/'.$CatProImgnewName;
                	$model->cat_pro_img->saveAs($fullImgSourcecatpro);
                	$model->cat_pro_img = $CatProImgnewName;
                	
                	$mysql1 = "Update category set cat_pro_img='$CatProImgnewName'  where category_id=$id";
                	$command = $connection->createCommand($mysql1);
                	$command->execute();
                }
             
               
                //End Images

            }



            $this->redirect(array('index', 'id' => $model->category_id));
        }

        $this->render('create', array(
        'model' => $model,
        'category_id' => $category_id,
        ));
    }
    
    
    public function actionSecondlabel()
    {
    	$data=Category::model()->findAll('parent_category_id=:parent_category_id',array(':parent_category_id'=>(int) $_POST['category_id']));
    	$data=CHtml::listData($data,'category_id','category_name');
    	
    	
    	echo "<option value=''>Second Label</option>";
    	foreach($data as $value=>$subcategory)  {
    		echo CHtml::tag('option',array('value'=>$value),CHtml::encode($subcategory),true);
    	}
    }
    
    public function actionThirdlabel()
    {
    	$data=Category::model()->findAll('parent_category_id=:parent_category_id',array(':parent_category_id'=>(int) $_POST['category_id']));
    	$data=CHtml::listData($data,'category_id','category_name');
    	echo "<option value=''>Third level</option>";
    	foreach($data as $value=>$subcategory)  {
    		echo CHtml::tag('option',array('value'=>$value),CHtml::encode($subcategory),true);
    	}
    }


    /**
    * Updates a particular model.
    * If update is successful, the browser will be redirected to the 'view' page.
    * @param integer $id the ID of the model to be updated
    */
    public function actionUpdate($id) {
        $model = $this->loadModel($id);

        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);

        if (isset($_POST['Category'])) {
            $model->attributes = $_POST['Category'];
            if ($model->save())
			{
			   //.......................solor backloag.................//
                         $solrBackLog = new SolrBackLog();
                         //$is_deleted =  ($model->status == 1) ? 0 : 1;
						 $is_deleted = '0';
                         $solrBackLog->insertByCategoryId($model->category_id,$is_deleted); 
					   //.........................end.....................................//
                $this->redirect(array('view', 'id' => $model->category_id));
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
    public function actionDelete($id) {
        $this->loadModel($id)->delete();

        // if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
        if (!isset($_GET['ajax']))
            $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
    }
    
    
    public function actionDeletecat($category_id)
    {
    	
     	
    	
    	if(isset($_REQUEST['Category'])){
    	
    		if(@$_REQUEST['Category']['catimgs'] =='1'){
    			$connection = Yii::app()->db;
    			$sql = "Update category set cat_banner_img='' where category_id=$category_id";
    			$command = $connection->createCommand($sql);
    			$command->execute();
    			$this->redirect(array('index'));
    		}else{
    	
    		}
    	
    		if(@$_REQUEST['Category']['catProimgs'] =='1'){
    			$connection = Yii::app()->db;
    			$sql = "Update category set cat_pro_img='' where category_id=$category_id";
    			$command = $connection->createCommand($sql);
    			$command->execute();
    			$this->redirect(array('index'));
    		}else{
    	
    		}
    		
    		if(@$_REQUEST['Category']['catDel'] =='1'){
		    		$chkchildinfo= $_REQUEST['Category']['DeleteId'];
		    		
		    		if(!empty($chkchildinfo))
		    		{
		    			$connection = Yii::app()->db;
		    			 
		    			$sql   = "SELECT category_id,parent_category_id,level,path FROM `category` WHERE `parent_category_id` ='".$chkchildinfo."'";
		    			$command = $connection->createCommand($sql);
		    			$command->execute();
		    			$rs=$command->queryAll();
		    			 
		    			if($rs ==NULL){
		    				$sqldel   = "DELETE FROM `category` WHERE `category_id` = '".$chkchildinfo."'";
		    				$command = $connection->createCommand($sqldel);
		    				$command->execute();
		    				Yii::app()->user->setFlash('success','Category Deleted successfully.');
		    				$this->redirect(array('index'));
		    			}else{
		    				Yii::app()->user->setFlash('error','Category can not Deleted.Delete all child first.! .');
		    				$this->redirect(array('index','category_id' =>$category_id));
		    			}
		    		}
    		}else{
    			
    		}
    		
    		
    		
    	}
    	
    	
	   
    	
    	
    }

    /**
    * Lists all models.
    */
    public function actionIndex($category_id = null) {
       		
		$model = new Category();
		$baseProductModel=new BaseProduct();
		$baseProductModel->unsetAttributes();
		$baseProductModel->setAttribute('is_deleted','=0');
		$baseProductModel->setAttribute('status','=1');
		
		
		
		
		
		
		
		//................................................//
		
	     if(isset($_POST['yt1']) && $_POST['yt1']=='Delete')
		  {
		     
		   //$chkchildinfo=$model->chkchildById($category_id);
		  	
		  	$chkchildinfo= $_REQUEST['Category']['DeleteId'];
			 
			
			 if(!empty($chkchildinfo))
			 {
			 Yii::app()->user->setFlash('error','Category can not Deleted.Delete all child first.! .');
			 $this->redirect(array('index','category_id' =>$category_id));
			 }
			 else
			 {
			   
			  $chkchildinfo=$model->delcatById($category_id);
			  Yii::app()->user->setFlash('success','Category Deleted successfully.');
			  $this->redirect(array('index','category_id' =>$chkchildinfo));
			 }
			 
		  }
		else
	    {
		   
	  
        //................................................//
		
        
			if(isset($_REQUEST['BaseProduct'])) {
				$baseProductModel->attributes=$_REQUEST['BaseProduct'];
				$baseProductModel->base_product_id = $_REQUEST['BaseProduct']['base_product_id'];
			}
			$cat_base_product_ids = null;
			if ($category_id == null) {
				$category_id = $model->getDefaultCategoryId();
			}
			if (!empty($category_id)) {
				$model=$this->loadModel($category_id);
				if(isset($_REQUEST['reset']))
				 $reset = 1 ;
				 else
				  $reset = 0 ;
				
				if(isset($_REQUEST['Category']))
				{
			
					
					
					
					$model->attributes=$_POST['Category'];
					$model->cat_base_product_ids = $model->getBaseProducts($category_id);
					
					//Images
					if($_FILES['Category']['name']['cat_banner_img'] !=''){
						$CatbannerImg 		  = $_FILES['Category']['name']['cat_banner_img'];
						$CatbannerImgName     = pathinfo($CatbannerImg, PATHINFO_FILENAME);
						$CatbannerImgExt      = pathinfo($CatbannerImg, PATHINFO_EXTENSION);
						$CatbannerImgnewName  = date("mdYhis", time())."-".$model->category_id.'.'.$CatbannerImgExt;
						$model->cat_banner_img = CUploadedFile::getInstance($model,'cat_banner_img');
					}
					if($_FILES['Category']['name']['cat_pro_img'] !=''){
						$CatProImg 			  = $_FILES['Category']['name']['cat_pro_img'];
						$CatProImgName   	  = pathinfo($CatProImg, PATHINFO_FILENAME);
						$CatProImgExt    	  = pathinfo($CatProImg, PATHINFO_EXTENSION);
						$CatProImgnewName     = date("mdYhis", time())."-".$model->category_id.'.'.$CatProImgExt;
						$model->cat_pro_img = CUploadedFile::getInstance($model,'cat_pro_img');
					}
					//End Images
					
					if($model->save()) {
						
						//Start Images
						if($_FILES['Category']['name']['cat_banner_img'] !=''){
							$fullImgSource = Yii::getPathOfAlias('webroot').'/banner/'.$CatbannerImgnewName;
							$model->cat_banner_img->saveAs($fullImgSource);
							$model->cat_banner_img = $CatbannerImgnewName;
							$model->save();
						}
						if($_FILES['Category']['name']['cat_pro_img'] !=''){
							$fullImgSourcecatpro = Yii::getPathOfAlias('webroot').'/catproductimages/'.$CatProImgnewName;
							$model->cat_pro_img->saveAs($fullImgSourcecatpro);
							$model->cat_pro_img = $CatProImgnewName;
							$model->save();
						}
						
					
						
						 
						
						//End images
						//$catsecond=$_REQUEST['Category']['selMainsecond'];
						$catsecond=$_REQUEST['selMainsecond'];
						$maincat=$_REQUEST['Category']['selMaincat'];
						$id=@$_REQUEST['category_id'];

						//START CHANGE THIS AREAY
						$model->attributes=$_POST['Category'];
						$model->cat_base_product_ids = $model->getBaseProducts($category_id);
					

						if($maincat !='' && $catsecond ==''){
							$model->parent_category_id = $maincat;
							$model->path = '1/2/'.$maincat.'/'.$category_id;
							$model->level = '3';
							$model->save();
						}
						if($maincat !='' && $catsecond !=''){
							$model->parent_category_id = $catsecond;
							$model->path = '1/2/'.$maincat.'/'.$catsecond.'/'.$category_id;
							$model->level = '4';
							$model->save();
						}
						if($maincat =='' && $catsecond ==''){
							$model->parent_category_id = 2;
							$model->path = '1/2/'.$category_id;
							$model->level = '2';
							$model->save();
						}
						
						//END CHANGE THIS AREAY
						
						
						if (isset($_POST['userids'])) {
							$model->insertBaseproductMappings($_POST['userids'], $category_id);
						}
						
						Yii::app()->user->setFlash('success','Category saved successfully.');
						//$reset = 0;
						$this->redirect(array('index','category_id' => $model->category_id));
					}
				}
				//get base product ids by category filter
				$cat_base_product_ids = $model->getBaseProducts($category_id);
			}
		}
		$this->render('index',array(
			'model'=>$model,
			'baseProductModel' => $baseProductModel,
			'cat_base_product_ids'=>$cat_base_product_ids,
			'category_id' => $category_id,
			'reset' => $reset,
		));
		
		
		
    }
    /**
    * Manages all models.
    */
    public function actionAdmin() {
        //$id = Yii::app()->session['store_id'];
        $id = Yii::app()->session['store_id'];
        $model = new Category('search');
        $dataTree = $model->getRecordById($id);
        //echo '<pre>';print_r($dataTree);die;$level = 1;



        $model = new Category('search');
        $model->unsetAttributes();  // clear any default values
        if (isset($_GET['Category']))
            $model->attributes = $_GET['Category'];

        $this->render('admin', array(
        'model' => $model,
        'dataTree' => $dataTree
        ));

    }


    /**
    * Returns the data model based on the primary key given in the GET variable.
    * If the data model is not found, an HTTP exception will be raised.
    * @param integer $id the ID of the model to be loaded
    * @return Category the loaded model
    * @throws CHttpException
    */
    public function loadModel($id) {
        $model = Category::model()->findByPk($id);
        if ($model === null)
            throw new CHttpException(404, 'The requested page does not exist.');
        return $model;
    }

    /**
    * Performs the AJAX validation.
    * @param Category $model the model to be validated
    */
    protected function performAjaxValidation($model) {
        if (isset($_POST['ajax']) && $_POST['ajax'] === 'category-form') {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
    }

    public function actionBulkUpload() {
	set_time_limit(0); 

        $model = new FrontCSV;
        //$file = CUploadedFile::getInstance($model,'csv_file');
        
        //unit_rate
        
        $logfile ='';
        $L1_info = array();
        $L2_info = array();
        $L3_info = array();
        $L4_info = array();
        $L5_info = array();


        if (isset($_POST['FrontCSV'])) {
            $model->attributes = $_POST['FrontCSV'];
            if (!empty($_FILES['FrontCSV']['tmp_name']['csv_file'])) {
                $csv = CUploadedFile::getInstance($model, 'csv_file');
                if (!empty($csv)) {
                    if($csv->size > 25*1024*1024){

                        Yii::app()->user->setFlash('error', 'Cannot upload file greater than 25MB');
                        $this->render('bulkupload', array('model' => $model));
                    }
                    $fileName = 'csvupload/'.$csv->name;

                    $filenameArr = explode('.',$fileName);
                    $fileName = $filenameArr[0].'-'.Yii::app()->session['sessionId'].'-'.time().'.'.end($filenameArr);
                    $csv->saveAs($fileName);
                }else{
                    Yii::app()->user->setFlash('error', 'Please browse a CSV file to upload.');
                    $this->render('bulkupload', array('model' => $model));
                }



                $ext = pathinfo($fileName, PATHINFO_EXTENSION);
                if ($ext != 'csv') {
                    Yii::app()->user->setFlash('error', 'Only .csv files allowed.');
                    $this->render('bulkupload', array('model' => $model));
                }

                $i = 0;
                $requiredFields = array('L1');
                $defaultFields = array('L1','L2','L3','L3','L4','L5','L6');

                if(($handle = fopen("$fileName", "r")) !== FALSE) {
                    $logDir = "log/";
                    $logfile = 'category_bulk_upload_log_' . Yii::app()->session['sessionId'] . '_' . time() . '.txt';
                    $handle1 = fopen($logDir . $logfile, "a");
                    while(($data = fgetcsv($handle, 0, ",")) !== FALSE){
                        if($i>=0 && count($data)>0){
                            $i++;
                            /*header*/
                            if($i==1){

                                $colDiff = array_diff($requiredFields, $data);

                                if (!empty($colDiff)) {
                                    Yii::app()->user->setFlash('error', 'Required columns missing : ' . implode(' , ', $colDiff));

                                    break;
                                }

                                foreach($data as $key=>$value){
                                    $data[$key] = trim($value);
                                    if(in_array($value,$defaultFields)){
                                        $cols[$value] = $key;
                                    }elseif($value != ""){
                                        $originalExtraAttrs[$value] = $key;
                                        $value = mysql_real_escape_string($value);
                                        $extraAttributes[$value] = $key;
                                    }
                                }
                            }
                            else
                            {
                                $row = array();

                                if(isset($cols['L1']))
                                    $row['L1'] = trim($data[$cols['L1']]);
                                else
                                    $row['L1']='';                                    
                                if(isset($cols['L2']))
                                    $row['L2'] = trim($data[$cols['L2']]);
                                else
                                    $row['L2']='';
                                if(isset($cols['L3']))
                                    $row['L3'] = trim($data[$cols['L3']]);
                                else
                                    $row['L3']='';

                                if(isset($cols['L4']))
                                    $row['L4'] = trim($data[$cols['L4']]);
                                else
                                    $row['L4']='';                                    
                                if(isset($cols['L5']))
                                    $row['L5'] = trim($data[$cols['L5']]);
                                else
                                    $row['L5']='';
                                if(isset($cols['L6']))
                                    $row['L6'] = trim($data[$cols['L6']]);
                                else
                                    $row['L6']='';                                   

                                if(!empty($row['L1']) && $row['L1']!='')
                                {
                                    //......................l1........................//    
                                    if(isset($L1_info['name']) || !isset($L1_info['name']))
                                    {

                                        if(!isset($L1_info['name']))
                                        {

                                            $connection = Yii::app()->db;
                                            $sqlL1   = "SELECT category_id,parent_category_id,level,path FROM `category` WHERE `category_name` ='".addslashes($row['L1'])."'";        
                                            $command = $connection->createCommand($sqlL1);
                                            $command->execute();
                                            $rsL1=$command->queryAll(); 

                                            if($rsL1)
                                            {
                                                $L1_info['name']               = $row['L1'];
                                                $L1_info['category_id']        = $rsL1[0]['category_id'];
                                                $L1_info['parent_category_id'] = $rsL1[0]['parent_category_id'];
                                                $L1_info['level']              = $rsL1[0]['level'];
                                                $L1_info['path']               = $rsL1[0]['path'];
                                            }                                
                                            if(!$rsL1)
                                            {

                                                $sql      = "INSERT INTO category(category_name, parent_category_id, level) VALUES('".addslashes($row['L1'])."', '2', '2')";
                                                $command  = $connection->createCommand($sql);
                                                $command->execute();
                                                $id = Yii::app()->db->getLastInsertID();

                                                $mysql   = "Update category set path='1/2/$id' where category_id=$id";
                                                $command = $connection->createCommand($mysql);
                                                $command->execute();   

                                                $L1_info['name']                = $row['L1'];
                                                $L1_info['category_id']         = $id;
                                                $L1_info['parent_category_id']  = '2';
                                                $L1_info['level']               = '2';
                                                $L1_info['path']                = '1/2/'.$id;;
                                                fwrite($handle1, "\nRow :  category_name :".$row['L1']." ID is :".$id.".");                                       
                                            }
                                        }
                                        if(isset($L1_info['name']))
                                        { 

                                            if($L1_info['name']!=$row['L1'])
                                            {

                                                $connection = Yii::app()->db;
                                                $sqlL1   = "SELECT category_id,parent_category_id,level,path FROM `category` WHERE `category_name` ='".addslashes($row['L1'])."'";        
                                                $command = $connection->createCommand($sqlL1);
                                                $command->execute();
                                                $rsL1=$command->queryAll(); 

                                                if($rsL1)
                                                {
                                                    $L1_info['name']               = $row['L1'];
                                                    $L1_info['category_id']        = $rsL1[0]['category_id'];
                                                    $L1_info['parent_category_id'] = $rsL1[0]['parent_category_id'];
                                                    $L1_info['level']              = $rsL1[0]['level'];
                                                    $L1_info['path']               = $rsL1[0]['path'];
                                                }                                
                                                if(!$rsL1)
                                                {

                                                    $sql      = "INSERT INTO category(category_name, parent_category_id, level) VALUES('".addslashes($row['L1'])."', '2', '2')";
                                                    $command  = $connection->createCommand($sql);
                                                    $command->execute();
                                                    $id = Yii::app()->db->getLastInsertID();

                                                    $mysql   = "Update category set path='1/2/$id' where category_id=$id";
                                                    $command = $connection->createCommand($mysql);
                                                    $command->execute();   

                                                    $L1_info['name']                = $row['L1'];
                                                    $L1_info['category_id']         = $id;
                                                    $L1_info['parent_category_id']  = '2';
                                                    $L1_info['level']               = '2';
                                                    $L1_info['path']                = '1/2/'.$id;;
                                                    fwrite($handle1, "\nRow :  category_name :".$row['L1']." ID is :".$id.".");                                   
                                                }

                                            }
                                        }
                                    }
                                    //....................end l1...........................//  
                                    //.....................l2....................................//
                                    if(!empty($row['L2']) && $row['L2']!='')
                                    {
                                        //......................l1........................//    
                                        if(isset($L2_info['name']) || !isset($L2_info['name']))
                                        {

                                            if(!isset($L2_info['name']))
                                            {

                                                $sqlL2   = "SELECT category_id,parent_category_id,level,path FROM `category` WHERE `category_name` ='".addslashes($row['L2'])."' and parent_category_id='".$L1_info['category_id']."'";        
                                                $command = $connection->createCommand($sqlL2);
                                                $command->execute();
                                                $rsL2=$command->queryAll();

                                                if($rsL2)
                                                {
                                                    $L2_info['name']                 = $row['L2'];
                                                    $L2_info['category_id']          = $rsL2[0]['category_id'];
                                                    $L2_info['parent_category_id']   = $rsL2[0]['parent_category_id'];
                                                    $L2_info['level']                = $rsL2[0]['level'];
                                                    $L2_info['path']                 = $rsL2[0]['path'];
                                                }            

                                                if(!$rsL2)
                                                {                                 
                                                    $level1  = $L1_info['level']+1; 
                                                    $sqll2   = "INSERT INTO category(category_name, parent_category_id, level) VALUES('".addslashes($row['L2'])."', '".$L1_info['category_id']."', '".$level1."')";
                                                    $command = $connection->createCommand($sqll2);
                                                    $command->execute();
                                                    $idl2    = Yii::app()->db->getLastInsertID();   

                                                    $mysqll2 = "Update category set path='".$L1_info['path']."/".$idl2."' where category_id=$idl2";
                                                    $command = $connection->createCommand($mysqll2);
                                                    $command->execute();

                                                    $L2_info['name']               = $row['L2'];
                                                    $L2_info['category_id']        = $idl2;
                                                    $L2_info['parent_category_id'] = $L1_info['category_id'];
                                                    $L2_info['level']              = $level1;
                                                    $L2_info['path']               = $L1_info['path']."/".$idl2;

                                                    fwrite($handle1, "\nRow :  category_name :".$row['L2']." ID is :".$idl2.".");                                       
                                                }    
                                            }
                                            if(isset($L2_info['name']))
                                            { 

                                                if($L2_info['name']!=$row['L2'])
                                                {

                                                    $sqlL2   = "SELECT category_id,parent_category_id,level,path FROM `category` WHERE `category_name` ='".addslashes($row['L2'])."' and parent_category_id='".$L1_info['category_id']."'";        
                                                    $command = $connection->createCommand($sqlL2);
                                                    $command->execute();
                                                    $rsL2=$command->queryAll();

                                                    if($rsL2)
                                                    {
                                                        $L2_info['name']                 = $row['L2'];
                                                        $L2_info['category_id']          = $rsL2[0]['category_id'];
                                                        $L2_info['parent_category_id']   = $rsL2[0]['parent_category_id'];
                                                        $L2_info['level']                = $rsL2[0]['level'];
                                                        $L2_info['path']                 = $rsL2[0]['path'];
                                                    }                
                                                    if(!$rsL2)
                                                    {                                 
                                                        $level1  = $L1_info['level']+1; 
                                                        $sqll2   = "INSERT INTO category(category_name, parent_category_id, level) VALUES('".addslashes($row['L2'])."', '".$L1_info['category_id']."', '".$level1."')";
                                                        $command = $connection->createCommand($sqll2);
                                                        $command->execute();
                                                        $idl2    = Yii::app()->db->getLastInsertID();   

                                                        $mysqll2 = "Update category set path='".$L1_info['path']."/".$idl2."' where category_id=$idl2";
                                                        $command = $connection->createCommand($mysqll2);
                                                        $command->execute();

                                                        $L2_info['name']               = $row['L2'];
                                                        $L2_info['category_id']        = $idl2;
                                                        $L2_info['parent_category_id'] = $L1_info['category_id'];
                                                        $L2_info['level']              = $level1;
                                                        $L2_info['path']               = $L1_info['path']."/".$idl2;

                                                        fwrite($handle1, "\nRow :  category_name :".$row['L2']." ID is :".$idl2.".");                                       
                                                    }   

                                                }
                                            }

                                        }
                                        //..................................l3.............................//
                                        if(!empty($row['L3']) && $row['L3']!='')
                                        {

                                            if(isset($L3_info['name']) || !isset($L3_info['name']))
                                            {

                                                if(!isset($L3_info['name']))
                                                {

                                                    $sqlL3   = "SELECT category_id,parent_category_id,level,path FROM `category` WHERE `category_name` ='".addslashes($row['L3'])."' and parent_category_id='".$L2_info['category_id']."'";        
                                                    $command = $connection->createCommand($sqlL3);
                                                    $command->execute();
                                                    $rsL3=$command->queryAll();

                                                    if($rsL3)
                                                    {
                                                        $L3_info['name']                 = $row['L3'];
                                                        $L3_info['category_id']          = $rsL3[0]['category_id'];
                                                        $L3_info['parent_category_id']   = $rsL3[0]['parent_category_id'];
                                                        $L3_info['level']                = $rsL3[0]['level'];
                                                        $L3_info['path']                 = $rsL3[0]['path'];
                                                    }    

                                                    if(!$rsL3)
                                                    {                                 
                                                        $level2  = $L2_info['level']+1; 
                                                        $sqll3   = "INSERT INTO category(category_name, parent_category_id, level) VALUES('".addslashes($row['L3'])."', '".$L2_info['category_id']."', '".$level2."')";
                                                        $command = $connection->createCommand($sqll3);
                                                        $command->execute();
                                                        $idl3 = Yii::app()->db->getLastInsertID();    

                                                        $mysqll3 = "Update category set path='".$L2_info['path']."/".$idl3."' where category_id=$idl3";
                                                        $command = $connection->createCommand($mysqll3);
                                                        $command->execute();

                                                        $L3_info['name']               = $row['L2'];
                                                        $L3_info['category_id']        = $idl3;
                                                        $L3_info['parent_category_id'] = $L2_info['category_id'];
                                                        $L3_info['level']              = $level2;
                                                        $L3_info['path']               = $L2_info['path']."/".$idl3;    
                                                        fwrite($handle1, "\nRow :  category_name :".$row['L3']." ID is :".$idl3.".");                                                       
                                                    }
                                                }
                                                if(isset($L3_info['name']))
                                                { 

                                                    if($L3_info['name']!=$row['L3'])
                                                    {

                                                        $sqlL3   = "SELECT category_id,parent_category_id,level,path FROM `category` WHERE `category_name` ='".addslashes($row['L3'])."' and parent_category_id='".$L2_info['category_id']."'";        
                                                        $command = $connection->createCommand($sqlL3);
                                                        $command->execute();
                                                        $rsL3=$command->queryAll();

                                                        if($rsL3)
                                                        {
                                                            $L3_info['name']                 = $row['L3'];
                                                            $L3_info['category_id']          = $rsL3[0]['category_id'];
                                                            $L3_info['parent_category_id']   = $rsL3[0]['parent_category_id'];
                                                            $L3_info['level']                = $rsL3[0]['level'];
                                                            $L3_info['path']                 = $rsL3[0]['path'];
                                                        }    
                                                        if(!$rsL3)
                                                        {                                 
                                                            $level2  = $L2_info['level']+1; 
                                                            $sqll3   = "INSERT INTO category(category_name, parent_category_id, level) VALUES('".addslashes($row['L3'])."', '".$L2_info['category_id']."', '".$level2."')";
                                                            $command = $connection->createCommand($sqll3);
                                                            $command->execute();
                                                            $idl3 = Yii::app()->db->getLastInsertID();    

                                                            $mysqll3 = "Update category set path='".$L2_info['path']."/".$idl3."' where category_id=$idl3";
                                                            $command = $connection->createCommand($mysqll3);
                                                            $command->execute();

                                                            $L3_info['name']               = $row['L2'];
                                                            $L3_info['category_id']        = $idl3;
                                                            $L3_info['parent_category_id'] = $L2_info['category_id'];
                                                            $L3_info['level']              = $level2;
                                                            $L3_info['path']               = $L2_info['path']."/".$idl3;    
                                                            fwrite($handle1, "\nRow :  category_name :".$row['L3']." ID is :".$idl3.".");                                                       
                                                        }

                                                    }
                                                }

                                            }

                                            //.......................................l4..........................//
                                            if(!empty($row['L4']) && $row['L4']!='')
                                            {

                                                if(isset($L4_info['name']) || !isset($L4_info['name']))
                                                {

                                                    if(!isset($L4_info['name']))
                                                    {

                                                        $sqlL4   = "SELECT category_id,parent_category_id,level,path FROM `category` WHERE `category_name` ='".addslashes($row['L4'])."' and parent_category_id='".$L3_info['category_id']."'";        
                                                        $command = $connection->createCommand($sqlL4);
                                                        $command->execute();
                                                        $rsL4=$command->queryAll();

                                                        if($rsL4)
                                                        {
                                                            $L4_info['name']                 = $row['L4'];
                                                            $L4_info['category_id']          = $rsL4[0]['category_id'];
                                                            $L4_info['parent_category_id']   = $rsL4[0]['parent_category_id'];
                                                            $L4_info['level']                = $rsL4[0]['level'];
                                                            $L4_info['path']                 = $rsL4[0]['path'];
                                                        }

                                                        if(!$rsL4)
                                                        {                                 
                                                            $level3  = $L3_info['level']+1; 
                                                            $sqll4   = "INSERT INTO category(category_name, parent_category_id, level) VALUES('".addslashes($row['L4'])."', '".$L3_info['category_id']."', '".$level3."')";
                                                            $command = $connection->createCommand($sqll4);
                                                            $command->execute();
                                                            $idl4 = Yii::app()->db->getLastInsertID();    

                                                            $mysqll4 = "Update category set path='".$L3_info['path']."/".$idl4."' where category_id=$idl4";
                                                            $command = $connection->createCommand($mysqll4);
                                                            $command->execute();

                                                            $L4_info['name']                  =$row['L4'];
                                                            $L4_info['category_id']           =$idl4;
                                                            $L4_info['parent_category_id']    =$L3_info['category_id'];
                                                            $L4_info['level']                 =$level3;
                                                            $L4_info['path']                  =$L3_info['path']."/".$idl4;    
                                                            fwrite($handle1, "\nRow :  category_name :".$row['L4']." ID is :".$idl4.".");                                                       
                                                        }
                                                    }
                                                    if(isset($L4_info['name']))
                                                    { 

                                                        if($L4_info['name']!=$row['L4'])
                                                        {

                                                            $sqlL4   = "SELECT category_id,parent_category_id,level,path FROM `category` WHERE `category_name` ='".addslashes($row['L4'])."' and parent_category_id='".$L3_info['category_id']."'";        
                                                            $command = $connection->createCommand($sqlL4);
                                                            $command->execute();
                                                            $rsL4=$command->queryAll();

                                                            if($rsL4)
                                                            {
                                                                $L4_info['name']                 = $row['L4'];
                                                                $L4_info['category_id']          = $rsL4[0]['category_id'];
                                                                $L4_info['parent_category_id']   = $rsL4[0]['parent_category_id'];
                                                                $L4_info['level']                = $rsL4[0]['level'];
                                                                $L4_info['path']                 = $rsL4[0]['path'];
                                                            }
                                                            if(!$rsL4)
                                                            {                                 
                                                                $level3  = $L3_info['level']+1; 
                                                                $sqll4   = "INSERT INTO category(category_name, parent_category_id, level) VALUES('".addslashes($row['L4'])."', '".$L3_info['category_id']."', '".$level3."')";
                                                                $command = $connection->createCommand($sqll4);
                                                                $command->execute();
                                                                $idl4 = Yii::app()->db->getLastInsertID();    

                                                                $mysqll4 = "Update category set path='".$L3_info['path']."/".$idl4."' where category_id=$idl4";
                                                                $command = $connection->createCommand($mysqll4);
                                                                $command->execute();

                                                                $L4_info['name']                  =$row['L4'];
                                                                $L4_info['category_id']           =$idl4;
                                                                $L4_info['parent_category_id']    =$L3_info['category_id'];
                                                                $L4_info['level']                 =$level3;
                                                                $L4_info['path']                  =$L3_info['path']."/".$idl4;    
                                                                fwrite($handle1, "\nRow :  category_name :".$row['L4']." ID is :".$idl4.".");                                                       
                                                            }

                                                        }
                                                    }

                                                }
                                                //..................................l5..............................//
                                                if(!empty($row['L5']) && $row['L5']!='')
                                                {

                                                    if(isset($L5_info['name']) || !isset($L5_info['name']))
                                                    {

                                                        if(!isset($L5_info['name']))
                                                        {

                                                            $sqlL5   = "SELECT category_id,parent_category_id,level,path FROM `category` WHERE `category_name` ='".addslashes($row['L5'])."' and parent_category_id='".$L4_info['category_id']."'";        
                                                            $command = $connection->createCommand($sqlL5);
                                                            $command->execute();
                                                            $rsL5=$command->queryAll();

                                                            if($rsL5)
                                                            {
                                                                $L5_info['name']                = $row['L5'];
                                                                $L5_info['category_id']         = $rsL5[0]['category_id'];
                                                                $L5_info['parent_category_id']  = $rsL5[0]['parent_category_id'];
                                                                $L5_info['level']               = $rsL5[0]['level'];
                                                                $L5_info['path']                = $rsL5[0]['path'];
                                                            }            

                                                            if(!$rsL5)
                                                            {                                 
                                                                $level4 = $L4_info['level']+1; 
                                                                $sqll5  = "INSERT INTO category(category_name, parent_category_id, level) VALUES('".addslashes($row['L5'])."', '".$L4_info['category_id']."', '".$level4."')";
                                                                $command = $connection->createCommand($sqll5);
                                                                $command->execute();
                                                                $idl5 = Yii::app()->db->getLastInsertID();    

                                                                $mysqll5 = "Update category set path='".$L4_info['path']."/".$idl5."' where category_id=$idl5";
                                                                $command = $connection->createCommand($mysqll5);
                                                                $command->execute();

                                                                $L5_info['name']                  = $row['L5'];
                                                                $L5_info['category_id']           = $idl5;
                                                                $L5_info['parent_category_id']    = $L4_info['category_id'];
                                                                $L5_info['level']                 = $level4;
                                                                $L5_info['path']                  = $L4_info['path']."/".$idl5;    
                                                                fwrite($handle1, "\nRow :  category_name :".$row['L5']." ID is :".$idl5.".");                                                       
                                                            }
                                                        }
                                                        if(isset($L5_info['name']))
                                                        { 

                                                            if($L5_info['name']!=$row['L5'])
                                                            {

                                                                $sqlL5   = "SELECT category_id,parent_category_id,level,path FROM `category` WHERE `category_name` ='".addslashes($row['L5'])."' and parent_category_id='".$L4_info['category_id']."'";        
                                                                $command = $connection->createCommand($sqlL5);
                                                                $command->execute();
                                                                $rsL5=$command->queryAll();

                                                                if($rsL5)
                                                                {
                                                                    $L5_info['name']                = $row['L5'];
                                                                    $L5_info['category_id']         = $rsL5[0]['category_id'];
                                                                    $L5_info['parent_category_id']  = $rsL5[0]['parent_category_id'];
                                                                    $L5_info['level']               = $rsL5[0]['level'];
                                                                    $L5_info['path']                = $rsL5[0]['path'];
                                                                }            

                                                                if(!$rsL5)
                                                                {                                 
                                                                    $level4 = $L4_info['level']+1; 
                                                                    $sqll5  = "INSERT INTO category(category_name, parent_category_id, level) VALUES('".addslashes($row['L5'])."', '".$L4_info['category_id']."', '".$level4."')";
                                                                    $command = $connection->createCommand($sqll5);
                                                                    $command->execute();
                                                                    $idl5 = Yii::app()->db->getLastInsertID();    

                                                                    $mysqll5 = "Update category set path='".$L4_info['path']."/".$idl5."' where category_id=$idl5";
                                                                    $command = $connection->createCommand($mysqll5);
                                                                    $command->execute();

                                                                    $L5_info['name']                  = $row['L5'];
                                                                    $L5_info['category_id']           = $idl5;
                                                                    $L5_info['parent_category_id']    = $L4_info['category_id'];
                                                                    $L5_info['level']                 = $level4;
                                                                    $L5_info['path']                  = $L4_info['path']."/".$idl5;    
                                                                    fwrite($handle1, "\nRow :  category_name :".$row['L5']." ID is :".$idl5.".");                                                       
                                                                }

                                                            }
                                                        }

                                                    }
                                                    //...............................l6....................................//
                                                    if(!empty($row['L6']) && $row['L6']!='')
                                                    {

                                                        if(isset($L6_info['name']) || !isset($L6_info['name']))
                                                        {

                                                            if(!isset($L6_info['name']))
                                                            {

                                                                $sqlL6   = "SELECT category_id,parent_category_id,level,path FROM `category` WHERE `category_name` ='".addslashes($row['L6'])."' and parent_category_id='".$L5_info['category_id']."'";        
                                                                $command = $connection->createCommand($sqlL6);
                                                                $command->execute();
                                                                $rsL6 = $command->queryAll();

                                                                if(!$rsL6)
                                                                {                                 
                                                                    $level5 = $L5_info['level']+1; 
                                                                    $sqll6  = "INSERT INTO category(category_name, parent_category_id, level) VALUES('".addslashes($row['L6'])."', '".$L5_info['category_id']."', '".$level5."')";
                                                                    $command = $connection->createCommand($sqll6);
                                                                    $command->execute();
                                                                    $idl6 = Yii::app()->db->getLastInsertID();    

                                                                    $mysqll6 = "Update category set path='".$L5_info['path']."/".$idl6."' where category_id=$idl6";
                                                                    $command = $connection->createCommand($mysqll6);
                                                                    $command->execute();
                                                                    fwrite($handle1, "\nRow :  category_name :".$row['L6']." ID is :".$idl6.".");                 

                                                                }
                                                            }
                                                            if(isset($L6_info['name']))
                                                            { 

                                                                if($L6_info['name']!=$row['L6'])
                                                                {

                                                                    $sqlL6   = "SELECT category_id,parent_category_id,level,path FROM `category` WHERE `category_name` ='".addslashes($row['L6'])."' and parent_category_id='".$L5_info['category_id']."'";        
                                                                    $command = $connection->createCommand($sqlL6);
                                                                    $command->execute();
                                                                    $rsL6 = $command->queryAll();

                                                                    if(!$rsL6)
                                                                    {                                 
                                                                        $level5 = $L5_info['level']+1; 
                                                                        $sqll6  = "INSERT INTO category(category_name, parent_category_id, level) VALUES('".$row['L6']."', '".addslashes($L5_info['category_id'])."', '".$level5."')";
                                                                        $command = $connection->createCommand($sqll6);
                                                                        $command->execute();
                                                                        $idl6 = Yii::app()->db->getLastInsertID();    

                                                                        $mysqll6 = "Update category set path='".$L5_info['path']."/".$idl6."' where category_id=$idl6";
                                                                        $command = $connection->createCommand($mysqll6);
                                                                        $command->execute();
                                                                        fwrite($handle1, "\nRow :  category_name :".$row['L6']." ID is :".$idl6.".");                 

                                                                    }

                                                                }
                                                            }

                                                        }
                                                    }

                                                    //.....................end..........................................//
                                                }
                                                //............................endl5......................................//
                                            }  
                                            //........................endl4......................................//
                                        }

                                        //................endl3................................................//

                                    }                          
                                    //..................end l2..................................//
                                }
                                else
                                {
                                    fwrite($handle1, "\nRow :  category may be Blank.");  
                                }
                            }
                        }
                    }
                    Yii::app()->user->setFlash('success', 'Upload and Render Successfully !.' );
                }
            }
        }

        @unlink($fileName);
        $this->render('bulkupload', array(
        'model'=>$model,
        'logfile' => $logfile
        ));


    }

    public function actionCreateFileDownload() {
        $file_name = 'Category_create.csv';
        $file_data = 'L1,L2,L3,L4,L5,L6';
        $size_of_file = strlen($file_data);
        $this->renderPartial('fileDownload',array(
        'file_name' => $file_name,
        'file_data' => $file_data,
        'size_of_file' => $size_of_file
        ));
    }
	
	public function actionExport($id){
	
		
	
		$defaultFieldsDW = array(
					"base_product_id","title","small_description","description","brand","brandcode",
					"model_name","model_number","manufacture","manufacture_country","manufacture_year",
					"key_features","status","categoryIds","product_content_type",
					"ISBN","product_shipping_charge","specifications","moq","VAT",
					"varient_id","varient_on","store_id","unit_rate","store_price","store_offer_price","color",
					"weight","height","length","size","qty","warranty"
				);
				
		$queryField = array(
					"base_product_id","title","small_description","description","brand","brand_id",
					"model_name","model_number","manufacture","manufacture_country","manufacture_year",
					"key_features","status","product_content_type",
					"ISBN","product_shipping_charge","specifications","moq","VAT"
				);	

		$varientqueryField = array(
					"subscribed_product_id","variant_on","store_id","unit_rate","store_price","store_offer_price","color",
					"weight","height","length","size","quantity","warranty"
				);					
				
		
		$fileName="Category_id_".$id.".csv"; 
		
		ob_clean();
		header('Pragma: public');
		header('Expires: 0');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Cache-Control: private', false);
		header('Content-Type: text/csv');
		header('Content-Disposition: attachment;filename=' . $fileName);
		
		$output = fopen('php://output', 'w');	
		fputcsv($output, $defaultFieldsDW);
		
		/* csv section starts */
		
		$field_str = implode(',',$queryField);
		$varient_field_str = implode(',',$varientqueryField);
		

		$connection = Yii::app()->db;
		
		$sqlchksubsid = "SELECT bs.$field_str FROM `base_product` bs JOIN product_category_mapping pcm ON bs.base_product_id = pcm.base_product_id where pcm.category_id ='".$id."'";
		
		$command1 = $connection->createCommand($sqlchksubsid);
		$command1->execute();
		
		$assocDataArray = $command1->queryAll();	
	
		if(count($assocDataArray)){
		foreach($assocDataArray as $key => $data_arr){
		
		//print_r($data_arr);
		//die;
		
		$CSV_DATA_ARR = array();
		
			/* brand info starts */
			$bquery = Yii::app()->db->createCommand()
					->select('store_front_id,redirect_url')
					->from('brand')
					->where('store_front_id = :brand_id', array(':brand_id'=>$data_arr['brand_id']))
					->queryRow();
					
			$brandcode = $bquery['redirect_url'];
			/* brand info ends */
		
			/* category info starts */
			$connection = Yii::app()->db;
			
			$cat_query = "SELECT category_id FROM `product_category_mapping` where base_product_id ='".$data_arr['base_product_id']."'";
			
			$command1 = $connection->createCommand($cat_query);
			$command1->execute();
			$categoryArray = $command1->queryAll();
			
			$cat_arr = array();
			
			if(count($categoryArray)){
				foreach($categoryArray as $key => $cat_data_arr){
					$cat_arr[] = $cat_data_arr['category_id'];
				}
				$cat_str = implode(',',$cat_arr);
			}
			
			/* category info ends */
		
		
			/* csv file data 1 starts */
				
				//print_r($data_arr);
			$specificationData = '';
			$keyfeaturedData = '';
			
			if($data_arr['key_features']!=''){
				$keyfeatures = json_decode($data_arr['key_features'],true);
				//$vars = get_object_vars($keyfeatures);			
				foreach ($keyfeatures as $key => $value) {
					$keyfeaturedData .= $key.":".$value.";";
				}
			}
			
			//$keyfeaturedData =$data_arr['key_features'];
			
			//exit;
			if($data_arr['specifications']!='[]' && $data_arr['specifications']!=''){
				$specificationss = json_decode(utf8_decode($data_arr['specifications']));
				//$vars2 = get_object_vars($specificationss);			
				foreach ($specificationss as $key => $value) {
					$specificationData .= $key.":".$value.";";
				}	
			}
			//$specificationData = $data_arr['specifications'];
						
			$CSV_DATA_ARR['base_product_id'] 	= $data_arr['base_product_id'];
			$CSV_DATA_ARR['title'] 				= $data_arr['title'];
			$CSV_DATA_ARR['small_description'] 	= $data_arr['small_description'];
			$CSV_DATA_ARR['description'] 		= $data_arr['description'];
			$CSV_DATA_ARR['brand'] 				= $data_arr['brand'];
			$CSV_DATA_ARR['brandcode'] 			= $brandcode;
			$CSV_DATA_ARR['model_name'] 		= $data_arr['model_name'];
			$CSV_DATA_ARR['model_number'] 		= $data_arr['model_number'];
			$CSV_DATA_ARR['manufacture'] 		= $data_arr['manufacture'];
			$CSV_DATA_ARR['manufacture_country']= $data_arr['manufacture_country'];
			$CSV_DATA_ARR['manufacture_year'] 	= $data_arr['manufacture_year'];
			$CSV_DATA_ARR['key_features'] 		= $keyfeaturedData;//$data_arr['key_features'];
			$CSV_DATA_ARR['status'] 			= $data_arr['status'];
			$CSV_DATA_ARR['categoryIds'] 			 = $cat_str;
			$CSV_DATA_ARR['product_content_type'] 	 = $data_arr['product_content_type'];
			$CSV_DATA_ARR['ISBN'] 					 = $data_arr['ISBN'];
			$CSV_DATA_ARR['product_shipping_charge'] = $data_arr['product_shipping_charge'];
			$CSV_DATA_ARR['specifications'] 		 = $specificationData;//$data_arr['specifications'];
			$CSV_DATA_ARR['moq'] 					 = $data_arr['moq'];
			$CSV_DATA_ARR['VAT'] 					 = $data_arr['VAT'];
			
			//print_r($CSV_DATA_ARR);
			//die;
			
			/* csv file data 1 ends */
		
		
			/* varient info ends */
			$connection = Yii::app()->db;
			
			$vari_query = "SELECT $varient_field_str FROM `subscribed_product` where base_product_id ='".$data_arr['base_product_id']."'";
			
			$command1 = $connection->createCommand($vari_query);
			$command1->execute();
			$varientArray = $command1->queryAll();
			
			//print_r($varientArray);
			
			if(count($varientArray)){
					$j = 1;
				foreach($varientArray as $key => $vari_data_arr){
					
					if($j == 1){
					
						$CSV_DATA_ARR['varient_id'] 		= $vari_data_arr['subscribed_product_id'];
						$CSV_DATA_ARR['variant_on'] 		= $vari_data_arr['variant_on'];
						$CSV_DATA_ARR['store_id'] 			= $vari_data_arr['store_id'];
						$CSV_DATA_ARR['unit_rate'] 			= $vari_data_arr['unit_rate'];
						$CSV_DATA_ARR['store_price'] 		= $vari_data_arr['store_price'];
						$CSV_DATA_ARR['store_offer_price'] 	= $vari_data_arr['store_offer_price'];
						$CSV_DATA_ARR['color'] 				= $vari_data_arr['color'];
						$CSV_DATA_ARR['weight'] 			= $vari_data_arr['weight'];
						$CSV_DATA_ARR['height'] 			= $vari_data_arr['height'];
						$CSV_DATA_ARR['length'] 			= $vari_data_arr['length'];
						$CSV_DATA_ARR['size'] 				= $vari_data_arr['size'];
						$CSV_DATA_ARR['qty'] 				= $vari_data_arr['quantity'];
						$CSV_DATA_ARR['warranty'] 			= $vari_data_arr['warranty'];
					
					}else{
					
					$CSV_DATA_ARR['base_product_id'] 	= "";
					$CSV_DATA_ARR['title'] 				= "";
					$CSV_DATA_ARR['small_description'] 	= "";
					$CSV_DATA_ARR['description'] 		= "";
					$CSV_DATA_ARR['brand'] 				= "";
					$CSV_DATA_ARR['brandcode'] 			= "";
					$CSV_DATA_ARR['model_name'] 		= "";
					$CSV_DATA_ARR['model_number'] 		= "";
					$CSV_DATA_ARR['manufacture'] 		= "";
					$CSV_DATA_ARR['manufacture_country']= "";
					$CSV_DATA_ARR['manufacture_year'] 	= "";
					$CSV_DATA_ARR['key_features'] 		= "";
					$CSV_DATA_ARR['status'] 			= "";
					$CSV_DATA_ARR['categoryIds'] 			 = "";
					$CSV_DATA_ARR['product_content_type'] 	 = "";
					$CSV_DATA_ARR['ISBN'] 					 = "";
					$CSV_DATA_ARR['product_shipping_charge'] = "";
					$CSV_DATA_ARR['specifications'] 		 = "";
					$CSV_DATA_ARR['moq'] 					 = "";
					$CSV_DATA_ARR['VAT'] 					 = "";
					
					$CSV_DATA_ARR['varient_id'] 		= $vari_data_arr['subscribed_product_id'];
					$CSV_DATA_ARR['variant_on'] 		= $vari_data_arr['variant_on'];
					$CSV_DATA_ARR['store_id'] 			= $vari_data_arr['store_id'];
					$CSV_DATA_ARR['unit_rate'] 			= $vari_data_arr['unit_rate'];
					$CSV_DATA_ARR['store_price'] 		= $vari_data_arr['store_price'];
					$CSV_DATA_ARR['store_offer_price'] 	= $vari_data_arr['store_offer_price'];
					$CSV_DATA_ARR['color'] 				= $vari_data_arr['color'];
					$CSV_DATA_ARR['weight'] 			= $vari_data_arr['weight'];
					$CSV_DATA_ARR['height'] 			= $vari_data_arr['height'];
					$CSV_DATA_ARR['length'] 			= $vari_data_arr['length'];
					$CSV_DATA_ARR['size'] 				= $vari_data_arr['size'];
					$CSV_DATA_ARR['qty'] 				= $vari_data_arr['quantity'];
					$CSV_DATA_ARR['warranty'] 			= $vari_data_arr['warranty'];
					
					}
					
					$j++;
					fputcsv($output, $CSV_DATA_ARR);
					//print_r($CSV_DATA_ARR);
				}
			}
			
			/* varient info ends */
		
		
		//die();
		
		//die(" -- in Loop");
		
		}
		}
		
		/* csv section ends */
		
		ob_flush();
	
    }
    
    
    public function actionDataexport($id){
    	
    	$defaultFieldsDW = array(
    			"base_product_id","title","small_description","description","brand","brandcode",
    			"model_name","model_number","manufacture","manufacture_country","manufacture_year",
    			"key_features","status","categoryIds","product_content_type",
    			"ISBN","product_shipping_charge","specifications","moq","VAT",
    			"varient_id","varient_on","store_id","unit_rate","store_price","store_offer_price","color",
    			"weight","height","length","size","qty","warranty"
    	);
    	
    	$queryField = array(
    			"base_product_id","title","small_description","description","brand","brand_id",
    			"model_name","model_number","manufacture","manufacture_country","manufacture_year",
    			"key_features","status","product_content_type",
    			"ISBN","product_shipping_charge","specifications","moq","VAT"
    	);
    	
    	$varientqueryField = array(
    			"subscribed_product_id","variant_on","store_id","unit_rate","store_price","store_offer_price","color",
    			"weight","height","length","size","quantity","warranty"
    	);
    	
    	
    	$fileName="Category_id_".$id.".csv";

    	
    	$connection = Yii::app()->db;
    	
    	ob_clean();
    	header('Pragma: public');
    	header('Expires: 0');
    	header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    	header('Cache-Control: private', false);
    	header('Content-Type: text/csv');
    	header('Content-Disposition: attachment;filename=' . $fileName);
    	 
    	$output = fopen('php://output', 'w');
    	fputcsv($output, $defaultFieldsDW);
    	 
    	$field_str = implode(',',$queryField);
    	$varient_field_str = implode(',',$varientqueryField);
    	$catSql="SELECT * FROM `category` WHERE parent_category_id='$id'";
    	$command = $connection->createCommand($catSql);
    	$catrows = $command->queryAll();
    	foreach ($catrows as $catrow){
    		$catidsfor[] = $catrow['category_id'];
    		$catlevel = $catrow['level'];
    	}
    	$catids = implode(",", $catidsfor);
    	if($catids !=''){
    		$sqlchksubsid = "SELECT bs.$field_str FROM `base_product` bs JOIN product_category_mapping pcm ON bs.base_product_id = pcm.base_product_id where pcm.category_id IN ($catids)";
    		$command1 = $connection->createCommand($sqlchksubsid);
    		$command1->execute();
    		
    		$assocDataArray = $command1->queryAll();
    		
    	}
    		
    		if(count($assocDataArray)){
    			foreach($assocDataArray as $key => $data_arr){
    		
    				$CSV_DATA_ARR = array();
    				 
    				$bquery = Yii::app()->db->createCommand()
    				->select('store_front_id,redirect_url')
    				->from('brand')
    				->where('store_front_id = :brand_id', array(':brand_id'=>$data_arr['brand_id']))
    				->queryRow();
    					
    				$brandcode = $bquery['redirect_url'];
    		
    				$connection = Yii::app()->db;
    				$cat_query = "SELECT category_id FROM `product_category_mapping` where base_product_id ='".$data_arr['base_product_id']."'";
    				$command1 = $connection->createCommand($cat_query);
    				$command1->execute();
    				$categoryArray = $command1->queryAll();
    					
    				$cat_arr = array();
    					
    				if(count($categoryArray)){
    					foreach($categoryArray as $key => $cat_data_arr){
    						$cat_arr[] = $cat_data_arr['category_id'];
    					}
    					$cat_str = implode(',',$cat_arr);
    				}
    					
    				$specificationData = '';
    				$keyfeaturedData = '';
    					
    				if($data_arr['key_features']!=''){
    					$keyfeatures = json_decode($data_arr['key_features'],true);
    					foreach ($keyfeatures as $key => $value) {
    						$keyfeaturedData .= $key.":".$value.";";
    					}
    				}
    					
    				if($data_arr['specifications']!='[]' && $data_arr['specifications']!=''){
    					$specificationss = json_decode(utf8_decode($data_arr['specifications']));
    					foreach ($specificationss as $key => $value) {
    						$specificationData .= $key.":".$value.";";
    					}
    				}
    		
    				$CSV_DATA_ARR['base_product_id'] 	= $data_arr['base_product_id'];
    				$CSV_DATA_ARR['title'] 				= $data_arr['title'];
    				$CSV_DATA_ARR['small_description'] 	= $data_arr['small_description'];
    				$CSV_DATA_ARR['description'] 		= $data_arr['description'];
    				$CSV_DATA_ARR['brand'] 				= $data_arr['brand'];
    				$CSV_DATA_ARR['brandcode'] 			= $brandcode;
    				$CSV_DATA_ARR['model_name'] 		= $data_arr['model_name'];
    				$CSV_DATA_ARR['model_number'] 		= $data_arr['model_number'];
    				$CSV_DATA_ARR['manufacture'] 		= $data_arr['manufacture'];
    				$CSV_DATA_ARR['manufacture_country']= $data_arr['manufacture_country'];
    				$CSV_DATA_ARR['manufacture_year'] 	= $data_arr['manufacture_year'];
    				$CSV_DATA_ARR['key_features'] 		= $keyfeaturedData;//$data_arr['key_features'];
    				$CSV_DATA_ARR['status'] 			= $data_arr['status'];
    				$CSV_DATA_ARR['categoryIds'] 			 = $cat_str;
    				$CSV_DATA_ARR['product_content_type'] 	 = $data_arr['product_content_type'];
    				$CSV_DATA_ARR['ISBN'] 					 = $data_arr['ISBN'];
    				$CSV_DATA_ARR['product_shipping_charge'] = $data_arr['product_shipping_charge'];
    				$CSV_DATA_ARR['specifications'] 		 = $specificationData;//$data_arr['specifications'];
    				$CSV_DATA_ARR['moq'] 					 = $data_arr['moq'];
    				$CSV_DATA_ARR['VAT'] 					 = $data_arr['VAT'];
    					
    				$connection = Yii::app()->db;
    					
    				$vari_query = "SELECT $varient_field_str FROM `subscribed_product` where base_product_id ='".$data_arr['base_product_id']."'";
    					
    				$command1 = $connection->createCommand($vari_query);
    				$command1->execute();
    				$varientArray = $command1->queryAll();
    					
    				if(count($varientArray)){
    					$j = 1;
    					foreach($varientArray as $key => $vari_data_arr){
    							
    						if($j == 1){
    		
    							$CSV_DATA_ARR['varient_id'] 		= $vari_data_arr['subscribed_product_id'];
    							$CSV_DATA_ARR['variant_on'] 		= $vari_data_arr['variant_on'];
    							$CSV_DATA_ARR['store_id'] 			= $vari_data_arr['store_id'];
    							$CSV_DATA_ARR['unit_rate'] 			= $vari_data_arr['unit_rate'];
    							$CSV_DATA_ARR['store_price'] 		= $vari_data_arr['store_price'];
    							$CSV_DATA_ARR['store_offer_price'] 	= $vari_data_arr['store_offer_price'];
    							$CSV_DATA_ARR['color'] 				= $vari_data_arr['color'];
    							$CSV_DATA_ARR['weight'] 			= $vari_data_arr['weight'];
    							$CSV_DATA_ARR['height'] 			= $vari_data_arr['height'];
    							$CSV_DATA_ARR['length'] 			= $vari_data_arr['length'];
    							$CSV_DATA_ARR['size'] 				= $vari_data_arr['size'];
    							$CSV_DATA_ARR['qty'] 				= $vari_data_arr['quantity'];
    							$CSV_DATA_ARR['warranty'] 			= $vari_data_arr['warranty'];
    		
    						}else{
    		
    							$CSV_DATA_ARR['base_product_id'] 	= "";
    							$CSV_DATA_ARR['title'] 				= "";
    							$CSV_DATA_ARR['small_description'] 	= "";
    							$CSV_DATA_ARR['description'] 		= "";
    							$CSV_DATA_ARR['brand'] 				= "";
    							$CSV_DATA_ARR['brandcode'] 			= "";
    							$CSV_DATA_ARR['model_name'] 		= "";
    							$CSV_DATA_ARR['model_number'] 		= "";
    							$CSV_DATA_ARR['manufacture'] 		= "";
    							$CSV_DATA_ARR['manufacture_country']= "";
    							$CSV_DATA_ARR['manufacture_year'] 	= "";
    							$CSV_DATA_ARR['key_features'] 		= "";
    							$CSV_DATA_ARR['status'] 			= "";
    							$CSV_DATA_ARR['categoryIds'] 			 = "";
    							$CSV_DATA_ARR['product_content_type'] 	 = "";
    							$CSV_DATA_ARR['ISBN'] 					 = "";
    							$CSV_DATA_ARR['product_shipping_charge'] = "";
    							$CSV_DATA_ARR['specifications'] 		 = "";
    							$CSV_DATA_ARR['moq'] 					 = "";
    							$CSV_DATA_ARR['VAT'] 					 = "";
    		
    							$CSV_DATA_ARR['varient_id'] 		= $vari_data_arr['subscribed_product_id'];
    							$CSV_DATA_ARR['variant_on'] 		= $vari_data_arr['variant_on'];
    							$CSV_DATA_ARR['store_id'] 			= $vari_data_arr['store_id'];
    							$CSV_DATA_ARR['unit_rate'] 			= $vari_data_arr['unit_rate'];
    							$CSV_DATA_ARR['store_price'] 		= $vari_data_arr['store_price'];
    							$CSV_DATA_ARR['store_offer_price'] 	= $vari_data_arr['store_offer_price'];
    							$CSV_DATA_ARR['color'] 				= $vari_data_arr['color'];
    							$CSV_DATA_ARR['weight'] 			= $vari_data_arr['weight'];
    							$CSV_DATA_ARR['height'] 			= $vari_data_arr['height'];
    							$CSV_DATA_ARR['length'] 			= $vari_data_arr['length'];
    							$CSV_DATA_ARR['size'] 				= $vari_data_arr['size'];
    							$CSV_DATA_ARR['qty'] 				= $vari_data_arr['quantity'];
    							$CSV_DATA_ARR['warranty'] 			= $vari_data_arr['warranty'];
    		
    						}
    							
    						$j++;
    						fputcsv($output, $CSV_DATA_ARR);
    					}
    				}
    		
    			}
    		}
    		ob_flush();
    		
    	
    		
    }
    
    public function actionDataexportbig($id){
    	
    	$defaultFieldsDW = array(
    			"base_product_id","title","small_description","description","brand","brandcode",
    			"model_name","model_number","manufacture","manufacture_country","manufacture_year",
    			"key_features","status","categoryIds","product_content_type",
    			"ISBN","product_shipping_charge","specifications","moq","VAT",
    			"varient_id","varient_on","store_id","unit_rate","store_price","store_offer_price","color",
    			"weight","height","length","size","qty","warranty"
    	);
    	 
    	$queryField = array(
    			"base_product_id","title","small_description","description","brand","brand_id",
    			"model_name","model_number","manufacture","manufacture_country","manufacture_year",
    			"key_features","status","product_content_type",
    			"ISBN","product_shipping_charge","specifications","moq","VAT"
    	);
    	 
    	$varientqueryField = array(
    			"subscribed_product_id","variant_on","store_id","unit_rate","store_price","store_offer_price","color",
    			"weight","height","length","size","quantity","warranty"
    	);
    	 
    	 
    	$fileName="Category_id_".$id.".csv";
    	
    	
    	ob_clean();
    	header('Pragma: public');
    	header('Expires: 0');
    	header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    	header('Cache-Control: private', false);
    	header('Content-Type: text/csv');
    	header('Content-Disposition: attachment;filename=' . $fileName);
    	
    	$output = fopen('php://output', 'w');
    	fputcsv($output, $defaultFieldsDW);
    	
    	
    	$field_str = implode(',',$queryField);
    	$varient_field_str = implode(',',$varientqueryField);
    	
    	
    	$connection = Yii::app()->db;
    	$catSql="SELECT * FROM `category` WHERE parent_category_id='$id'";
    	$command = $connection->createCommand($catSql);
    	$catrows = $command->queryAll();
    	foreach ($catrows as $catrow){
    		$catidsfor[] = $catrow['category_id'];
    		$catlevel = $catrow['level'];
    	}
    	$catids = implode(",", $catidsfor);
    		if($catids !=''){
    			foreach ($catidsfor as $key => $value) {
    				$catSql1="SELECT * FROM `category` WHERE parent_category_id='$value'";
    				$command = $connection->createCommand($catSql1);
    				$catrows1 = $command->queryAll();
    				foreach ($catrows1 as $catrow1){
    					$catidsfor1[]=$catrow1['category_id'];
    				}
	    				$catids1 = implode(",", $catidsfor1);
	    				$sqlchksubsid = "SELECT bs.$field_str FROM `base_product` bs JOIN product_category_mapping pcm ON bs.base_product_id = pcm.base_product_id where pcm.category_id IN ($catids1)";
	    				$command1 = $connection->createCommand($sqlchksubsid);
	    				$command1->execute();
	    				$assocDataArray = $command1->queryAll();
    				}
    		}
    				if(count($assocDataArray)){
    					foreach($assocDataArray as $key => $data_arr){
    				
    						$CSV_DATA_ARR = array();
    							
    						$bquery = Yii::app()->db->createCommand()
    						->select('store_front_id,redirect_url')
    						->from('brand')
    						->where('store_front_id = :brand_id', array(':brand_id'=>$data_arr['brand_id']))
    						->queryRow();
    							
    						$brandcode = $bquery['redirect_url'];
    				
    						$connection = Yii::app()->db;
    						$cat_query = "SELECT category_id FROM `product_category_mapping` where base_product_id ='".$data_arr['base_product_id']."'";
    						$command1 = $connection->createCommand($cat_query);
    						$command1->execute();
    						$categoryArray = $command1->queryAll();
    							
    						$cat_arr = array();
    							
    						if(count($categoryArray)){
    							foreach($categoryArray as $key => $cat_data_arr){
    								$cat_arr[] = $cat_data_arr['category_id'];
    							}
    							$cat_str = implode(',',$cat_arr);
    						}
    							
    						$specificationData = '';
    						$keyfeaturedData = '';
    							
    						if($data_arr['key_features']!=''){
    							$keyfeatures = json_decode($data_arr['key_features'],true);
    							foreach ($keyfeatures as $key => $value) {
    								$keyfeaturedData .= $key.":".$value.";";
    							}
    						}
    							
    						if($data_arr['specifications']!='[]' && $data_arr['specifications']!=''){
    							$specificationss = json_decode(utf8_decode($data_arr['specifications']));
    							foreach ($specificationss as $key => $value) {
    								$specificationData .= $key.":".$value.";";
    							}
    						}
    				
    						$CSV_DATA_ARR['base_product_id'] 	= $data_arr['base_product_id'];
    						$CSV_DATA_ARR['title'] 				= $data_arr['title'];
    						$CSV_DATA_ARR['small_description'] 	= $data_arr['small_description'];
    						$CSV_DATA_ARR['description'] 		= $data_arr['description'];
    						$CSV_DATA_ARR['brand'] 				= $data_arr['brand'];
    						$CSV_DATA_ARR['brandcode'] 			= $brandcode;
    						$CSV_DATA_ARR['model_name'] 		= $data_arr['model_name'];
    						$CSV_DATA_ARR['model_number'] 		= $data_arr['model_number'];
    						$CSV_DATA_ARR['manufacture'] 		= $data_arr['manufacture'];
    						$CSV_DATA_ARR['manufacture_country']= $data_arr['manufacture_country'];
    						$CSV_DATA_ARR['manufacture_year'] 	= $data_arr['manufacture_year'];
    						$CSV_DATA_ARR['key_features'] 		= $keyfeaturedData;//$data_arr['key_features'];
    						$CSV_DATA_ARR['status'] 			= $data_arr['status'];
    						$CSV_DATA_ARR['categoryIds'] 			 = $cat_str;
    						$CSV_DATA_ARR['product_content_type'] 	 = $data_arr['product_content_type'];
    						$CSV_DATA_ARR['ISBN'] 					 = $data_arr['ISBN'];
    						$CSV_DATA_ARR['product_shipping_charge'] = $data_arr['product_shipping_charge'];
    						$CSV_DATA_ARR['specifications'] 		 = $specificationData;//$data_arr['specifications'];
    						$CSV_DATA_ARR['moq'] 					 = $data_arr['moq'];
    						$CSV_DATA_ARR['VAT'] 					 = $data_arr['VAT'];
    							
    						$connection = Yii::app()->db;
    							
    						$vari_query = "SELECT $varient_field_str FROM `subscribed_product` where base_product_id ='".$data_arr['base_product_id']."'";
    							
    						$command1 = $connection->createCommand($vari_query);
    						$command1->execute();
    						$varientArray = $command1->queryAll();
    							
    						if(count($varientArray)){
    							$j = 1;
    							foreach($varientArray as $key => $vari_data_arr){
    									
    								if($j == 1){
    				
    									$CSV_DATA_ARR['varient_id'] 		= $vari_data_arr['subscribed_product_id'];
    									$CSV_DATA_ARR['variant_on'] 		= $vari_data_arr['variant_on'];
    									$CSV_DATA_ARR['store_id'] 			= $vari_data_arr['store_id'];
    									$CSV_DATA_ARR['unit_rate'] 			= $vari_data_arr['unit_rate'];
    									$CSV_DATA_ARR['store_price'] 		= $vari_data_arr['store_price'];
    									$CSV_DATA_ARR['store_offer_price'] 	= $vari_data_arr['store_offer_price'];
    									$CSV_DATA_ARR['color'] 				= $vari_data_arr['color'];
    									$CSV_DATA_ARR['weight'] 			= $vari_data_arr['weight'];
    									$CSV_DATA_ARR['height'] 			= $vari_data_arr['height'];
    									$CSV_DATA_ARR['length'] 			= $vari_data_arr['length'];
    									$CSV_DATA_ARR['size'] 				= $vari_data_arr['size'];
    									$CSV_DATA_ARR['qty'] 				= $vari_data_arr['quantity'];
    									$CSV_DATA_ARR['warranty'] 			= $vari_data_arr['warranty'];
    				
    								}else{
    				
    									$CSV_DATA_ARR['base_product_id'] 	= "";
    									$CSV_DATA_ARR['title'] 				= "";
    									$CSV_DATA_ARR['small_description'] 	= "";
    									$CSV_DATA_ARR['description'] 		= "";
    									$CSV_DATA_ARR['brand'] 				= "";
    									$CSV_DATA_ARR['brandcode'] 			= "";
    									$CSV_DATA_ARR['model_name'] 		= "";
    									$CSV_DATA_ARR['model_number'] 		= "";
    									$CSV_DATA_ARR['manufacture'] 		= "";
    									$CSV_DATA_ARR['manufacture_country']= "";
    									$CSV_DATA_ARR['manufacture_year'] 	= "";
    									$CSV_DATA_ARR['key_features'] 		= "";
    									$CSV_DATA_ARR['status'] 			= "";
    									$CSV_DATA_ARR['categoryIds'] 			 = "";
    									$CSV_DATA_ARR['product_content_type'] 	 = "";
    									$CSV_DATA_ARR['ISBN'] 					 = "";
    									$CSV_DATA_ARR['product_shipping_charge'] = "";
    									$CSV_DATA_ARR['specifications'] 		 = "";
    									$CSV_DATA_ARR['moq'] 					 = "";
    									$CSV_DATA_ARR['VAT'] 					 = "";
    				
    									$CSV_DATA_ARR['varient_id'] 		= $vari_data_arr['subscribed_product_id'];
    									$CSV_DATA_ARR['variant_on'] 		= $vari_data_arr['variant_on'];
    									$CSV_DATA_ARR['store_id'] 			= $vari_data_arr['store_id'];
    									$CSV_DATA_ARR['unit_rate'] 			= $vari_data_arr['unit_rate'];
    									$CSV_DATA_ARR['store_price'] 		= $vari_data_arr['store_price'];
    									$CSV_DATA_ARR['store_offer_price'] 	= $vari_data_arr['store_offer_price'];
    									$CSV_DATA_ARR['color'] 				= $vari_data_arr['color'];
    									$CSV_DATA_ARR['weight'] 			= $vari_data_arr['weight'];
    									$CSV_DATA_ARR['height'] 			= $vari_data_arr['height'];
    									$CSV_DATA_ARR['length'] 			= $vari_data_arr['length'];
    									$CSV_DATA_ARR['size'] 				= $vari_data_arr['size'];
    									$CSV_DATA_ARR['qty'] 				= $vari_data_arr['quantity'];
    									$CSV_DATA_ARR['warranty'] 			= $vari_data_arr['warranty'];
    				
    								}
    									
    								$j++;
    								fputcsv($output, $CSV_DATA_ARR);
    							}
    						}
    				
    					}
    				}
    				ob_flush();

    }	

}
