<?php
namespace bl\cms\shop\console\actions\import;

use bl\cms\shop\common\entities\Category;
use bl\cms\shop\common\entities\Combination;
use bl\cms\shop\common\entities\CombinationAttribute;
use bl\cms\shop\common\entities\Param;
use bl\cms\shop\common\entities\ParamTranslation;
use bl\cms\shop\common\entities\Product;
use bl\cms\shop\common\entities\ProductImage;
use bl\cms\shop\common\entities\ProductTranslation;
use bl\cms\shop\common\entities\ShopAttribute;
use bl\cms\shop\common\entities\ShopAttributeValue;
use bl\cms\shop\common\entities\Vendor;
use bl\cms\shop\console\util\XlsProductImportReader;
use bl\multilang\entities\Language;
use bl\seo\entities\SeoData;
use Exception;
use Yii;
use yii\base\Action;
use yii\console\Controller;
use yii\helpers\Console;
use yii\helpers\Inflector;

/**
 * @author Gutsulyak Vadim <guts.vadim@gmail.com>
 */
class BasicAction extends Action
{

    /**
     * @var Controller
     */
    public $controller;
    public $filesDir = '@console/runtime/import/base';
    public $files = [];
    private $language;

    public function run()
    {
        $this->language = Language::findOne(['lang_id' => 'ru']);
        $this->loadFilesFromDir();
        foreach ($this->files as $filename) {
            $transaction = Product::getDb()->beginTransaction();
            try {
                $this->import($filename);
                $transaction->commit();
            }
            catch (Exception $exception) {
                $transaction->rollBack();
                $this->controller->stdout($exception->getMessage(), Console::FG_RED);
            }
        }
    }

    private function import($filename) {
        $this->controller->stdout("Importing from $filename \n", Console::BG_GREEN);
        $reader = new XlsProductImportReader($filename);
        foreach ($reader as $productImportModel) {
            if(empty($productImportModel->sku)) {
                continue;
            }

            // PRODUCT
            $product = Product::findOne([
                'sku' => $productImportModel->sku
            ]);

            if(empty($product)) {
                $product = new Product([
                    'sku' => $productImportModel->sku,
                    'availability' => 1,
                    'status' => Product::STATUS_SUCCESS
                ]);
            }

            $product->category_id = !empty(Category::findOne($productImportModel->categoryId)) ? $productImportModel->categoryId : null;
            $product->vendor_id = !empty(Vendor::findOne($productImportModel->vendorId)) ? $productImportModel->vendorId : null;

            if(!$product->save()) {
                throw new Exception("Product::save() error");
            }

            // TRANSLATION
            $productTranslation = ProductTranslation::findOne([
                'product_id' => $product->id,
                'language_id' => $this->language->id
            ]);

            if(empty($productTranslation)) {
                $productTranslation = new ProductTranslation([
                    'product_id' => $product->id,
                    'language_id' => $this->language->id
                ]);
            }

            $productTranslation->title = $productImportModel->title;

            if(empty($productTranslation->seoUrl)) {
                $seoUrl = Inflector::slug($productTranslation->title);
                $count = SeoData::find()
                    ->where([
                        'entity_name' => 'bl\\cms\\shop\\common\\entities\\ProductTranslation',
                        'seo_url' => $seoUrl
                    ])
                    ->count();

                if($count > 0) {
                    $seoUrl = $seoUrl . "_" . $product->id;
                }
                $productTranslation->seoUrl = $seoUrl;
            }

            if(empty($productTranslation->seoTitle)) {
                $productTranslation->seoTitle = $productTranslation->title;
            }

            if(!$productTranslation->save()) {
                throw new Exception("ProductTranslation::save() error $productTranslation->title " . json_encode($productTranslation->errors));
            }

            // PARAMS
            if(!empty($product->params)) {
                foreach ($product->params as $param) {
                    $param->delete();
                }
            }

            foreach ($productImportModel->properties as $propertyImportModel) {
                $param = new Param([
                    'product_id' => $product->id
                ]);

                if(!$param->save()) {
                    throw new Exception("Param::save() error");
                }

                $paramTranslation = new ParamTranslation([
                    'param_id' => $param->id,
                    'language_id' => $this->language->id,
                    'name' => $propertyImportModel->title,
                    'value' => $propertyImportModel->value
                ]);

                if(!$paramTranslation->save()) {
                    throw new Exception("ParamTranslation::save() error");
                }
            }

            // IMAGES
            if(!empty($product->images)) {
                foreach ($product->images as $image) {
                    $image->delete();
                }
            }
            foreach ($productImportModel->images as $imageImportModel) {
                $productImage = new ProductImage([
                    'file_name' => $imageImportModel,
                    'product_id' => $product->id
                ]);

                if(!$productImage->save()) {
                    throw new Exception("ProductImage::save() error");
                }
            }

            // PRICES
            if(!empty($product->prices)) {
                foreach ($product->prices as $productPrice) {
                    $productPrice->delete();
                }
            }

            $groupId = 1;
            foreach ($productImportModel->prices as $priceImportModel) {
                $price = $product->getOrCreatePrice($groupId);
                $price->price = floatval(str_replace(',', '.', $priceImportModel));
                if(!$price->save()) {
                    throw new Exception("ProductPrice::save() error");
                }
                $groupId++;
            }

            // COMBINATIONS
            if(!empty($product->combinations)) {
                foreach ($product->combinations as $productCombination) {
                    if(!empty($productCombination->prices)) {
                        foreach ($productCombination->prices as $combinationPrice) {
                            $combinationPrice->delete();
                        }
                    }
                    $productCombination->delete();
                }
            }

            $isDefault = true;
            foreach ($productImportModel->combinations as $combinationImportModel) {
                /* @var ShopAttributeValue[] $attributeValues */
                $attributeValues = [];
                $combinationSku = $product->sku;

                foreach ($combinationImportModel->attributeValues as $attributeValuesImportModel) {
                    $shopAttribute = ShopAttribute::find()
                        ->joinWith('translations t')
                        ->where([
                            't.title' => $attributeValuesImportModel->title,
                            't.language_id' => $this->language->id
                        ])->one();

                    if(empty($shopAttribute)) {
                        throw new Exception("Attribute is not existing: $attributeValuesImportModel->title");
                    }

                    if($shopAttribute->type_id == ShopAttribute::TYPE_DROP_DOWN_LIST
                        || $shopAttribute->type_id == ShopAttribute::TYPE_RADIO_BUTTON) {

                        $shopAttributeValue = ShopAttributeValue::find()
                            ->joinWith('shopAttributeValueTranslations t')
                            ->where([
                                'attribute_id' => $shopAttribute->id,
                                't.value' => $attributeValuesImportModel->value,
                                't.language_id' => $this->language->id
                            ])->one();

                    }
                    else if($shopAttribute->type_id == ShopAttribute::TYPE_COLOR
                        || $shopAttribute->type_id == ShopAttribute::TYPE_TEXTURE) {

                        $shopAttributeValue = ShopAttributeValue::find()
                            ->joinWith('shopAttributeValueTranslations.shopAttributeValueColorTexture t')
                            ->where([
                                'attribute_id' => $shopAttribute->id,
                                't.title' => $attributeValuesImportModel->value,
                            ])->one();
                    }

                    if(empty($shopAttributeValue)) {
                        throw new Exception("Attribute value is not existing: $attributeValuesImportModel->value");
                    }
                    else {
                        $combinationSku .= '_' . $shopAttributeValue->id;
                        $attributeValues[] = $shopAttributeValue;
                    }

                }

                $combination = new Combination([
                    'product_id' => $product->id,
                    'sku' => $combinationSku,
                    'default' => intval($isDefault),
                    'availability' => 1,
                ]);

                if(!$combination->save()) {
                    throw new Exception("ShopCombination::save() error" . json_encode($combination->errors));
                }

                foreach ($attributeValues as $attributeValue) {
                    $combinationAttribute = new CombinationAttribute([
                        'combination_id' => $combination->id,
                        'attribute_id' => $attributeValue->attribute_id,
                        'attribute_value_id' => $attributeValue->id
                    ]);

                    if(!$combinationAttribute->save()) {
                        throw new Exception("CombinationAttribute::save() error");
                    }
                }

                $groupId = 1;
                foreach ($combinationImportModel->prices as $priceImportModel) {
                    $price = $combination->getOrCreatePrice($groupId);
                    $price->price = floatval(str_replace(',', '.', $priceImportModel));
                    if(!$price->save()) {
                        throw new Exception("CombinationPrice::save() error: " . json_encode($price->errors));
                    }
                    $groupId++;
                }

                $isDefault = false;
            }

            $this->controller->stdout("Product saved $product->id $productTranslation->title \n", Console::FG_GREEN);
        }
    }

    private function loadFilesFromDir() {
        $filesDirFullPath = Yii::getAlias($this->filesDir);
        $files = array_diff(scandir($filesDirFullPath), array('..', '.'));
        foreach ($files as $fileName) {
            $this->files[] = $filesDirFullPath . '/' . $fileName;
        }
    }

}