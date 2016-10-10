<?php
namespace bl\cms\shop\backend\controllers;

use bl\cms\shop\common\entities\ProductAvailability;
use bl\multilang\entities\Language;
use yii\web\Controller;

/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 */
class ProductAvailabilityController extends Controller
{
    public function actionIndex() {

        $availabilities = ProductAvailability::find()->with(['translations'])->all();

        return $this->render('index', [
            'availabilities' => $availabilities
        ]);
    }

}