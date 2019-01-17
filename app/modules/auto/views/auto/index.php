<?php

use kartik\select2\Select2;
use dosamigos\datepicker\DatePicker;
use common\components\GridView;
use yii\widgets\Pjax;
use yii\helpers\ArrayHelper;
use common\models\frontend\Auto;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $searchModel common\models\frontend\AutoMileageSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

?>
<div class="body-content-header">
    <div class='col-md-12'>
        <div class='row'>
            <?php $form = ActiveForm::begin([
                'action' => ['index'],
                'method' => 'get',
                'options' => [
                    'class' => 'form-horizontal'
                ],
                'fieldConfig' => [
                    'options' => [
                        'tag' => false,
                    ]
                ],
            ]); ?>

            <?= $form->field($searchModel, 'auto_id',
                ['template' => '<div class="col-md-4"><div class="col-md-2">{label}</div><div class="col-md-10">{input}</div></div>'])
                ->widget(Select2::classname(), [
                    'data' => ArrayHelper::map(Auto::getAll(), 'id', 'auto_name'),
                    'options' => ['placeholder' => 'Select Auto'],
                    'pluginOptions' => [
                        'allowClear' => true
                    ]
                ]) ?>
            <div class="col-md-5">
                <div class="date">
                    <?= $form->field($searchModel, 'dateFrom', ['template' => '{label}{input}'])
                        ->widget(DatePicker::className(), ['addon' => ''])
                        ->label('Date Range') ?>
                </div>
                <div class="date">
                    <?= $form->field($searchModel, 'dateTo', ['template' => '{label}{input}'])
                        ->widget(DatePicker::className(), ['addon' => ''])
                        ->label('To') ?>
                </div>
            </div>
            <div class="col-md-3">
                <?= Html::submitButton('Search', ['class' => ' btn btn-success']) ?>
                <?= Html::a('Reset', [''], ['class' => 'btn btn-success']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
<div class="body-content-options">
    <div class="col-md-12">
        <div class="row">
            <div class="col-md-6">
                <div class="row">
                    <button class="btn btn-success plus" id="btnMileageAdd" data-url="<?= Url::to([
                        'create',
                    ]) ?>"><i
                                class="fa fa-plus" aria-hidden="true"> </i> Add
                    </button>
                </div>
            </div>
            <div class="col-md-6 actions">
                <button title="Update" data-action="update" disabled="disabled" class="btn btn-action"
                        type="button">
                    <span class="action edit"></span>
                </button>
                <button title="Delete" data-action="delete" disabled="disabled" class="btn btn-action"
                        type="button">
                    <span class="action delete"></span>
                </button>
            </div>
        </div>
    </div>
</div>
<div>
    <?php Pjax::begin(['id' => 'mileage-pjax-grid']) ?>
    <?= GridView::widget([
        'id' => 'table',
        'dataProvider' => $dataProvider,
        'showFooter' => true,
        'columns' => [
            [
                'class' => 'yii\grid\SerialColumn',
                'contentOptions' => ['style' => 'width:50px;']
            ],
            [
                'label' => 'Auto',
                'attribute' => 'auto.auto_name',
                'contentOptions' => ['style' => 'width:170px;', 'class' => 'text-left']
            ],
            [
                'attribute' => 'date',
                'format' => 'date',
                'contentOptions' => ['style' => 'width:90px;']
            ],
            [
                'attribute' => 'odo_start',
                'contentOptions' => ['style' => 'width:100px;']
            ],
            [
                'attribute' => 'odo_end',
                'contentOptions' => ['style' => 'width:100px;']
            ],
            [
                'attribute' => 'mileage',
                'contentOptions' => ['style' => 'width:80px;'],
                'footer' => $searchModel->totalMileage
            ],
            [
                'attribute' => 'note',
                'contentOptions' => ['class' => 'text-left']
            ],

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{update} {delete}',
                'contentOptions' => ['style' => 'display:none;'],
                'buttons' => [
                    'update' => function ($url, $model) {
                        return Html::button('<span class="glyphicon glyphicon-pencil"></span>', [
                            'title' => 'Update',
                            'data-action' => 'update',
                            'class' => 'btn btn-link',
                            'data-url' => Url::to([
                                'auto/update',
                                'id' => $model->id
                            ])
                        ]);
                    },
                    'delete' => function ($url, $model) {
                        return Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, [
                            'data-action' => 'delete',
                            'title' => 'Delete',
                            'data-pjax' => 'mileage-pjax-grid',
                            'data-method' => 'POST'
                        ]);
                    },
                ]
            ],
        ],
    ]); ?>
    <?php Pjax::end() ?>
</div>

<?= \app\components\PjaxModalForm::get([
    'modalId' => 'modalMileage',
    'modalHeader' => 'Mileage',
    'selectorActivation' => '#btnMileageAdd,[data-action="update"]',
    'formId' => 'mileage-form',
    'gridPjaxId' => 'mileage-pjax-grid'
]) ?>
