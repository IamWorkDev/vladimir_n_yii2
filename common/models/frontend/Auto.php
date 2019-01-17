<?php

namespace common\models\frontend;

use Yii;
use common\components\Helper;

/**
 * This is the model class for table "{{%AUTO}}".
 *
 * @property string $id
 * @property string $auto_name
 * @property string $make
 * @property string $model
 * @property string $purch_date
 * @property double $cost
 * @property double $trade_in_allowance
 * @property string $personal_vehicle
 * @property string $service_entry_date
 * @property double $begin_odo
 * @property string $locked
 * @property string $active
 *
 * @property AutoAnnual[] $autoAnnual
 */
class Auto extends \common\components\AppActiveRecord
{
    const LOCKED_UNLOCKED = 'Y';
    const LOCKED_LOCKED = 'N';

    const ACTIVE_ACTIVE = 'Y';
    const ACTIVE_INACTIVE = 'N';

    public $total_mileage;

    /**
     * Using table name
     *
     * @return  string
     */
    public static function tableName()
    {
        return '{{%AUTO}}';
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
                    'auto_name',
                    'active',
                ],
                'required'
            ],
            [
                [
                    'auto_name',
                    'make',
                    'model',
                ],
                'trim',
            ],
            [
                [
                    'make',
                    'model',
                    'purch_date',
                    'cost',
                    'service_entry_date'
                ],
                'safe'
            ],
            [
                [
                    'cost',
                    'trade_in_allowance',
                    'begin_odo'
                ],
                'number'
            ],
            [
                ['id'],
                'string',
                'max' => 32
            ],
            [
                ['auto_name'],
                'string',
                'max' => 20
            ],
            [
                ['make'],
                'string',
                'max' => 12
            ],
            [
                ['model'],
                'string',
                'max' => 25
            ],
            [
                [
                    'personal_vehicle',
                    'locked',
                    'active'
                ],
                'string',
                'max' => 1
            ],
            [
                ['auto_name'],
                'unique'
            ],
            [
                [
                    'purch_date',
                    'service_entry_date'
                ],
                'default',
                'value' => '0000-00-00'
            ],
            [
                ['cost'],
                'default',
                'value' => '0'
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
            'auto_name' => 'Description/Name',
            'make' => 'Make',
            'model' => 'Model',
            'purch_date' => 'Purchase Date',
            'cost' => 'Purchase Price',
            'trade_in_allowance' => 'Trade In Allowance',
            'personal_vehicle' => 'Personal Vehicle',
            'service_entry_date' => 'Date Placed Into Service',
            'begin_odo' => 'Begin Odo',
            'locked' => 'Locked',
            'active' => 'Active',
        ];
    }

    /**
     * Get the values for purch_date and service_entry_date attributes
     *
     * @return bool
     */
    public function afterFind()
    {
        $this->purch_date = $this->toAppDate($this->purch_date);
        $this->service_entry_date = $this->toAppDate($this->service_entry_date);

        parent::afterFind();
    }

    /**
     * Set the values for purch_date and service_entry_date attributes
     *
     * @return bool
     */
    public function beforeValidate()
    {
        $this->purch_date = $this->toStorageDate($this->purch_date);
        $this->service_entry_date = $this->toStorageDate($this->service_entry_date);

        return parent::beforeValidate();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAutoAnnual()
    {
        return $this->hasMany(AutoAnnual::className(), ['auto_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAutoMileage()
    {
        return $this->hasMany(AutoMileage::className(), ['auto_id' => 'id']);
    }

    /**
     * Get list of all autos
     *
     * @param $showInactive boolean
     *
     * @return array
     */
    public static function getAll($showInactive = false)
    {
        $query = self::find();
        if (!$showInactive) {
            $query->where(['active' => self::ACTIVE_ACTIVE]);
        }

        return $query->all();
    }


    /**
     * Get total mileage
     *
     * @param $includeInactive boolean
     * @param $year
     *
     * @return integer
     */
    public static function getTotalMileage($includeInactive = false, $year = false)
    {
        $query = self::find();
        if (!$includeInactive) {
            $query->where(['active' => self::ACTIVE_ACTIVE]);
        }
        if ($year) {
            $query->where('date>=:year',[':year' =>$year]);
            $query->andWhere('DATE_ADD(:year,INTERVAL 1 YEAR)>date',[':year' =>$year]);
        }
        else {
            $query->where('date>=:year',[':year' =>Helper::getCurrentYearDate()]);
            $query->andWhere('DATE_ADD(:year,INTERVAL 1 YEAR)>date',[':year' =>Helper::getCurrentYearDate()]);
        }

        $total = $query->joinWith('autoMileage')
            ->sum('{{AUTO_MILEAGE}}.mileage');

        return is_null($total)
            ? 0
            : $total;
    }

    /**
     * Get cost of total mileage
     *
     * @param $year
     *
     * @return float
     */
    public static function getMileageDed($year = false)
    {
        $userDB = Yii::$app->user->id;
        $appDB = Helper::getAppDb();

        $dateStart = (!$year)?Helper::getCurrentYearDate():$year;

        $sql = " Select (sum(am.mileage) * a.cents_per_mile) as m_exp FROM ";
        $sql .= $userDB . ".AUTO_MILEAGE am, " . $appDB . ".AUTO a where am.date>='" . $dateStart . "' and am.date<DATE_ADD('" . $dateStart . "',INTERVAL 1 YEAR)";
        $sql .= "and am.date>=a.start_date and am.date<=a.end_date";
        $sql .= " GROUP BY a.cents_per_mile";

        $result = Yii::$app->db->createCommand($sql)->queryAll();

        return (count($result)==0)
            ? 0
            : $result[0]["m_exp"];

    }

    /**
     * Get list of all expenses
     *
     * @param $year boolean
     *
     * @return array
     */
    public static function getActualExpense($year = false)
    {

        $query = Transaction::find()
            ->select([
                'type',
                'sum(amount) as amount'
            ])
            ->where(['auto' => 'Y']);
        if ($year) {
            $query->where('date>=:year',[':year' =>$year]);
            $query->andWhere('DATE_ADD(:year,INTERVAL 1 YEAR)>date',[':year' =>$year]);
        }
        else {
            $query->where('date>=:year',[':year' =>Helper::getCurrentYearDate()]);
            $query->andWhere('DATE_ADD(:year,INTERVAL 1 YEAR)>date',[':year' =>Helper::getCurrentYearDate()]);
        }
        $query = $query
            ->asArray()
            ->groupBy(['type']);

        $results = $query->all();

        $amountIn = 0;
        $amountOut = 0;

        foreach ($results as $result) {
            if ($result["type"] == 'E') {
                $amountIn = $result["amount"];
            }
            if ($result["type"] == 'I') {
                $amountOut = $result["amount"];
            }
        }

        return $amountIn - $amountOut;
    }

    /**
     * Get list of all estimated deductions
     *
     * @param $year boolean
     * @param $year_digit boolean
     *
     * @return array
     */
    public static function getEstimatedDeduction($year = false, $year_digit = false)
    {
        $userDB = Yii::$app->user->id;

        $dateStart = (!$year)?Helper::getCurrentYearDate():$year;
        $yearStart = (!$year_digit)?Helper::getCurrentYear():$year_digit;

        $sql = " SELECT a.*," .
            " ifnull(sum(CASE  WHEN tr.type='I' THEN  amount ELSE 0 END),0)  as i_expense, " .
            " ifnull(sum(CASE  WHEN tr.type='E' THEN  amount ELSE 0 END),0)  as e_expense " .
            " FROM " .
            " (select " .
            "     a.id, " .
            "     a.auto_name, " .
            "     ifnull(aa.end_odo,0) end_odo, " .
            "     ifnull(aa.begin_odo,0) begin_odo, " .
            "     ifnull(sum(am.mileage),0) as total_mileage " .
            " from " . $userDB . ".AUTO a " .
            " left JOIN " . $userDB . ".AUTO_ANNUAL aa ON a.id= aa.auto_id and aa.year=" . $yearStart . " " .
            " LEFT JOIN " . $userDB . ".AUTO_MILEAGE am ON a.id=am.auto_id and am.date>='" . $dateStart . "' and am.date<DATE_ADD('" . $dateStart . "',INTERVAL 1 YEAR) " .
            " GROUP BY " .
            "    a.id, " .
            "    a.auto_name, " .
            "    aa.end_odo, " .
            "    aa.begin_odo " .
            " ) as a " .
            " LEFT JOIN " . $userDB . ".TRANSACTION as tr ON a.id=tr.auto_id and tr.category_id = 'fa2f21f556984750e5954a9da01315b7' and tr.date>='" . $dateStart . "' and tr.date<DATE_ADD('" . $dateStart . "',INTERVAL 1 YEAR) " .
            "GROUP BY " .
            "    a.id, " .
            "    a.auto_name, " .
            "    a.end_odo, " .
            "    a.begin_odo, " .
            "    a.total_mileage ";


        $results = Yii::$app->db->createCommand($sql)->queryAll();
        $estDeduction = 0;

        foreach ($results as $result) {
            $totalExpense = $result["e_expense"] - $result["i_expense"];
            $current_ytd_mileage = $result["total_mileage"];
            $current_ytd_reported = $result["end_odo"] - $result["begin_odo"];
            $business_use_percentage = 0;
            if ($current_ytd_reported != 0) {
                $business_use_percentage = ($current_ytd_mileage / $current_ytd_reported) * 100;
            }
            $est_deduction = $totalExpense * $business_use_percentage / 100;
            $estDeduction += $est_deduction;
        }

        return $estDeduction;
    }

    /**
     * Get list of all annual auto data
     *
     * @param $year_digit boolean
     *
     * @return array
     */
    public static function needAutoAnnual($year_digit = false)
    {
        $userDB = Yii::$app->user->id;

        $yearStart = (!$year_digit)?Helper::getCurrentYear():$year_digit;

        $sql = ' select begin_odo,end_odo from '.$userDB.'.AUTO_ANNUAL where year = \''.$yearStart.'\'';


        $results = Yii::$app->db->createCommand($sql)->queryAll();
        $end_total = 0;
        $begin_total = 0;

        $result =[
            'Update Annual Auto Data' => '/auto/update-annual'
        ];



        foreach ($results as $result) {
            $begin_total += $result["begin_odo"];
            $end_total += $result["end_odo"];
            if ($result["end_odo"] == 0) return [];
        }

        if (($end_total != 0) && (($end_total - $begin_total) != 0)) {
            return [];
        }
        else {
            return $result;
        }
    }
}
