<?php

namespace common\models\frontend;

use app\components\validators\OdometerValidator;
use Yii;

/**
 * This is the model class for table "{{%AUTO_MILEAGE}}".
 *
 * @property string $id
 * @property string $auto_id
 * @property string $date
 * @property string $date_entered
 * @property double $odo_start
 * @property double $odo_end
 * @property double $mileage
 * @property string $note
 * @property string $locked
 */
class AutoMileage extends \common\components\AppActiveRecord
{
    /**
     * Using table name
     *
     * @return  string
     */
    public static function tableName()
    {
        return '{{%AUTO_MILEAGE}}';
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
                    'date',
                    'auto_id',
                ],
                'required'
            ],
            [
                [
                    'odo_start',
                    'odo_end',
                ],
                'required',
                'when' => function ($model) {
                    return is_null($model->mileage);
                },
                'whenClient' => "function (attribute, value) {
                    return $('#automileage-mileage').val() == '';
                }",
                'message' => '*',
            ],
            [
                ['mileage',],
                'required',
                'whenClient' => "function (attribute, value) {
                    return $('#automileage-odo_start').val() == '' && $('#automileage-odo_end').val() == '';
                }",
                'message' => '*',
            ],
            [
                [
                    'odo_start',
                    'odo_end',
                ],
                OdometerValidator::className(),
            ],
            [
                [
                    'note',
                ],
                'safe'
            ],
            [
                [
                    'odo_start',
                    'odo_end',
                    'mileage'
                ],
                'number'
            ],
            [
                [
                    'id',
                    'auto_id'
                ],
                'string',
                'max' => 32
            ],
            [
                ['note'],
                'string',
                'max' => 255
            ],
            [
                ['locked'],
                'string',
                'max' => 1
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
            'id' => 'ID',
            'auto_id' => 'Auto',
            'date' => 'Date',
            'date_entered' => 'Date Entered',
            'odo_start' => 'Starting Odometer',
            'odo_end' => 'Ending Odometer',
            'mileage' => 'Business Trip Mileage',
            'note' => 'Memo',
            'locked' => 'Locked',
        ];
    }

    /**
     * Get the value for date attribute
     *
     * @return bool
     */
    public function afterFind()
    {
        $this->date = $this->toAppDate($this->date);

        parent::afterFind();
    }

    /**
     * Set the value for date attribute
     *
     * @return bool
     */
    public function beforeValidate()
    {
        $this->date = $this->toStorageDate($this->date);

        return parent::beforeValidate();
    }

    /**
     * Set the values for odo_start and odo_end attributes
     *
     * @return bool
     */
    public function afterValidate()
    {
        parent::afterValidate();
        if (!$this->odo_start) {
            $this->odo_start = 0;
        }
        if (!$this->odo_end) {
            $this->odo_end = 0;
        }
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAuto()
    {
        return $this->hasOne(Auto::className(), ['id' => 'auto_id']);
    }

    /**
     * Get last mileage date
     *
     * @param $year
     *
     * @return string
     */
    public static function getLast($year = null) {
        $query = self::find();

        if (!is_null($year)) {
            $query->where('date>=:year',[':year' =>$year]);
            $query->andWhere('DATE_ADD(:year,INTERVAL 1 YEAR)>date',[':year' =>$year]);
        }

        return $query->max('date');
    }
}
