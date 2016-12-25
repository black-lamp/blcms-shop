<?php
namespace bl\cms\shop\backend\assets;
use yii\web\AssetBundle;

/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 */

class CategoriesIndexAsset extends AssetBundle
{
    public $sourcePath = '@vendor/GutsVadim/blcms-itpl/modules/blcms-shop/backend/web';

    public $css = [
    ];

    public $js = [
        'js/categories-index.js'
    ];

    public $depends = [
        'yii\web\JqueryAsset',
    ];
}