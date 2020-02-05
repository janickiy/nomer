<?php
namespace app\modules\admin\assets;

use yii\web\AssetBundle;

class GlobalAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        '//fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all',
        'metronic/global/plugins/font-awesome/css/font-awesome.min.css',
        'metronic/global/plugins/simple-line-icons/simple-line-icons.min.css',
        'metronic/global/plugins/bootstrap/css/bootstrap.min.css',
        'metronic/global/plugins/bootstrap-switch/css/bootstrap-switch.min.css',
    ];

    public $js = [
        'metronic/global/plugins/jquery-1.11.0.min.js',
        'metronic/global/plugins/jquery-migrate.min.js',
        'metronic/global/plugins/jquery-ui/jquery-ui-1.10.3.custom.min.js',
        'metronic/global/plugins/bootstrap/js/bootstrap.min.js',
        'metronic/global/plugins/bootstrap-hover-dropdown/bootstrap-hover-dropdown.min.js',
        'metronic/global/plugins/jquery-slimscroll/jquery.slimscroll.min.js',
        'metronic/global/plugins/jquery.blockui.min.js',
        'metronic/global/plugins/jquery.cokie.min.js',
        'metronic/global/plugins/bootstrap-switch/js/bootstrap-switch.min.js',
    ];

    public $depends = [
        'yii\web\YiiAsset',
    ];
}
