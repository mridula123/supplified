<?php

class BrandController extends Controller {

    public $status;

    /**
    * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
    * using two-column layout. See 'protected/views/layouts/column2.php'.
    */
   // public $layout = '//layouts/column2';

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
       /* array('allow', // allow all users to perform 'index' and 'view' actions
        	'actions'=>array(''),
			'users'=>array('*'),
        ),
        array('allow', // allow authenticated user to perform 'create' and 'update' actions
        'actions' => array('create', 'update','bulkupload','admin','CreateFileDownload','UpdateFileDownload','export'),
        'users' => array('@'),
        ),
        array('allow', // allow admin user to perform 'admin' and 'delete' actions
        'actions' => array('admin', 'delete','bulkupload','CreateFileDownload','UpdateFileDownload','export'),
        'users' => array('admin'),
        ),*/
        array('allow', // allow admin user to perform 'admin' and 'delete' actions
            'actions' =>$this->getActionList(),
            'users' => array('@'),

            ),
        array('deny', // deny all users
        'users' => array('*'),
        'deniedCallback' => function() { Yii::app()->controller->redirect(array ('/access/denied')); }
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
		
		
        if (isset($_POST['Brand'])&& isset($id) ) { 
		
            $model->attributes = $_POST['Brand'];	
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


        $model = new Brand('search'); 

        $model->unsetAttributes();  // clear any default values
        if (isset($_GET['StoreFront']))
            $model->attributes = $_GET['StoreFront'];

        $this->render('admin', array(
        'model' => $model,

        ));  

    }
	
	
	public function actionCreate() {
        
		$model = new Brand(); 
       
	    if (isset($_POST['Brand'])) {

		//print_r($_POST);
		//die("Brand Posted");
		
		        $chk = $model->ChkStoreFront($_POST['Brand']);
				
				if($chk)
				{
				   //$apikey= substr($_POST['Brand']['store_front_name'],0,2);
				   /*
				   if(!empty($_POST['Brand']['store_front_api_password']) && $_POST['StoreFront']['store_front_api_password']!=null && $_POST['StoreFront']['store_front_api_password']!='')
				   {
					$password= md5($_POST['StoreFront']['store_front_api_password']);
					$_POST['StoreFront']['store_front_api_password']= $password;
			       }
				   */
				   
					  $model->attributes = $_POST['Brand'];			
					  if($model->save())
					  {
					  //$model->UpdateStoreFront($apikey);
					  Yii::app()->user->setFlash('success','Brand Created Successfully');
					  $this->redirect(array('admin'));
                      }					   
				
				}
				else
				{
				Yii::app()->user->setFlash('success','Brand already Created !');
				}
			
				
			
        }

        $this->render('create', array(
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
        $model = Brand::model()->findByPk($id);
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
    
	public function actionBulkUpload() {

        $model = new FrontCSV;
        //$file = CUploadedFile::getInstance($model,'csv_file');
		$logfile='';

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

                // $fileName = $model->csv_file;


                $ext = pathinfo($fileName, PATHINFO_EXTENSION);
                if ($ext != 'csv') {
                     Yii::app()->user->setFlash('error', 'Only .csv files allowed.');
					$this->render('bulkupload', array('model' => $model));
                }

                $i = 0;
                $requiredFields = array('subscribed_product_id' , 'store_front_id');
                $defaultFields = array('subscribed_product_id','store_front_id','is_deleted');

                if(($handle = fopen("$fileName", "r")) !== FALSE) {
                    $logDir = "log/";
                    $logfile = 'bulk_upload_log_' . Yii::app()->session['sessionId'] . '_' . time() . '.txt';
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
								  
								 
                                 if(isset($cols['subscribed_product_id']))
                                    $row['subscribed_product_id'] = trim($data[$cols['subscribed_product_id']]);
									else
									$row['subscribed_product_id']='';									
								if(isset($cols['store_front_id']))
                                    $row['store_front_id'] = trim($data[$cols['store_front_id']]);
									else
									$row['store_front_id']='';
								if(isset($cols['is_deleted']))
                                     $row['is_deleted'] = trim($data[$cols['is_deleted']]);
							    else
								    $row['is_deleted']='';
									
						   			
										
								if(!empty($row['store_front_id']) && !empty($row['subscribed_product_id'])  && $row['is_deleted']!='')
								  {										
                                $connection = Yii::app()->db;
                                $sqlchk = "SELECT `store_front_id` FROM `brand` WHERE `store_front_id`='".$row['store_front_id']."'";
								
                                $command = $connection->createCommand($sqlchk);
                                $command->execute();
                                $rs=$command->queryAll();	



                              $sqlchksubsid = "SELECT `subscribed_product_id` FROM `subscribed_product` WHERE `subscribed_product_id`='".$row['subscribed_product_id']."'";								
                                $command1 = $connection->createCommand($sqlchksubsid);
                                $command1->execute();
                                $rssubs=$command1->queryAll();
								
								
                                if(isset($rs[0]['store_front_id']) && isset($rssubs[0]['subscribed_product_id']))
                                {					   
											   

                                   //.............storeFront..................//
								   
								    $sqlDeletesf = "Delete From `product_frontend_mapping` Where subscribed_product_id='".$row['subscribed_product_id']."' And store_front_id='".$row['store_front_id']."'";
                                    $command = $connection->createCommand($sqlDeletesf);
                                    $command->execute();
								   
								    if($row['is_deleted']==0)
									{
										$sqlInsertsf = "INSERT INTO `product_frontend_mapping`(`subscribed_product_id`,`store_front_id`) VALUES ('".$row['subscribed_product_id']."','".$row['store_front_id']."')";
										$command = $connection->createCommand($sqlInsertsf);
										$command->execute();
									}
									//..........................end.........................//
									
									//...............solor backlog.................//
									
									$sqlDeletesbl = "Delete From `solr_back_log` Where subscribed_product_id='".$row['subscribed_product_id']."'";
                                    $command = $connection->createCommand($sqlDeletesbl);
                                    $command->execute();
									
									 $sqlInsertsbl = "INSERT INTO `solr_back_log`(`subscribed_product_id`) VALUES ('".$row['subscribed_product_id']."')";
                                     $command = $connection->createCommand($sqlInsertsbl);
                                     $command->execute();                                     								   
								//..........................end...........................//
								 if($row['is_deleted']==0)
									{
                                    fwrite($handle1, "\nRow :  Store Front id is '".$row['store_front_id']."' mapping with subscribed_product_id '".$row['subscribed_product_id']."' ");  
									}
									else
									{
									fwrite($handle1, "\nRow :  Store Front id is '".$row['store_front_id']."' Not mapping with subscribed_product_id '".$row['subscribed_product_id']."' "); 
									}
                                }
                                else
                                {
								fwrite($handle1, "\nRow :  Store Front ID ('".$row['store_front_id']."')  Or  Subscribed_product_id ('".$row['subscribed_product_id']."') Not Found.");   
                                                                  
                                }
								}
								 else
								  {
								    fwrite($handle1, "\nRow :  Store Front ID Or Subscribed_product_id May be Blank.");  
								  }
								
								}
                        }
                    }
					Yii::app()->user->setFlash('success', 'Upload Successfully !.' );
                }

            }
        }

        @unlink($fileName);
        $this->render('bulkupload', array(
        'model'=>$model,
        'logfile' => $logfile
        ));


    }
	
	public function actionExport($id){	
	$connection = Yii::app()->db;
	$sqlchksubsid = "SELECT `subscribed_product_id`,store_front_id FROM `product_frontend_mapping` where store_front_id='".$id."'";   
	$command1 = $connection->createCommand($sqlchksubsid);
    $command1->execute();
    $assocDataArray=$command1->queryAll();	
	$fileName="Store_frontend_mapping_id_".$id.".csv"; 
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
	
	
	
    public function actionUpdateFileDownload() {
        $file_name = 'StoreFront_update.csv';
        $file_data = 'subscribed_product_id,store_front_id,is_deleted';
        $size_of_file = strlen($file_data);
        $this->renderPartial('fileDownload',array(
        'file_name' => $file_name,
        'file_data' => $file_data,
        'size_of_file' => $size_of_file
        ));
    }
}
