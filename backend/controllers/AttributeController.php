<?php

namespace bl\cms\shop\backend\controllers;

use bl\cms\shop\backend\components\form\AttributeTextureForm;
use bl\cms\shop\common\entities\SearchAttributeValue;
use bl\cms\shop\common\entities\ShopAttributeTranslation;
use bl\cms\shop\common\entities\ShopAttributeType;
use bl\cms\shop\common\entities\ShopAttributeValue;
use bl\cms\shop\common\entities\ShopAttributeValueColorTexture;
use bl\cms\shop\common\entities\ShopAttributeValueTranslation;
use bl\multilang\entities\Language;
use Yii;
use bl\cms\shop\common\entities\ShopAttribute;
use bl\cms\shop\common\entities\SearchAttribute;
use yii\base\Exception;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;

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
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['index', 'get-attribute-values'],
                        'roles' => ['viewAttributeList'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['save', 'remove-attribute-value'],
                        'roles' => ['saveAttribute'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['remove'],
                        'roles' => ['deleteAttribute'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['add-value'],
                        'roles' => ['addAttributeValue'],
                        'allow' => true,
                    ],
                ],
            ]
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
     * Save data action for ShopAttribute.
     * If user has not permissions to do this action, a 403 HTTP exception will be thrown.
     *
     * @param integer $languageId
     * @param integer $attrId
     * @return mixed
     * @throws ForbiddenHttpException if user has not permissions
     */
    public function actionSave($languageId = null, $attrId = null)
    {
        if (empty($languageId)) {
            $languageId = Language::getCurrent()->id;
        }
        $selectedLanguage = Language::findOne($languageId);
        $languages = Language::find()->all();
        $attributeType = ArrayHelper::toArray(ShopAttributeType::find()->all(), [
            'bl\cms\shop\common\entities\ShopAttributeType' =>
                [
                    'id',
                    'title' => function ($attributeType) {
                        return \Yii::t('shop', $attributeType->title);
                    }
                ]
        ]);

        if (empty($attrId)) {
            $model = new ShopAttribute();
            $modelTranslation = new ShopAttributeTranslation();

            $searchAttributeValueModel = null;
            $dataProviderAttributeValue = null;

        } else {

            $searchAttributeValueModel = new SearchAttributeValue();
            $dataProviderAttributeValue = $searchAttributeValueModel->search(Yii::$app->request->queryParams);

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

        if (Yii::$app->request->isPost) {
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
                return $this->redirect(['save', 'attrId' => $model->id, 'languageId' => $languageId]);
            }
        }

        return $this->render('save', [
            'attribute' => $model,
            'attributeTranslation' => $modelTranslation,
            'attributeType' => $attributeType,
            'languages' => $languages,
            'selectedLanguage' => $selectedLanguage,
            'searchModel' => $searchAttributeValueModel,
            'dataProvider' => $dataProviderAttributeValue,
            'valueModel' => new ShopAttributeValue(),
            'valueModelTranslation' => new ShopAttributeValueTranslation(),
            'attributeTextureModel' => new AttributeTextureForm()
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


    /**
     * @param integer $attrId
     * @param integer $languageId
     * @return mixed
     * @throws Exception
     */
    public function actionAddValue($attrId, $languageId)
    {
        if (!empty($attrId)) {
            $languageId = empty($languageId) ? Language::getCurrent()->id : $languageId;
            $attributeValue = new ShopAttributeValue();
            $attributeValueTranslation = new ShopAttributeValueTranslation();
            $attributeTextureModel = new AttributeTextureForm();

            $searchAttributeValueModel = new SearchAttributeValue();
            $dataProviderAttributeValue = $searchAttributeValueModel->search(Yii::$app->request->queryParams);

            if (Yii::$app->request->isPost) {
                $post = Yii::$app->request->post();
                if ($attributeValueTranslation->load($post) || $attributeTextureModel->load($post)) {

                    $attributeValue->attribute_id = $attrId;
                    $shopAttribute = ShopAttribute::findOne($attrId);

                    if ($shopAttribute->type_id == ShopAttribute::TYPE_COLOR || $shopAttribute->type_id == ShopAttribute::TYPE_TEXTURE) {
                        $colorTexture = new ShopAttributeValueColorTexture();
                        if ($shopAttribute->type_id == ShopAttribute::TYPE_COLOR) {
                            $colorTexture->color = $attributeTextureModel->color;
                        } elseif ($shopAttribute->type_id == ShopAttribute::TYPE_TEXTURE) {
                            $attributeTextureModel->imageFile = UploadedFile::getInstance($attributeTextureModel, 'imageFile');
                            $colorTexture->texture = $attributeTextureModel->upload();
                        }
                        if ($colorTexture->validate()) {
                            $colorTexture->save();
                            $attributeValueTranslation->value = (string) $colorTexture->id;
                        }
                        else throw new Exception('Color or texture saving error');
                    }

                    if ($attributeValue->validate()) {
                        $attributeValue->save();

                        $attributeValueTranslation->value_id = $attributeValue->id;
                        $attributeValueTranslation->language_id = $languageId;

                        if ($attributeValueTranslation->validate()) {
                            $attributeValueTranslation->save();
                            if (\Yii::$app->request->isPjax) {
                                return $this->renderPartial('add-value', [
                                    'dataProvider' => $dataProviderAttributeValue,
                                    'attribute' => ShopAttribute::findOne($attrId),
                                    'selectedLanguage' => Language::findOne($languageId),
                                    'valueModel' => new ShopAttributeValue(),
                                    'valueModelTranslation' => new ShopAttributeValueTranslation(),
                                    'attributeTextureModel' => $attributeTextureModel
                                ]);
                            } else {
                                return $this->redirect(Url::toRoute(['save', 'attrId' => $attrId, 'languageId' => $languageId]));
                            }
                        }
                    }
                }
            }
        }
        throw new NotFoundHttpException();
    }

    public function actionRemoveAttributeValue($attributeValueId) {
        $attributeValue = ShopAttributeValue::findOne($attributeValueId);

        if ($attributeValue->shopAttribute->type_id == ShopAttribute::TYPE_TEXTURE ||
            $attributeValue->shopAttribute->type_id == ShopAttribute::TYPE_COLOR) {

            if ($attributeValue->shopAttribute->type_id == ShopAttribute::TYPE_TEXTURE) {
                $path = Yii::getAlias('@frontend/web/images/shop/attribute-texture/') .
                    $attributeValue->translation->colorTexture->texture;
                unlink($path);
            }

            $colorTexture = ShopAttributeValueColorTexture::findOne($attributeValue->translation->value);
            $colorTexture->delete();
        }
        $attributeValue->delete();
        return $this->redirect(\Yii::$app->request->referrer);
    }

    /**
     * Return attribute values by ajax request.
     *
     * @param $attributeId
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionGetAttributeValues($attributeId)
    {
        if (\Yii::$app->request->isAjax) {
            $attributeValues = ShopAttributeValue::find()
                ->where(['attribute_id' => $attributeId])->all();

            $attributeValuesArray = ArrayHelper::toArray($attributeValues, [
                'bl\cms\shop\common\entities\ShopAttributeValue' => [
                    'id',
                    'attribute_id',
                    'translation' => 'translation'
                ]
            ]);
            return json_encode($attributeValuesArray);
        } else throw new NotFoundHttpException();
    }
}
