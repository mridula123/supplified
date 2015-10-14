<?php

/**
 * This is the model class for table "admin_user".
 *
 * The followings are the available columns in table 'admin_user':
 * @property integer $id
 * @property string $name
 * @property string $username
 * @property string $password
 * @property string $role
 * @property integer $status
 * @property string $auth_key
 * @property string $created_date
 * @property string $last_login
 */
class AdminUser extends CActiveRecord
{
	public $confirm_password;
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'admin_user';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name, email, username, role, status, created_date', 'required'),
			array('status', 'numerical', 'integerOnly'=>true),
			array('password', 'required', 'on' => 'createuser, changePassword' ),
			array('username', 'length', 'min'=>6,'max'=>15),
			array('email','email','message'=>'Please enter valid email address'),
			array(array('email','username'),'unique'),
			array('password', 'length', 'min'=>6,'max'=>15,'allowEmpty'=>false, 'on' => 'createuser, changePassword'),
			array('password', 'match','pattern'=> '/^[A-Za-z0-9_!@#$%^&*()+=?.,]+$/u','message'=> '{attribute} can contain only alphanumeric characters', 'on' => 'createuser, changePassword'),
			array('confirm_password', 'compare', 'compareAttribute'=>'password', 'on' => 'changePassword','message'=> 'Pdssfsdfsd',),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, name, username, password, role, status, auth_key, created_date, last_login, email', 'safe', 'on'=>'search'),
			array('last_login', 'safe', 'on'=>'createuser'),
			array('password', 'safe', 'on'=>'updateuser'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'Users' => array(self::HAS_MANY, 'Responsibilities', 'user_id')
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'name' => 'Name',
			'username' => 'Username',
			'password' => 'Password',
			'role' => 'Role',
			'status' => 'Status',
			'auth_key' => 'Auth Key',
			'created_date' => 'Created Date',
			'last_login' => 'Last Login',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * Typical usecase:
	 * - Initialize the model fields with values from filter form.
	 * - Execute this method to get CActiveDataProvider instance which will filter
	 * models according to data in model fields.
	 * - Pass data provider to CGridView, CListView or any similar widget.
	 *
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;
		$criteria->condition = 'role!="SUPERADMIN" AND status!=2';

		$criteria->compare('name',$this->name,true);
		$criteria->compare('username',$this->username,true);
		$criteria->compare('role',$this->role,true);
		$criteria->compare('status',$this->status);
		$criteria->compare('created_date',$this->created_date,true);
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return AdminUser the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	protected function beforeSave()
	{
		if ( $this->scenario == 'createuser' || $this->scenario == 'changePassword' )
			$this->password = md5($this->password);

		return parent::beforeSave();
	}

	public function beforeValidate()
	{
		if(parent::beforeValidate())
		{
			$this->setAttribute('created_date', date('Y-m-d H:i:s'));
			return true;
		}
	}


}
