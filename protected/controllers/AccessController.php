<?php

class AccessController extends Controller {

    /**
    * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
    * using two-column layout. See 'protected/views/layouts/column2.php'.
    */
   // public $layout = '//layouts/column2';

    /**
    * @return array action filters
    */
    public function filters() {

    }

    /**
    * Specifies the access control rules.
    * This method is used by the 'accessControl' filter.
    * @return array access control rules
    */
    public function accessRules() {
        return array();

    }

    public function actionDenied() {
        $this->render('denied');
    }
}
