<?php
namespace app\modules\api;

use yii\base\Module;

class ApiModule extends Module
{
    public $controllerNamespace = 'app\modules\api\controllers';

    public $defaultRoute = 'search';

    public $layout = null;
}
