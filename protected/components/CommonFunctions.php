<?php

/**
 * Created by PhpStorm.
 * user: MridulaSingh
 * Date: 10/9/2015
 * Time: 12:10 PM
 */

class CommonFunctions extends CApplicationComponent
{
    // function to fetch status
    public function getStatus()
    {
        $status = array('0' => 'Inactive', '1' => 'Active');
        return $status;
    }

    // function to fetch all admin roles
    public function getRole()
    {
        $Criteria = new CDbCriteria();
        $Criteria->condition = "status = 1";
        $roleArray = Role::model()->findAll($Criteria);
        $role = CHtml::listData($roleArray,
            'name', 'name');
        return $role;

    }

    public function getKeyValue($key)
    {
        $status = array('0' => 'Inactive', '1' => 'Active');
        return $status[$key];
    }
}