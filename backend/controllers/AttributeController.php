<?php

namespace bl\cms\shop\backend\controllers;

use bl\cms\shop\common\entities\ShopAttributeTranslation;
use bl\multilang\entities\Language;
use Yii;
use bl\cms\shop\common\entities\ShopAttribute;
use bl\cms\shop\common\entities\SearchAttribute;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * AttributeController implements the CRUD actions for ShopAttribute model.
 */
class AttributeController extends Controller
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
     * Lists all ShopAttribute models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SearchAttribute();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single ShopAttribute model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new ShopAttribute model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($languageId = null, $attrId = null)
    {
        if (empty($languageId)) {
            $languageId =Language::getCurrent()->id;
        }

        if (empty($attrId)) {
            $model = new ShopAttribute();
            $modelTranslation = new ShopAttributeTranslation();
        }
        else {
            $model = ShopAttribute::findOne($attrId);
            $modelTranslation = ShopAttributeTranslation::find()->where(['attr_id' => $attrId, 'lang_id' => $languageId]);
        }

        if(Yii::$app->request->isPost) {
            $model->load(Yii::$app->request->post());
            $modelTranslation->load(Yii::$app->request->post());

            if ($model->validate()) {
                $model->save();
                $modelTranslation->attr_id = $model->id;
                $modelTranslation->language_id = $languageId;
            }

            if ($modelTranslation->validate()) {
                $modelTranslation->save();
                Yii::$app->getSession()->setFlash('success', 'Data were successfully modified.');
                return $this->render('create', ['model' => $model, 'modelTranslation' => $modelTranslation]);
            }
        }

        else {
            return $this->render('create', [
                'model' => $model,
                'modelTranslation' => $modelTranslation,
            ]);
        }
    }

    /**
     * Updates an existing ShopAttribute model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing ShopAttribute model.
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
     * Finds the ShopAttribute model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ShopAttribute the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ShopAttribute::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
