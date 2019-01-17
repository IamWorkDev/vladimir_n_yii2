<?php

namespace app\modules\auto\controllers;

use Yii;
use common\models\frontend\Auto;
use app\modules\auto\components\Controller;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;

/**
 * ManageAutoController
 */
class ManageAutoController extends Controller
{
    /**
     * List of all auto models.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Auto::find()
                ->select([
                    '{{AUTO}}.*',
                    'ifnull(sum({{AUTO_MILEAGE}}.mileage), 0) as total_mileage'
                ])
                ->joinWith('autoMileage')
                ->groupBy('{{AUTO}}.id')
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Create new auto model.
     *
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Auto();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
        } else {
            return $this->renderAjax('_form', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Update auto expense.
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
     * Finds the Auto model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Auto the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Auto::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
