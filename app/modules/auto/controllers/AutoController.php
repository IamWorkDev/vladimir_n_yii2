<?php

namespace app\modules\auto\controllers;

use Yii;
use common\models\frontend\AutoMileageSearch;
use common\models\frontend\AutoMileage;
use app\modules\auto\components\Controller;
use yii\web\NotFoundHttpException;

/**
 * AutoController
 */
class AutoController extends Controller
{
    /**
     * List of all auto mileages.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new AutoMileageSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Create new auto mileage.
     *
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new AutoMileage();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
        } else {
            return $this->renderAjax('_form', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Update auto mileage.
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
     * Delete auto mileage.
     *
     * @param integer $id

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
     * @return AutoMileage the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = AutoMileage::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
