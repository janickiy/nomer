<?php
namespace app\modules\admin;

use yii\base\Module;

class AdminModule extends Module
{
    public $controllerNamespace = 'app\modules\admin\controllers';

    public $defaultRoute = 'dashboard';

    public $layout = 'main';
}
