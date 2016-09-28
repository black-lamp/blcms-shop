<?php
namespace bl\cms\shop\backend\controllers;

use Yii;
use bl\cms\cart\models\OrderStatus;
use bl\cms\cart\models\SearchOrderStatus;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * OrderStatusController implements the CRUD actions for OrderStatus model.
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 */
class OrderStatusController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all OrderStatus models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SearchOrderStatus();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new or updates existing OrderStatus model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionSave($id = null)
    {
        $model = (!empty($id)) ? $this->findModel($id) : new OrderStatus();

        if ($model->load(Yii::$app->request->post())) {
            if ($model->save()) {
                \Yii::$app->session->setFlash('success', \Yii::t('shop', 'The record was successfully saved.'));

            } else {
                \Yii::$app->session->setFlash('error', \Yii::t('shop', 'An error occurred when saving the record.'));
            }

            return $this->redirect(['save', 'id' => $model->id]);
        }

        return $this->render('save', ['model' => $model]);
    }


    /**
     * Deletes an existing OrderStatus model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the OrderStatus model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return OrderStatus the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = OrderStatus::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}