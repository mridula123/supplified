<?php

/**
 * UserIdentity represents the data needed to identity a user.
 * It contains the authentication method that checks if the provided
 * data can identity the user.
 */
class UserIdentity extends CUserIdentity
{
	public $user;
	public $_id;
	/**
	 * Authenticates a user.
	 * The example implementation makes sure if the username and password
	 * are both 'demo'.
	 * In practical applications, this should be changed to authenticate
	 * against some persistent user identity storage (e.g. database).
	 * @return boolean whether authentication succeeds.
	 */

	public function authenticate()
	{
		$record=AdminUser::model()->findByAttributes(array('username'=>$this->username));

		if($record===null)
			$this->errorCode=self::ERROR_USERNAME_INVALID;
		else if($record->password!=md5($this->password)){
			$this->errorCode=self::ERROR_PASSWORD_INVALID;
		}
		else
		{
			$this->_id=$record->id;
			$this->setState('id', $record->id);
			$this->setState('name', $record->name);
			$this->setState('role', $record->role);
			$this->errorCode=self::ERROR_NONE;
			$this->setUser($record);
		}
		unset($record);
		return !$this->errorCode;
	}

	/*public function getId()
	{
		return $this->_id;
	}*/

	public function getUser()
	{
		return $this->user;
	}

	public function setUser(CActiveRecord $user)
	{
		$this->user=$user->attributes;
	}
}