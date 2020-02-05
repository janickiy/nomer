<?php
namespace app\commands;

use app\models\Call;
use yii\console\Controller;

class CallsController extends Controller {

    public function actionMan() {
        //select status, count(1) from calls where id < 35191 and id >9810 group by status order by count(1);

        $calls = Call::find()
            ->andWhere([">", "id", 9810])
            ->andWhere(["<", "id", 35191])
            ->andWhere(["status" => ["timeout", "hangup", "no-answer"]])
            ->asArray()->all();

        $f = fopen(\Yii::getAlias('@runtime').'/man.txt', 'a+');
        foreach ($calls as $call) {
            fwrite($f, $call["phone"]."\n");
        }
        fclose($f);
    }

    public function actionWoman() {
        //select status, count(1) from calls where id < 35191 and id >9810 group by status order by count(1);

        $calls = Call::find()
            ->andWhere([">", "id", 35191])
            ->andWhere(["status" => ["timeout", "hangup", "no-answer"]])
            ->asArray()->all();

        $f = fopen(\Yii::getAlias('@runtime').'/woman.txt', 'a+');
        foreach ($calls as $call) {
            fwrite($f, $call["phone"]."\n");
        }
        fclose($f);
    }

}