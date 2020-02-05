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
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        '//fonts.googleapis.com/css?family=Open+Sans:400,300,400italic,300italic,600,600italic,700,700italic,800italic,800&amp;subset=latin,cyrillic-ext,cyrillic,latin-ext',
        'css/site.css',
        'css/swipebox.min.css',
        'css/a.css',
        'css/jquery-confirm.min.css'
    ];
    public $js = [
        'https://vk.com/js/api/openapi.js?146',
        'js/swfobject-2.2.min.js',
        'js/socket.io.min.js',
        'js/jquery.swipebox.min.js',
        'js/masonry.pkgd.min.js',
        'js/jquery-confirm.min.js',
        'js/app.js',

    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\web\JqueryAsset',
//        'yii\bootstrap\BootstrapAsset',
//        'yii\bootstrap\BootstrapPluginAsset',
        'yii\authclient\widgets\AuthChoiceAsset',
        'yii\widgets\MaskedInputAsset'
    ];
}
