<?php

use app\components\AuthClientCollection;

$params = require(__DIR__ . '/params.php');

$config = [
    'id' => $_SERVER["HTTP_HOST"],
    'name' => preg_replace("/dev\-alexander\./", "", $_SERVER["HTTP_HOST"]),
    'basePath' => dirname(__DIR__),
    'charset' => 'utf-8',
    'bootstrap' => ['log', 'queue'],
    'language' => 'ru',
    'sourceLanguage' => 'ru',
    'timezone' => 'Europe/Moscow',
    'modules' => [
        'admin' => [
            'class' => 'app\modules\admin\AdminModule',
        ],
        'api' => [
            'class' => 'app\modules\api\ApiModule',
        ],
    ],
    'components' => [
        'queue' => [
            'class' => \zhuravljov\yii\queue\file\Queue::class,
            'as log' => \zhuravljov\yii\queue\LogBehavior::class,
            'path' => '@runtime/queue'
            // Other driver options
        ],
        'reCaptcha' => [
            'name' => 'reCaptcha',
            'class' => 'himiklab\yii2\recaptcha\ReCaptcha',
            'siteKey' => '6LdpNCMUAAAAAIcP8mBuH3JhDl8zP2QADGoFtVYw',
            'secret' => '6LdpNCMUAAAAABTYWw_Eaca7iGlbXaCWWe0fqqp7',
        ],
        'formatter' => [
            'class' => 'app\components\Formatter',
            'numberFormatterOptions' => [
                NumberFormatter::MIN_FRACTION_DIGITS => 0,
                NumberFormatter::MAX_FRACTION_DIGITS => 2,
            ],
            'defaultTimeZone' => 'Europe/Moscow',
            'nullDisplay' => ''
        ],
        'devicedetect' => [
            'class' => 'alexandernst\devicedetect\DeviceDetect'
        ],
        'authClientCollection' => [
            'class' => AuthClientCollection::class,
        ],
        'assetManager' => [
            'appendTimestamp' => true,
        ],
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'HZ5DgjL?LqV~VcVq?EtdmrIWBsz{%WHl*ceiTJvV?*{PlEha|7b~6kv1bF~acxWG',
        ],
        'cache' => [
            'class' => 'yii\caching\MemCache',
            'useMemcached' => true
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                'host' => 'smtp.gmail.com',
                'username' => 'no-reply@nomer.io',
                'password' => 'cJqLmVysMr2C',
                'port' => 465,
                'encryption' => 'ssl',
            ],
        ],
        'session' => [
            'cookieParams' => [
                'domain' => $_SERVER["HTTP_HOST"],
                'httpOnly' => true,
            ],
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                    'except' => ['yii\web\HttpException:404', 'yii\web\HttpException:400', 'yii\web\HttpException:403']
                ],
                [
                    'enabled' => (YII_ENV != 'dev'?true:false),
                    'class' => 'airani\log\TelegramTarget',
                    'levels' => ['error'],
                    'botToken' => '377062288:AAEs1S7OPwRym49su6624iITRzmPddr_A4E', // bot token secret key
                    'chatId' => '-1001076571212', // chat id or channel username with @ like 12345 or @channel
                    'except' => ['yii\web\HttpException:404', 'yii\web\HttpException:400', 'yii\web\HttpException:403']
                ],
            ],
        ],
        'db' => require(__DIR__ . '/db.php'),
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            //'cache' => false,
            'rules' => [
                'https://apinomer.com/contact'                                     => 'api/contact/index',
                'https://apinomer.com/status'                                      => 'api/buy/status',
                'https://apinomer.com/buy'                                      => 'api/buy/index',
                'https://apinomer.com/free'                                      => 'api/free/index',

                'https://apinomer.com/ponomeru'                             => 'api/ponomeru/index',

                'https://apinomer.com/check/<phone:\d+>'                             => 'api/check/index',


                'https://apinomer.com/results/<id:\d+>'                             => 'api/result/index',
                'https://apinomer.com/search/'                                       => 'api/search/index',
                'https://apinomer.com/telegram/<phone:\d+>'                          => 'api/telegram/index',
                'https://apinomer.com/plans/'                                        => 'api/plans/index',
                'https://apinomer.com/info/'                                         => 'api/info/index',
                'https://apinomer.com/search'                                       => 'api/search/index',
                'https://apinomer.com/plans'                                        => 'api/plans/index',
                'https://apinomer.com/info'                                         => 'api/info/index',
                'https://apinomer.com/call'                                         => 'api/call/index',
                'https://apinomer.com/notify/<token:>'                                         => 'api/notify/index',
                'https://apinomer.com/history'                                         => 'api/history/index',

                'https://apinomer.com/exit'                                       => 'api/signin/exit',
                'https://apinomer.com/signin/gg'                                       => 'api/signin/google',
                'https://apinomer.com/signin/fb'                                       => 'api/signin/facebook',
                'https://apinomer.com/signin/vk'                                       => 'api/signin/vk',
                'https://apinomer.com/signin'                                       => 'api/signin/index',
                'https://apinomer.com/signup'                                       => 'api/signin/reg',

                'https://apinomer.com/'                                       => 'site/api',

                'https://tels.gg/c/<code:\w+>' => 'site/code',
                'https://tels.io/c/<code:\w+>' => 'site/code',

                'https://wcaller.com' => 'wcaller/index',
                'https://wcaller.ru' => 'wcaller/index',


                'ios_coming_soon' => 'site/ios',
                'find-phone'                                    => 'site/find-phone',
                'signup'                                        => 'site/signup',
                'signin'                                        => 'site/signin',
                'remind'                                        => 'site/remind',
                'logout'                                        => 'site/logout',
                'auth'                                          => 'site/auth',
                'confirm'                                       => 'site/confirm',
                'contact'                                      => 'site/contacts',
                'remind/<token:>'                               => 'site/remind',

                'ref<id:\d+>' => 'referrals/new',

                'fp/<hash>.gif' => 'site/fingerprint',
                'ec/<hash>.gif' => 'site/evercookie',

                'images/<uuid>.jpg' => 'site/image',

                'retargeting/<uuid>.gif'                    => 'retargeting/pic',
                'get/<uuid>'                                => 'retargeting/redirect',

                'http://z.fcheck.ru/<phone:8[\d]{10}>'                             => 'frame/index',
                'http://z.fcheck.ru/<phone:8[\d]{10}>/<action>/<id:\d+>'           => 'frame/<action>',
                'http://z.fcheck.ru/<phone:8[\d]{10}>/<action>'                    => 'frame/<action>',


                '<phone:8[\d]{10}>'                             => 'result/index',
                '<phone:8[\d]{10}>/<action>/<id:\d+>'           => 'result/<action>',
                '<phone:8[\d]{10}>/<action>'                    => 'result/<action>',

                '<module:(admin)>'                                  => 'admin/dashboard/index',
                '<module:(admin)>/<controller:\w+>'                     => 'admin/<controller>/index',
                '<module:(admin)>/<controller:\w+>/<action>/<id:\d+>'   => 'admin/<controller>/<action>',
                '<module:(admin)>/<controller:\w+>/<action>'            => 'admin/<controller>/<action>',

                '<controller:[A-Za-z]+>'                              => '<controller>/index',
                '<controller:[A-Za-z]+>/<action>/<id:\d+>'            => '<controller>/<action>',
                '<controller:[A-Za-z]+>/<action>'                     => '<controller>/<action>',




                [
                    'pattern' => '<phone>',
                    'route' => 'site/redirect',
                    'mode' => \yii\web\UrlRule::PARSING_ONLY
                ],
            ],
        ],
    ],
    'params' => $params,
];

if (YII_ENV_DEV && in_array($_SERVER["REMOTE_ADDR"], ['81.88.218.82', '82.204.203.174', '127.0.0.1'])) {
    // configuration adjustments for 'dev' environment
    //$config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        'allowedIPs' => ['81.88.218.82', '82.204.203.174', '127.0.0.1'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];
}

return $config;

