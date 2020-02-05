<?php
namespace app\modules\api\controllers;

use app\models\User;
use app\models\UserSub;
use Yii;
use yii\db\Expression;
use yii\rest\Controller;

class PlansController extends Controller {

    public function actionIndex() {
        $userAgent = \Yii::$app->getRequest()->getUserAgent();
        if(preg_match("/Nomer\/2\.5\.7/", $userAgent) || preg_match("/Nomer\/2\.5\.8/", $userAgent) ) {
            $uuid = Yii::$app->getRequest()->getHeaders()->get('uuid', false);
            $user = User::find()->where(["uuid" => $uuid])->one();
            if(!$user) {
                $user = new User();
                $user->email = null;
                $user->uuid = $uuid;
                $user->save();
            }

            $sub = UserSub::find()->where(["user_id" => $user->id])->andWhere([">=", "tm_expires", new Expression("NOW()")])->orderBy(["tm_expires" => SORT_DESC])->one();
            if($sub) {
                return [];
            }
            /*
            $packs = [
                [
                    "count"         => -1,
                    "product_id"    => "com.wcaller.Wcaller.sub.month.0",
                    "type"          => "sub",
                    "caption"       => "Безлимит на месяц",
                    "subtitle"      => "в неделю",
                    "special"       => 0,
                    "style"         => 0 // Большая кнопка
                ],
                [
                    "count"         => -2,
                    "product_id"    => "com.wcaller.Wcaller.sub.6month.0",
                    "type"          => "sub6",
                    "caption"       => "Безлимит на пол года",
                    "subtitle"      => "в неделю",
                    "special"       => 0,
                    "style"         => 0 // Большая кнопка
                ],
            ];
            */
            $packs = [
                [
                    "count"         => -2,
                    "product_id"    => "com.wcaller.Wcaller.sub.month.0",
                    "type"          => "sub",
                    "caption"       => "3 дня - БЕСПЛАТНО",
                    "subtitle"      => "",
                    "special"       => 0,
                    "style"         => 1,
                ],
                [
                    "count"         => -1,
                    "product_id"    => "com.wcaller.Wcaller.sub.6month.0",
                    "type"          => "sub6",
                    "caption"       => "Лучшая цена",
                    "subtitle"      => "0.49$ в день",
                    "special"       => 0,
                    "style"         => 1 // Большая кнопка
                ],
            ];

            /*
            $packs = [
                [
                    "count"         => -2,
                    "product_id"    => "com.wcaller.Wcaller.sub.month.0",
                    "type"          => "sub",
                    "caption"       => "3 дня - БЕСПЛАТНО",
                    "subtitle"      => "",
                    "special"       => 0,
                    "style"         => 1,
                ],
                [
                    "count"         => -1,
                    "product_id"    => "com.wcaller.Wcaller.sub.6month.0",
                    "type"          => "sub6",
                    "caption"       => "Лучшая цена",
                    "subtitle"      => "0.49$ в день",
                    "special"       => 0,
                    "style"         => 1 // Большая кнопка
                ],
            ];
            */

        return $packs;
    } elseif(preg_match("/Nomer\/2\.5/", $userAgent)) {
            $uuid = Yii::$app->getRequest()->getHeaders()->get('uuid', false);
            $user = User::find()->where(["uuid" => $uuid])->one();

            /*
            $packs = [
                [
                    "count"         => -2,
                    "product_id"    => "com.wcaller.Wcaller.sub.week.999",
                    "type"          => "sub",
                    "caption"       => "Полный безлимит",
                    "subtitle"      => "Все провеки бесплатно",
                    "special"       => 0,
                    "style"         => 1,
                ],
                [
                    "count"         => -1,
                    "product_id"    => "com.wcaller.Wcaller.sub.6month.0",
                    "type"          => "sub6",
                    "caption"       => "Лучшая цена",
                    "subtitle"      => "0.49$ в день",
                    "special"       => 0,
                    "style"         => 1 // Большая кнопка
                ],
            ];
            */

            $packs = [
                [
                    "count"         => -2,
                    "product_id"    => "com.wcaller.Wcaller.sub.month.0",
                    "type"          => "sub",
                    "caption"       => "3 дня - БЕСПЛАТНО",
                    "subtitle"      => "",
                    "special"       => 0,
                    "style"         => 1,
                ],
                [
                    "count"         => -1,
                    "product_id"    => "com.wcaller.Wcaller.sub.6month.0",
                    "type"          => "sub6",
                    "caption"       => "Лучшая цена",
                    "subtitle"      => "0.49$ в день",
                    "special"       => 0,
                    "style"         => 1 // Большая кнопка
                ],
            ];

            /*
            $packs = [
                [
                    "count"         => -2,
                    "product_id"    => "com.wcaller.Wcaller.sub.month.999",
                    "type"          => "sub",
                    "caption"       => "БЕСПЛАТНО",
                    "subtitle"      => "безлимит на месяц",
                    "special"       => 0,
                    "style"         => 1,
                ],
                [
                    "count"         => -1,
                    "product_id"    => "com.wcaller.Wcaller.sub.6month.0",
                    "type"          => "sub6",
                    "caption"       => "Лучшая цена",
                    "subtitle"      => "0.49$ в день",
                    "special"       => 0,
                    "style"         => 1 // Большая кнопка
                ],
            ];
            */

            if(!$user) return $packs;

            $sub = UserSub::find()->where(["user_id" => $user->id])->andWhere([">=", "tm_expires", new Expression("NOW()")])->orderBy(["tm_expires" => SORT_DESC])->one();
            if($sub && $user->checks == 0) {
                $packs = [
                    [
                        "count" => 1,
                        "product_id" => "com.wcaller.Wcaller.search1",
                        "type" => "inapp",
                    ],
                    [
                        "count" => 10,
                        "product_id" => "com.wcaller.Wcaller.search10",
                        "type" => "inapp",
                    ],
                    [
                        "count" => 30,
                        "product_id" => "com.wcaller.Wcaller.search30",
                        "type" => "inapp",
                    ],
                ];
            } elseif($sub && $user->checks < 0) {
                $packs = [];
            }

            return $packs;
        } elseif(preg_match("/Nomer\/2\.3/", $userAgent) || preg_match("/Nomer\/2\.4/", $userAgent)) {
            $uuid = Yii::$app->getRequest()->getHeaders()->get('uuid', false);
            $user = User::find()->where(["uuid" => $uuid])->one();

            $packs = [
                /*
                [
                    "count" => 1,
                    "product_id" => "com.wcaller.Wcaller.search1",
                    "type" => "inapp",
                ],
                */
                [
                    "count"         => 10,
                    "product_id"    => "com.wcaller.Wcaller.sub.month.10",
                    "type"          => "sub",
                    "special"       => 1
                ],
                [
                    "count"         => 15,
                    "product_id"    => "com.wcaller.Wcaller.sub.month.15",
                    "type"          => "sub",
                    "special"       => 0
                ],
                [
                    "count"         => 50,
                    "product_id"    => "com.wcaller.Wcaller.sub.month.50",
                    "type"          => "sub",
                    "special"       => 0
                ],
            ];
            if(!$user) return $packs;

            $sub = UserSub::find()->where(["user_id" => $user->id])->andWhere([">=", "tm_expires", new Expression("NOW()")])->orderBy(["tm_expires" => SORT_DESC])->one();
            if($sub) {
                $packs = [
                    [
                        "count" => 1,
                        "product_id" => "com.wcaller.Wcaller.search1",
                        "type" => "inapp",
                    ],
                    [
                        "count" => 10,
                        "product_id" => "com.wcaller.Wcaller.search10",
                        "type" => "inapp",
                    ],
                    [
                        "count" => 30,
                        "product_id" => "com.wcaller.Wcaller.search30",
                        "type" => "inapp",
                    ],
                ];
            }

            return $packs;
        }
        $plans = [
            [
                "count" => 1,
                "product_id" => "com.wcaller.Wcaller.search1",
            ],
            [
                "count" => 10,
                "product_id" => "com.wcaller.Wcaller.search10",
            ],
            [
                "count" => 30,
                "product_id" => "com.wcaller.Wcaller.search30",
            ]
            /*
            [
                "count" => 100,
                "product_id" => "com.nomer.app.search100",
            ],
            [
                "count" => 300,
                "product_id" => "com.nomer.app.search300",
            ]
            */
        ];

        return $plans;
    }
}