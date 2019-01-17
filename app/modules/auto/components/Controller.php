<?php

namespace app\modules\auto\components;

use Yii;
use common\models\frontend\Auto;
use common\components\Helper;

/**
 * Controller
 */
class Controller extends \app\components\Controller
{
    public $relatedLinks = [
        '/auto' => 'Track Auto',
        '/auto/expense' => 'Actual Expense',
        '/auto/manage-auto' => 'Manage Autos',
        '/auto/auto-annual' => 'Auto Annual',
        '/auto/first-time' => 'First Time In Help',
    ];


    public function getInfoWidget()
    {
        $businessMiles = Auto::getTotalMileage(true);
        $actualExpense = Auto::getActualExpense();
        $estMileageDed = Auto::getMileageDed();
        $estActualDed = Auto::getEstimatedDeduction();
        $result = [
            'header' => date('Y') . ' YTD AUTO METER',
            'items' => [
                'METER' => [
                    'items' => [
                        'Business Miles' => Helper::formatNumber($businessMiles, ''),
                        'Actual Expense' => Helper::formatNumber($actualExpense, ''),
                        'Est Mileage Ded' => Helper::formatNumber($estMileageDed),
                        'Est Actual Ded' => Helper::formatNumber($estActualDed, ''),
                    ],
                ],
            ],
        ];

        return $result;
    }
}
