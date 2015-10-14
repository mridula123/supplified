<?php

class SubscribedProductController extends Controller {

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
                'actions' => array('create', 'update', '_forms', 'csvrailcars','CreateFileDownload','UpdateFileDownload'),
                'users' => array('@'),
            ),
            array('allow', // allow admin user to perform 'admin' and 'delete' actions
                'actions' => array('admin', 'delete', 'bulkupload','CreateFileDownload','UpdateFileDownload'),
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
        $base_product_id = $_REQUEST['base_product_id'];
		$store_id=$_REQUEST['store_id'];
		$models = new BaseSubscibedProduct();
        $record = $models->getRecordById($base_product_id);
        $model = new SubscribedProduct();
        if (isset($_POST['SubscribedProduct'])) {
            $model->attributes = $_POST['SubscribedProduct'];
            if ($model->save())
			{
			//.......................solor backloag.................//
                         $solrBackLog = new SolrBackLog();
                         //$is_deleted =  ($model->status == 1) ? 0 : 1;
						 $is_deleted = '0';
                         $solrBackLog->insertBySubscribedProductId($model->subscribed_product_id,$is_deleted); 
					   //.........................end.....................................//
			
                $this->redirect(array('update', 'id' => $model->subscribed_product_id));
				}
        }
		
        $this->render('create', array(
            'model' => $model,
            'record' => $record,
			'store_id' => $store_id,
        ));
    }


    /**
     * Updates a particular model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id the ID of the model to be updated
     */
    public function actionUpdate($id) {
        $model = $this->loadModel($id);

        
        if (isset($_POST['SubscribedProduct'])) {
            $model->attributes = $_POST['SubscribedProduct'];
            if ($model->save())
			{
			    //.......................solor backloag.................//
                         $solrBackLog = new SolrBackLog();
                         //$is_deleted =  ($model->status == 1) ? 0 : 1;
						 $is_deleted = '0';
                         $solrBackLog->insertBySubscribedProductId($model->subscribed_product_id,$is_deleted); 
					   //.........................end.....................................//
                $this->redirect(array('admin', 'id' => $model->subscribed_product_id));
				}
        }

		$models1 = new BaseSubscibedProduct();
        $record = $models1->getRecordById($model->base_product_id);
		$this->render('update', array(
            'model' => $model,
            'record' => $record,
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
        $dataProvider = new CActiveDataProvider('SubscribedProduct');
        $this->render('index', array(
            'dataProvider' => $dataProvider,
        ));
       
    }

    /**
     * Manages all models.
     */
    public function actionAdmin($store_id = null) {
        $model = new SubscribedProduct('search');
        $model->unsetAttributes();  // clear any default values
        if (isset($_GET['SubscribedProduct']))
            $model->attributes = $_GET['SubscribedProduct'];
			
	    $model->setAttribute('store_id',$store_id);
        $model->setAttribute('is_deleted','=0');	

        $this->render('admin', array(
            'model' => $model,
			'store_id'=>$store_id,
        ));
    }

   public function actionBulkUpload() {
        set_time_limit(0); 
       $logfile='';
        $model = new Csv;
		
        if (isset($_POST['Csv'])) {
		 $model->action = $_POST['Csv']['action'];
            $model->attributes = $_POST['Csv'];
            if (!empty($_FILES['Csv']['tmp_name']['csv_file'])) {
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
                $requiredFields = array('base_product_id','store_price','store_offer_price','status','quantity','is_cod');
                $defaultFields = array('base_product_id','store_price','store_offer_price','status','quantity','is_cod','store_id','sku','subscribe_shipping_charge');
				
				
				if ($model->action == 'update') {
                    unset($requiredFields[array_search('base_product_id', $requiredFields)]);
                    unset($defaultFields[array_search('base_product_id', $defaultFields)]);
                    $requiredFields = array('subscribed_product_id');
                    $defaultFields[] = 'subscribed_product_id';
                }
				
				
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
                                  $this->render('bulkupload', array('model' => $model));
                                    // unlink($fileName);
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
                            {       if(isset($cols['base_product_id']))
                                    $row['base_product_id'] = trim($data[$cols['base_product_id']]);
									
									 if(isset($cols['store_id']))
                                    $row['store_id'] = trim($data[$cols['store_id']]);
									 if(isset($cols['store_price']))
                                    $row['store_price'] = trim($data[$cols['store_price']]);
									if(isset($cols['store_offer_price']))
                                    $row['store_offer_price'] = trim($data[$cols['store_offer_price']]);
									
									if(isset($cols['status']))
                                    $row['status'] = trim($data[$cols['status']]);
									if(isset($cols['checkout_url']))
                                    $row['checkout_url'] = trim($data[$cols['checkout_url']]);
									if(isset($cols['sku']))
                                    $row['sku'] = trim($data[$cols['sku']]);
									if(isset($cols['quantity']))
                                    $row['quantity'] = trim($data[$cols['quantity']]);
									if(isset($cols['is_cod']))
                                    $row['is_cod'] = trim($data[$cols['is_cod']]);
									if(isset($cols['subscribe_shipping_charge']))
                                     $row['subscribe_shipping_charge'] = trim($data[$cols['subscribe_shipping_charge']]);
									 
									 
								
									 
									 if(isset($cols['subscribed_product_id'])) {
                                    try {
                                        if (trim($data[$cols['subscribed_product_id']]) == null) {
                                            fwrite($handle1, "\nRow : ". $i . " Subscribed product id can not be empty.");
                                            continue;
                                        }
                                        $model1=$this->loadModel(trim($data[$cols['subscribed_product_id']]));
                                    } catch (Exception $e) {
                                        fwrite($handle1, "\nRow : ". $i . " Subscribed product {$data[$cols['subscribed_product_id']]} is invalid.");
                                        continue;
                                    }
                                } else {
                                    $model1=new SubscribedProduct();
                                }
									 
									
									  $model1->attributes = $row;

									  $action = $model->action == 'update' ? 'updated' : 'created';
                                      
                                      
                                      
                                   $subs_id='';
                                   if(Isset($cols['subscribed_product_id'])) 
                                          $subs_id=$data[$cols['subscribed_product_id']];
                                    else
                                      $subs_id=$model1->subscribed_product_id;    
							
								if(isset($subs_id) && !empty($subs_id))
								{
								
								 //.......................solor backloag.................//
								 $solrBackLog = new SolrBackLog();
								 //$is_deleted =  ($model->status == 1) ? 0 : 1;
								 $is_deleted = '0';
								 $solrBackLog->insertBySubscribedProductId($subs_id,$is_deleted); 
								
								  //.........................end.....................................//
								}
								  								  
									  
									  
									  if (!$model1->save(true)) {
                                    foreach($model1->getErrors() as $errors) {
                                        $error[] = implode(' AND ', $errors);
                                    }
                                    fwrite($handle1, "\nRow : ". $i . " Subscribed product not $action. ". implode(' AND ', $error));
                                } else {
                                    $error = array();
                                    foreach($model1->getErrors() as $errors) {
                                        $error[] = implode(' AND ', $errors);
                                    }
                                    fwrite($handle1, "\nRow : ". $i  . " Subscribed product $model1->subscribed_product_id $action. ". implode(' AND ', $error));
                                }
								Yii::app()->user->setFlash('success', 'Upload Successfully !.' );	 
                                

                            }
                        }
                    }
					
					
                }

            }


        }

       // @unlink($fileName);
       $this->render('bulkupload', array(
        'model'=>$model,
        'logfile' => $logfile
        ));


    }

    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     * @param integer $id the ID of the model to be loaded
     * @return SubscribedProduct the loaded model
     * @throws CHttpException
     */
    public function loadModel($id) {
        $model = SubscribedProduct::model()->findByPk($id);
        $record = BaseSubscibedProduct::model()->findByPk($id);
        if ($model === null)
            throw new CHttpException(404, 'The requested page does not exist.');
        return $model;
        return $record;
    }

    /**
     * Performs the AJAX validation.
     * @param SubscribedProduct $model the model to be validated
     */
    protected function performAjaxValidation($model) {
        if (isset($_POST['ajax']) && $_POST['ajax'] === 'subscribed-product-form') {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
    }
	public function actionCreateFileDownload() {
        $file_name = 'bulk_upload_subscribedproduct_create.csv';
        $file_data = 'base_product_id,store_id,store_price,store_offer_price,status,quantity,is_cod,sku,subscribe_shipping_charge';
        $size_of_file = strlen($file_data);
        $this->renderPartial('fileDownload',array(
        'file_name' => $file_name,
        'file_data' => $file_data,
        'size_of_file' => $size_of_file
        ));
    }

    public function actionUpdateFileDownload() {
        $file_name = 'bulk_upload_subscribedproduct_update.csv';
        $file_data = 'subscribed_product_id,store_id,store_price,store_offer_price,status,quantity,is_cod,sku,subscribe_shipping_charge';
        $size_of_file = strlen($file_data);
        $this->renderPartial('fileDownload',array(
        'file_name' => $file_name,
        'file_data' => $file_data,
        'size_of_file' => $size_of_file
        ));
    }
    
    
   

}
