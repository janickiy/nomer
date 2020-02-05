<?php
namespace app\commands;

use app\models\Site;
use app\models\Vk;
use app\models\VkComment;
use yii\console\Controller;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

class VkController extends Controller {

    public function actionComments() {
        foreach (Site::find()->where(["NOT IN", "id", [14]])->all() as $s) {
            $appID = $s->vk_id;
            $siteID = $s->id;
            $response = @file_get_contents("https://api.vk.com/method/widgets.getComments?widget_api_id=".$appID."&page_id=777&fields=replies&order=date&count=100&v=5.65");
            if($response) {
                $response = Json::decode($response);
                $posts = ArrayHelper::getValue($response, ["response", "posts"], []);
                if(count($posts)) foreach($posts as $post) {
                    $vkPost = VkComment::find()->where(["id" => ArrayHelper::getValue($post, "id"), "site_id" => $siteID])->one();
                    $vkID = ArrayHelper::getValue($post, "from_id");
                    if(!$vkPost) {
                        $responseVkUser = file_get_contents('https://api.vk.com/method/users.get?user_id=' . $vkID . '&v=5.65&lang=ru&fields=photo_50&access_token=8f95fab19fb8d3d41bdeeb28f0112cb2cd3c86a93fc66acbd29f327d1aa3f196540bfe10dcd4ca97baf37');
                        $responseVkUser = Json::decode($responseVkUser);
                        $vkUser = ArrayHelper::getValue($responseVkUser, ['response', 0]);
                        $vkUserPhoto = @file_get_contents(ArrayHelper::getValue($vkUser, "photo_50"));
                        if($vkUserPhoto) {
                            $vkUserPhoto = base64_encode($vkUserPhoto);
                        }

                        $vkPost = new VkComment();
                        $vkPost->id = ArrayHelper::getValue($post, "id");
                        $vkPost->site_id = $siteID;
                        $vkPost->comment = ArrayHelper::getValue($post, "text");
                        $vkPost->tm = \Yii::$app->formatter->asDatetime(ArrayHelper::getValue($post, "date"), "yyyy-MM-dd HH:mm:ss");
                        $vkPost->vk_id = (string)ArrayHelper::getValue($post, "from_id");
                        if($vkUserPhoto) {
                            $vkPost->photo = $vkUserPhoto;
                        }
                        $vkPost->name = join(" ", [ArrayHelper::getValue($vkUser, "first_name"), ArrayHelper::getValue($vkUser, "last_name")]);
                        if(!$vkPost->save()) {
                            print_r($vkPost->getErrors()); die();
                        }
                    }
                    $comments = ArrayHelper::getValue($post, "comments.replies", []);
                    if(count($comments)) {
                        foreach ($comments as $c) {
                            $vkID = ArrayHelper::getValue($c, "uid");
                            $vkComment = VkComment::find()->where(["id" => ArrayHelper::getValue($c, "cid"), "site_id" => $siteID])->one();
                            if(!$vkComment) {
                                $responseVkUser = file_get_contents('https://api.vk.com/method/users.get?user_id=' . $vkID . '&v=5.65&lang=ru&fields=photo_50&access_token=8f95fab19fb8d3d41bdeeb28f0112cb2cd3c86a93fc66acbd29f327d1aa3f196540bfe10dcd4ca97baf37');
                                $responseVkUser = Json::decode($responseVkUser);
                                $vkUser = ArrayHelper::getValue($responseVkUser, ['response', 0]);
                                $vkUserPhoto = @file_get_contents(ArrayHelper::getValue($vkUser, "photo_50"));
                                if($vkUserPhoto) {
                                    $vkUserPhoto = base64_encode($vkUserPhoto);
                                }

                                $vkComment = new VkComment();
                                $vkComment->id = ArrayHelper::getValue($c, "cid");
                                $vkComment->site_id = $siteID;
                                $vkComment->pid = ArrayHelper::getValue($vkPost, "id");
                                $vkComment->comment = ArrayHelper::getValue($c, "text");
                                $vkComment->tm = \Yii::$app->formatter->asDatetime(ArrayHelper::getValue($c, "date"), "yyyy-MM-dd HH:mm:ss");
                                $vkComment->vk_id = (string)ArrayHelper::getValue($c, "uid");
                                if($vkUserPhoto) {
                                    $vkComment->photo = $vkUserPhoto;
                                }
                                $vkComment->name = join(" ", [ArrayHelper::getValue($vkUser, "first_name"), ArrayHelper::getValue($vkUser, "last_name")]);
                                if(!$vkComment->save()) {
                                    print_r($vkComment->getErrors()); die();
                                }
                            }
                        }
                    }
                }
            }
        }


    }
}