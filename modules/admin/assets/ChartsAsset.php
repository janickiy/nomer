<?php
namespace app\modules\admin\assets;

use yii\web\AssetBundle;

class ChartsAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [

    ];

    public $js = [
        'js/amcharts/amcharts.js',
        'js/amcharts/serial.js',
        'js/amcharts/themes/light.js',
    ];

    public $depends = [
        'app\modules\admin\assets\GlobalAsset',
    ];
}
