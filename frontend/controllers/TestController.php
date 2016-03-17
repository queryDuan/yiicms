<?php

namespace frontend\controllers;

class TestController extends \yii\web\Controller
{

    public function actionIndex() {
        $phpExcel = \Yii::$app->PHPExcel;
        var_dump($phpExcel);
//        return $this->render('index');
    }

}
