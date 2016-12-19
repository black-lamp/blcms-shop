<?php
namespace bl\cms\shop\frontend\widgets;

use bl\cms\shop\common\entities\ProductAdditionalProduct;
use bl\cms\shop\frontend\widgets\assets\AdditionalProductsAsset;
use yii\base\Model;
use yii\base\Widget;
use yii\widgets\ActiveForm;


/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 */
class AdditionalProducts extends Widget
{

    /**
     * @var integer
     */
    public $productId;

    /**
     * @var ActiveForm
     */
    public $form;

    /**
     * @var Model
     */
    public $model;

    /**
     * @var string
     */
    public $attribute;

    public function init()
    {
        AdditionalProductsAsset::register($this->getView());
    }

    public function run()
    {
        parent::run();

        $productAdditionalProducts = ProductAdditionalProduct::find()->where(['product_id' => $this->productId])->all();

        return $this->render('additional-products/index',
            [
                'productAdditionalProducts' => $productAdditionalProducts,
                'form' => $this->form,
                'model' => $this->model,
                'modelAttribute' => $this->attribute
            ]);

    }
}