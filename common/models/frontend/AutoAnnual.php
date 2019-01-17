<?php

namespace common\models\frontend;

use Yii;

/**
 * This is the model class for table "{{%AUTO_ANNUAL}}".
 *
 * @property string $auto_id
 * @property integer $year
 * @property double $begin_odo
 * @property double $end_odo
 */
class AutoAnnual extends \common\components\AppActiveRecord
{
    /**
     * Using table name
     *
     * @return  string
     */
    public static function tableName()
    {
        return '{{%AUTO_ANNUAL}}';
    }

    /**
     * Get the validation rules that apply to the model.
     *
     * @return array
     */
    public function rules()
    {
        return [
            [
                [
                    'auto_id',
                    'year'
                ],
                'required'
            ],
            [
                [
                    'auto_id', 'year'
                ],
                'unique',
                'targetAttribute' => ['auto_id', 'year'],
                'message' => 'Auto and Year have already been taken'
            ],
            [
                ['year'],
                'integer'
            ],
            [
                [
                    'begin_odo',
                    'end_odo'
                ],
                'number'
            ],
            [
                ['auto_id'],
                'string',
                'max' => 32
            ],
        ];
    }

    /**
     * Get the labels for attributes
     *
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'auto_id' => 'Auto ID',
            'year' => 'Year',
            'begin_odo' => 'Begin Odo',
            'end_odo' => 'End Odo',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAuto()
    {
        return $this->hasOne(Auto::className(), ['id' => 'auto_id']);
    }
}
