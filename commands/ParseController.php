<?php

namespace app\commands;

use app\models\Call;
use app\models\Organization;
use app\models\OrganizationEmail;
use app\models\OrganizationPhone;
use yii\console\Controller;
use yii\helpers\Json;

class ParseController extends Controller
{
    public function actionPrepare() {
        $f = fopen(\Yii::getAlias('@runtime').'/lists.txt', 'a+');
        $orgs = Organization::find()->all();
        foreach($orgs as $org) {
            foreach($org->emails as $email) {
                fwrite($f, $email->email.";".$org->name.";".$org->inn."\n");
            }
        }
        fclose($f);
    }

    public function actionNew() {
        $f = fopen(\Yii::getAlias('@runtime').'/new-calls.txt', 'a+');
        $calls = Call::find()->where(["status" => ["no-answer", "timeout"]])->all();
        foreach($calls as $call) {
            fwrite($f, $call->phone."\n");
        }
    }

    public function actionCheck($file) {
        $fp = fopen(\Yii::getAlias('@runtime').'/new-phones.txt', 'a+');
        $fe = fopen(\Yii::getAlias('@runtime').'/new-emails.txt', 'a+');
        $data = file_get_contents($file);
        $rows = preg_split("/\n/", $data);
        foreach($rows as $phone) {
            $ch = curl_init("http://ssd.nomer.io/api/".$phone."?token=d131BpdeqbFJMasdfaVYJU6ydeyhgX");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if ($httpCode == 200) { // Все ок, берем данные
                $response = Json::decode($response);
                foreach ($response as $r) {
                    if(isset($r["type"])) {
                        switch ($r["type"]) {
                            case "phone":
                                fwrite($fp, $phone.";".$r["data"]."\n");
                                break;
                            case "email":
                                if (strpos($r["data"], '@') !== false) {
                                    fwrite($fe, $phone.";".$r["data"]."\n");
                                }
                                break;
                        }
                    }
                }
            }
        }
        fclose($fe);
        fclose($fp);
    }

    public function actionExportPhones() {
        $f = fopen(\Yii::getAlias('@runtime').'/phones.txt', 'a+');
        $phones = OrganizationPhone::find()->all();
        foreach($phones as $phone) {
            fwrite($f, $phone->phone."\n");
        }
        fclose($f);
    }

    public function actionExportEmails() {
        $f = fopen(\Yii::getAlias('@runtime').'/emails.txt', 'a+');
        $emails = OrganizationEmail::find()->all();
        foreach($emails as $email) {
            fwrite($f, $email->email."\n");
        }
        fclose($f);
    }

    protected function getOrgsPage($page)
    {
        $ch = curl_init('https://crmbg.su/actions.php');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Cookie: PHPSESSID=6mu3svdm2bgl3tpgr3oihc5k36'
        ]);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, [
            'action' => 'tender_search',
            'page' => $page,
            'protocol' => 'undefined',
            'sort' => 'id:DESC',
            'price' => 0,
            'online' => 0,
            'type' => 1,
            'first' => 'undefined',
            'onlyone' => 'undefined',
            'contact' => '30.06.2017'
        ]);

        $response = curl_exec($ch);

        curl_close($ch);

        return $response;
    }

    protected function getCardPage($id)
    {
        $ch = curl_init('https://crmbg.su/actions.php');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Cookie: PHPSESSID=6mu3svdm2bgl3tpgr3oihc5k36'
        ]);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, [
            'action' => 'get_card_info',
            'id' => $id,
        ]);

        $response = curl_exec($ch);

        curl_close($ch);

        return $response;
    }

    public function actionOrgs($from = 1, $to = 22)
    {
        for ($i = $from; $i <= $to; $i++) {
            echo 'Получаем организации со страницы ' . $i . PHP_EOL;

            $response = $this->getOrgsPage($i);

            preg_match('/(<table xmlns="http:\/\/www\.w3\.org\/1999\/xhtml" cellapdding="0" cellspacing="0" border="0" width="100%" class="table_result_tender">.*<\/table>)/s', $response, $matches);

            $xml = simplexml_load_string($matches[1]);

            $header = true;

            foreach ($xml->tr as $row) {
                if ($header) {
                    $header = false;
                    continue;
                }

                if (count($row->td) != 10) continue;

                $number = (string) $row->td[1]->a;
                $date = (string) $row->td[3];
                $sum = preg_replace('/[^\d.]/', '', (string) $row->td[4]->nobr);
                $inn = (string) $row->td[6];
                $name = (string) $row->td[7]['data-tip'];
                $region = (string) $row->td[9]['data-tip'];

                $org = new Organization();
                $org->name = $name;
                $org->date = $date;
                $org->maximum_sum = $sum;
                $org->inn = $inn;
                $org->number = $number;
                $org->region = $region;

                $org->save();

                var_dump($number);
                var_dump($date);
                var_dump($sum);
                var_dump($inn);
                var_dump($name);
                var_dump($region);
                var_dump('----------------------------');

                $response = $this->getCardPage($inn);

                preg_match('/<td align="right">\s+Телефон\s+<\/td><td align="right">(.*?)<\/td>/s', $response, $mainPhone);
                $mainPhone = $mainPhone[1];

                preg_match('/<td align="right">\s+Email\s+<\/td><td align="right">(.*?)<\/td>/s', $response, $mainEmail);
                $mainEmail = $mainEmail[1];

                $orgPhone = new OrganizationPhone();
                $orgPhone->org_id = $org->id;
                $orgPhone->name = 'main';
                $orgPhone->phone = $mainPhone;
                $orgPhone->save();

                $orgEmail = new OrganizationEmail();
                $orgEmail->org_id = $org->id;
                $orgEmail->name = 'main';
                $orgEmail->email = $mainEmail;
                $orgEmail->save();

                preg_match_all('/(<table class="contacts" width="100%" cellpadding="0" cellspacing="0">.*?<\/table>)/s', $response, $matches);

                $phones = simplexml_load_string($matches[1][1]);

                foreach ($phones->tr as $phoneRow) {
                    if (count($phoneRow->td) != 5) continue;

                    $phoneName = (string) $phoneRow->td[1]['data-tip'];
                    $phoneValue = (string) $phoneRow->td[2]['data-tip'];

                    $orgAdditionalPhone = new OrganizationPhone();
                    $orgAdditionalPhone->org_id = $org->id;
                    $orgAdditionalPhone->name = $phoneName;
                    $orgAdditionalPhone->phone = $phoneValue;
                    $orgAdditionalPhone->save();
                }

                $emails = simplexml_load_string($matches[1][0]);

                foreach ($emails->tr as $emailRow) {
                    if (count($emailRow->td) != 5) continue;

                    $emailName = (string) $emailRow->td[1]['data-tip'];
                    $emailValue = (string) $emailRow->td[2]['data-tip'];

                    $orgAdditionalEmail = new OrganizationEmail();
                    $orgAdditionalEmail->org_id = $org->id;
                    $orgAdditionalEmail->name = $emailName;
                    $orgAdditionalEmail->email = $emailValue;
                    $orgAdditionalEmail->save();
                }
            }
        }
    }
}
