<?php
namespace bl\cms\shop\common\components\events;

use bl\cms\cart\CartComponent;
use bl\cms\shop\backend\controllers\CategoryController;
use bl\cms\shop\backend\controllers\ProductController;
use bl\cms\shop\common\entities\Category;
use bl\cms\shop\common\entities\FavoriteProduct;
use bl\cms\shop\common\entities\Product;
use bl\cms\shop\common\entities\ProductAvailability;
use bl\cms\shop\common\entities\ProductCountry;
use bl\cms\shop\common\entities\ShopAttribute;
use bl\cms\shop\common\entities\ShopAttributeValue;
use bl\cms\shop\common\entities\Vendor;
use Yii;
use yii\base\BootstrapInterface;
use yii\base\Event;
use yii\caching\TagDependency;

/**
 * @author Gutsulyak Vadim <guts.vadim@gmail.com>
 */
class CacheBootstrap implements BootstrapInterface
{

    /**
     * Bootstrap method to be called during application bootstrap stage.
     * @param \yii\base\Application $app the application currently running
     */
    public function bootstrap($app)
    {

        Event::on(ProductController::className(),
            ProductController::EVENT_AFTER_CREATE_PRODUCT, [$this, 'invalidateCache']);
        Event::on(ProductController::className(),
            ProductController::EVENT_AFTER_EDIT_PRODUCT, [$this, 'invalidateCache']);
        Event::on(ProductController::className(),
            ProductController::EVENT_AFTER_DELETE_PRODUCT, [$this, 'invalidateCache']);

        Event::on(CategoryController::className(),
            CategoryController::EVENT_AFTER_CREATE_CATEGORY, [$this, 'invalidateCache']);
        Event::on(CategoryController::className(),
            CategoryController::EVENT_AFTER_EDIT_CATEGORY, [$this, 'invalidateCache']);
        Event::on(CategoryController::className(),
            CategoryController::EVENT_AFTER_DELETE_CATEGORY, [$this, 'invalidateCache']);

        Event::on(Category::className(),
            Category::EVENT_AFTER_DELETE, [$this, 'invalidateCache']);
        Event::on(Category::className(),
            Category::EVENT_AFTER_INSERT, [$this, 'invalidateCache']);
        Event::on(Category::className(),
            Category::EVENT_AFTER_UPDATE, [$this, 'invalidateCache']);

        Event::on(Product::className(),
            Product::EVENT_AFTER_DELETE, [$this, 'invalidateCache']);
        Event::on(Product::className(),
            Product::EVENT_AFTER_INSERT, [$this, 'invalidateCache']);
        Event::on(Product::className(),
            Product::EVENT_AFTER_UPDATE, [$this, 'invalidateCache']);

        Event::on(Vendor::className(),
            Vendor::EVENT_AFTER_DELETE, [$this, 'invalidateCache']);
        Event::on(Vendor::className(),
            Vendor::EVENT_AFTER_INSERT, [$this, 'invalidateCache']);
        Event::on(Vendor::className(),
            Vendor::EVENT_AFTER_UPDATE, [$this, 'invalidateCache']);

        Event::on(Vendor::className(),
            Vendor::EVENT_AFTER_DELETE, [$this, 'invalidateCache']);
        Event::on(Vendor::className(),
            Vendor::EVENT_AFTER_INSERT, [$this, 'invalidateCache']);
        Event::on(Vendor::className(),
            Vendor::EVENT_AFTER_UPDATE, [$this, 'invalidateCache']);

        Event::on(ProductAvailability::className(),
            ProductAvailability::EVENT_AFTER_DELETE, [$this, 'invalidateCache']);
        Event::on(ProductAvailability::className(),
            ProductAvailability::EVENT_AFTER_INSERT, [$this, 'invalidateCache']);
        Event::on(ProductAvailability::className(),
            ProductAvailability::EVENT_AFTER_UPDATE, [$this, 'invalidateCache']);

        Event::on(ShopAttribute::className(),
            ShopAttribute::EVENT_AFTER_DELETE, [$this, 'invalidateCache']);
        Event::on(ShopAttribute::className(),
            ShopAttribute::EVENT_AFTER_INSERT, [$this, 'invalidateCache']);
        Event::on(ShopAttribute::className(),
            ShopAttribute::EVENT_AFTER_UPDATE, [$this, 'invalidateCache']);

        Event::on(ShopAttributeValue::className(),
            ShopAttributeValue::EVENT_AFTER_DELETE, [$this, 'invalidateCache']);
        Event::on(ShopAttributeValue::className(),
            ShopAttributeValue::EVENT_AFTER_INSERT, [$this, 'invalidateCache']);
        Event::on(ShopAttributeValue::className(),
            ShopAttributeValue::EVENT_AFTER_UPDATE, [$this, 'invalidateCache']);

        Event::on(ProductCountry::className(),
            ProductCountry::EVENT_BEFORE_DELETE, [$this, 'invalidateCache']);
        Event::on(ProductCountry::className(),
            ProductCountry::EVENT_BEFORE_INSERT, [$this, 'invalidateCache']);
        Event::on(ProductCountry::className(),
            ProductCountry::EVENT_BEFORE_UPDATE, [$this, 'invalidateCache']);

        Event::on(FavoriteProduct::className(),
            FavoriteProduct::EVENT_AFTER_DELETE, [$this, 'invalidateCache']);
        Event::on(FavoriteProduct::className(),
            FavoriteProduct::EVENT_AFTER_INSERT, [$this, 'invalidateCache']);
        Event::on(FavoriteProduct::className(),
            FavoriteProduct::EVENT_AFTER_UPDATE, [$this, 'invalidateCache']);

        Event::on(CartComponent::className(),
            CartComponent::EVENT_AFTER_CLEAR, [$this, 'invalidateCache']);
        Event::on(CartComponent::className(),
            CartComponent::EVENT_AFTER_ADD_PRODUCT, [$this, 'invalidateCache']);
        Event::on(CartComponent::className(),
            CartComponent::EVENT_AFTER_REMOVE_PRODUCT, [$this, 'invalidateCache']);
        Event::on(CartComponent::className(),
            CartComponent::EVENT_BEFORE_GET_ORDER, [$this, 'invalidateCache']);
        Event::on(CartComponent::className(),
            CartComponent::EVENT_BEFORE_GET_ORDER_FROM_DB, [$this, 'invalidateCache']);

    }

    public function invalidateCache($event) {
        TagDependency::invalidate(Yii::$app->cache, 'blcms-shop-catalogue');
    }
}