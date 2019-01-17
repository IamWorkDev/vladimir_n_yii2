<?php

namespace app\modules\auto\controllers;

use common\models\frontend\AutoAnnual;
use common\models\frontend\AutoAnnualSearch;
use Yii;
use common\models\frontend\AutoMileageSearch;
use common\models\frontend\AutoMileage;
use app\modules\auto\components\Controller;
use yii\web\NotFoundHttpException;

/**
 * AutoAnnualController
 */
class AutoAnnualController extends Controller
{
    /**
     * List of all auto annual.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new AutoAnnualSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Create new auto annual.
     *
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new AutoAnnual();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
        } else {
            return $this->renderAjax('_form', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Update auto annual.
     *
     * @param integer $id
     *
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
        } else {
            return $this->renderAjax('_form', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Delete auto annual.
     *
     * @param integer $id
     *
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)
            ->delete();
        $searchModel = new AutoMileageSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Finds the Order model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return AutoAnnual the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        $str = $id;
        $pos = strpos($str, '_', 0);
        $auto_id = substr($str, 0, $pos);
        $year = substr($str, $pos + 1, strlen($str) - $pos - 1);

        if (($model = AutoAnnual::find()->where(['auto_id' => $auto_id, 'year' => $year])->one()) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
