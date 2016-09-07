<?php

namespace bl\cms\shop\backend\controllers;

use bl\cms\shop\common\entities\ShopAttributeTranslation;
use bl\cms\shop\common\entities\ShopAttributeType;
use bl\multilang\entities\Language;
use Yii;
use bl\cms\shop\common\entities\ShopAttribute;
use bl\cms\shop\common\entities\SearchAttribute;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
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
    public function actionSave($languageId = null, $attrId = null)
    {
        if (empty($languageId)) {
            $languageId =Language::getCurrent()->id;
        }
        $attributeType = ArrayHelper::toArray(ShopAttributeType::find()->all(), [
            'bl\cms\shop\common\entities\ShopAttributeType' =>
                [
                    'id',
                    'title' => function($attributeType) {
                        return \Yii::t('shop', $attributeType->title);
                    }
                ]
        ]);


        if (empty($attrId)) {
            $model = new ShopAttribute();
            $modelTranslation = new ShopAttributeTranslation();
        }
        else {
            $model = ShopAttribute::findOne($attrId);
            $modelTranslation = ShopAttributeTranslation::find()
                ->where([
                    'attr_id' => $attrId,
                    'language_id' => $languageId
                ])->one();
            if (empty($modelTranslation)) {
                $modelTranslation = new ShopAttributeTranslation();
            }
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
//                return $this->render(['save', 'id' => $model->id]);
                return $this->redirect(['save', 'attrId' => $model->id, 'languageId' => $languageId]);
            }
        }



        return $this->render('save', [
            'attribute' => $model,
            'attributeTranslation' => $modelTranslation,
            'attributeType' => $attributeType
        ]);
    }

    /**
     * Deletes an existing ShopAttribute model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionRemove($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(Url::to(['/shop/attribute']));
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
