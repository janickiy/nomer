<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\assets;

use yii\web\AssetBundle;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class ZAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = 'http://z.fcheck.ru';
    public $css = [
        '//fonts.googleapis.com/css?family=Open+Sans:400,300,400italic,300italic,600,600italic,700,700italic,800italic,800&amp;subset=latin,cyrillic-ext,cyrillic,latin-ext',
        'css/frame.css',
        'css/jquery.fancybox.min.css',
    ];
    public $js = [
        'https://cdnjs.cloudflare.com/ajax/libs/fingerprintjs2/1.5.0/fingerprint2.min.js',
        'js/swfobject-2.2.min.js',
        'js/evercookie.js',
        'js/socket.io.min.js',
        'js/jquery.fancybox.min.js',
        'js/app.js'
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
        'yii\bootstrap\BootstrapPluginAsset',
        'yii\authclient\widgets\AuthChoiceAsset'
    ];
}
