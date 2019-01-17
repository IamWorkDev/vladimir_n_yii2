<?php

namespace app\modules\auto\controllers;

use Yii;
use app\modules\auto\components\Controller;

/**
 * FirstTimeController
 */
class FirstTimeController extends Controller
{
    /**
     * Get help page.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        return $this->render('index');
    }
}
