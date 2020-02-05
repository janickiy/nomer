<?php
namespace app\modules\admin\assets;

use yii\web\AssetBundle;

class ThemeAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'metronic/global/css/components.css',
        'metronic/global/css/plugins.css',
        'metronic/layout/css/layout.css',
        'metronic/layout/css/themes/default.css',
        'metronic/layout/css/custom.css',
    ];

    public $js = [
        'metronic/global/scripts/app.js',
        'metronic/layout/scripts/layout.js',
    ];

    public $depends = [
        'app\modules\admin\assets\GlobalAsset',
    ];
}
