<?php
namespace bl\cms\shop\backend\assets;
use yii\web\AssetBundle;

/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 */

class EditCombinationAsset extends AssetBundle
{
    public $sourcePath = '@vendor/black-lamp/blcms-shop/backend/web';

    public $css = [
    ];

    public $js = [
        'js/edit-combination.js'
    ];

    public $depends = [
        'yii\web\JqueryAsset'
    ];
}