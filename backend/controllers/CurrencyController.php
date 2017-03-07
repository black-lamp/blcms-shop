<?php
namespace bl\cms\shop\backend\controllers;

use Yii;
use bl\cms\shop\common\entities\Currency;
use bl\cms\shop\common\entities\SearchCurrency;
use yii\base\Exception;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * CurrencyController implements the CRUD actions for Currency model.
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 */
class CurrencyController extends Controller
{
    /**
     * Event is triggered before creating new product.
     * Triggered with bl\cms\shop\backend\events\ProductEvent.
     */
    const EVENT_AFTER_CHANGE = 'afterChangeCurrency';

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
                        'actions' => ['index'],
                        'roles' => ['viewCurrencyList'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['update'],
                        'roles' => ['updateCurrency'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['remove'],
                        'roles' => ['deleteCurrency'],
                        'allow' => true,
                    ]
                ],
            ]
        ];
    }

    /**
     * Lists all Currency models.
     * @return mixed
     * @throws Exception
     */
    public function actionIndex()
    {
        $rates = Currency::find()->orderBy('id DESC')->all();
        $model = new Currency();

        if (Yii::$app->request->isPost) {
            if ($model->load(Yii::$app->request->post())) {

                if ($model->validate()) {
                    $model->save();
                    $this->trigger(self::EVENT_AFTER_CHANGE);
                }
                return $this->redirect(\Yii::$app->request->referrer);
            }
            else {
                throw new Exception();
            }
        }
        return $this->render('index', [
            'rates' => $rates,
            'model' => $model
        ]);
    }

    /**
     * Deletes an existing Currency model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionRemove($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Updates an existing Currency model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws Exception
     */
    public function actionUpdate($id)
    {
        if (!empty($id)) {
            $model = Currency::findOne($id);
        }
        else {
            $model = new Currency();
        }

        if (Yii::$app->request->isPost) {
            if ($model->load(Yii::$app->request->post())) {

                if ($model->validate()) {
                    $model->save();
                }
                return $this->redirect(\Yii::$app->request->referrer);
            }
            else {
                throw new Exception();
            }
        }
        return $this->render('update', [
            'model' => $model
        ]);
    }

    /**
     * Finds the Currency model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Currency the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Currency::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}