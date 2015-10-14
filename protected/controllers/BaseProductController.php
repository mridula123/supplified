<?php

class BaseProductController extends Controller {

    /**
    * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
    * using two-column layout. See 'protected/views/layouts/column2.php'.
    */

    public $layout = '//layouts/column2';
    public $image;
    public $base_product_id;

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
        'actions' => array('create', 'update', 'subscribegrid','bulkupload','CreateFileDownload','UpdateFileDownload','export','media','MediaFileDownload','configurablegrid','createconfigurable'),
        'users' => array('@'),
        ),
        array('allow', // allow admin user to perform 'admin' and 'delete' actions
        'actions' => array('admin', 'delete','CreateFileDownload','UpdateFileDownload','export','media','MediaFileDownload','create','configurablegrid','createconfigurable'),
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

        set_time_limit(0);
        $model = new BaseProduct;
        if (isset($_POST['BaseProduct'])) {
            $model->attributes = $_POST['BaseProduct'];

            /* if(isset($model->key_features))
            {
            $datakey=array();
            $key_featuressArr = explode(';', $model->key_features);

            foreach ($key_featuressArr as $value){
            if(!empty($value)){
            $spec = explode(':', trim($value));
            foreach ($spec as $value1){

            $datakey[]=trim($value1);
            }
            }
            }
            $model->key_features=json_encode($datakey);
            }

            if(isset($model->specifications))
            {
            $specifications=array();
            $specificationsArr = explode(';', $model->specifications);

            foreach ($specificationsArr as $value){
            if(!empty($value)){
            $spec = explode(':', trim($value));
            foreach ($spec as $value1){

            $specifications[]=trim($value1);
            }
            }
            }
            $model->specifications=json_encode($specifications);
            }   */
            $model->modified_date=date("Y-m-d H:i:s");
            $images = CUploadedFile::getInstancesByName('images');
            if ($model->save()){
                if(isset($images) && count($images) > 0)
                {
                    foreach ($images as $image => $pic) {
                        $pic->saveAs(UPLOAD_MEDIA_PATH.$pic->name);
                        $base_img_name  = uniqid();
                        //$path  = pathinfo($media);
                        $file1 = $base_img_name;
                        $baseDir = MAIN_BASE_MEDAI_DIRPATH;
                        if ($file1[0]) {
                            $baseDir .= $file1[0] . '/';
                        }

                        if ($file1[1]) {
                            $baseDir .= $file1[1] . '/';
                        } else {
                            $baseDir .= '_/';
                        }
                        $media_url_dir=$baseDir;
                        $content_medai_img=@file_get_contents('images/'.$pic->name);
                        $media_main=$media_url_dir.$base_img_name.'.jpg'; //name
                        @mkdir($media_url_dir, 0777, true);
                        $success = file_put_contents($media_main, $content_medai_img);

                        $baseThumbPath =THUMB_BASE_MEDIA_DIRPATH;
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
                        $midia_type='image';
                        $base_product_id = Yii::app()->db->getLastInsertID();
                        $connection = Yii::app()->db; $sql = "INSERT INTO media(media_url, thumb_url, base_product_id, media_type) VALUES('$media_main', '$media_thumb_url', '$base_product_id', '$midia_type')";
                        $command=$connection->createCommand($sql);
                        $command->execute();

                        $connection = Yii::app()->db;
                        if(isset($_POST['aiotree']['category_id']))
                        {
                            foreach ($_POST['aiotree']['category_id'] as $key => $value) {
                                $val= explode("-",$value);
                                $sql = "INSERT INTO product_category_mapping(base_product_id, category_id) VALUES('$base_product_id', '".$val[3]."')";
                                $command=$connection->createCommand($sql);
                                $command->execute();

                            }
                        }
                        @mkdir($thumb_url_dir, 0777, true);
                        $width = 150; $height = 150;
                        $image = $this->createImage(UPLOAD_MEDIA_PATH.$pic->name,$width,$height,$media_thumb_url);
                    }
                }
            }
            @unlink(UPLOAD_MEDIA_PATH.$pic->name);
            $this->redirect(array('update', 'id' => $model->base_product_id));
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

        set_time_limit(0); 
        $getrecord = new ProductCategoryMapping();
        $record = $getrecord->getRecordById($id);
        $imageinfo = $getrecord->getImageById($id);



        $model = $this->loadModel($id);
        if (isset($_POST['BaseProduct'])) {

            if(isset($_POST['media_remove']))
            {
                foreach($_POST['media_remove'] as $keyrm=>$valuerm)
                {
                    $mediaremove = Media::model()->deleteMediaByMediaId($valuerm);
                }
            }
            if(isset($_POST['media_is_default'])){


                Media::model()->updateDefaultMediaByBaseProductId($_POST['media_is_default'],$id);

            }
			
           /* $connection = Yii::app()->db;
            $sqldel = "Delete from  product_category_mapping where base_product_id=".$id."";
            $command=$connection->createCommand($sqldel);     
            $command->execute();


            if(isset($_POST['aiotree']['category_id']))
            {
                foreach ($_POST['aiotree']['category_id'] as $key => $value)
                {
                    $val= explode("-",$value);                
                    $sql = "INSERT INTO product_category_mapping(base_product_id, category_id) VALUES('$id', '$val[3]')";
                    $command=$connection->createCommand($sql);     
                    $command->execute();
                }
            }
			*/
            $model->attributes = $_POST['BaseProduct'];
            $images = CUploadedFile::getInstancesByName('images');            
            //unset($model['password']);

            /* if(isset($model->key_features)) 
            {			 
            $datakey=array();
            $key_featuressArr = explode(';', $model->key_features);
            $data['attributes'] = array();
            foreach ($key_featuressArr as $value){
            if(!empty($value)){
            $spec = explode(':', trim($value));
            foreach ($spec as $value1){

            $datakey[]=trim($value1);
            }				
            }             
            }
            $model->key_features=json_encode($datakey);
            }

            if(isset($model->specifications)) 
            {			 
            $specifications=array();
            $specificationsArr = explode(';', $model->specifications);
            $data['attributes'] = array();
            foreach ($specificationsArr as $value){
            if(!empty($value)){
            $spec = explode(':', trim($value));
            foreach ($spec as $value1){

            $specifications[]=trim($value1);
            }				
            }             
            }
            $model->specifications=json_encode($specifications);
            }*/	 
            $model->modified_date=date("Y-m-d H:i:s"); 		
            if ($model->save())
            {
                if(isset($images) && count($images) > 0)
                {
                    foreach ($images as $image => $pic) {
                        $pic->saveAs(UPLOAD_MEDIA_PATH.$pic->name);                 
                        $base_img_name  = uniqid();   
                        //$path  = pathinfo($media);
                        $file1 = $base_img_name;
                        $baseDir = MAIN_BASE_MEDAI_DIRPATH;
                        if ($file1[0]) {
                            $baseDir .= $file1[0] . '/';
                        }

                        if ($file1[1]) {
                            $baseDir .= $file1[1] . '/';
                        } else {
                            $baseDir .= '_/';
                        }      
                        $media_url_dir=$baseDir;
                        $content_medai_img=@file_get_contents(UPLOAD_MEDIA_PATH.$pic->name);
                        $media_main=$media_url_dir.$base_img_name.'.jpg'; //name
                        @mkdir($media_url_dir, 0777, true);    
                        $success = file_put_contents($media_main, $content_medai_img); 

                        $baseThumbPath =THUMB_BASE_MEDIA_DIRPATH;
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
                        $midia_type='image';



                        $connection = Yii::app()->db;
                        $sql = "INSERT INTO media(media_url, thumb_url, base_product_id, media_type) VALUES('$media_main', '$media_thumb_url', '$id', '$midia_type')";
                        $command=$connection->createCommand($sql);
                        $command->execute();
                        @mkdir($thumb_url_dir, 0777, true);
                        $width = 150; $height = 150;
                        $image = $this->createImage(UPLOAD_MEDIA_PATH.$pic->name,$width,$height,$media_thumb_url); 
                        @unlink(UPLOAD_MEDIA_PATH.$pic->name);
                    }
                }
            }

            //.......................solor backloag.................//
            $solrBackLog = new SolrBackLog();
            //$is_deleted =  ($model->status == 1) ? 0 : 1;
            $is_deleted = '0';
            $solrBackLog->insertByBaseProductId($model->base_product_id,$is_deleted); 
            //.........................end.....................................//

            $this->redirect(array('update', 'id' => $model->base_product_id));
        }

        $this->render('update', array(
        'model' => $model,
        'record' => $record,
        'imageinfo'  => $imageinfo,
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
        $dataProvider = new CActiveDataProvider('BaseProduct');
        $this->render('index', array(
        'dataProvider' => $dataProvider,
        ));
    }

    /**
    * Manages all models.
    */
    public function actionAdmin() {
        $model = new BaseProduct('search');
        $model->unsetAttributes();  // clear any default values

        if (isset($_GET['BaseProduct']))
            $model->attributes = $_GET['BaseProduct'];

        $this->render('admin', array(
        'model' => $model,
        ));
    }


    public function actionBulkUpload() {

	
        set_time_limit(0); 
        $logfile='';
        $baseid='';
        $model = new Bulk;
        $keycsv=1;
        $csv_filename='';

        $insert_base_csv_info=array(); 

        $insert_base_csv_info[$keycsv]['base_product_id'] = 'base_product_id';
        $insert_base_csv_info[$keycsv]['model_name']     = 'model_name';
        $insert_base_csv_info[$keycsv]['model_number']    = 'model_number';
        $keycsv++;

		
		if (isset($_POST['Bulk'])) {
			
			//print_r($_FILES);
			//print_r($_POST);
			
		
            $model->action = $_POST['Bulk']['action'];
            $model->attributes = $_POST['Bulk'];
            if (!empty($_FILES['Bulk']['tmp_name']['csv_file'])) {
                $csv = CUploadedFile::getInstance($model, 'csv_file');
                if (!empty($csv)) {
				
				
				
                    if($csv->size > 25*1024*1024){

                        Yii::app()->user->setFlash('error', 'Cannot upload file greater than 25MB.');
                        $this->render('bulkupload', array('model' => $model));
                    }
                    $fileName = 'csvupload/'.$csv->name;
                    $filenameArr = explode('.',$fileName);
                    $fileName = $filenameArr[0].'-'.Yii::app()->session['sessionId'].'-'.time().'.'.end($filenameArr);
                    $csv->saveAs($fileName);
                }else{

				//die("222");
				
                    Yii::app()->user->setFlash('error', 'Please browse a CSV file to upload.');
                    $this->render('bulkupload', array('model' => $model));
                }
				
				
				
                $ext = pathinfo($fileName, PATHINFO_EXTENSION);
                if ($ext != 'csv') {


                    Yii::app()->user->setFlash('error', 'Only .csv files allowed.');
                    $this->render('bulkupload', array('model' => $model));
                }
                $i = 0;



                $requiredFields = array('title', 'small_description', 'categoryIds', 'status' ,'model_number','brand','qty');
                $defaultFields = array(
					"title","small_description","description","brand","brandcode",
					"model_name","model_number","manufacture","manufacture_country","manufacture_year",
					"heavy_and_bulks","key_features","status","categoryIds","product_content_type",
					"ISBN","product_shipping_charge","specifications","moq","VAT",
					"meta_title","meta_keyword","meta_description",
					"variant_on","store_id","unit_rate","store_price","store_offer_price","color",
					"weight","height","length","size","qty","warranty",
					"average_rating","other_website_rating","media","is_serial_required","video_url"
				);
				
                if ($model->action == 'update') {
                    $requiredFields = array('base_product_id','varient_id');
                    $defaultFields[] = 'base_product_id';
					$defaultFields[] = 'varient_id';
                }
                if(($handle = fopen("$fileName", "r")) !== FALSE) {
                    $logDir = "log/";
                    $logfile = 'bulk_upload_log_' . Yii::app()->session['sessionId'] . '_' . time() . '.txt';
                    $handle1 = fopen($logDir . $logfile, "a");
					
					if($model->action=='create')
                     {
                    while(($data = fgetcsv($handle, 0, ",")) !== FALSE){
                        if($i>=0 && count($data)>0){
                            $i++;
							
							//print_r($data);
							//die("444");
							
                            /*header*/
                            if($i==1){

                                $colDiff = array_diff($requiredFields, $data);
                                if (!empty($colDiff)) {
                                    Yii::app()->user->setFlash('error', 'Required columns missing : ' . implode(' , ', $colDiff));

                                    // unlink($fileName);
                                    break;
                                }

								
								
                                foreach($data as $key=>$value){
                                    $data[$key] = trim($value);
                                    if(in_array($value,$defaultFields)){
                                        $cols[$value] = $key;
                                    }elseif($value != ""){
                                        $originalExtraAttrs[$value] = $key;
                                        $value = addslashes($value);
                                        $extraAttributes[$value] = $key;
                                    }
                                }
								
								//die("888");
                            }
                            else
                            {
							
							//die("666");
                                  $row = array();        

                                if(isset($cols['base_product_id'])) {


                                    try {
                                        if (trim($data[$cols['base_product_id']]) == null) {
                                            fwrite($handle1, "\nRow : ". $i . " Base product id is empty.");
                                            continue;
                                        }
                                        $model1=$this->loadModel(trim($data[$cols['base_product_id']]));
                                    } catch (Exception $e) {
                                        fwrite($handle1, "\nRow : ". $i . " Base product {$data[$cols['base_product_id']]} does not exist.");
                                        continue;
                                    }
                                } else {
                                    $model1=new BaseProduct;
                                }
                                $model1->action = $model->action;


								//print_r($data);
							    //die("444");
							
					if(isset($cols['title']) &&  trim($data[$cols['title']]) != ''){
					$connection = Yii::app()->db;
					
                                if(isset($cols['base_product_id']))
                                    $baseid = str_replace("’","'",trim($data[$cols['base_product_id']]));

                                if(isset($cols['title']))
                                    $row['title'] = str_replace("’","'",trim(htmlentities($data[$cols['title']]),ENT_COMPAT));
									
                                if(isset($cols['small_description']))
                                    $row['small_description'] = str_replace(" "," ",trim(htmlentities($data[$cols['small_description']]),ENT_COMPAT));
									
								if(isset($cols['description']))
                                    $row['description'] =str_replace("’","'",trim(htmlentities($data[$cols['description']]),ENT_COMPAT));
									
								if(isset($cols['brand']))
                                    $row['brand'] =  str_replace("’","'",trim($data[$cols['brand']]));	
						
						
				//	print_r($row['small_description']);
				//	print_r("<br>");
					//exit();		
								if(isset($cols['brandcode'])){
										
										$brandcode = trim($data[$cols['brandcode']]);
										
										$bquery = Yii::app()->db->createCommand()
												->select('store_front_id,redirect_url')
												->from('brand')
												->where('redirect_url = :brandcode', array(':brandcode'=>$brandcode))
												->queryRow();
												
										//print_r($bquery);
										$brandcodeID = $bquery['store_front_id'];
										$brandcodeDB = $bquery['redirect_url'];
										
										$row['brand_id'] = $brandcodeID;
											
								}
								
								if(isset($cols['model_name']))
                                    $row['model_name'] =  str_replace("’","'",trim($data[$cols['model_name']]));
									
                                if(isset($cols['model_number']))
                                    $row['model_number'] = trim($data[$cols['model_number']]);
									
								if(isset($cols['manufacture']))
                                    $row['manufacture'] = str_replace("’","'",trim($data[$cols['manufacture']]));
									
                                if(isset($cols['manufacture_country']))
                                    $row['manufacture_country'] =trim($data[$cols['manufacture_country']]);
									
                                if(isset($cols['manufacture_year']))
                                    $row['manufacture_year'] = trim($data[$cols['manufacture_year']]);
									
		
								if(isset($cols['key_features']))
                                {

                                    $datakey=array();
                                    $key_featuressArr = explode(';', str_replace("’","'",trim($data[$cols['key_features']])));

									foreach ($key_featuressArr as $value){

                                        if(!empty($value))
                                        {
                                            $spec = array();
                                            $spec = explode(':', trim($value));
                                            
                                              if(isset($spec[1]) && !empty($spec[1]))
                                               $datakey[utf8_encode($spec[0])]=trim(utf8_encode($spec[1]));
                                               else
                                                 $datakey[utf8_encode($spec[0])]='';    
                                            
                                        }							  
                                    }                         
                                    $row['key_features'] = json_encode($datakey); 
                                }
								
								if(isset($cols['status']))
                                    $row['status'] = trim($data[$cols['status']]);

		
								if(isset($cols['categoryIds'])){
								 
									$categoryIds_str = trim($data[$cols['categoryIds']]);
									
									$categoryId = (int)trim($data[$cols['categoryIds']]);
									
									
									$bquery = Yii::app()->db->createCommand()
											->select('category_name')
											->from('category')
											->where('category_id = :catid', array(':catid'=>$categoryId))
											->queryRow();
											
									//print_r($bquery);
									$categoryCodeDB = strtoupper(substr($bquery['category_name'],0,2));
									
								}
								
								if(isset($cols['product_content_type']))
                                    $row['product_content_type'] =  strtolower(str_replace(" ","",$data[$cols['product_content_type']]));	
									
                                if(isset($cols['ISBN']))
                                    $row['ISBN'] =  trim($data[$cols['ISBN']]); 
		
								if(isset($cols['product_shipping_charge']))
                                    $row['product_shipping_charge'] = trim($data[$cols['product_shipping_charge']]);
									
								if(isset($cols['specifications']))
                                {
                                    $dataspecfica=array();
                                    $specficationArr = explode(';', str_replace("’","'",trim($data[$cols['specifications']])));
                                    
                                    //$data['attributes'] = array();
                                    foreach ($specficationArr as $value)
                                    {
                                        if(!empty($value))
                                        {
                                            $spec = array();
                                            $spec = explode(':', trim($value));
                                            if(isset($spec[1]) && !empty($spec[1]))
                                            $dataspecfica[utf8_encode($spec[0])]= trim(utf8_encode($spec[1]));
                                            else
                                            	$dataspecfica[utf8_encode($spec[0])]='';
                                            
                                        } 								  
                                    }											 

                                    $row['specifications'] = json_encode($dataspecfica);                                    
                                }
								
								if(isset($cols['moq']))
                               		$row['moq'] = trim($data[$cols['moq']]);
									
								if(isset($cols['VAT']))
                                    $row['VAT'] = trim($data[$cols['VAT']]);
		
					}
		
		
								/* Variant Section Starts */
								
								if(isset($cols['variant_on']))
                               		$store_row['variant_on'] = trim($data[$cols['variant_on']]);
								
								if(isset($cols['store_id']))
                               		$store_row['store_id'] = trim($data[$cols['store_id']]);
									
								//$store_id = trim($data[$cols['store_id']]);
								
								if(isset($cols['unit_rate']))
									$store_row['unit_rate']           = trim($data[$cols['unit_rate']]);
								
				
								if(isset($cols['store_price']))
									$store_row['store_price']           = trim($data[$cols['store_price']]);
								
								if(isset($cols['store_offer_price']))
									$store_row['store_offer_price'] 	= trim($data[$cols['store_offer_price']]);
								
								if(isset($cols['color']))
                                    $store_row['color'] 						= trim($data[$cols['color']]);
								
								if(isset($cols['weight']))
									$store_row['weight']            	= trim($data[$cols['weight']]);
								
								if(isset($cols['height']))
									$store_row['height']            	= trim($data[$cols['height']]);
									
								if(isset($cols['length']))
									$store_row['length']            	= trim($data[$cols['length']]);
								
								if(isset($cols['size']))
                                    $store_row['size'] = trim($data[$cols['size']]);
								
								if(isset($cols['qty']))
                               		$store_row['quantity'] = trim($data[$cols['qty']]);									
								
								if(isset($cols['warranty']))
                               		$store_row['warranty'] = trim($data[$cols['warranty']]);
									
								/* Variant Section Ends */
		
								/* Not Used Fields
								
									if(isset($cols['quantity']))
										$row['quantity'] = trim($data[$cols['quantity']]);
									
									if(isset($cols['is_cod']))
										$row['is_cod'] = trim($data[$cols['is_cod']]);
										
									if(isset($cols['media']))
										$row['media'] = trim($data[$cols['media']]);  
								
							   */
                               
                            
							//print_r($row);
							//print_r($store_row);
							
							//die("999");
                                
                                       
							$errorFlag = 0;                    
							$model1->attributes = $row;

	
		if(isset($cols['title']) &&  trim($data[$cols['title']]) != ''){
		
		
			/* Product Insertion Starts */
			
				$key_arr1 = array();
				$key_arr2 = array();
				$key_arr3 = array();

				foreach($row as $key => $value){

					$key_arr1[] 		= $key;
					$key_arr2[] 		= ":".$key;
					$key_arr3[":".$key] = $value;

				}

				$sql = "insert into base_product (".implode(',',$key_arr1).") values (".implode(',',$key_arr2).")";

				$parameters = $key_arr3;

				Yii::app()->db->createCommand($sql)->execute($parameters);
				
				$insert_id = Yii::app()->db->getLastInsertID();
			
			/* Product Insertion Ends */
		
			//die("1010");
		
			/* SKU Code Generation Starts */
			
				$random_no = rand(1001,9999);
				$SKUCode = "SU".$brandcodeDB.$brandcodeID.$categoryCodeDB.$random_no.$insert_id;
				
				Yii::app()->db->createCommand()->update('base_product', array(
					'SKUCode'=>$SKUCode,
				), 'base_product_id = :id', array(':id'=>$insert_id));
			
			/* SKU Code Generation Ends */	
		
		
			/* Category Mapping Starts */
			
				$categoryIds_arr = @explode(",",$categoryIds_str);
				
				if(count($categoryIds_arr)){
					foreach($categoryIds_arr as $key => $catID){
						
						Yii::app()->db->createCommand()->insert('product_category_mapping', array(
								'base_product_id' => $insert_id,
								'category_id'=> $catID
						));
					}
				}
				
			/* Category Mapping Ends */
		
		
		}
		
		//die("1011");
		
		/* Product Subscribe Mapping Starts */
		
			if(count($store_row)){
					
					$store_row['base_product_id']   = $insert_id;
					
					$key_arr1 = array();
					$key_arr2 = array();
					$key_arr3 = array();
					
					foreach($store_row as $key => $value){
					
						$key_arr1[] 		= $key;
						$key_arr2[] 		= ":".$key;
						$key_arr3[":".$key] = $value;
						
					}//foreach ends

					$sql = "insert into subscribed_product (".implode(',',$key_arr1).") values (".implode(',',$key_arr2).")";

					$parameters = $key_arr3;

					Yii::app()->db->createCommand($sql)->execute($parameters);
					
					$subs_insert_id = Yii::app()->db->getLastInsertID();
				
					$random_no = rand(1001,9999);
					$SKUCode = "VT".$insert_id.$random_no.$subs_insert_id;
					
					Yii::app()->db->createCommand()->update('subscribed_product', array(
						'sku'=>$SKUCode,
					), 'subscribed_product_id = :subs_id', array(':subs_id' => $subs_insert_id));
			
			}
			
		/* Product Subscribe Mapping Ends  */
		
		//die(" product Inserted ");

                            }
                        }
                    }//while loop ends
					
					//die("ALL PRODUCTS UPLOADED");
					
				} // if action mode close
				else{   // amar
				
					while(($data = fgetcsv($handle, 0, ",")) !== FALSE){
					
					
                        if($i>=0 && count($data)>0){
                            $i++;
                            /*header*/
                            if($i==1){

                                $colDiff = array_diff($requiredFields, $data);
                                if (!empty($colDiff)) {
                                    Yii::app()->user->setFlash('error', 'Required columns missing : ' . implode(' , ', $colDiff));

                                    // unlink($fileName);
                                    break;
                                }

                                foreach($data as $key=>$value){
                                    $data[$key] = trim($value);
                                    if(in_array($value,$defaultFields)){
                                        $cols[$value] = $key;
                                    }elseif($value != ""){
                                        $originalExtraAttrs[$value] = $key;
                                        $value = addslashes($value);
                                        $extraAttributes[$value] = $key;
                                    }
                                }
                            }
                            else
                            {
                                  $row = array();        

                                if(isset($cols['base_product_id'])) {

                                    try {
                                        if (trim($data[$cols['base_product_id']]) == null) {
                                            fwrite($handle1, "\nRow : ". $i . " Base product id is empty.");
                                            continue;
                                        }
                                        $model1=$this->loadModel(trim($data[$cols['base_product_id']]));
                                    } catch (Exception $e) {
                                        fwrite($handle1, "\nRow : ". $i . " Base product {$data[$cols['base_product_id']]} does not exist.");
                                        continue;
                                    }
                                } else {
                                    $model1=new BaseProduct;
                                }
                                $model1->action = $model->action;


									
                     if(isset($cols['title']) &&  trim($data[$cols['title']]) != ''){
						
                                if(isset($cols['base_product_id']))
                                    $baseid = str_replace("’","'",trim($data[$cols['base_product_id']]));

                                if(isset($cols['title']))
                                    $row['title'] = str_replace("’","'",trim(htmlentities($data[$cols['title']]),ENT_COMPAT));
									
                                if(isset($cols['small_description']))
                                    $row['small_description'] = str_replace("’","'",trim(htmlentities($data[$cols['small_description']]),ENT_COMPAT));
									
								if(isset($cols['description']))
                                    $row['description'] =str_replace("’","'",trim(htmlentities($data[$cols['description']]),ENT_COMPAT));
									
								if(isset($cols['brand']))
                                    $row['brand'] =  str_replace("’","'",trim($data[$cols['brand']]));	
								
								if(isset($cols['brandcode'])){
										
										$brandcode = trim($data[$cols['brandcode']]);
										
										$bquery = Yii::app()->db->createCommand()
												->select('store_front_id,redirect_url')
												->from('brand')
												->where('redirect_url = :brandcode', array(':brandcode'=>$brandcode))
												->queryRow();
												
										//print_r($bquery);
										$brandcodeID = $bquery['store_front_id'];
										$brandcodeDB = $bquery['redirect_url'];
										
										$row['brand_id'] = $brandcodeID;
											
								}
								
								if(isset($cols['model_name']))
                                    $row['model_name'] =  str_replace("’","'",trim($data[$cols['model_name']]));
									
                                if(isset($cols['model_number']))
                                    $row['model_number'] = trim($data[$cols['model_number']]);
									
								if(isset($cols['manufacture']))
                                    $row['manufacture'] = str_replace("’","'",trim($data[$cols['manufacture']]));
									
                                if(isset($cols['manufacture_country']))
                                    $row['manufacture_country'] =trim($data[$cols['manufacture_country']]);
									
                                if(isset($cols['manufacture_year']))
                                    $row['manufacture_year'] = trim($data[$cols['manufacture_year']]);
									
		
								if(isset($cols['key_features']))
                                {

                                    $datakey=array();
                                    $key_featuressArr = explode(';', str_replace("’","'",trim($data[$cols['key_features']])));

									foreach ($key_featuressArr as $value){

                                        if(!empty($value))
                                        {
                                            $spec = array();
                                            $spec = explode(':', trim($value));
                                            
                                              if(isset($spec[1]) && !empty($spec[1]))
                                               $datakey[utf8_encode($spec[0])]=trim(utf8_encode($spec[1]));
                                               else
                                                 $datakey[utf8_encode($spec[0])]='';    
                                            
                                        }							  
                                    }                         
                                    $row['key_features'] = json_encode($datakey); 
                                }
								
								if(isset($cols['status']))
                                    $row['status'] = trim($data[$cols['status']]);

		
								if(isset($cols['categoryIds'])){
								 
									$categoryIds_str = trim($data[$cols['categoryIds']]);
									
								}
								
								if(isset($cols['product_content_type']))
                                    $row['product_content_type'] =  strtolower(str_replace(" ","",$data[$cols['product_content_type']]));	
									
                                if(isset($cols['ISBN']))
                                    $row['ISBN'] =  trim($data[$cols['ISBN']]); 
		
								if(isset($cols['product_shipping_charge']))
                                    $row['product_shipping_charge'] = trim($data[$cols['product_shipping_charge']]);
									
								if(isset($cols['specifications']))
                                {
                                    $dataspecfica=array();
                                    $specficationArr = explode(';', str_replace("’","'",trim($data[$cols['specifications']])));
                                    
                                    //$data['attributes'] = array();
                                    foreach ($specficationArr as $value)
                                    {
                                        if(!empty($value))
                                        {
                                            $spec = array();
                                            $spec = explode(':', trim($value));
                                            if(isset($spec[1]) && !empty($spec[1]))
                                            $dataspecfica[utf8_encode($spec[0])]= trim(utf8_encode($spec[1]));
                                            else
                                            	$dataspecfica[utf8_encode($spec[0])]='';
                                            
                                        } 								  
                                    }											 

                                    $row['specifications'] = json_encode($dataspecfica);                                    
                                }
								
								if(isset($cols['moq']))
                               		$row['moq'] = trim($data[$cols['moq']]);
									
								if(isset($cols['VAT']))
                                    $row['VAT'] = trim($data[$cols['VAT']]);
		
						 }
	//	print_r($row);
	//	exit;
		
								/* Variant Section Starts */
								
								if(isset($cols['varient_id']))
                                    $varient_id = str_replace("’","'",trim($data[$cols['varient_id']]));
									
								if(isset($cols['variant_on']))
                               		$store_row['variant_on'] = trim($data[$cols['variant_on']]);
								
								if(isset($cols['store_id']))
                               		$store_row['store_id'] = trim($data[$cols['store_id']]);
									
								//$store_id = trim($data[$cols['store_id']]);
								
								if(isset($cols['unit_rate']))
									$store_row['unit_rate']           = trim($data[$cols['unit_rate']]);
								
				
								if(isset($cols['store_price']))
									$store_row['store_price']           = trim($data[$cols['store_price']]);
								
								if(isset($cols['store_offer_price']))
									$store_row['store_offer_price'] 	= trim($data[$cols['store_offer_price']]);
								
								if(isset($cols['color']))
                                    $store_row['color'] 						= trim($data[$cols['color']]);
								
								if(isset($cols['weight']))
									$store_row['weight']            	= trim($data[$cols['weight']]);
								
								if(isset($cols['height']))
									$store_row['height']            	= trim($data[$cols['height']]);
									
								if(isset($cols['length']))
									$store_row['length']            	= trim($data[$cols['length']]);
								
								if(isset($cols['size']))
                                    $store_row['size'] = trim($data[$cols['size']]);
								
								if(isset($cols['qty']))
                               		$store_row['quantity'] = trim($data[$cols['qty']]);									
								
								if(isset($cols['warranty']))
                               		$store_row['warranty'] = trim($data[$cols['warranty']]);
									
																	
								/* Variant Section Ends */
								
								 
                               if(isset($cols['media']))
                                    $row['media'] = trim($data[$cols['media']]);  
 
                                $errorFlag = 0;                    
                                $model1->attributes = $row;
								
								
                                $error = array();
                                $action = $model->action == 'update' ? 'updated' : 'created';

                                if (!$model1->save(true)) {

                                    foreach($model1->getErrors() as $errors) {
                                        $error[] = implode(' AND ', $errors);
                                    }
                                    fwrite($handle1, "\nRow : ". $i . " Product not $action. ". implode(' AND ', $error));


                                }


                                else {
                                    //...............................................//

                                    if(isset($row['media']) && !empty($row['media']))
                                    {
                                   

                                        $images = $row['media'];   
                                        $insertImages = array();
                                        if (isset($images) && $model1->base_product_id >0) {
                                            if (!$model1->isNewRecord) {
                                                $sql = "DELETE FROM media WHERE base_product_id = $model1->base_product_id and variant_id='$varient_id'";
                                                $connection = Yii::app() -> db;
                                                $command = $connection -> createCommand($sql);
                                                $command -> execute();
                                            }

                                            $images = explode(";",$images);
                                            $insertImages = $this->uploadImages($images,$i,$model1->base_product_id,$varient_id);
											
                                            if (!empty($insertImages['error'])) {
                                                $model1->addError('csv_file', $insertImages['error']);
                                            }
                                            
                                           
                                        }
                                        /*save each uploaded media into database*/
                                        if (!empty($insertImages['images'])) {
                                            $insertRows = array();
                                            foreach ($insertImages['images'] as $key => $value) {
                                                $error = array();
                                                $media_model=new Media;
                                                if ($key !== 'error') {
                                                    $media_model->attributes=$value;
                                                    $media_model->save(true);
                                                } else {
                                                    $error[] = $value;
                                                }
                                                foreach($media_model->getErrors() as $errors) {
                                                    $error[] = implode(' AND ', $errors);
                                                }
                                                if (!empty($error)) {
                                                    $model1->addError('csv_file', implode(' AND ', $error));
                                                }
                                            }
                                        }

                                    }

                                    //...............................................//		
                                }

                                $error = array();
                                foreach($model1->getErrors() as $errors) {
                                    $error[] = implode(' AND ', $errors);
                                }

                                fwrite($handle1, "\nRow : ". $i  . " Product $model1->base_product_id $action. ". implode(' AND ', $error));

								
                                if(isset($cols['title']) &&  trim($data[$cols['title']]) != ''){
								
									/* Product Updation Starts */
										
										Yii::app()->db->createCommand()->update('base_product', $row, 'base_product_id = :id', array(':id'=>$baseid));
										
									/* Product Updation Ends */
									
									/* Updating Category Mapping Starts */
										
										Yii::app()->db->createCommand()->delete('product_category_mapping', 'base_product_id = :base_product_id', array(':base_product_id' => $baseid));
										
										$categoryIds_arr = @explode(",",$categoryIds_str);
										
										if(count($categoryIds_arr)){
											foreach($categoryIds_arr as $key => $catID){
												
												Yii::app()->db->createCommand()->insert('product_category_mapping', array(
														'base_product_id' => $baseid,
														'category_id'=> $catID
												));
											}
										}
										
									/* Updating Category Mapping Ends */
								
								}
								
								/* Product Subscribe Mapping Starts */
		
									if(count($store_row)){
									
									//echo $varient_id;
									//print_r($store_row);
									//die;
										
										Yii::app()->db->createCommand()->update('subscribed_product', $store_row, 'subscribed_product_id = :varient_id', array(':varient_id'=>$varient_id));
									
									}
									
								/* Product Subscribe Mapping Ends  */
		
								/*
                                if($model->action=='update')
                                {                                    
                                    if(isset($cols['categoryIds']))
                                    {
                                    $categories='';
                                    $categories = explode(';',trim($data[$cols['categoryIds']], ';'));

                                    if(isset($categories) && !empty($categories))
                                    {
                                        $connection = Yii::app()->db;
                                        $sql = "SELECT DISTINCT category_id 
                                        FROM `category` 
                                        WHERE category_id IN ( " . implode(',', $categories) . " )
                                        AND is_deleted = 0";


                                        $catinfo='';
                                        $command = $connection->createCommand($sql);
                                        $catinfo=$command->queryAll();
                                        $catIds = array();
                                        if(isset($catinfo) && !empty($catinfo))
                                        {
                                            foreach ($catinfo as $cat) {
                                                $catIds[] = $cat['category_id'];
                                            }

                                            Category::model()->insertCategoryMappings($baseid,$catIds);

                                            $catDiff = array_diff($categories, $catIds);
                                            if (!empty($catDiff)) {

                                                Yii::app()->user->setFlash('error', 'Invalid Category ids :' . implode(' , ', $colDiff));

                                                break;
                                            }
                                        }
                                    }
                                    }
                                }
								*/
								/*
                                if($model->action=='update' && !empty($baseid))
                                {
                                    //.......................solor backloag.................//
                                    $solrBackLog = new SolrBackLog();
                                    //$is_deleted =  ($model->status == 1) ? 0 : 1;
                                    $is_deleted = '0';
                                    $solrBackLog->insertByBaseProductId($baseid,$is_deleted); 
                                    //.........................end.....................................//
                                }
								*/
								
                                Yii::app()->user->setFlash('success', 'Upload Successfully !.' );	 

                            }
                        }
                    }
				   	
					
				}
					
					
					
					
					Yii::app()->user->setFlash('success', 'Upload Successfully !.' );
					
                }

            }

			

        }
		

        // @unlink($fileName);
        $this->render('bulkupload',array(
        'model'=>$model,
        'logfile' => $logfile ,
        'csv_filename' => $csv_filename
        ));


    }

    public function actionMedia() {

		$oldfilename='';
        if(isset($_FILES["media_zip_file"]["name"])) {
		
		
		
            if($_FILES["media_zip_file"]["size"] > 25*1024*1024){
                throw new Exception('Cannot upload file greater than 25MB');
            }
            $oldfilename = $_FILES["media_zip_file"]["name"];
            $filename = $_FILES["media_zip_file"]["name"];
            $filenameArr = explode('.',$filename);
            $filename = $filenameArr[0].'-'.Yii::app()->session['sessionId'].'-'.time().'.'.end($filenameArr);

            $source = $_FILES["media_zip_file"]["tmp_name"];
            $type = $_FILES["media_zip_file"]["type"];

            $target_path ='zips/'.$filename;  // change this to the correct site path

            if(move_uploaded_file($source, $target_path)) {

                $accepted_types = array('application/zip', 'application/x-zip-compressed', 'multipart/x-zip', 'application/x-compressed');
                $okay = false;

                $file_info = new finfo(FILEINFO_MIME);  // object oriented approach!
                $file_mime_type = $file_info->buffer(@file_get_contents($target_path));  // e.g. gives "image/jpeg"

                foreach($accepted_types as $mime_type) {
                    if(strpos($file_mime_type, $mime_type) !== FALSE){
                        $okay = true;
                        break;
                    } 
                }

                $name = explode(".", $filename);
                $continue = strtolower($name[1]) == 'zip' ? true : false;
                if(!$okay OR !$continue) {
                    Yii::app()->user->setFlash('error', 'The file you are trying to upload not a .zip file. Please try again.');
                    $this->render('media');

                }

                $zip = new ZipArchive();
                $x = $zip->open($target_path);
                if ($x === true) {
                    $zip->extractTo('images/'); // change this to the correct site path
                    $zip->close();
                    @system('chmod -R 0777 '.'images/');
                    $response['status'] = 'success';
                    $response['message'] = "Your $oldfilename file was uploaded and unpacked.";
                    @unlink($target_path);
                    Yii::app()->user->setFlash('success', "Your ".$oldfilename." file was uploaded and unpacked.");
                    $this->render('media');
                }else{
                    Yii::app()->user->setFlash('error', 'Unable to read zip file. Please check file type & try again.');
                    $this->render('media');

                }
            } else {


                Yii::app()->user->setFlash('error', 'There was a problem with the upload. Please try again.');
                $this->render('media');
            }
        }




        // @unlink($fileName);
		
		
        $this->render('media');

    }




    public function actionSubscribegrid($store_id) {

        $id = $store_id;
        $model = new BaseProduct('search');
        $model->unsetAttributes();  // clear any default values
        if (isset($_GET['BaseProduct']))
            $model->attributes = $_GET['BaseProduct'];

        $this->render('subscribegrid', array(
        'model' => $model,
        'store_id'=>$id,
        ));
    }

    /**
    * Returns the data model based on the primary key given in the GET variable.
    * If the data model is not found, an HTTP exception will be raised.
    * @param integer $id the ID of the model to be loaded
    * @return BaseProduct the loaded model
    * @throws CHttpException
    */
    public function loadModel($id, $condition = null) {
        $model = BaseProduct::model()->findByPk($id,$condition);
        //print_r($model1);die;
        if ($model === null )
            throw new CHttpException(404, 'The requested page does not exist.');
        return $model;
    }

    /**
    * Performs the AJAX validation.
    * @param BaseProduct $model the model to be validated
    */
    protected function performAjaxValidation($model) {
        if (isset($_POST['ajax']) && $_POST['ajax'] === 'base-product-form') {
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
    public function actionCreateFileDownload() {
        $file_name = 'bulk_upload_baseproduct_create.csv';
        $file_data = 'title,small_description,description,color,size,product_weight,brand,brandcode,model_name,model_number,manufacture,manufacture_country,manufacture_year,heavy_and_bulks,key_features,status,categoryIds,product_content_type,ISBN,product_shipping_charge,specifications,store_id,unit_rate,store_price,store_offer_price,weight,height,length,VAT,qty,moq,warranty';
        $size_of_file = strlen($file_data);
        $this->renderPartial('fileDownload',array(
        'file_name' => $file_name,
        'file_data' => $file_data,
        'size_of_file' => $size_of_file
        ));
    }

    public function actionUpdateFileDownload() {
        $file_name = 'bulk_upload_baseproduct_update.csv';
        $file_data = 'base_product_id,title,small_description,description,color,size,product_weight,brand,brandcode,model_name,model_number,manufacture,manufacture_country,manufacture_year,heavy_and_bulks,key_features,status,categoryIds,product_content_type,ISBN,product_shipping_charge,specifications,store_id,unit_rate,unit_ratestore_price,store_offer_price,weight,height,length,VAT,qty,moq,warranty';
        $size_of_file = strlen($file_data);
        $this->renderPartial('fileDownload',array(
        'file_name' => $file_name,
        'file_data' => $file_data,
        'size_of_file' => $size_of_file
        ));
    }
    public function actionExport($id){	
        $connection = Yii::app()->db;
        $sqlchksubsid = "SELECT sp.`subscribed_product_id`,sp.`base_product_id`,sp.`store_id`,s.store_name,bs.title,bs.color,sp.`unit_rate`,sp.`store_price`,sp.`store_offer_price`,sp.`weight`,sp.`length`,sp.`width`,sp.`height`,sp.`status`,sp.sku,sp.`quantity`,sp.`is_cod`,sp.`created_date`,sp.`modified_date` FROM `subscribed_product` sp join store as s on s.store_id=sp.`store_id` join base_product as bs on bs.`base_product_id`=sp.`base_product_id` WHERE sp.`base_product_id`=".$id." group by sp.`base_product_id`";   
        $command1 = $connection->createCommand($sqlchksubsid);
        $command1->execute();
        $assocDataArray=$command1->queryAll();	
        $fileName="Base_product_id_".$id.".csv"; 
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

    public function uploadImages($images,$i,$id,$vid) {
        set_time_limit(0); 
        $insertImages = $error = array();
        //$width = 150;
        //$height = 150;
        $width = 250;
        $height = 250;
        foreach($images as $key=>$image){
            $base_img_name  = uniqid();   
            //$path  = pathinfo($media);
            $file1 = $base_img_name;
            $baseDir = MAIN_BASE_MEDAI_DIRPATH;
            if ($file1[0]) {
                $baseDir .= $file1[0] . '/';
            }

            if ($file1[1]) {
                $baseDir .= $file1[1] . '/';
            } else {
                $baseDir .= '_/';
            }      
            $media_url_dir=$baseDir;
            $content_medai_img=@file_get_contents(UPLOAD_MEDIA_PATH.$image);
            $media_main=$media_url_dir.$base_img_name.'.jpg'; //name
            @mkdir($media_url_dir, 0777, true);    
            $success = file_put_contents($media_main, $content_medai_img); 

            $baseThumbPath =THUMB_BASE_MEDIA_DIRPATH;
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
            $midia_type='image';
            @mkdir($thumb_url_dir, 0777, true);
            $width = 150; $height = 150;
            $image = $this->createImage(UPLOAD_MEDIA_PATH.$image,$width,$height,$media_thumb_url); 
            @unlink(UPLOAD_MEDIA_PATH.$image);	
            $media1['media_url'] = $media_main;
            $media1['thumb_url'] = $media_thumb_url;
            $media1['is_default'] = ($key==0) ? 1 : 0;
            $media1['base_product_id'] = $id;
			$media1['variant_id'] = $vid;
            $insertImages['images'][] = $media1;							
        }
        return $insertImages;
    }
    public function actionMediaFileDownload() {
        $file_name = 'bulk_upload_Media_File.csv';
        $file_data = 'base_product_id,variant_id,media';
        $size_of_file = strlen($file_data);
        $this->renderPartial('fileDownload',array(
        'file_name' => $file_name,
        'file_data' => $file_data,
        'size_of_file' => $size_of_file
        ));
    }

    public function actionConfigurablegrid() {
        $cat_base_product_ids = null;
        $category_id = isset($_GET['category_id'])?$_GET['category_id']:null;
        if(!empty($category_id)){
            //get base product ids by category filter
            $cat_base_product_ids = Category::model()->getBaseProductIdsByCategory($category_id);
        }

        $model=new BaseProduct('configurablegrid');

        $model->unsetAttributes();  // clear any default values
        if(isset($_GET['BaseProduct'])){
            $model->attributes=$_GET['BaseProduct'];
            $model->base_product_id = $_GET['BaseProduct']['base_product_id'];
        }

        $model->setAttribute('is_deleted','=0');
        if(!((isset($model->status) AND $model->status != '' AND $model->status == 0) OR $model->status == 1)) {
            $model->setAttribute('status' ,'<2');
        }

        $this->render('configurablegrid',array(
        'model'=>$model,
        'category_id'=>$category_id,
        'cat_base_product_ids'=>$cat_base_product_ids,
        ));
    }


    public function actionCreateconfigurable($id = null,$category_id=null){


        if(!empty($id)){



            $cat_base_product_ids=''; 
            if(!empty($category_id)){
                //get base product ids by category filter
                $cat_base_product_ids = Category::model()->getBaseProductIdsByCategoryone($category_id,$id);
            }

            // $model=new BaseProduct('configurablegrid');  



            $model = $this->loadModel($id,'is_deleted = 0 AND (status = 0 OR status = 1)');
            if(!empty($model)){
                $reset = 0;




                if(isset($_POST['BaseProduct'])){ 




                    try{
                        $oldConfigurableIds = trim($_POST['oldIds'],',');
                        $newConfigurableIds = trim($_POST['selectedIds'],',');
                        if($model->updateConfigurations($newConfigurableIds,$oldConfigurableIds)){

                            Yii::app()->user->setFlash('success','Changes Saved Successfully');
                        }else{

                            Yii::app()->user->setFlash('error','Changes Not Saved');
                        }
                    }catch(Exception $e){
                        Yii::app()->user->setFlash('error','Changes Not Saved. '.$e->getMessage());
                    }

                    $this->redirect(array('createconfigurable','id'=>$id,'category_id'=>$category_id));
                    return;
                }



                $baseProductModel=new BaseProduct();
                $baseProductModel->unsetAttributes();
                $baseProductModel->setAttribute('is_deleted','=0');
                $baseProductModel->setAttribute('status','<2');
                //$baseProductModel->setAttribute('base_product_id','<>'.$id);
                if(isset($_GET['BaseProduct'])) {
                    $baseProductModel->attributes=$_GET['BaseProduct'];
                    $baseProductModel->base_product_id = $_GET['BaseProduct']['base_product_id'];
                }       





                $this->render('createconfigurable',array(
                'model'=>$model,
                'base_product_model'=>$baseProductModel,
                'cat_base_product_ids'=>$cat_base_product_ids,
                'base_product_id'=>$id,
                'reset'=>$reset,
                'category_id'=>$category_id,
                ));
            }else{
                throw new CHttpException(401,'Invalid Base Product');
            }
        }else{
            throw new CHttpException(401,'Invalid Base Product');
        }
    }



}
