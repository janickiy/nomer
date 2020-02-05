<?php
namespace app\commands;

use app\components\Qiwi;
use app\models\Site;
use app\models\Wallet;
use yii\base\Exception;
use yii\data\ArrayDataProvider;
use yii\db\Expression;
use yii\console\Controller;
use yii\helpers\ArrayHelper;
use yii\helpers\Console;
use yii\helpers\Json;

class WalletsController extends Controller {
    protected function getProxy()
    {
        $cachedProxy = \Yii::$app->getCache()->get('proxy');

        try {
            $proxies = file_get_contents('http://nalevo.net/qiwiproxy.php');
            $proxies = Json::decode($proxies);
            if (count($proxies) == 0) throw new \Exception();
        } catch (\Exception $e) {
            if (!$cachedProxy) throw new \Exception('No proxy available');
            return $cachedProxy;
        }

        $proxy = $proxies[array_rand($proxies)];
        \Yii::$app->getCache()->set('proxy', $proxy);
        return $proxy;
    }

    public function actionIndex() {
        $wallets = Wallet::find()->where(["type_id" => Wallet::TYPE_YANDEX])->orderBy(["id" => SORT_ASC])->all();

        foreach($wallets as $w) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://passport.yandex.ru/auth');
            curl_setopt($ch, CURLOPT_POSTFIELDS, 'login='.urlencode($w->login).'&passwd='.urlencode($w->password)."&retpath=".urlencode("https://money.yandex.ru/new")."&from=money&origin&timestamp");
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_HEADER, 1);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($ch, CURLOPT_COOKIEJAR, \Yii::getAlias('@runtime')."/yandex.".$w->wallet_id.".cookie");
            curl_setopt($ch, CURLOPT_COOKIEFILE, \Yii::getAlias('@runtime')."/yandex.".$w->wallet_id.".cookie");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:51.0) Gecko/20100101 Firefox/51.0");
            $response = curl_exec($ch);

            if(preg_match("#<span class=\"price__whole-amount\">(.+?)</span>#", $response, $m)) {
                $balance = preg_replace("/\&\#160\;/", "", $m[1]);
                $w->balance = $balance;
                $w->tm_last_balance = new Expression('NOW()');
                $w->save();

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, 'https://money.yandex.ru/ajax/history/partly?history_shortcut=history_all&search=&start-record=0&record-count=100');
                curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);
                curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);
                curl_setopt($ch, CURLOPT_TIMEOUT, 30);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
                curl_setopt($ch, CURLOPT_COOKIEJAR, \Yii::getAlias('@runtime')."/yandex.".$w->wallet_id.".cookie");
                curl_setopt($ch, CURLOPT_COOKIEFILE, \Yii::getAlias('@runtime')."/yandex.".$w->wallet_id.".cookie");
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:51.0) Gecko/20100101 Firefox/51.0");

                $response = curl_exec($ch);

                $data = Json::decode($response);
                foreach($data["history"] as $item) {
                    if((int)$item["type"] == 1 || (int)$item["type"] == 4) {
                        $w->tm_last_transaction = date("Y-m-d H:i:s", strtotime(\Yii::$app->formatter->asDatetime($item["date"], "yyyy-MM-dd HH:mm:ss")) + 60 * 60 * 3);
                        $w->save();
                        break;
                    }
                }

                foreach($data["history"] as $item) {
                    if((int)$item["type"] == 2 || $item["sum"] < 0) {
                        $w->tm_last_transaction_out = date("Y-m-d H:i:s", strtotime(\Yii::$app->formatter->asDatetime($item["date"], "yyyy-MM-dd HH:mm:ss")) + 60 * 60 * 3);
                        $w->save();
                        break;
                    }
                }
            }
        }

//        $proxy = 'socks5://proxy:q2LVelfhoNbo@' . $this->getProxy();
        //$proxy_ip = file_get_contents("https://awmproxy.com/getproxy.php?country=ru");
        //$proxy = 'socks5://'.trim($proxy_ip);
        /*
        $proxy = 'socks5://TG:tel.gg@proxy.rip:50000';
        $ruCaptcha = [
            'proxyType' => 'SOCKS5',
            'apiKey' => '0d4004a0d4b7510706ca98dd09f3ec17',
            'googleToken' => '6LfjX_4SAAAAAFfINkDklY_r2Q5BRiEqmLjs4UAC'
        ];

        $wallets = Wallet::find()->where(["type_id" => Wallet::TYPE_QIWI])->andWhere(["NOT IN", "login", ["79295595495", "79295044638"]])->orderBy(["id" => SORT_ASC])->all();
        foreach($wallets as $w) {
            Console::output("get data for ".$w->login);
            $Qiwi = new Qiwi(null, null, '', $proxy, $ruCaptcha);
            $Qiwi->setCookieFile(\Yii::getAlias('@runtime').'/qiwi'.$w->login.'.cookie');

            $p = "";
            if($w->login == "79269516206") {
                $p = "Admeo!31337";
            } else {
                $p = "Ag6K2oxG2";
            }

            try {
                $Qiwi->login($w->login, $p);
            } catch (\Exception $e) {
                print_r($e->getMessage());
                continue;
            }

            try {
                $balances = $Qiwi->wallets();
                if($balances) {
                    $w->balance = $balances["RUB"];
                    $w->tm_last_balance = new Expression('NOW()');
                    $w->save();
                }
            } catch (Exception $e) {
                Console::output("Не получили баланс у ".$w->login);
                continue;
            }

            $transactions = $Qiwi->transactions(TRUE, date("d.m.Y", (time()-86400*7)), date("d.m.Y", time()+86400));
            foreach($transactions as $t) {
                if((!(int)$t["incoming"]) && $t["status"] == "SUCCESS") {
                    $w->tm_last_transaction_out = preg_replace("/(\d\d)\.(\d\d).(\d\d\d\d)/", "$3-$2-$1", $t["date"])." ".$t["time"];
                    $w->save();
                    break;
                }
            }
            foreach($transactions as $t) {
                if(((int)$t["incoming"]) && $t["status"] == "SUCCESS") {
                    $w->tm_last_transaction = preg_replace("/(\d\d)\.(\d\d).(\d\d\d\d)/", "$3-$2-$1", $t["date"])." ".$t["time"];
                    $w->save();
                    break;
                }
            }
        }
        */
    }

    public function actionSites()
    {
        foreach (Site::find()->all() as $site) {
            /* @var $site Site */

            echo $site->name . PHP_EOL;

            if ($site->yandex_money_account) {
                echo 'Yandex Money ' . $site->yandex_money_account . PHP_EOL;

                $wallet = Wallet::find()
                    ->where(['type_id' => Wallet::TYPE_YANDEX])
                    ->andWhere(['wallet_id' => $site->yandex_money_account])
                    ->one();

                if ($wallet) {
                    echo 'Кошелёк ' . $wallet->id . PHP_EOL;
                    $wallet->site_id = $site->id;
                    $wallet->save();
                } else {
                    echo 'Кошелёк не найден' . PHP_EOL;
                }
            }

            if ($site->phone) {
                echo 'Qiwi ' . $site->phone . PHP_EOL;

                $wallet = Wallet::find()
                    ->where(['type_id' => Wallet::TYPE_QIWI])
                    ->andWhere(['wallet_id' => '+' . $site->phone])
                    ->one();

                if ($wallet) {
                    echo 'Кошелёк ' . $wallet->id . PHP_EOL;
                    $wallet->site_id = $site->id;
                    $wallet->save();
                } else {
                    echo 'Кошелёк не найден' . PHP_EOL;
                }
            }
        }
    }
}