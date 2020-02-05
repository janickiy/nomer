<?php
namespace app\commands;

use app\models\RequestResult;
use app\models\ResultCache;
use yii\console\Controller;
use yii\helpers\Json;

class CleanController extends Controller {

    public function actionResults() {
        $pid = "/tmp/last.results.vk.log";
        $last = `cat $pid`;
        if(!$last) $last = 0;
        foreach (RequestResult::find()->where(["type_id" => [ResultCache::TYPE_VK_2012, ResultCache::TYPE_VK, ResultCache::TYPE_VK_OPEN]])->andWhere([">", "id", $last])->limit(1000)->orderBy(["id" => SORT_ASC])->batch(10) as $results) {
            foreach($results as $result) {
                /* @var $result \app\models\RequestResult */

                $data = Json::decode($result->data);
                foreach($data as $vkId => $vkProfile) {
                    if(isset($vkProfile["photo"])) {
                        $tmp = "/tmp/vk-".$vkId.".jpg";
                        $photo = $vkProfile["photo"];
                        $this->base64_to_jpeg($photo, $tmp);

                        $file_path_str = '/vk/'.$vkId.'.jpg';
                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, 'http://q.apinomer.com/upload'.$file_path_str);

                        curl_setopt($ch, CURLOPT_PUT, 1);

                        $fh_res = fopen($tmp, 'r');

                        curl_setopt($ch, CURLOPT_INFILE, $fh_res);
                        curl_setopt($ch, CURLOPT_INFILESIZE, filesize($tmp));

                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                        curl_setopt($ch, CURLOPT_BINARYTRANSFER, TRUE); // --data-binary

                        $curl_response_res = curl_exec ($ch);
                        fclose($fh_res);
                        unlink($tmp);
                        unset($data[$vkId]["photo"]);
                        $data[$vkId]["photo"] = "https://q.apinomer.com'.$file_path_str";
                    }
                    if(isset($vkProfile["raw"])) {
                        unset($data[$vkId]["raw"]);
                    }
                }
                $result->data = Json::encode($data);
                $result->save();
                $last = $result->id;
            }
            `echo $last > $pid`;
        }
    }

    public function actionIndex() {
        $last = `cat /tmp/last.cache.log`;
        if(!$last) $last = 0;
        foreach (ResultCache::find()->where(["type_id" => ResultCache::TYPE_VK_2012])->andWhere([">", "id", $last])->limit(1000)->orderBy(["id" => SORT_ASC])->batch(10) as $results) {
            foreach($results as $result) {
                /* @var $result \app\models\ResultCache */
                $data = Json::decode($result->data);
                foreach($data as $vkId => $vkProfile) {
                    if(isset($vkProfile["photo"])) {
                        $tmp = "/tmp/".$vkId.".jpg";
                        $photo = $vkProfile["photo"];
                        $this->base64_to_jpeg($photo, $tmp);

                        $file_path_str = '/vk2012/'.$vkId.'.jpg';
                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, 'http://q.apinomer.com/upload'.$file_path_str);

                        curl_setopt($ch, CURLOPT_PUT, 1);

                        $fh_res = fopen($tmp, 'r');

                        curl_setopt($ch, CURLOPT_INFILE, $fh_res);
                        curl_setopt($ch, CURLOPT_INFILESIZE, filesize($tmp));

                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                        curl_setopt($ch, CURLOPT_BINARYTRANSFER, TRUE); // --data-binary

                        $curl_response_res = curl_exec ($ch);
                        echo $curl_response_res;
                        fclose($fh_res);
                        unlink($tmp);
                        unset($data[$vkId]["photo"]);
                        $data[$vkId]["photo"] = "https://q.apinomer.com'.$file_path_str";
                    }
                    if(isset($vkProfile["raw"])) {
                        unset($data[$vkId]["raw"]);
                    }
                }
                $result->data = Json::encode($data);
                $result->save();
                $last = $result->id;
            }
            `echo $last > /tmp/last.cache.log`;
        }
    }

    public function actionFacebook() {
        $last = `cat /tmp/last.fb.cache.log`;
        if(!$last) $last = 0;
        foreach (ResultCache::find()->where(["type_id" => ResultCache::TYPE_FACEBOOK])->andWhere([">", "id", $last])->limit(1000)->orderBy(["id" => SORT_ASC])->batch(10) as $results) {
            foreach($results as $result) {
                /* @var $result \app\models\ResultCache */
                $data = Json::decode($result->data);
                foreach($data as $fbId => $fbProfile) {
                    if(isset($fbProfile["photo"])) {
                        $tmp = "/tmp/".$fbId.".jpg";
                        $photo = $fbProfile["photo"];
                        $this->base64_to_jpeg($photo, $tmp);

                        $file_path_str = '/fb/'.$fbId.'.jpg';
                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, 'http://q.apinomer.com/upload'.$file_path_str);

                        curl_setopt($ch, CURLOPT_PUT, 1);

                        $fh_res = fopen($tmp, 'r');

                        curl_setopt($ch, CURLOPT_INFILE, $fh_res);
                        curl_setopt($ch, CURLOPT_INFILESIZE, filesize($tmp));

                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                        curl_setopt($ch, CURLOPT_BINARYTRANSFER, TRUE); // --data-binary

                        $curl_response_res = curl_exec ($ch);
                        echo $curl_response_res;
                        fclose($fh_res);
                        unlink($tmp);
                        unset($data[$fbId]["photo"]);
                        $data[$fbId]["photo"] = "https://q.apinomer.com".$file_path_str;
                    }
                }

                $result->data = Json::encode($data);
                $result->save();
                $last = $result->id;
            }
            `echo $last > /tmp/last.fb.cache.log`;
        }
    }

    private function base64_to_jpeg($base64_string, $output_file) {
        // open the output file for writing
        $ifp = fopen( $output_file, 'wb' );

        // we could add validation here with ensuring count( $data ) > 1
        fwrite( $ifp, base64_decode($base64_string));

        // clean up the file resource
        fclose( $ifp );

        return $output_file;
    }
}