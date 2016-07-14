<?php
namespace bl\cms\shop\backend\assets;
use yii\web\AssetBundle;

/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 */

class EditCategoryAsset extends AssetBundle
{
    public $sourcePath = '@vendor/black-lamp/blcms-shop/backend/web';

    public $css = [
        'css/input-tree.css'
    ];
    
    public $js = [
        'js/input-tree.js'
    ];
    
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}