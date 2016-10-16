<?php

namespace bl\cms\shop\widgets\assets;

use yii\web\AssetBundle;

/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 */
class NovaPoshtaAsset extends AssetBundle
{

    public $sourcePath = '@vendor/black-lamp/blcms-shop/frontend/web';

    public $css = [
    ];
    public $js = [
        'scripts/nova-poshta.js'
    ];
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}
