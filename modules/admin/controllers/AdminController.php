<?php

namespace app\modules\admin\controllers;

use yii\filters\AccessControl;
use yii\web\Controller;

class AdminController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function ($rule, $action) {
                            /* @var $identity \app\models\User */
                            $identity = \Yii::$app->getUser()->getIdentity();
                            return $identity->is_admin;
                        }
                    ],
                ],
            ],
        ];
    }
}
