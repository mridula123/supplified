<?php

class OrderLineController extends Controller
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
        'actions'=>array('create','update'),
        'users'=>array('@'),
        ),
        array('allow', // allow admin user to perform 'admin' and 'delete' actions
        'actions'=>array('admin','delete'),
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
        $model=new OrderLine;

        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);

        if(isset($_POST['OrderLine']))
        {
            $model->attributes=$_POST['OrderLine'];
            if($model->save())
                $this->redirect(array('view','id'=>$model->id));
        }

        $this->render('create',array(
        'model'=>$model,
        ));
    }

    /**
    * Updates a particular model.
    * If update is successful, the browser will be redirected to the 'view' page.
    * @param integer $id the ID of the model to be updated
    */
    public function actionUpdate() 
    {    
    	
    	
   
        if(isset($_REQUEST['id']))
        {

            foreach($_REQUEST['id']  as $key=>$value)
            {
                $model=$this->loadModel($_REQUEST['id'][$key]); 


                $_POST['OrderLine']['status']=$_REQUEST['status'][$key] ;
                $_POST['yt0'] ='Save';             
                $model->attributes=  $_POST['OrderLine'];                 
                $model->save();
				//SMS START
				$connection = Yii::app()->dbadvert;
                $sql = "SELECT * FROM `order_header` WHERE order_id='".$_REQUEST['order_id']."'";
                $command = $connection->createCommand($sql);
                $row = $command->queryAll();
				
                $orderNo = $row[0]['transaction_id'];
				$billing_phone = $row[0]['billing_phone'];

				if($_REQUEST['status'][$key] =='1'){
					$HRSVal="24";
					$smstxt = urlencode("Your Supplified Order No".$orderNo."is ready for shipment & will be shipped any time within".$HRSVal." hrs.");
				}
				/*
				if($_REQUEST['status'][$key] =='2'){
					
				}
				if($_REQUEST['status'][$key] =='3'){
					
				}
				*/
				if($_REQUEST['status'][$key] =='4'){
					$smstxt = urlencode("Your Supplified Order No ".$orderNo." is cancelled. If you have already paid, refund will be initiated shortly. Check emails for more details.");
				}

				$url = "http://bulksmsindia.mobi/sendurlcomma.aspx?user=20074197&pwd=suppl123&senderid=SUPTRX&mobileno=".$billing_phone."&msgtext=".$smstxt;
				
				$ch = curl_init($url);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				$curl_scraped_page = curl_exec($ch);
				curl_close($ch);
				//SMS END
				
				
				
				$this->Sendemail($_REQUEST['status'][$key],$_REQUEST['order_id']);
				
                //................send Sms......................//
                $this->SendSms($_REQUEST['id'][$key],$_REQUEST['order_id']);

            }
        }                                              
        Yii::app()->user->setFlash('success', 'Update and Sms Send Successfully.');  
        $this->redirect(array('OrderHeader/update', 'id' => $_REQUEST['order_id']));        
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
        $dataProvider=new CActiveDataProvider('OrderLine');
        $this->render('index',array(
        'dataProvider'=>$dataProvider,
        ));
    }

    /**
    * Manages all models.
    */
    public function actionAdmin()
    {

        $model=new OrderLine('search');
        $model->unsetAttributes();  // clear any default values
        if(isset($_GET['OrderLine']))
            $model->attributes=$_GET['OrderLine'];

        $this->render('admin',array(
        'model'=>$model,
        ));
    }

    /**
    * Returns the data model based on the primary key given in the GET variable.
    * If the data model is not found, an HTTP exception will be raised.
    * @param integer $id the ID of the model to be loaded
    * @return OrderLine the loaded model
    * @throws CHttpException
    */
    public function loadModel($id)
    {
        $model=OrderLine::model()->findByPk($id);
        if($model===null)
            throw new CHttpException(404,'The requested page does not exist.');
        return $model;
    }

    /**
    * Performs the AJAX validation.
    * @param OrderLine $model the model to be validated
    */
    protected function performAjaxValidation($model)
    {
        if(isset($_POST['ajax']) && $_POST['ajax']==='order-line-form')
        {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
    }

    protected function SendSms($ordersub_Id,$orderId)
    {
        $mobile='';
        $model=OrderLine::model()->findByPk($ordersub_Id);
        $modelorderheader=OrderHeader::model()->findByPk($orderId); 

        $mobile=$modelorderheader->attributes['billing_phone'];

        if($model->attributes['status']=='Processing')
        {         
            $msg="Hi, your Supplified order No".$model->attributes['order_id'].",has been successfully placed. Order will be confirmed soon. Check mails for details. Thank you for shopping at Supplified.";
        }
        elseif($model->attributes['status']=='Packaging')
        {         
            $msg="Your Supplified Order No%".$model->attributes['order_id']."%is ready for shipment & will be shipped any time within%24%hrs.";
        }
        elseif($model->attributes['status']=='Out for Delivery')
        {         
            $msg="Your Supplified Order No%".$model->attributes['order_id']."%has been shipped. Tracking ID is%ABH2%and will reach you on or before%%";
        }
        elseif($model->attributes['status']=='Cancelled')
        {         
            $msg="Your Supplified Order No%".$model->attributes['order_id']."%is cancelled. If you have already paid, refund will be initiated shortly. Check emails for more details.";
        }

        $SMS_Url="http://bulksmsindia.mobi/sendurlcomma.aspx?user=20074197&pwd=ngiurg&senderid=SUPTRX&mobileno=".$mobile."&msgtext=".urlencode($msg)."&smstype=0";

        /*$curl = curl_init($SMS_Url);
        curl_setopt($curl, CURLOPT_URL, $SMS_Url);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        $data = curl_exec($curl);
        curl_close($curl);   */
        
       

    }
    
    
    protected function Sendemail($orderstatusID,$orderId)
    {
    	 
    	
    	$mobile='';
    	$model=OrderLine::model()->findByPk($ordersub_Id);
    	$modelorderheader=OrderHeader::model()->findByPk($orderId);
    
    	$billingName=$modelorderheader->attributes['billing_name'];
    	$billingEmail=$modelorderheader->attributes['billing_email'];
    	
    	$BaseUrl=Yii::app()->getBaseUrl(true);
    	$BaseUrlNew = explode('/', $BaseUrl);
    	if($durl='demo'){
    		$rutUrl= 'http://'.$Burl.'/'.$durl;
    	}else{
    		$rutUrl= 'http://'.$Burl;
    	}
    	
    	if($orderstatusID=='1')
    	{
    		$emailsubject='Supplified Order Processing';
    		$mailText='<table width="700" style=" border:1px solid #ccc; font-size:13px; font-family:Arial, Helvetica, sans-serif; margin:auto; padding:0 20px;">
	<tr>
    	<td style="float:left; margin:40px 0 30px 0;"><img src="'.$rutUrl.'/assets/appearance/home/supplified.png" width="270"></td>
    </tr>
    <tr>
    	<td style="width:450px; float:left; padding:10px; background:#035898; font-size:20px; color:#fff;">&nbsp;</td>
    </tr>
    <tr>
    	<td style="color:#035898; font-size:20px; margin-top:20px; float:left;">&nbsp;</td>
    </tr>
    <tr>
    	<td style="margin-top:20px; float:left;">Dear, ' . $billingName . '</td>
    </tr>
    <tr>
    	<td style="margin-top:20px; float:left;">Thank you for your order!</td>
    </tr>
    <tr>
    	<td style="margin-top:20px; float:left;">We will send you an email once the items have been shipped out.  Meanwhile, you can check the status of your order on</td>
    </tr>
    
    <tr>
    	<td style="margin-top:20px; float:left; background-color:#FFFF00;">Will be delivered by delivery date</td>
    </tr>
    <tr>
    	<td style="margin-top:20px; float:left;">Please find below, the summary for your order ()</td>
    </tr>
    <tr>
    	<td style="margin-top:20px; float:left;">
        	
    <tr>
    	<td style="margin-top:20px; float:left;">Best Regards,</td>
    </tr>
    <tr>
    	<td style="margin-top:20px; float:left;">Team Supplified</td>
    </tr>
    <tr>
    	<td style="margin:20px 0; float:left;"><a href="http://www.supplified.com/" ><img src="'.$rutUrl.'/assets/appearance/home/supplified.png" width="270"></a></td>
    </tr>
</table>';
    	}
    	elseif($orderstatusID=='2')
    	{
    		$emailsubject='Supplified Order Packaging';
    		$mailText='<table width="700" style=" border:1px solid #ccc; font-size:13px; font-family:Arial, Helvetica, sans-serif; margin:auto; padding:0 20px;">
	<tr>
    	<td style="float:left; margin:40px 0 30px 0;"><img src="'.$rutUrl.'/assets/appearance/home/supplified.png" width="270"></td>
    </tr>
    <tr>
    	<td style="width:450px; float:left; padding:10px; background:#035898; font-size:20px; color:#fff;">&nbsp;</td>
    </tr>
    <tr>
    	<td style="color:#035898; font-size:20px; margin-top:20px; float:left;">&nbsp;</td>
    </tr>
    <tr>
    	<td style="margin-top:20px; float:left;">Dear, ' . $billingName . '</td>
    </tr>
    <tr>
    	<td style="margin-top:20px; float:left;">Thank you for your order!</td>
    </tr>
    <tr>
    	<td style="margin-top:20px; float:left;">We will send you an email once the items have been shipped out.  Meanwhile, you can check the status of your order on</td>
    </tr>
    
    <tr>
    	<td style="margin-top:20px; float:left; background-color:#FFFF00;">Will be delivered by delivery date</td>
    </tr>
    <tr>
    	<td style="margin-top:20px; float:left;">Please find below, the summary for your order ()</td>
    </tr>
    <tr>
    	<td style="margin-top:20px; float:left;">
        	
    <tr>
    	<td style="margin-top:20px; float:left;">Best Regards,</td>
    </tr>
    <tr>
    	<td style="margin-top:20px; float:left;">Team Supplified</td>
    </tr>
    <tr>
    	<td style="margin:20px 0; float:left;"><a href="http://www.supplified.com/" ><img src="'.$rutUrl.'/assets/appearance/home/supplified.png" width="270"></a></td>
    </tr>
</table>';
    	}
    	elseif($orderstatusID=='3')
    	{
    		$emailsubject='Supplified Order Out for Delivery';
    		$mailText='<table width="700" style=" border:1px solid #ccc; font-size:13px; font-family:Arial, Helvetica, sans-serif; margin:auto; padding:0 20px;">
	<tr>
    	<td style="float:left; margin:40px 0 30px 0;"><img src="'.$rutUrl.'/assets/appearance/home/supplified.png" width="270"></td>
    </tr>
    <tr>
    	<td style="width:450px; float:left; padding:10px; background:#035898; font-size:20px; color:#fff;">&nbsp;</td>
    </tr>
    <tr>
    	<td style="color:#035898; font-size:20px; margin-top:20px; float:left;">&nbsp;</td>
    </tr>
    <tr>
    	<td style="margin-top:20px; float:left;">Dear, ' . $billingName . '</td>
    </tr>
    <tr>
    	<td style="margin-top:20px; float:left;">Thank you for your order!</td>
    </tr>
    <tr>
    	<td style="margin-top:20px; float:left;">We will send you an email once the items have been shipped out.  Meanwhile, you can check the status of your order on</td>
    </tr>
    
    <tr>
    	<td style="margin-top:20px; float:left; background-color:#FFFF00;">Will be delivered by delivery date</td>
    </tr>
    <tr>
    	<td style="margin-top:20px; float:left;">Please find below, the summary for your order ()</td>
    </tr>
    <tr>
    	<td style="margin-top:20px; float:left;">
        	
    <tr>
    	<td style="margin-top:20px; float:left;">Best Regards,</td>
    </tr>
    <tr>
    	<td style="margin-top:20px; float:left;">Team Supplified</td>
    </tr>
    <tr>
    	<td style="margin:20px 0; float:left;"><a href="http://www.supplified.com/" ><img src="'.$rutUrl.'/assets/appearance/home/supplified.png" width="270"></a></td>
    </tr>
</table>';
    	}
    	elseif($orderstatusID=='4')
    	{
    		$emailsubject='Supplified Order Cancelled';
    		$mailText='<table width="700" style=" border:1px solid #ccc; font-size:13px; font-family:Arial, Helvetica, sans-serif; margin:auto; padding:0 20px;">
	<tr>
    	<td style="float:left; margin:40px 0 30px 0;"><img src="'.$rutUrl.'/assets/appearance/home/supplified.png" width="270"></td>
    </tr>
    <tr>
    	<td style="width:450px; float:left; padding:10px; background:#035898; font-size:20px; color:#fff;">&nbsp;</td>
    </tr>
    <tr>
    	<td style="color:#035898; font-size:20px; margin-top:20px; float:left;">&nbsp;</td>
    </tr>
    <tr>
    	<td style="margin-top:20px; float:left;">Dear, ' . $billingName . '</td>
    </tr>
    <tr>
    	<td style="margin-top:20px; float:left;">Thank you for your order!</td>
    </tr>
    <tr>
    	<td style="margin-top:20px; float:left;">We will send you an email once the items have been shipped out.  Meanwhile, you can check the status of your order on</td>
    </tr>
    
    <tr>
    	<td style="margin-top:20px; float:left; background-color:#FFFF00;">Will be delivered by delivery date</td>
    </tr>
    <tr>
    	<td style="margin-top:20px; float:left;">Please find below, the summary for your order ()</td>
    </tr>
    <tr>
    	<td style="margin-top:20px; float:left;">
        	
    <tr>
    	<td style="margin-top:20px; float:left;">Best Regards,</td>
    </tr>
    <tr>
    	<td style="margin-top:20px; float:left;">Team Supplified</td>
    </tr>
    <tr>
    	<td style="margin:20px 0; float:left;"><a href="http://www.supplified.com/" ><img src="'.$rutUrl.'/assets/appearance/home/supplified.png" width="270"></a></td>
    </tr>
</table>';
    	}
    	
    	Yii::import('application.extensions.phpmailer.JPhpMailer');
    	$mailer = new JPhpMailer();
    	$mailer->IsSMTP();
    	$mailer->Host = 'ssl://smtp.gmail.com';
    	$mailer->SMTPAuth = TRUE;
    	$mailer->Port='465';
    	$mailer->Username = 'no-reply@supplified.com';  
    	$mailer->Password = 'supplified@123';  
    	$mailer->From = 'no-reply@supplified.com';  
    	$mailer->FromName = $emailsubject;
    	$mailer->AltBody = 'To view the message, please use an HTML compatible email viewer!';
    	$mailer->MsgHTML($mailText);
    	$mailer->Subject = $emailsubject;
    	$mailer->AddAddress($billingEmail);
    	$mailer->Send();
    	
    	
    	
    	
    	 
    }

  

}
