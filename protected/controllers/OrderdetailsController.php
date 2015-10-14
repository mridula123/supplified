<?php
class OrderdetailsController extends Controller 
{
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
		        array('allow', 
		        	'actions'=>array(''),
					'users'=>array('*'),
		        ),
		        array('allow', 
		        	'actions' => array('admin','changedstatus'),
		        	'users' => array('@'),
		        ),
		        array('allow', 
		        	'actions' => array('admin','changedstatus'),
		        	'users' => array('admin','changedstatus'),
		        ),
		        array('deny',  
		        	'users' => array('*'),
		        ),
        );
    }

	public function actionAdmin() {
       $model = new OrderLine('search');
       $model->unsetAttributes();
       if(isset($_GET['OrderLine']))
       $model->attributes=$_GET['OrderLine'];
       $this->render('admin', array('model' => $model,));
	}
	 
	public function convert_number_to_words($number) {
		//http://www.karlrixon.co.uk/writing/convert-numbers-to-words-with-php/ 
		$hyphen      = '-';
		$conjunction = ' and ';
		$separator   = ', ';
		$negative    = 'negative ';
		$decimal     = ' point ';
		$dictionary  = array(
				0                   => 'zero',
				1                   => 'one',
				2                   => 'two',
				3                   => 'three',
				4                   => 'four',
				5                   => 'five',
				6                   => 'six',
				7                   => 'seven',
				8                   => 'eight',
				9                   => 'nine',
				10                  => 'ten',
				11                  => 'eleven',
				12                  => 'twelve',
				13                  => 'thirteen',
				14                  => 'fourteen',
				15                  => 'fifteen',
				16                  => 'sixteen',
				17                  => 'seventeen',
				18                  => 'eighteen',
				19                  => 'nineteen',
				20                  => 'twenty',
				30                  => 'thirty',
				40                  => 'fourty',
				50                  => 'fifty',
				60                  => 'sixty',
				70                  => 'seventy',
				80                  => 'eighty',
				90                  => 'ninety',
				100                 => 'hundred',
				1000                => 'thousand',
				1000000             => 'million',
				1000000000          => 'billion',
				1000000000000       => 'trillion',
				1000000000000000    => 'quadrillion',
				1000000000000000000 => 'quintillion'
		);
		 
		if (!is_numeric($number)) {
			return false;
		}
		 
		if (($number >= 0 && (int) $number < 0) || (int) $number < 0 - PHP_INT_MAX) {
			// overflow
			trigger_error(
					'convert_number_to_words only accepts numbers between -' . PHP_INT_MAX . ' and ' . PHP_INT_MAX,
					E_USER_WARNING
			);
			return false;
		}
	
		if ($number < 0) {
			return $negative . convert_number_to_words(abs($number));
		}
		 
		$string = $fraction = null;
		 
		if (strpos($number, '.') !== false) {
			list($number, $fraction) = explode('.', $number);
		}
		 
		switch (true) {
			case $number < 21:
				$string = $dictionary[$number];
				break;
			case $number < 100:
				$tens   = ((int) ($number / 10)) * 10;
				$units  = $number % 10;
				$string = $dictionary[$tens];
				if ($units) {
					$string .= $hyphen . $dictionary[$units];
				}
				break;
			case $number < 1000:
				$hundreds  = $number / 100;
				$remainder = $number % 100;
				$string = $dictionary[$hundreds] . ' ' . $dictionary[100];
				if ($remainder) {
					$string .= $conjunction . convert_number_to_words($remainder);
				}
				break;
			default:
				$baseUnit = pow(1000, floor(log($number, 1000)));
				$numBaseUnits = (int) ($number / $baseUnit);
				$remainder = $number % $baseUnit;
				$string = convert_number_to_words($numBaseUnits) . ' ' . $dictionary[$baseUnit];
				if ($remainder) {
					$string .= $remainder < 100 ? $conjunction : $separator;
					$string .= convert_number_to_words($remainder);
				}
				break;
		}
		 
		if (null !== $fraction && is_numeric($fraction)) {
			$string .= $decimal;
			$words = array();
			foreach (str_split((string) $fraction) as $number) {
				$words[] = $dictionary[$number];
			}
			$string .= implode(' ', $words);
		}
		 
		return $string;
	}
	
	public function actionPurchaseorder(){
		
		$autoId = Yii::app()->request->getParam('autoId');
		$allIds = (is_array($autoId)) ? implode(",",$autoId) : $autoId;
		$models = OrderLine::model()->getFindOrderline($allIds);
		
		$this->render('index',array(
				'models'=>$models,
		));
		
		/*
		
		foreach ($models as $model){
			$orderId=$model['order_id'];
			$subscribed_product_id=$model['subscribed_product_id'];
		}
		$orderdetails = OrderHeader::model()->getOrderNumber($orderId);
		
		$connection = Yii::app()->db;
		$sql ="SELECT sp.*, st.* FROM subscribed_product sp INNER JOIN store st ON st.store_id = sp.store_id  WHERE  sp.subscribed_product_id='".$subscribed_product_id."'";
		$command  = $connection->createCommand($sql)->queryAll();
		
		
		//$modelsPurchase = OrderLine::model()->getFindOrderlinePurchase($allIds);
		//$modelsInvoice = OrderLine::model()->getFindOrderlineInvoice($allIds);
		
		
		$stop_date = date('Y-m-d');
		$stop_date = date('d-M-Y', strtotime($stop_date . ' +7 day'));
		$povalideday=$stop_date;
		
				
		$mailpurchase='<table width="100%" cellspacing="0" cellpadding="0" border="0" align="center" style="font-size:12px; font-family:Arial, Helvetica, sans-serif; margin:auto; padding:0 20px; max-width:730px">
		<tr>
		<td align="center" colspan="2"><img alt="Manor cart Po" src="http://supplified.com/assets/appearance/home/manorcart.jpg" align="left"></td>
		<td align="center" colspan="2">
				<span style="font-size: 10pt;font-weight: bold;">Ordered through:</span>
				<img alt="Manor cart Po" src="http://supplified.com/assets/appearance/home/image00.png" align="right">
				<p><span>Customer Care 999999995, </span><span style="color:#0563c1;text-decoration:underline;"><a href="http://www.supplified.com" style="color:inherit;text-decoration: inherit;">www.supplified.com</a></span></p>
				</td>
		</tr>
		<tr>
		<td height="20px" colspan="4"></td>
		</tr>
		<tr>
		<td align="center" bgcolor="#0047A1" colspan="4" style="padding-left:5px;padding-bottom:3px;color:#FFFFFF;font-weight:bold">PURCHASE ORDER</td>
		</tr>
		<tr>
		<td height="20px" colspan="4"></td>
		</tr>
		<tr>
		<td>Order Ref. No.</td>
		<td>'.$orderdetails[0]['order_number'].'</td>
		<td>Pickup Date</td>
		<td>'.date('d-M-Y').'</td>
		</tr>
		<tr>
		<td>PO No.</td>
		<td>Scom/15-16/'.$orderId.'</td>
		<td>Pickup Time (Approx.)</td>
		<td>1200 - 1400 hrs.</td>
		</tr>
		<tr>
		<td>PO Date</td>
		<td>'.date('d-M-Y').'</td>
		<td>PO Valid till</td>
		<td>'.$povalideday.'</td>
		</tr>
		<tr>
		<td height="20px" colspan="4"></td>
		</tr>
		<tr>
		<td colspan="2" bgcolor="#0047A1" style="padding-left:5px;padding-bottom:3px;color:#FFFFFF;font-weight:bold">PURCHASER</td>
		<td colspan="2" bgcolor="#0047A1" style="padding-left:5px;padding-bottom:3px;color:#FFFFFF;font-weight:bold">SUPPLIER</td>
		</tr>
		
	<tr>
	  	<td colspan="2">Manor Kart Retail Private Limited</td>
	  	<td colspan="2">'.$command[0]['seller_name'].'</td>
	  </tr>
		
	  <tr>
	  	<td colspan="2">Regd. Office: K-3/40-A West Ghonda, Shahdara</td>
	  	<td colspan="2">'.$command[0]['business_address'].','.$command[0]['business_address_country'].'</td>
	  </tr>
		
	  <tr>
	  	<td colspan="2">New Delhi</td>
	  	<td colspan="2">'.$command[0]['business_address_state'].'</td>
	  </tr>
		
	  <tr>
	  	<td colspan="2">Pin Code - 110053</td>
	  	<td colspan="2">Pin Code -'.$command[0]['business_address_pincode'].'</td>
	  </tr>
		
		
		<tr>
		<td>TIN Number</td>
		<td>07766980433</td>
		<td>TIN Number</td>
		<td>'.$command[0]['tin'].'</td>
		</tr>
		<tr>
		<td>PAN Number</td>
		<td>AAJCM8498G</td>
		<td>PAN Number</td>
		<td>'.$command[0]['pan'].'</td>
		</tr>
		<tr>
		<td>Contact / Mobile No.</td>
		<td></td>
		<td>Contact / Mobile No.</td>
		<td>'.$command[0]['con_per_mobile'].'</td>
		</tr>
		<tr>
		<td>Contact / Mobile No.</td>
		<td></td>
		<td>Landline No.</td>
		<td>'.$command[0]['telephone_numbers'].'</td>
		</tr>
		<tr>
		<td>Email:</td>
		<td></td>
		<td>Email:</td>
		<td>'.$command[0]['con_per_email'].'</td>
		</tr>
				
		<tr>
		<td height="20px" colspan="4"></td>
		</tr>	
					
		<tr>
		<td colspan="2" bgcolor="#0047A1" style="padding-left:5px;padding-bottom:3px;color:#FFFFFF;font-weight:bold">Ship To / Delivery Address:</td>
		<td colspan="2" bgcolor="#0047A1" style="padding-left:5px;padding-bottom:3px;color:#FFFFFF;font-weight:bold">Bank / RTGS / IFSC - Particulars:</td>
		</tr>
		<tr>
		<td>Not Applicable (Self Pickup)</td>
		<td></td>
		<td>Bank Name</td>
		<td>'.$command[0]['bank_name'] .'</td>
		</tr>
		
		<tr>
		<td></td>
		<td></td>
		<td>Account Number</td>
		<td>'.$command[0]['ac_number'] .'</td>
		</tr>
		
		<tr>
		<td></td>
		<td></td>
		<td>Branch Name</td>
		<td></td>
		</tr>
		
		<tr>
		<td></td>
		<td></td>
		<td>Branch Code</td>
		<td>'.$command[0]['branch_code'].'</td>
		</tr>
		<tr>
		<td></td>
		<td></td>
		<td>IFSC Code</td>
		<td>'.$command[0]['ifsc_code'].'</td>
		</tr>
		<tr>
		<td></td>
		<td></td>
		<td>RTGS Code</td>
		<td>'.$command[0]['rtgs_code'].'</td>
		</tr>
		
		<tr>
		<td height="20px" colspan="4"></td>
		</tr>
		<tr>
		<td align="center" colspan="4" bgcolor="#0047A1" style="padding-left:5px;padding-bottom:3px;color:#FFFFFF;font-weight:bold">ORDER DETAILS</td>
		</tr>		
		<tr>
		<td colspan="4" >
		
					
		<table width="100%" border="1" cellpadding="1" cellspacing="0" style="border:1px solid #ccc;font-size:11px; font-family:Arial, Helvetica, sans-serif; margin:auto; padding:0 20px;max-width:800px">
		<tr>
		<th style="text-align:left;">S.No.</th>
		<th style="text-align:left;">Code</th>
		<th style="text-align:left;">Product Desc.</th>
		<th style="text-align:left;">Model No.</th>
		<th style="text-align:left;">Qty</th>
		<th style="text-align:left;">UOM</th>
		<th style="text-align:left;">Unit Rate</th>
		<th style="text-align:left;">Tax Rate</th>
		<th style="text-align:left;">Tax Form</th>
		<th style="text-align:left;">Tax Amount</th>
		<th style="text-align:left;">Net Rate</th>
		<th style="text-align:left;">Amount</th>
		</tr>';
		
		$counter = 1;
		$sum=0;
		foreach ($models as $key => $ordline){
			$baseproId=$ordline['base_product_id'];
			$scribedproductproId=$ordline['subscribed_product_id'];
		
			$connection = Yii::app ()->db;
		
			$sql = "Select * FROM base_product where base_product_id = $baseproId";
			$command = $connection->createCommand($sql);
			$baseproduct=$command->queryAll();
		
			$subscribed_sql = "Select * FROM subscribed_product where subscribed_product_id = $scribedproductproId";
			$command = $connection->createCommand($subscribed_sql);
			$subscribedproduct=$command->queryAll();
		
			$TaxAmount = $ordline['tax'] * $ordline['unit_price'];
			
			$NetRate =$TaxAmount + $ordline['unit_price'];
			
			$total = $NetRate * $ordline['product_qty'];
			
			$sum += $total;
			
			
			 
			
			
		  $mailpurchase.='<tr>
		  	<td style="text-align:right;">'.$counter.'</td>
		  	<td style="text-align:right;">'.$baseproduct[0]['SKUCode'].'</td>
		  	<td style="text-align:right;">'.$baseproduct[0]['title'].' ,'.$subscribedproduct[0]['size'].'</td>
		  	<td style="text-align:right;">'.$baseproduct[0]['model_number'].'</td>
		  	<td style="text-align:right;">'.$ordline['product_qty'].'</td>
		  	<td style="text-align:right;">Nos</td>
		  	<td style="text-align:right;">'.$ordline['unit_price'].'</td>
		  	<td style="text-align:right;">'.$ordline['tax'].'</td>
		  	<td style="text-align:right;">D-VAT</td>
		  	<td style="text-align:right;">'.$TaxAmount.'</td>
		  	<td style="text-align:right;">'.number_format($NetRate, 2).'</td>
		  	<td style="text-align:right;">'.number_format($total, 2).'</td>
		  </tr>';
		  
		  	$counter ++; 
			}
			
			
			
			
		
		   $mailpurchase.='<tr>
		  	<td></td>
		  	<td colspan="7">Sub-total</td>
		  	<td></td>
		  	<td></td>
		  	<td></td>
		  	<td style="text-align: right;">'.number_format($sum, 2).'</td>
		  </tr>
		
		  <tr>
		  	<td></td>
		  	<td colspan="10">Add : Additional Freight</td>
		  	<td>--</td>
		  </tr>
		
		  <tr>
		  	<td></td>
		  	<td colspan="10">Add : Other Charges</td>
		  	<td>--</td>
		  </tr>
		
		  <tr>
		  	<td></td>
		  	<td colspan="10">Total </td>
		  	<td style="text-align: right;">'.number_format($sum, 2).'</td>
		  </tr>
		
		  <tr>
		  	<td></td>
		  	<td colspan="10">Round Off </td>
		  	<td style="text-align: right;">0.00</td>
		  </tr>
		
		  <tr>
		  	<td></td>
		  	<td colspan="10">Total Order Value (Inclusive of all Incidental charges and Taxes) In Rupees</td>
		  	<td style="text-align: right;">'.number_format($sum, 2).'</td>
		  </tr>
		
		
		  <tr>
		  	<td></td>
		  	<td colspan="11">Total Order Value (in words): </td>
		  </tr>
		  			
		   <tr>
			<td height="20px" colspan="12"></td>
		   </tr>
		  </table>			
		  </td>			
		  </tr>
					
		
		  			
		  <tr>
		  <td colspan="4" >						
		 <table width="800"  style="font-size:12px; font-family:Arial, Helvetica, sans-serif; margin:auto; padding:0 20px;"> 			
			<tr>
			<td height="20px" colspan="12"></td>
		   </tr>
		   <tr>
		  	<td></td>
		  	<td colspan="11">TERMS & CONDITIONS:</td>
		  </tr>
		  <tr>
			<td height="20px" colspan="12"></td>
		   </tr>			
		
		   <tr>
		  	<td></td>
		  	<td colspan="11">This Purchase order, together with standard terms and conditions, and any attachments and exhibits, specifications, drawings, notes, instructions and other information, whether physically attached or incorporated by reference (collectively the "Purchase Order"), constitutes the entire & exclusive agreement between ("PURCHASER") & ("SUPPLIER") as identified in this Purchase Order. Notwithstanding the foregoing, if a master agreement covering procurement of the Products / Material / Goods or Work described in this Purchase Order exists between "SUPPLIER" and "PURCHASER", the terms of such master agreement shall prevail over any inconsistent terms herein.</td>
		  </tr>
		
		  <tr>
		  	<td>1</td>
		  	<td colspan="5">Payment terms</td>
		  	<td colspan="5">15 days from date of invoice.</td>
		  </tr>
		
		  <tr>
		  	<td>2</td>
		  	<td colspan="5">Quality Standard</td>
		  	<td colspan="5">Compliance to relevant IS / ASTM Standard</td>
		  </tr>
		
		  <tr>
		  	<td>3</td>
		  	<td colspan="5">Road Permit</td>
		  	<td colspan="5">Not Applicable</td>
		  </tr>
		
		  <tr>
		  	<td>4</td>
		  	<td colspan="5">Test Certificate</td>
		  	<td colspan="5">No</td>
		  </tr>
		
		
		  <tr>
		  	<td>5</td>
		  	<td colspan="5">Warranty / Guarantee</td>
		  	<td colspan="5">As per manufacturer&#39;s warranty policy.</td>
		  </tr>
		
		
		  <tr>
		  	<td>6</td>
		  	<td colspan="10">You shall provide only approved quality of material, any inferior quality provided has to be replaced by you at your own risk and cost.</td>
		  </tr>
		
		  <tr>
		  	<td>7</td>
		  	<td colspan="10">In case you fail to supply the material to our requirement, we reserve all rights to withhold your payment and arrange supply from any other agency at your risk and cost.</td>
		  </tr>
		
		  <tr>
		  	<td>8</td>
		  	<td colspan="10">All disputes are to be settled at Delhi jurisdiction.</td>
		  </tr>
		
		  <tr>
		  	<td>9</td>
		  	<td colspan="10">For billing on local vat, only tax invoice is acceptable</td>
		  </tr>
		
		
		   <tr>
		  	<td>10</td>
		  	<td colspan="10">All the tax liabilities e.g. sales tax, excise duty etc. shall be applicable as per government policy at the time of delivery of material.</td>
		  </tr>
		
		
		  <tr>
		  	<td>11</td>
		  	<td colspan="10">Billing address has to be given on the bill as shown on top of the purchase order.</td>
		  </tr>
		
		  <tr>
		  	<td>12</td>
		  	<td colspan="10">Supplified will return products in all the following cases:-</td>
		  </tr>
		
		  <tr>
		  	<td></td>
		  	<td colspan="10">Size/dimension mismatch in comparison to the ordered materials/products.</td>
		  </tr>
		
		  <tr>
		  	<td></td>
		  	<td colspan="10">Significantly different from the description given by the merchant ( wrong size, color, quality or material related issues)</td>
		  </tr>
		
		  <tr>
		  	<td></td>
		  	<td colspan="10">The packet was empty / some item or accessory was missing</td>
		  </tr>
		
		  <tr>
		  	<td></td>
		  	<td colspan="10">Defective items/malfunctioning materials/products are received.</td>
		  </tr>
		
		  <tr>
		  	<td></td>
		  	<td colspan="10">Not conforming to the specified compliance standard with a test certificate from accredited testing laboratory.</td>
		  </tr>
		
		  <tr>
		  	<td>13</td>
		  	<td colspan="10">Please acknowledge the order by mailing a line of acceptance of the same to support@supplified.com for our record purpose.</td>
		  </tr>
		
		  <tr>
		  	<td>14</td>
		  	<td colspan="10">After acceptance of order the consignment should be kept ready to be picked by our courier partner.</td>
		  </tr>
		
		  <tr>
		  	<td height="100px" colspan="10"></td>
		  </tr>
		
		  <tr>
		  	<td></td>
		  	<td colspan="3" align="center">Prepared by</td>
		  	<td colspan="3" align="center">Checked by</td>
		  	<td colspan="4" align="center">Approved by</td>
		  </tr>
		
		
		  <tr>
		  	<td></td>
		  	<td colspan="3" align="center">(Operation)</td>
		  	<td colspan="3" align="center">(Taxation)</td>
		  	<td colspan="4" align="center">(Operation)</td>
		  </tr>
		
		
		  	</table>
		  	</td>
		  </tr>
		  </table>';
		   $emailsubject='Purchase Order';
		   
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
		   $mailer->MsgHTML($mailpurchase);
		   $mailer->Subject = $emailsubject;
		   $mailer->AddAddress('amit.sinha@supplified.com');
		   $mailer->Send();
		   */
		 
	}
	
	public function ActionInvoicegenerate(){
		
		$autoId = Yii::app()->request->getParam('autoId');
		$allIds = (is_array($autoId)) ? implode(",",$autoId) : $autoId;
		$models = OrderLine::model()->getFindOrderline($allIds);
		
		$myfilename =(is_array($autoId)) ? implode("-invoice-",$autoId) : $autoId;
		
		foreach ($models as $model){
			$orderId=$model['order_id'];
			$subscribed_product_id=$model['subscribed_product_id'];
		}
		
		$modelsInvoice = OrderLine::model()->getFindOrderlineInvoice($allIds);
		
		
		$orderdetails = OrderHeader::model()->getOrderNumber($orderId);
		
		//$modelsInvoice = OrderLine::model()->getFindOrderlineInvoice($allIds);
		
		$invoiceDate= date ('d-M-Y',strtotime($orderdetails[0]['created_date']));
		
		$connection = Yii::app ()->db;
		$sql = "SELECT * FROM order_invoice ORDER BY id DESC";
		$command = $connection->createCommand($sql);
		$orderinvoice=$command->queryAll();
		$Invnumber=$orderinvoice[0]['invoicenumber'];
	    $InvoiceNumber=sprintf("%'.06d\n", $Invnumber);
		
		$mailpurchase = '<table width="100%" cellspacing="0" cellpadding="0" border="0" align="center" style="font-size:12px; font-family:Arial, Helvetica, sans-serif; margin:auto; padding:0 20px; max-width:730px">
<tr>
<td colspan="4" style="direction:ltr;orphans:2;text-align:center;widows:2;color:#000000;font-size:14px;margin:0;font-weight:bold;">Retail/Tax Invoice</td>
</tr>
<tr>
<td height="20px" colspan="4"></td>
</tr>				
<tr>
<td colspan="4" style="color:#000000;font-size:12px;margin:0;font-weight:bold;">Sold By:</td>
</tr>
<tr>
<td colspan="2" style="color:#000000;font-size:12px;margin:0;font-weight:bold;"></td>
<td colspan="2" style="color:#000000;font-size:12px;margin:0;font-weight:bold;"><img alt="Manor cart Po" src="http://supplified.com/assets/appearance/home/manorcart.jpg" align="right"></td>
</tr>
<tr>
<td colspan="2" style="color:#000000;font-size:12px;margin:0;font-weight:bold;">Manor Kart Retail Private Limited</td>
<td colspan="2" style="color:#000000;font-size:12px;margin:0;font-weight:bold;"></td>
</tr>
<tr>
<td colspan="2" style="color:#000000;font-size:12px;margin:0;font-weight:bold;">Regd. Office: K-3/40-A West Ghonda, Shahdara, </td>
<td colspan="2" style="color:#000000;font-size:12px;margin:0;font-weight:bold;"></td>
</tr>
<tr>
<td colspan="2" style="color:#000000;font-size:12px;margin:0;font-weight:bold;">New Delhi-110053</td>
<td colspan="2" style="color:#000000;font-size:12px;margin:0;font-weight:bold;"></td>
</tr>
<tr>
<td colspan="2" style="color:#000000;font-size:12px;margin:0;font-weight:bold;">PAN&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: AAJCM8498G</td>
<td colspan="2" style="color:#000000;font-size:12px;margin:0;font-weight:bold;"></td>
</tr>
<tr>
<td colspan="2" style="color:#000000;font-size:12px;margin:0;font-weight:bold;">VAT/TIN&nbsp;: 07766980433</td>
<td colspan="2" style="color:#000000;font-size:12px;margin:0;font-weight:bold;"> </td>
</tr>								
<tr>
<td colspan="2" height="20px" style="color:#000000;font-size:12px;margin:0;font-weight:bold;">&nbsp;</td>
<td colspan="2" height="20px" style="color:#000000;font-size:12px;margin:0;font-weight:bold;">&nbsp;</td>
</tr>			
<tr>
<td colspan="2" style="color:#000000;font-size:12px;margin:0;font-weight:bold;">Order Number &nbsp;: '.$orderdetails[0]['order_number'].'</td>
<td colspan="2" style="color:#000000;font-size:12px;margin:0;font-weight:bold;">Invoice Number&nbsp;: MKRPL/2015-16/'.$InvoiceNumber.'</td>
</tr>					
<tr>
<td colspan="2" style="color:#000000;font-size:12px;margin:0;font-weight:bold;">Order Date&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: '.$invoiceDate.'</td>
<td colspan="2" style="color:#000000;font-size:12px;margin:0;font-weight:bold;">Invoice Date&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: '.date('d-M-Y').'</td>
</tr>	
<tr>
<td height="20px" colspan="4">&nbsp;</td>
</tr>		
<tr>
<td colspan="2" style="color:#000000;font-size:12px;margin:0;font-weight:bold;">Bill To</td>
<td colspan="2" style="color:#000000;font-size:12px;margin:0;font-weight:bold;">Ship To &nbsp;&nbsp;</td>
</tr>	
<tr>
<td height="20px" colspan="4">&nbsp;</td>
</tr>		
<tr>
<td colspan="2" style="color:#000000;font-size:12px;margin:0;font-weight:bold;">&nbsp;&nbsp;'.$orderdetails[0]['billing_name'].'</td>
<td colspan="2" style="color:#000000;font-size:12px;margin:0;font-weight:bold;">&nbsp;&nbsp;'.$orderdetails[0]['shipping_name'].'</td>
</tr>
<tr>
<td colspan="2" style="color:#000000;font-size:12px;margin:0;font-weight:bold;">&nbsp;&nbsp;'.$orderdetails[0]['billing_address'].'</td>
<td colspan="2" style="color:#000000;font-size:12px;margin:0;font-weight:bold;">&nbsp;&nbsp;'.$orderdetails[0]['shipping_address'].'</td>
</tr>
<tr>
<td colspan="2" style="color:#000000;font-size:12px;margin:0;font-weight:bold;">&nbsp;&nbsp;'. $orderdetails[0]['billing_city'].','.  $orderdetails[0]['billing_state'].'</td>
<td colspan="2" style="color:#000000;font-size:12px;margin:0;font-weight:bold;">&nbsp;&nbsp;'. $orderdetails[0]['shipping_city'].','.  $orderdetails[0]['shipping_state'].'</td>
</tr>
<tr>
<td colspan="2" style="color:#000000;font-size:12px;margin:0;font-weight:bold;">&nbsp;&nbsp;'.$orderdetails[0]['billing_pincode'].'</td>
<td colspan="2" style="color:#000000;font-size:12px;margin:0;font-weight:bold;">&nbsp;&nbsp;'.$orderdetails[0]['shipping_pincode'].'</td>
</tr>
<tr>
<td colspan="2" style="color:#000000;font-size:12px;margin:0;font-weight:bold;">&nbsp;&nbsp;'.$orderdetails[0]['billing_phone'].'</td>
<td colspan="2" style="color:#000000;font-size:12px;margin:0;font-weight:bold;">&nbsp;&nbsp;'.$orderdetails[0]['shipping_phone'].'</td>
</tr>
<tr>
<td height="20px" colspan="4">&nbsp;</td>
</tr>	
<tr>
<td height="20px" colspan="4">&nbsp;</td>
</tr>		
<tr>
<td colspan="4" align="center" colspan="4" bgcolor="#0047A1" style="padding-left:5px;padding-bottom:3px;color:#FFFFFF;font-weight:bold">ORDER DETAILS</td>
</tr>
<tr>
<td colspan="4">
<table width="100%" border="1" cellpadding="1" cellspacing="0" style="border:1px solid #ccc;font-size:11px; font-family:Arial, Helvetica, sans-serif; margin:auto; padding:0 20px;max-width:800px">
	<tr>
		<th style="text-align:left;">S.No.</th>
		<th style="text-align:left;">Description</th>
		<th style="text-align:left;">Qty</th>
		<th style="text-align:left;">Item Price (Rs.)</th>
		<th style="text-align:left;">Tax (%)</th>
		<th style="text-align:left;">Tax Type</th>
		<th style="text-align:left;">Tax Amount</th>
		<th style="text-align:right;">Amount</th>
	</tr>';
	$counter = 1;
				$sum=0;
				$totals=0;
				$taxtotal=0;
				foreach ($models as $key => $ordline){
					$baseproId=$ordline['base_product_id'];
					$scribedproductproId=$ordline['subscribed_product_id'];
				
					$connection = Yii::app ()->db;
				
					$sql = "Select * FROM base_product where base_product_id = $baseproId";
					$command = $connection->createCommand($sql);
					$baseproduct=$command->queryAll();
				
					$subscribed_sql = "Select * FROM subscribed_product where subscribed_product_id = $scribedproductproId";
					$command = $connection->createCommand($subscribed_sql);
					$subscribedproduct=$command->queryAll();
					
					//$total = $subscribedproduct[0]['store_offer_price'] * $ordline['product_qty'];
					//$taxval= $total * $baseproduct[0]['VAT'] / 100 ;
					//$total =$taxval + $subscribedproduct[0]['store_offer_price'];
					
					$taxval =  $subscribedproduct[0]['store_offer_price'] * $baseproduct[0]['VAT'] / 100 ;
					$total = $subscribedproduct[0]['store_offer_price'] * $ordline['product_qty'];
					$totaltax = $taxval * $ordline['product_qty'];
					
					$shippcha=$subscribedproduct[0]['shipping_charges'];
					
					$grandtotal = $totaltax + $total + $shippcha; 
					
					
					
					$totals += $total;
					$sum += $grandtotal;
					$taxtotal += $totaltax;
					
	$mailpurchase .= '<tr>
		<td style="text-align:left;">'.$counter.'</td>
		<td style="text-align:left;">'.$baseproduct[0]['title'].'</td>
		<td style="text-align:left;">'.$ordline['product_qty'].'</td>
		<td style="text-align:left;">'.number_format($subscribedproduct[0]['store_offer_price'], 2).'</td>
		<td style="text-align:left;">'.$baseproduct[0]['VAT'].'%</td>
		<td style="text-align:left;">VAT</td>
		<td style="text-align:left;">'.$taxval.'</td>
		<td style="text-align:right;">'.number_format($total, 2).'</td>
	</tr>';
               
               $counter ++;
				}
$mailpurchase .= '
<tr>
<td style="text-align:left;"></td>
<td  colspan="6" style="text-align:left;">Sub Total</td>
<td style="text-align:right;">'.number_format($totals, 2).'</td>						
</tr>
<tr>
<td style="text-align:left;"></td>
<td  colspan="6" style="text-align:left;">Total Tax Amount</td>
<td style="text-align:right;">'.number_format($taxtotal, 2).'</td>						
</tr>
<tr>
<td style="text-align:left;"></td>
<td  colspan="6" style="text-align:left;">Shipping Charge</td>
<td style="text-align:right;">'.number_format($shippcha, 2).'</td>						
</tr>
<tr>
<td style="text-align:left;"></td>
<td  colspan="6" style="text-align:left;">Grand Total</td>
<td style="text-align:right;">'.number_format($sum, 2).'</td>						
</tr>				
</table></td>
</tr>
<tr>
<td height="20px" colspan="4">&nbsp;&nbsp;</td>
</tr>		
<tr>
<td colspan="4" style="color:#000000;font-size:12px;margin:0;font-weight:bold;">*This is a computer generated Invoice.</td>
</tr>
<tr>
<td style="color:#000000;font-size:12px;margin:0;font-weight:bold;"></td>
<td style="color:#000000;font-size:12px;margin:0;font-weight:bold;"></td>
<td style="color:#000000;font-size:12px;margin:0;font-weight:bold;"></td>
<td style="color:#000000;font-size:12px;margin:0;font-weight:bold;">For Manor Kart Retail Private Limited</td>
</tr>
<tr>
<td style="color:#000000;font-size:12px;margin:0;font-weight:bold;"></td>
<td style="color:#000000;font-size:12px;margin:0;font-weight:bold;"></td>
<td style="color:#000000;font-size:12px;margin:0;font-weight:bold;"></td>
<td style="color:#000000;font-size:12px;margin:0;font-weight:bold;float:right;">(Authorized Signatory)</td>
</tr>
<tr>
<td style="color:#000000;font-size:12px;margin:0;font-weight:bold;"></td>
<td style="color:#000000;font-size:12px;margin:0;font-weight:bold;"></td>
<td style="color:#000000;font-size:12px;margin:0;font-weight:bold;"></td>
<td style="color:#000000;font-size:12px;margin:0;font-weight:bold;">Ordered through:</td>
</tr>
<tr>
<td style="color:#000000;font-size:12px;margin:0;font-weight:bold;"></td>
<td style="color:#000000;font-size:12px;margin:0;font-weight:bold;"></td>
<td style="color:#000000;font-size:12px;margin:0;font-weight:bold;"></td>
<td style="color:#000000;font-size:12px;margin:0;font-weight:bold;"> <img alt="supplified" src="http://supplified.com/assets/appearance/home/supplified.png" style="width: 227.65px; height: 42.37px; margin-left: -0.00px; margin-top: -0.00px; transform: rotate(0.00rad) translateZ(0px); -webkit-transform: rotate(0.00rad) translateZ(0px);" title=""></td>
</tr>
<tr>
<td style="color:#000000;font-size:12px;margin:0;font-weight:bold;"></td>
<td style="color:#000000;font-size:12px;margin:0;font-weight:bold;"></td>
<td style="color:#000000;font-size:12px;margin:0;font-weight:bold;"></td>
<td style="color:#000000;font-size:12px;margin:0;font-weight:bold;">Customer Care 999999995,  <a href="http://www.supplified.com">www.supplified.com</a></td>
</tr>
<tr>
<td colspan="4"  style="color:#000000;font-size:12px;margin:0;font-weight:bold;">To return an item, visit  <a href="http://www.supplified.com">www.supplified.com</a></td>
</tr>
<tr>
<td colspan="4" style="color:#000000;font-size:12px;margin:0;font-weight:bold;">For more information on your order, visit <a href="http://www.supplified.com">www.supplified.com</a></td>
</tr>
</table>';

			$connection = Yii::app ()->db;
			$sql = "SELECT * FROM order_invoice ORDER BY id DESC";
			$command = $connection->createCommand($sql);
			$orderinvoice=$command->queryAll();
			$Invnumber=$orderinvoice[0]['invoicenumber'];
			$invoicenumbernum=$Invnumber + 1;
			$sql = "INSERT INTO order_invoice(invoicenumber) VALUES('".$invoicenumbernum."')";
			$command = $connection->createCommand($sql);
			$command->execute();
               
               $filemydoc = $myfilename.'.doc';
               $fp = fopen($_SERVER['DOCUMENT_ROOT'] . "/admin/invoice/$filemydoc","wb");
               fwrite($fp,$mailpurchase);
               fclose($fp);
	               //email
	               $emailAttachment = ($myfilename.'.doc');
	               $emailAttachment = chunk_split(base64_encode($myfilename.'.doc'));
	               $emailsubject='Invoice';
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
	               $mailer->MsgHTML($mailpurchase);
	               $mailer->Subject = $emailsubject;
	               $mailer->AddAttachment($emailAttachment);
	               $mailer->AddAddress('amit.sinha@supplified.com');
				   $mailer->addBcc('manorkartpurchase@gmail.com');
				   $mailer->addCc('himanshu.singh@supplified.com');
				   $mailer->addCc('ravi.mishra@supplified.com');
	               $file = $_SERVER['DOCUMENT_ROOT'] . "/admin/invoice/$filemydoc";
	               $mailer->AddAttachment( $file, $filemydoc );
	               $mailer->Send();

	               //email	
		
	}
	
	public function actionGenerateword() {
		
		$autoId = Yii::app()->request->getParam('autoId');
		$allIds = (is_array($autoId)) ? implode(",",$autoId) : $autoId;
		$myfilename =(is_array($autoId)) ? implode("-purchase-order-",$autoId) : $autoId;
		
		$models = OrderLine::model()->getFindOrderline($allIds);
		foreach ($models as $model){
			$orderId=$model['order_id'];
			$subscribed_product_id=$model['subscribed_product_id'];
		}
		$modelsPurchase = OrderLine::model()->getFindOrderlinePurchase($allIds);
		$orderdetails = OrderHeader::model()->getOrderNumber($orderId);
		
		
		
		$connection = Yii::app()->db;
		$sql ="SELECT sp.*, st.* FROM subscribed_product sp INNER JOIN store st ON st.store_id = sp.store_id  WHERE  sp.subscribed_product_id='".$subscribed_product_id."'";
		$command  = $connection->createCommand($sql)->queryAll();
		//$modelsPurchase = OrderLine::model()->getFindOrderlinePurchase($allIds);
		$stop_date = date('Y-m-d');
		$stop_date = date('d-M-Y', strtotime($stop_date . ' +7 day'));
		$povalideday=$stop_date;
		
		$mailpurchase='<table width="100%" cellspacing="0" cellpadding="0" border="0" align="center" style="font-size:12px; font-family:Arial, Helvetica, sans-serif; margin:auto; padding:0 20px; max-width:730px">
		<tr>
		<td align="center" colspan="2"><img alt="Manor cart Po" src="http://supplified.com/assets/appearance/home/manorcart.jpg" align="left"></td>
		<td align="center" colspan="2">
				<span style="font-size: 10pt;font-weight: bold;">Ordered through:</span><img alt="Manor cart Po" src="http://supplified.com/assets/appearance/home/image00.png" align="right" width="260" hight=50>
				<p><span>Customer Care 999999995, </span><span style="color:#0563c1;text-decoration:underline;"><a href="http://www.supplified.com" style="color:inherit;text-decoration: inherit;">www.supplified.com</a></span></p>
				</td>
		</tr>
		<tr>
		<td height="20px" colspan="4"></td>
		</tr>
		<tr>
		<td colspan="2" bgcolor="#0047A1" style="padding-left:5px;padding-bottom:3px;color:#FFFFFF;font-weight:bold">&nbsp;</td>
		<td colspan="2" bgcolor="#0047A1" style="padding-left:5px;padding-bottom:3px;color:#FFFFFF;font-weight:bold">PURCHASE ORDER</td>
		</tr>
		<tr>
		<td height="20px" colspan="4"></td>
		</tr>
		<tr>
		<td>Order Ref. No.</td>
		<td>'.$orderdetails[0]['order_number'].'</td>
		<td>Pickup Date</td>
		<td>'.date('d-M-Y').'</td>
		</tr>
		<tr>
		<td>PO No.</td>
		<td>Scom/15-16/'.$orderId.'</td>
		<td>Pickup Time (Approx.)</td>
		<td>1200 - 1400 hrs.</td>
		</tr>
		<tr>
		<td>PO Date</td>
		<td>'.date('d-M-Y').'</td>
		<td>PO Valid till</td>
		<td>'.$povalideday.'</td>
		</tr>
		<tr>
		<td height="20px" colspan="4">&nbsp;</td>
		</tr>
		<tr>
		<td colspan="2" bgcolor="#0047A1" style="padding-left:5px;padding-bottom:3px;color:#FFFFFF;font-weight:bold">PURCHASER</td>
		<td colspan="2" bgcolor="#0047A1" style="padding-left:5px;padding-bottom:3px;color:#FFFFFF;font-weight:bold">SUPPLIER</td>
		</tr>
		
	<tr>
	  	<td colspan="2">Manor Kart Retail Private Limited</td>
	  	<td colspan="2">'.$command[0]['seller_name'].'</td>
	  </tr>
		
	  <tr>
	  	<td colspan="2">Regd. Office: K-3/40-A West Ghonda, Shahdara</td>
	  	<td colspan="2">'.$command[0]['business_address'].','.$command[0]['business_address_country'].'</td>
	  </tr>
		
	  <tr>
	  	<td colspan="2">New Delhi</td>
	  	<td colspan="2">'.$command[0]['business_address_state'].'</td>
	  </tr>
		
	  <tr>
	  	<td colspan="2">Pin Code - 110053</td>
	  	<td colspan="2">Pin Code -'.$command[0]['business_address_pincode'].'</td>
	  </tr>
		
		
		<tr>
		<td>TIN Number</td>
		<td>07766980433</td>
		<td>TIN Number</td>
		<td>'.$command[0]['tin'].'</td>
		</tr>
		<tr>
		<td>PAN Number</td>
		<td>AAJCM8498G</td>
		<td>PAN Number</td>
		<td>'.$command[0]['pan'].'</td>
		</tr>
		<tr>
		<td>Contact / Mobile No.</td>
		<td>Ravi / 9711622254</td>
		<td>Contact / Mobile No.</td>
		<td>'.$command[0]['con_per_mobile'].'</td>
		</tr>
		<tr>
		<td>Contact / Mobile No.</td>
		<td>Himanshu / 9711622237</td>
		<td>Landline No.</td>
		<td>'.$command[0]['telephone_numbers'].'</td>
		</tr>
		<tr>
		<td>Email:</td>
		<td></td>
		<td>Email:</td>
		<td>'.$command[0]['con_per_email'].'</td>
		</tr>
		
		<tr>
		<td height="20px" colspan="4">&nbsp;</td>
		</tr>
			
		<tr>
		<td colspan="2" bgcolor="#0047A1" style="padding-left:5px;padding-bottom:3px;color:#FFFFFF;font-weight:bold">Ship To / Delivery Address:</td>
		<td colspan="2" bgcolor="#0047A1" style="padding-left:5px;padding-bottom:3px;color:#FFFFFF;font-weight:bold">Bank / RTGS / IFSC - Particulars:</td>
		</tr>
		<tr>
		<td>Not Applicable (Self Pickup)</td>
		<td></td>
		<td>Bank Name</td>
		<td>'.$command[0]['bank_name'] .'</td>
		</tr>
		
		<tr>
		<td></td>
		<td></td>
		<td>Account Number</td>
		<td>'.$command[0]['ac_number'] .'</td>
		</tr>
		
		<tr>
		<td></td>
		<td></td>
		<td>Branch Name</td>
		<td></td>
		</tr>
		
		<tr>
		<td></td>
		<td></td>
		<td>Branch Code</td>
		<td>'.$command[0]['branch_code'].'</td>
		</tr>
		<tr>
		<td></td>
		<td></td>
		<td>IFSC Code</td>
		<td>'.$command[0]['ifsc_code'].'</td>
		</tr>
		<tr>
		<td></td>
		<td></td>
		<td>RTGS Code</td>
		<td>'.$command[0]['rtgs_code'].'</td>
		</tr>
		
		<tr>
		<td height="20px" colspan="4">&nbsp;</td>
		</tr>
		<tr>
		<td align="center" colspan="4" bgcolor="#0047A1" style="padding-left:5px;padding-bottom:3px;color:#FFFFFF;font-weight:bold">ORDER DETAILS</td>
		</tr>
		<tr>
		<td colspan="4" >
		
			
		<table width="100%" border="1" cellpadding="1" cellspacing="0" style="border:1px solid #ccc;font-size:11px; font-family:Arial, Helvetica, sans-serif; margin:auto; padding:0 20px;max-width:800px">
		<tr>
		<th style="text-align:left;">S.No.</th>
		<th style="text-align:left;">Code</th>
		<th style="text-align:left;">Product Desc.</th>
		<th style="text-align:left;">Model No.</th>
		<th style="text-align:left;">Qty</th>
		<th style="text-align:left;">UOM</th>
		<th style="text-align:left;">Unit Rate</th>
		<th style="text-align:left;">Tax Rate %</th>
		<th style="text-align:left;">Tax Form</th>
		<th style="text-align:left;">Tax Amount</th>
		<th style="text-align:left;">Net Rate</th>
		<th style="text-align:left;">Amount</th>
		</tr>';
		
		if($command[0]['business_address_state'] =='Delhi'){
			$vatDisplay='D-VAT';
		}else{
			$vatDisplay='CST';
		}
		
		$counter = 1;
		$subtotal=0;
		$Ttotaltaxamounts=0;
		$grandTotal=0;
		foreach ($models as $key => $ordline){
			$baseproId=$ordline['base_product_id'];
			$scribedproductproId=$ordline['subscribed_product_id'];
		
			$connection = Yii::app ()->db;
		
			$sql = "Select * FROM base_product where base_product_id = $baseproId";
			$command = $connection->createCommand($sql);
			$baseproduct=$command->queryAll();
		
			$subscribed_sql = "Select * FROM subscribed_product where subscribed_product_id = $scribedproductproId";
			$command = $connection->createCommand($subscribed_sql);
			$subscribedproduct=$command->queryAll();
			
			$TaxAmount = $subscribedproduct[0]['unit_rate'] * $baseproduct[0]['VAT'] / 100 ;
			
			$NetRate =$TaxAmount + $subscribedproduct[0]['unit_rate'];
			
			$amount = $subscribedproduct[0]['unit_rate'] * $ordline['product_qty'];
			
			$Ttotaltaxamount = $TaxAmount * $ordline['product_qty'];
			
			$Ttotaltaxamounts += $Ttotaltaxamount;
			
			
			$subtotal += $amount;
			
			$grandTotal += $amount + $Ttotaltaxamount;
			
			$mailpurchase.='<tr>
		  	<td style="text-align:right;">'.$counter.'</td>
		  	<td style="text-align:right;">'.$baseproduct[0]['SKUCode'].'</td>
		  	<td style="text-align:right;">'.$baseproduct[0]['title'].' ,'.$subscribedproduct[0]['size'].'</td>
		  	<td style="text-align:right;">'.$baseproduct[0]['model_number'].'</td>
		  	<td style="text-align:right;">'.$ordline['product_qty'].'</td>
		  	<td style="text-align:right;">Nos</td>
		  	<td style="text-align:right;">'.number_format($subscribedproduct[0]['unit_rate'], 2).'</td>
		  	<td style="text-align:right;">'.$baseproduct[0]['VAT'].'</td>
		  	<td style="text-align:right;">'.$vatDisplay.'</td>
		  	<td style="text-align:right;">'.number_format($TaxAmount, 2).'</td>
		  	<td style="text-align:right;">'.number_format($NetRate, 2).'</td>
		  	<td style="text-align:right;">'.number_format($amount, 2).'</td>
		  </tr>';
		
			$counter ++;
		}
		
		$mailpurchase.='<tr>
		  	<td></td>
		  	<td colspan="10">Sub-total</td>
		  	<td style="text-align: right;">'.number_format($subtotal, 2).'</td>
		  </tr>
		
		  <tr>
		  	<td></td>
		  	<td colspan="10">Add : Additional Freight</td>
		  	<td>--</td>
		  </tr>
		
		  <tr>
		  	<td></td>
		  	<td colspan="10">Add : Other Charges</td>
		  	<td>--</td>
		  </tr>
		  			
		  <tr>
		  	<td></td>
		  	<td colspan="10">Total Tax Amount</td>
		  	<td style="text-align: right;"> '.number_format($Ttotaltaxamounts,2).'</td>
		  </tr>
		
		  <tr>
		  	<td></td>
		  	<td colspan="10">Total </td>
		  	<td style="text-align: right;">'.number_format($grandTotal, 2).'</td>
		  </tr>
		
		  <tr>
		  	<td></td>
		  	<td colspan="10">Round Off </td>
		  	<td style="text-align: right;">0.00</td>
		  </tr>
		
		  <tr>
		  	<td></td>
		  	<td colspan="10">Total Order Value (Inclusive of all Incidental charges and Taxes) In Rupees</td>
		  	<td style="text-align: right;">'.number_format($grandTotal, 2).'</td>
		  </tr>
		
		
		  <tr>
		  	<td></td>
		  	<td colspan="11">Total Order Value (in words): </td>
		  </tr>
		  
		   <tr>
			<td height="20px" colspan="12"></td>
		   </tr>
		  </table>
		  </td>
		  </tr>
			
		
		  
		  <tr>
		  <td colspan="4" >
		 <table width="800"  style="font-size:12px; font-family:Arial, Helvetica, sans-serif; margin:auto; padding:0 20px;">
			<tr>
				<td height="20px" colspan="4">&nbsp;</td>
			</tr>
		   <tr>
		  	<td></td>
		  	<td colspan="11">TERMS & CONDITIONS:</td>
		  </tr>
		  <tr>
			<td height="20px" colspan="12"></td>
		   </tr>
		
		   <tr>
		  	<td></td>
		  	<td colspan="11">This Purchase order, together with standard terms and conditions, and any attachments and exhibits, specifications, drawings, notes, instructions and other information, whether physically attached or incorporated by reference (collectively the "Purchase Order"), constitutes the entire & exclusive agreement between ("PURCHASER") & ("SUPPLIER") as identified in this Purchase Order. Notwithstanding the foregoing, if a master agreement covering procurement of the Products / Material / Goods or Work described in this Purchase Order exists between "SUPPLIER" and "PURCHASER", the terms of such master agreement shall prevail over any inconsistent terms herein.</td>
		  </tr>
		
		  <tr>
		  	<td>1</td>
		  	<td colspan="5">Payment terms</td>
		  	<td colspan="5">30 days from date of invoice.</td>
		  </tr>
		
		  <tr>
		  	<td>2</td>
		  	<td colspan="5">Quality Standard</td>
		  	<td colspan="5">Compliance to relevant IS / ASTM Standard</td>
		  </tr>
		
		  <tr>
		  	<td>3</td>
		  	<td colspan="5">Road Permit</td>
		  	<td colspan="5">Not Applicable</td>
		  </tr>
		
		  <tr>
		  	<td>4</td>
		  	<td colspan="5">Test Certificate</td>
		  	<td colspan="5">No</td>
		  </tr>
		
		
		  <tr>
		  	<td>5</td>
		  	<td colspan="5">Warranty / Guarantee</td>
		  	<td colspan="5">As per manufacturer&#39;s warranty policy.</td>
		  </tr>
		
		
		  <tr>
		  	<td>6</td>
		  	<td colspan="10">You shall provide only approved quality of material, any inferior quality provided has to be replaced by you at your own risk and cost.</td>
		  </tr>
		
		  <tr>
		  	<td>7</td>
		  	<td colspan="10">In case you fail to supply the material to our requirement, we reserve all rights to withhold your payment and arrange supply from any other agency at your risk and cost.</td>
		  </tr>
		
		  <tr>
		  	<td>8</td>
		  	<td colspan="10">All disputes are to be settled at Delhi jurisdiction.</td>
		  </tr>
		
		  <tr>
		  	<td>9</td>
		  	<td colspan="10">For billing on local vat, only tax invoice is acceptable</td>
		  </tr>
		
		
		   <tr>
		  	<td>10</td>
		  	<td colspan="10">All the tax liabilities e.g. sales tax, excise duty etc. shall be applicable as per government policy at the time of delivery of material.</td>
		  </tr>
		
		
		  <tr>
		  	<td>11</td>
		  	<td colspan="10">Billing address has to be given on the bill as shown on top of the purchase order.</td>
		  </tr>
		
		  <tr>
		  	<td>12</td>
		  	<td colspan="10">Manor Kart will return products in all the following cases:-</td>
		  </tr>
		
		  <tr>
		  	<td></td>
		  	<td colspan="10">Size/dimension mismatch in comparison to the ordered materials/products.</td>
		  </tr>
		
		  <tr>
		  	<td></td>
		  	<td colspan="10">Significantly different from the description given by the merchant ( wrong size, color, quality or material related issues)</td>
		  </tr>
		
		  <tr>
		  	<td></td>
		  	<td colspan="10">The packet was empty / some item or accessory was missing</td>
		  </tr>
		
		  <tr>
		  	<td></td>
		  	<td colspan="10">Defective items/malfunctioning materials/products are received.</td>
		  </tr>
		
		  <tr>
		  	<td></td>
		  	<td colspan="10">Not conforming to the specified compliance standard with a test certificate from accredited testing laboratory.</td>
		  </tr>
		
		  <tr>
		  	<td>13</td>
		  	<td colspan="10">Please acknowledge the order by mailing a line of acceptance of the same to support@supplified.com for our record purpose.</td>
		  </tr>
		
		  <tr>
		  	<td>14</td>
		  	<td colspan="10">After acceptance of order the consignment should be kept ready to be picked by our courier partner.</td>
		  </tr>
		
		  <tr>
		  	<td height="100px" colspan="10"></td>
		  </tr>
		
		  <tr>
		  	<td></td>
		  	<td colspan="3" align="center">Prepared by</td>
		  	<td colspan="3" align="center">Checked by</td>
		  	<td colspan="4" align="center">Approved by</td>
		  </tr>
		
		
		  <tr>
		  	<td></td>
		  	<td colspan="3" align="center">(Operation)</td>
		  	<td colspan="3" align="center">(Taxation)</td>
		  	<td colspan="4" align="center">(Operation)</td>
		  </tr>
		
		
		  	</table>
		  	</td>
		  </tr>
		  </table>';
		
		$filemydoc = $myfilename.'.doc';
		$fp = fopen($_SERVER['DOCUMENT_ROOT'] . "/admin/purchase-order/$filemydoc","wb");
		fwrite($fp,$mailpurchase);
		fclose($fp);
		
		
		
			//email
			$emailAttachment = ($filemydoc);
			$emailAttachment = chunk_split(base64_encode($filemydoc));
			$emailsubject='Purchase Order';
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
			$mailer->MsgHTML($mailpurchase);
			$mailer->Subject = $emailsubject;
			$mailer->AddAddress('amit.sinha@supplified.com');
			$mailer->addBcc('manorkartpurchase@gmail.com');
			$mailer->addCc('himanshu.singh@supplified.com');
			$mailer->addCc('ravi.mishra@supplified.com');
			$file = $_SERVER['DOCUMENT_ROOT'] . "/admin/purchase-order/$filemydoc";
			$mailer->AddAttachment( $file, $filemydoc );
			$mailer->Send();
			
			//email
		
		
	}
	
	public function actionChangedstatus(){
		
		$autoId=$_REQUEST['id'];
		$statuschange=$_REQUEST['changevalue'];
		$sql="update order_line set status='$statuschange' where id='$autoId'";
		$orderline=OrderLine::model()->findAllBySql($sql);
		
		//$chstatus = OrderLine::model()->getFindChangedstatus($autoId,$statuschange);
		
	}
		
}

?>
