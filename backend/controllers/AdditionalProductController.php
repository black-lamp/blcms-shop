<?php
namespace bl\cms\shop\backend\controllers;

use bl\cms\shop\backend\components\events\ProductEvent;
use bl\cms\shop\common\entities\Category;
use bl\cms\shop\common\entities\Product;
use bl\cms\shop\common\entities\ProductAdditionalProduct;
use bl\multilang\entities\Language;
use Exception;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;

/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 */
class AdditionalProductController extends Controller
{

    /**
     * Event is triggered after editing product translation.
     * Triggered with bl\cms\shop\backend\events\ProductEvent.
     */
    const EVENT_BEFORE_EDIT_PRODUCT = 'beforeEditProduct';
    /**
     * Event is triggered before editing product translation.
     * Triggered with bl\cms\shop\backend\events\ProductEvent.
     */
    const EVENT_AFTER_EDIT_PRODUCT = 'afterEditProduct';

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
                        'actions' => [
                            'add-additional', 'add-to-additional-products', 'remove-additional-product'
                        ],
                        'roles' => ['createProduct', 'createProductWithoutModeration',
                            'updateProduct', 'updateOwnProduct'],
                        'allow' => true,
                    ]
                ],
            ],
        ];
    }

    /**
     * Adds additional products
     *
     * @param int $productId
     * @param int $languageId
     *
     * @return mixed
     */
    public function actionAddAdditional(int $productId, int $languageId)
    {
        $additionalProductsCategories = Category::find()->with('products')
            ->where(['additional_products' => true])->all();

        $productAdditionalProducts = ProductAdditionalProduct::find()->where(['product_id' => $productId])->all();
        return $this->render('../product/save', [
            'viewName' => '../additional-product/add-additional',
            'selectedLanguage' => Language::findOne($languageId),
            'product' => Product::findOne($productId),
            'languages' => Language::find()->all(),

            'params' => [
                'additionalProductsCategories' => $additionalProductsCategories,
                'productAdditionalProducts' => $productAdditionalProducts,
                'productId' => $productId
            ]
        ]);
    }

    /**
     * @param $productId
     * @param $additionalProductId
     * @return bool
     * @throws Exception
     */
    public function actionAddToAdditionalProducts($productId, $additionalProductId)
    {
        $this->trigger(self::EVENT_BEFORE_EDIT_PRODUCT, new ProductEvent([
            'id' => $productId
        ]));

        $productAdditionalProduct = ProductAdditionalProduct::find()
            ->where(['product_id' => $productId, 'additional_product_id' => $additionalProductId])->one();
        if (empty($productAdditionalProduct)) {
            $productAdditionalProduct = new ProductAdditionalProduct();
            $productAdditionalProduct->product_id = $productId;
            $productAdditionalProduct->additional_product_id = $additionalProductId;

            if ($productAdditionalProduct->validate()) {
                $productAdditionalProduct->save();

                $this->trigger(self::EVENT_AFTER_EDIT_PRODUCT, new ProductEvent([
                    'id' => $productId
                ]));

                if (\Yii::$app->request->isAjax) {
                    return $productAdditionalProduct->id;
                }

                return true;
            }
        }
        throw new Exception();
    }

    /**
     * @param $id
     * @return bool|\yii\web\Response
     */
    public function actionRemoveAdditionalProduct($id)
    {
        $productAdditionalProduct = ProductAdditionalProduct::findOne($id);
        if (!empty($productAdditionalProduct)) {
            $this->trigger(self::EVENT_AFTER_EDIT_PRODUCT, new ProductEvent([
                'id' => $productAdditionalProduct->product_id
            ]));

            $productAdditionalProduct->delete();

            $this->trigger(self::EVENT_AFTER_EDIT_PRODUCT, new ProductEvent([
                'id' => $productAdditionalProduct->product_id
            ]));
        }

        if (!Yii::$app->request->isAjax) {
            return $this->redirect(\Yii::$app->request->referrer);
        }
        return true;
    }
}