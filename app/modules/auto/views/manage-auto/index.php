<?php

use yii\helpers\Html;
use yii\helpers\Url;
use common\components\GridView;
use yii\widgets\Pjax;
use common\models\frontend\Auto;

/* @var $dataProvider yii\data\ActiveDataProvider */
?>

<div class="body-content-options">
    <div class="col-md-12">
        <div class="row">
            <div class="col-md-6">
                <div class="row">
                    <button class="btn btn-success plus" id="btnAutoAdd" data-url="<?= Url::to([
                        'create',
                    ]) ?>"><i
                                class="fa fa-plus" aria-hidden="true"> </i> Add New Auto
                    </button>
                </div>
            </div>
            <div class="col-md-6 actions">
                <button title="Update" data-action="update" disabled="disabled" class="btn btn-action"
                        type="button">
                    <span class="action edit"></span>
                </button>
            </div>
        </div>
    </div>
</div>
<div>
    <?php Pjax::begin(['id' => 'auto-pjax-grid']) ?>
    <?= GridView::widget([
        'id' => 'table',
        'dataProvider' => $dataProvider,
        'showFooter' => true,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'label' => 'Active',
                'attribute' => 'active',
                'contentOptions' => ['style' => 'width:70px;']
            ],
            [
                'label' => 'Description/Name',
                'attribute' => 'auto_name',
                'contentOptions' => ['class' => 'text-left']
            ],
            [
                'attribute' => 'make',
                'contentOptions' => ['style' => 'width:70px;']
            ],


            [
                'label' => 'Model',
                'attribute' => 'model',
                'contentOptions' => ['class' => 'text-left']
            ],
            [
                'label' => 'YTD Business Miles Tracked',
                'value' => function ($model) {
                    return $model->total_mileage;
                },
                'contentOptions' => ['style' => 'width:100px;'],
                'footer' => Auto::getTotalMileage(true)
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{update}',
                'contentOptions' => ['style' => 'display:none;'],
                'buttons' => [
                    'update' => function ($url, $model) {
                        return Html::button('<span class="glyphicon glyphicon-pencil"></span>', [
                            'title' => 'Update',
                            'data-action' => 'update',
                            'class' => 'btn btn-link',
                            'data-url' => Url::to([
                                'manage-auto/update',
                                'id' => $model->id
                            ])
                        ]);
                    }
                ],
            ],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>

<?= \app\components\PjaxModalForm::get([
    'modalId' => 'modalAuto',
    'modalHeader' => 'Auto',
    'selectorActivation' => '#btnAutoAdd,[data-action="update"]',
    'formId' => 'auto-form',
    'gridPjaxId' => 'auto-pjax-grid'
]) ?>
