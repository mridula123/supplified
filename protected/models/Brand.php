<?php


class Brand extends CActiveRecord {

public $password2;
    /**
    * @return string the associated database table name
    */
    public function tableName() {
        return 'brand';
    }

    /**
    * @return array validation rules for model attributes.
    */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
        array('store_front_name', 'required'),
        array('seller_mailer_flag, buyer_mailer_flag, parent_id, is_deleted', 'numerical', 'integerOnly' => true),
        array('redirect_url,order_prefix','length', 'max' => 100),
		
        	        // The following rule is used by search().
        // @todo Please remove those attributes that should not be searched.
        array('store_front_id,store_front_name,store_front_api_key,order_prefix,redirect_url', 'safe', 'on' => 'search'),
        );
    }

    /**
    * @return array relational rules.
    */
    public function relations() {

        return array(

        );
    }

    /**
    * @return array customized attribute labels (name=>label)
    */
    public function attributeLabels() {
        return array(
        'store_front_name' => 'Brand Name',
        'store_front_api_key' => 'store_front_api_key',
        'store_front_api_password' => 'store_front_api_password',
        'store_front_api_token' => 'store_front_api_token',
        'is_deleted' => 'is_deleted',
        'parent_id' => 'parent_id',
        'tagline' => 'tagline',
        'redirect_url' => 'Brand Code',
        'seller_mailer_flag' => 'seller_mailer_flag',
        'buyer_mailer_flag' => 'buyer_mailer_flag',
        'vendor_coupon_prefix' => 'vendor_coupon_prefix',
        'order_prefix' => 'order_prefix',
        'store_front_id' => 'ID',            
        );
    }

    
    public function search() {
        // @todo Please modify the following code to remove attributes that should not be searched.

		
        $criteria = new CDbCriteria;
        
        $criteria->compare('store_front_id', $this->store_front_id, true);
        $criteria->compare('order_prefix', $this->order_prefix, true);
        $criteria->compare('vendor_coupon_prefix', $this->vendor_coupon_prefix, true);
        $criteria->compare('buyer_mailer_flag', $this->buyer_mailer_flag, true);
        $criteria->compare('seller_mailer_flag', $this->seller_mailer_flag, true);
        $criteria->compare('redirect_url', $this->redirect_url, true);
        $criteria->compare('tagline', $this->tagline, true);
        $criteria->compare('is_deleted', $this->is_deleted, true);
        $criteria->compare('parent_id', $this->parent_id, true);
        $criteria->compare('store_front_api_token', $this->store_front_api_token, true);
        $criteria->compare('store_front_api_password', $this->store_front_api_password, true);
        $criteria->compare('store_front_api_key', $this->store_front_api_key, true);
        $criteria->compare('store_front_name', $this->store_front_name, true);
		
        return new CActiveDataProvider(get_class($this), array(
        'criteria' => $criteria,
        ));
    }

    
	
	//..................
	
	
	public function ChkStoreFront($StoreFrontinfo){
                                $connection = Yii::app()->db;
	                            $sqlchk = "select store_front_id from  brand where redirect_url ='".$StoreFrontinfo['redirect_url']."'";
                                $command = $connection->createCommand($sqlchk);
                                $command->execute();
                                $rs=$command->queryAll();
                               if(isset($rs[0]['store_front_id']))
                                {
                                  return false;                                								  
                                }
                                else
                                {
								 return true;
								}
 }
   public function UpdateStoreFront($apikey)
   {
                                   $connection = Yii::app()->db;
                                   $sqlmaxid = "SELECT MAX(`store_front_id`) as id  FROM `brand`";
                                    $command = $connection->createCommand($sqlmaxid);
                                    $command->execute();
                                    $rs1=$command->queryAll();									 
									
                                     $insert_id = $rs1[0]['id'];
									 $apikey=$apikey.$insert_id;
									 $password= md5($apikey);									
									 $sqlup = "update `brand` set store_front_api_key='".$apikey."',store_front_api_password='". $password."' WHERE `store_front_id`='".$insert_id."' ";
									$command = $connection->createCommand($sqlup);
                                    $command->execute();
									                         
   }
   public function SaveStoreFront($StoreFrontinfo){
		
				$apikey= substr($StoreFrontinfo['store_front_name'],0,2);
		       $password= md5($StoreFrontinfo['store_front_api_password']);
		                        $connection = Yii::app()->db;
	                            $sqlchk = "select store_front_id from  brand where store_front_name ='".$StoreFrontinfo['store_front_name']."'";
                                $command = $connection->createCommand($sqlchk);
                                $command->execute();
                                $rs=$command->queryAll();
								
								
                                if(isset($rs[0]['store_front_id']))
                                {
                                  Yii::app()->user->setFlash('error', 'Store Front is already  created !.' ); 
                                 								  
                                }
                                else
                                {
								    $connection = Yii::app()->db;
                                    $sql = "INSERT INTO `brand`(`store_front_name`,`is_tagline`) VALUES ('". $StoreFrontinfo['store_front_name']."','1','0')";
                                    $command = $connection->createCommand($sql);
                                    $command->execute();
									
									$sqlmaxid = "SELECT MAX(`store_front_id`) as id  FROM `brand`";
                                    $command = $connection->createCommand($sqlmaxid);
                                    $command->execute();
                                    $rs1=$command->queryAll();									 
									
                                     $insert_id = $rs1[0]['id'];
									 $apikey=$apikey.$insert_id;
									
									$sqlup = "update `brand` set store_front_api_key='".$apikey."' WHERE `store_front_id`='".$insert_id."' ";
									$command = $connection->createCommand($sqlup);
                                    $command->execute();
									                         
                                }
								
		                        
	}
	
	
    public function beforeSave() {
        //$pass = md5($this->password);
        // $this->password = $pass;
        return true;
    }

    /**
    * Returns the static model of the specified AR class.
    * Please note that you should have this exact method in all your CActiveRecord descendants!
    * @param string $className active record class name.
    * @return Store the static model class
    */
    public static function model($className = __CLASS__) {
        
		return parent::model($className);
    }
    public function getDbConnection()
    {
        return Yii::app()->db2;
    }
}
