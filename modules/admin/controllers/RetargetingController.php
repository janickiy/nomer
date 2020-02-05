<?php
namespace app\modules\admin\controllers;

use app\models\Retargeting;
use yii\base\Exception;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
use yii\db\Expression;
use yii\helpers\ArrayHelper;

class RetargetingController extends AdminController {

    public function actionIndex() {
        $dataProvider = new ActiveDataProvider([
            'query' => Retargeting::find()->orderBy(["id" => SORT_DESC])
        ]);

        $todaySent = Retargeting::find()
            ->where(['status' => Retargeting::STATUS_SENT])
            ->andWhere(["=", new Expression("extract(month from tm_send)"), date("m")])
            ->andWhere(["=", new Expression("extract(year from tm_send)"), date("Y")])
            ->andWhere(["=", new Expression("extract(day from tm_send)"), date("d")])
            ->count();

        $todayRead = Retargeting::find()
            ->where(['status' => Retargeting::STATUS_READ])
            ->andWhere(["=", new Expression("extract(month from tm_read)"), date("m")])
            ->andWhere(["=", new Expression("extract(year from tm_read)"), date("Y")])
            ->andWhere(["=", new Expression("extract(day from tm_read)"), date("d")])
            ->count();

        $todayClick = Retargeting::find()
            ->where(['status' => Retargeting::STATUS_CLICK])
            ->andWhere(["=", new Expression("extract(month from tm_click)"), date("m")])
            ->andWhere(["=", new Expression("extract(year from tm_click)"), date("Y")])
            ->andWhere(["=", new Expression("extract(day from tm_click)"), date("d")])
            ->count();

        $yesterdaySent = Retargeting::find()
            ->where(['status' => Retargeting::STATUS_SENT])
            ->andWhere(["=", new Expression("extract(month from tm_send)"), date("m", strtotime("-1 day"))])
            ->andWhere(["=", new Expression("extract(year from tm_send)"), date("Y", strtotime("-1 day"))])
            ->andWhere(["=", new Expression("extract(day from tm_send)"), date("d", strtotime("-1 day") )])
            ->count();

        $yesterdayRead = Retargeting::find()
            ->where(['status' => Retargeting::STATUS_READ])
            ->andWhere(["=", new Expression("extract(month from tm_read)"), date("m", strtotime("-1 day"))])
            ->andWhere(["=", new Expression("extract(year from tm_read)"), date("Y", strtotime("-1 day"))])
            ->andWhere(["=", new Expression("extract(day from tm_read)"), date("d", strtotime("-1 day"))])
            ->count();

        $yesterdayClick = Retargeting::find()
            ->where(['status' => Retargeting::STATUS_CLICK])
            ->andWhere(["=", new Expression("extract(month from tm_click)"), date("m", strtotime("-1 day"))])
            ->andWhere(["=", new Expression("extract(year from tm_click)"), date("Y", strtotime("-1 day"))])
            ->andWhere(["=", new Expression("extract(day from tm_click)"), date("d", strtotime("-1 day"))])
            ->count();

        $monthSent = Retargeting::find()
            ->where(['status' => Retargeting::STATUS_SENT])
            ->andWhere([">=", "tm_send", date("Y-m-d 00:00:00", strtotime("-30 days"))])
            ->count();

        $monthRead = Retargeting::find()
            ->where(['status' => Retargeting::STATUS_READ])
            ->andWhere([">=", "tm_read", date("Y-m-d 00:00:00", strtotime("-30 days"))])
            ->count();

        $monthClick = Retargeting::find()
            ->where(['status' => Retargeting::STATUS_CLICK])
            ->andWhere([">=", "tm_click", date("Y-m-d 00:00:00", strtotime("-30 days"))])
            ->count();

        return $this->render("index", compact('todaySent', 'todayRead', 'todayClick','yesterdaySent','yesterdayRead','yesterdayClick', 'monthSent', 'monthRead','monthClick'));
    }
}