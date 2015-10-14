<?php

/**
 * This is the model class for table "responsibilities".
 *
 * The followings are the available columns in table 'responsibilities':
 * @property integer $id
 * @property integer $user_id
 * @property integer $admin_func_id
 * @property integer $status
 */
class Responsibilities extends CActiveRecord
{
	public $functionList = array();
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'responsibilities';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('user_id, status', 'required'),
			array('user_id, admin_func_id, status', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('functionList','type','type'=>'array','allowEmpty'=>false),
			array(' admin_func_id', 'safe'),
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
			'Users' => array(self::BELONGS_TO, 'AdminUser', 'user_id'),
			'UserFunction' => array(self::BELONGS_TO, 'AdminFunctions', 'admin_func_id')
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'user_id' => 'User',
			'admin_func_id' => 'Admin Functions',
			'status' => 'Status',
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
		$criteria->with = array('Users' => array('select' => 'name, role'));
		$criteria->compare('id',$this->id);
		$criteria->compare('user_id',$this->user_id);
		$criteria->compare('admin_func_id',$this->admin_func_id);
		$criteria->compare('status',$this->status);
		$criteria->group = 'user_id';
        $criteria->condition = 't.status!=2';
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Responsibilities the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

}
