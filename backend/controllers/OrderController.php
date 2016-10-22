<?php
namespace bl\cms\shop\backend\controllers;

use bl\cms\cart\models\OrderProduct;
use bl\cms\cart\models\OrderStatus;
use bl\cms\cart\models\SearchOrderProduct;
use Yii;
use bl\cms\cart\models\Order;
use bl\cms\cart\models\SearchOrder;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * OrdersController implements the CRUD actions for Order model.
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 */
class OrderController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['login', 'error'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['logout', 'index'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                    'logout' => ['post'],
                ],
            ],
        ];
    }


    /**
     * Lists all Order models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SearchOrder();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Order model and changes status for this model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionView($id)
    {
        if (!empty($id)) {
            $model = Order::findOne($id);
            if (empty($model)) {
                $model = new Order();
            }
            if ($model->load(Yii::$app->request->post())) {
                if ($model->save()) {
                    \Yii::$app->session->setFlash('success', \Yii::t('shop', 'The record was successfully saved.'));

                } else {
                    \Yii::$app->session->setFlash('error', \Yii::t('shop', 'An error occurred when saving the record.'));
                }
                return $this->redirect(['view', 'id' => $id]);
            } else {
                $searchModel = new SearchOrderProduct();
                $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

                return $this->render('view', [
                    'model' => $this->findModel($id),
                    'statuses' => OrderStatus::find()->all(),
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                ]);
            }
        } else throw new NotFoundHttpException();
    }


    /**
     * Deletes an existing Order model.
     * If deletion is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Deletes product from existing Order model.
     * If deletion is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionDeleteProduct($id)
    {
        if (($model = OrderProduct::findOne($id)) !== null) {
            $model->delete();
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        return $this->redirect(\Yii::$app->request->referrer);
    }

    /**
     * Finds the Order model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Order the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Order::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}