<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use dosamigos\datepicker\DatePicker;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model common\models\frontend\Auto */
/* @var $form yii\widgets\ActiveForm */
?>

<?php Pjax::begin(); ?>
<?php $form = ActiveForm::begin([
    'options' => [
        'class' => 'form-horizontal',
        'data-pjax' => true,
        'id' => 'auto-form'
    ],
    'fieldConfig' => [
        'template' => "<div class=\"col-md-3\">{label}</div>\n<div class=\"col-md-9\">{input}</div>\n<div class=\"col-md-12\">{error}</div>",
    ],
]); ?>

<?= $form->field($model, 'auto_name')
    ->textInput(['maxlength' => true]) ?>

<?= $form->field($model, 'make')
    ->textInput(['maxlength' => true]) ?>

<?= $form->field($model, 'model')
    ->textInput(['maxlength' => true]) ?>

<?= $form->field($model, 'purch_date')
    ->widget(DatePicker::className()) ?>

<?= $form->field($model, 'cost')
    ->textInput() ?>

<?= $form->field($model, 'service_entry_date')
    ->widget(DatePicker::className()) ?>

<?= $form->field($model, 'active')
    ->defaultValue($model::ACTIVE_ACTIVE)
    ->label('Status')
    ->radioList([
        'Y' => 'Active',
        'N' => 'Inactive',
    ], [
        'item' => function ($index, $label, $name, $checked, $value) use ($model) {
            $id = Html::getInputId($model, 'active') . '_' . $index;

            return '<div class="col-md-6 labeled">' . Html::radio($name, $checked, [
                    'id' => $id,
                    'value' => $value
                ]) . '<label for="' . $id . '"><span>' . $label . '</span></label></div>';
        },
    ]) ?>

<div class="form-group modal-footer">
    <?= Html::submitButton('<span class="glyphicon glyphicon-ok-circle"></span>' . ($model->isNewRecord
            ? ' Add'
            : ' Update'), [
        'class' => 'btn btn-success'
    ]) ?>
    <button type="button" class="btn btn-success" data-dismiss="modal"><span
                class="glyphicon glyphicon-ban-circle"></span> Cancel
    </button>
</div>

<?php ActiveForm::end(); ?>
<?php Pjax::end(); ?>
