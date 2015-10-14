<?php

class OrderHeaderController extends Controller
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
     
	public $layout='//layouts/column2';

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
				'actions'=>array(''),
				'users'=>array('*'),
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('create','update','invoice'),
				'users'=>array('@'),
			),
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array('admin','delete','invoice'),
				'users'=>array('admin'),
			),
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
		$model=new OrderHeader;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['OrderHeader']))
		{
			$model->attributes=$_POST['OrderHeader'];
			if($model->save())
				$this->redirect(array('view','id'=>$model->order_id));
		}

		$this->render('create',array(
			'model'=>$model,
		));
	}
	
	
	
	public function actionInvoice()
	{
	
		//http://yiibook.blogspot.in/2012/10/html-to-pdf-in-yii-framework.html
	
		echo $this->renderPartial('invoice');
	
		/*
			ob_start();
			echo $this->renderPartial('invoice');
			$content = ob_get_clean();
	
			Yii::import('application.extensions.tcpdf.HTML2PDF');
			try
			{
			$html2pdf = new HTML2PDF('P', 'A4', 'en');
			//$html2pdf->setModeDebug();
			$html2pdf->setDefaultFont('Arial');
			$html2pdf->writeHTML($content,false);
			$html2pdf->Output("pdfdemo.pdf");
	
			}
			catch(HTML2PDF_exception $e) {
			echo $e;
			exit;
			}
		*/
	
	
	}
	

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id)
	{
		
         
        // $model=OrderLine::model()->getOrderById($id);
         
         
        // print_r($model);
        //    die;
          $model=OrderLine::model()->findAllByAttributes(array('order_id'=>$id));
          $modelOrder = $this->loadModel($id);

          
          
         
          
         // $model1= new OrderLine; 
            if(isset($_POST['Update']))
            {
               
         
              $this->redirect(array('OrderLine/update', 'id' => $_POST['id'],'order_id' => $_POST['order_id'],'status' => $_POST['Status']));        
            }
         
            
          
            
   

		$this->render('update',array(
			'model'=>$model,
            'modelOrder'=>$modelOrder,
            
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
		$dataProvider=new CActiveDataProvider('OrderHeader');
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		
		if(isset($_REQUEST['OrderHeader']['year_id'])){
			if($_REQUEST['yt1'] == 'Create CSV'){
		
				$year=$_REQUEST['OrderHeader']['year_id'];
				$month=$_REQUEST['OrderHeader']['month_id'];
				$day=$_REQUEST['OrderHeader']['day_id'];
				$monthn=str_pad($month, 2, "0", STR_PAD_LEFT);
				$dayn=str_pad($day, 2, "0", STR_PAD_LEFT);
				$finddmy=$year.'-'.$monthn.'-'.$dayn;
		
				$yearto=$_REQUEST['OrderHeader']['year_idto'];
				$monthto=$_REQUEST['OrderHeader']['month_idto'];
				$dayto=$_REQUEST['OrderHeader']['day_idto'];
				$monthton=str_pad($monthto, 2, "0", STR_PAD_LEFT);
				$dayton=str_pad($dayto, 2, "0", STR_PAD_LEFT);
		
				$findtodmy=$yearto.'-'.$monthton.'-'.$dayton;
		
				$model=new OrderHeader();
				$csvData=$model->getcsvdateRange($finddmy,$findtodmy);
				
				
				
					
			}
		}
		
		
		
		$model=new OrderHeader('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['OrderHeader']))
			$model->attributes=$_GET['OrderHeader'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return OrderHeader the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model=OrderHeader::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param OrderHeader $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='order-header-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
	
	
	public function actionExport($id){
	
	
	
		$defaultFieldsDW = array(
				"order_id","order_number","user_id","created_date","payment_status","billing_name",
				"billing_phone","billing_email","billing_address","billing_state","billing_city",
				"billing_pincode","shipping_name","shipping_phone","shipping_email",
				"shipping_address","shipping_state","shipping_city","shipping_pincode"
		);
		
		
		$queryField = array(
				"base_product_id","title","small_description","description","brand","brand_id",
				"model_name","model_number","manufacture","manufacture_country","manufacture_year",
				"key_features","status","product_content_type",
				"ISBN","product_shipping_charge","specifications","moq","VAT"
		);
	
		$varientqueryField = array(
				"subscribed_product_id","variant_on","store_id","store_price","store_offer_price","color",
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
				$CSV_DATA_ARR['key_features'] 		= $data_arr['key_features'];
				$CSV_DATA_ARR['status'] 			= $data_arr['status'];
				$CSV_DATA_ARR['categoryIds'] 			 = $cat_str;
				$CSV_DATA_ARR['product_content_type'] 	 = $data_arr['product_content_type'];
				$CSV_DATA_ARR['ISBN'] 					 = $data_arr['ISBN'];
				$CSV_DATA_ARR['product_shipping_charge'] = $data_arr['product_shipping_charge'];
				$CSV_DATA_ARR['specifications'] 		 = $data_arr['specifications'];
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
