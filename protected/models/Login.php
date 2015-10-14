<?php

/**
 * This is the model class for table "login".
 *
 * The followings are the available columns in table 'login':
 * @property integer $id
 * @property string $username
 * @property string $password
 * @property string $type
 * @property string $status
 * @property string $date
 */
class Login extends CActiveRecord {

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return 'login';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('username, password, type, status, date', 'required'),
            array('username, password, type, status', 'length', 'max' => 50),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, username, password, type, status, date', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'id' => 'ID',
            'username' => 'Username',
            'password' => 'Password',
            'type' => 'Type',
            'status' => 'Status',
            'date' => 'Date',
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
    public function search() {
        // @todo Please modify the following code to remove attributes that should not be searched.

        $criteria = new CDbCriteria;

        $criteria->compare('id', $this->id);
        $criteria->compare('username', $this->username, true);
        $criteria->compare('password', $this->password, true);
        $criteria->compare('type', $this->type, true);
        $criteria->compare('status', $this->status, true);
        $criteria->compare('date', $this->date, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }
    
    /**
     * Author:Ravi Kr Shrama
     * Declares the function create password MD5 data
     * and function to be declears.
     */

    public function beforeSave() {
        $pass = md5($this->password);
        $this->password = $pass;
        return true;
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return Login the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

}
