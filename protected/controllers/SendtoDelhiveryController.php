<?php
class SendtoDelhiveryController extends Controller {
	
	/**
    * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
    * using two-column layout. See 'protected/views/layouts/column2.php'.
    */

    public $layout = '//layouts/column1';
    public $image;
    public $base_product_id;

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
        'actions' => array('admin'),
        'users' => array('@'),
        ),
        array('allow', // allow admin user to perform 'admin' and 'delete' actions
        'actions' => array('admin'),
        'users' => array('admin'),
        ),
        array('deny', // deny all users
        'users' => array('*'),
        ),
        );
    }

	public function actionAdmin() {
       $model = new BaseProduct('search');
        $model->unsetAttributes();  
        if (isset($_GET['BaseProduct']))
            $model->attributes = $_GET['BaseProduct'];
        $this->render('admin', array('model' => $model,));
	}

	public function actionGotodelhivery()
	{
		$idList = Yii::app()->request->getParam('idList');
		$allIds = (is_array($idList)) ? implode(",",$idList) : $idLsit;
		
		//$sql = "DELETE FROM category WHERE id IN ($allIds)";
		//$cmd = Yii::app()->db->createCommand($sql);
		//$cmd->execute();
		//$this->actionIndex();
		$connection = Yii::app()->db;
		//$sql = "SELECT * FROM base_product WHERE base_product_id IN ($allIds)";
		$sql ="SELECT bp.base_product_id, bp.title, bp.SKUCode, sp.store_offer_price, sp.store_price, sp.base_product_id  FROM base_product bp INNER JOIN subscribed_product sp ON bp.base_product_id = sp.base_product_id WHERE bp.base_product_id IN ($allIds)";
		$command = $connection->createCommand($sql);
        $rows = $command->queryAll();
		foreach($rows as $row)
		{
			$productID=$row['base_product_id'];
			$productName=$row['title'];
			$productSku=$row['SKUCode'];
			$storePrice=$row['store_price'];
			$storeOfferPrice=$row['store_offer_price'];
		}
		$storePriceround = number_format(round($storePrice, 2), 2);
		$storeOfferPriceround = number_format(round($storeOfferPrice, 2), 2);
		
		//$token = "d101f59b57868baf874480ae36bb116067eeeb63";
		$url = "https://stg2-godam.delhivery.com/pcm/api/create/?client_store=Supplified&fulfillment_center=DELFC2&version=2014.09";
		$data = array(
				array('product_number' => $productID,'sku'=> $productSku,'name'=>$productName,'supplier_id'=>'','length'=>'','width'=>'','height'=>'','weight'=>'',),
				$b['Extra']=array('category'=>'','catalogue'=>'','offer'=>'','description'=>'','reject_duration'=>'','mrp_required'=>'','expiry_date_required'=>'','imei_required'=>'','url'=>'')
		);
		//$data = 'json'; 
		$data = json_encode($data);
		
		print_r($data);
		exit;
		$header[] = "Accept:application/json";
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		$result = curl_exec($ch);
		
		curl_close($ch);
		
		
	}
}
?>