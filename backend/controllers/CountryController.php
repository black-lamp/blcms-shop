<?php
namespace bl\cms\shop\backend\controllers;
use bl\cms\shop\common\entities\ProductCountry;
use bl\cms\shop\common\entities\ProductCountryTranslation;

use bl\cms\shop\common\entities\Product;
use bl\cms\shop\common\entities\ProductPrice;
use bl\cms\shop\common\entities\ProductPriceTranslation;
use bl\multilang\entities\Language;
use yii\helpers\Url;
use yii\web\Controller;

/**
 * @author Gutsulyak Vadim <guts.vadim@gmail.com>
 */
class CountryController extends Controller
{
    public function actionIndex() {
        return $this->render('index', [
            'countries' => ProductCountry::find()->all(),
            'countryTranslations' => ProductCountryTranslation::find()->all(),
            'languages' => Language::findAll(['active' => true])
        ]);
    }
    
    public function actionSave($countryId = null, $languageId = null) {

        $selectedLanguage = Language::findOne($languageId);

        if (!empty($countryId)) {
            $country = ProductCountry::find()->where([
                'id' => $countryId
            ])->one();
            $countryTranslation = ProductCountryTranslation::find()->where([
                'country_id' => $countryId,
                'language_id' => $languageId
            ])->one();
            if (empty($countryTranslation)) {
                $countryTranslation = new ProductCountryTranslation;
            }
        }
        else {
            $country = new ProductCountry();
            $countryTranslation = new ProductCountryTranslation();
        }

        if(\Yii::$app->request->isPost) {
            $country->load(\Yii::$app->request->post());
            $countryTranslation->load(\Yii::$app->request->post());

            if ($countryTranslation->validate()) {
                $country->save();
                $countryTranslation->country_id = $country->id;
                $countryTranslation->language_id = $selectedLanguage->id;
                $countryTranslation->save();
                return $this->redirect(Url::toRoute('/shop/country'));
            }
        }

        return $this->render('save', [
            'country' => $country,
            'countryTranslation' => $countryTranslation,
            'languages' => Language::findAll(['active' => true]),
            'selectedLanguage' => $selectedLanguage
        ]);

    }

    public function actionRemove($id) {
        ProductCountry::deleteAll(['id' => $id]);
        return $this->redirect(Url::to(['/shop/country']));
    }
}