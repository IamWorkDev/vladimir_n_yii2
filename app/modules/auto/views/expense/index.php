<?php

use yii\helpers\Html;
use common\components\GridView;
use yii\widgets\Pjax;
use yii\bootstrap\ActiveForm;
use dosamigos\datepicker\DatePicker;
use yii\helpers\Url;
use common\models\frontend\Auto;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $searchModel common\models\frontend\TransactionSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Transactions';
?>
<div class="body-content-header">
    <div class='col-md-12'>
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
        <div class='row'>

            <?= $form->field($searchModel, 'auto_id',
                ['template' => '<div class="col-md-4"><div class="col-md-3">{label}</div><div class="col-md-9 no-margin">{input}</div></div>'])
                ->widget(Select2::classname(), [
                    'data' => ArrayHelper::map(Auto::getAll(), 'id', 'auto_name'),
                    'options' => ['placeholder' => 'Select Auto'],
                    'pluginOptions' => [
                        'allowClear' => true
                    ]

                    /*
                    'pluginOptions' => [
                        'width' => '100px',
                    ],
                    */
                ])


            ?>
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

        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>
<div class="body-content-options">
    <div class="col-md-12">
    </div>
</div>
<div>
    <?php Pjax::begin(['id' => 'transaction-pjax-grid']); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'showFooter' => true,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'date',
                'format' => 'date',
                'contentOptions' => ['style' => 'width:90px;']
            ],
            'bankAccount.name',
            [
                'label' => 'Method',
                'value' => function ($model) {
                    $val = $model->method_id;
                    if ($model->check_number) {
                        $val = $val . ' #' . $model->check_number;
                    }
                    return $val;
                },
                'contentOptions' => ['class' => 'text-left'],
            ],
            [
                'attribute' => 'source_name',
                'label' => 'To/From',
                'contentOptions' => ['class' => 'text-left'],
            ],
            [
                'attribute' => 'funds_in',
                'label' => 'Funds in',
                'footer' => $searchModel->funds_in_total
            ],
            [
                'attribute' => 'funds_out',
                'label' => 'Funds out',
                'footer' => $searchModel->funds_out_total
            ],
            [
                'attribute' => 'category.name',
                'label' => $searchModel->getAttributeLabel('category'),
                'contentOptions' => ['class' => 'text-left'],
            ],
            [
                'attribute' => 'customTag.name',
                'label' => $searchModel->getAttributeLabel('custom_tag'),
                'contentOptions' => ['class' => 'text-left'],

            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'contentOptions' => ['style' => 'display:none;'],
                'template' => '{update}',
                'buttons' => [
                    'update' => function ($url, $model) {
                        return Html::button('<span class="glyphicon glyphicon-pencil"></span>', [
                            'title' => 'Update',
                            'class' => 'btn btn-link update',
                            'data-action' => 'update',
                            'data-url' => Url::to([
                                'transaction/update',
                                'id' => $model->id
                            ])
                        ]);
                    },
                ]
            ],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>

<?= \app\components\PjaxModalForm::get([
    'modalId' => 'modal',
    'modalHeader' => 'Transaction',
    'selectorActivation' => '#btnAdd,[data-action="update"]',
    'formId' => 'transaction-form',
    'gridPjaxId' => 'transaction-pjax-grid'
]) ?>
