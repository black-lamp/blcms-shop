<?php
namespace bl\cms\shop\widgets;
use bl\cms\shop\widgets\assets\NovaPoshtaAsset;
use yii\base\Exception;
use yii\base\Widget;


/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 *
 * Requirements:
 */
class NovaPoshta extends Widget
{
    public $token;
    public $url = 'https://api.novaposhta.ua/v2.0/json/';

    public $modelName = 'AddressGeneral';
    public $calledMethod = 'getWarehouses';
    public $language = 'ru';
    public $defaultCityName = 'Киев';

    public $formModel;
    public $formAttribute;

    public function init()
    {
        NovaPoshtaAsset::register($this->getView());
    }

    public function run($params = null)
    {

        $warehouses = json_decode($this->getWarehouses());

        return $this->render('nova-poshta', [
            'language' => $this->language,
            'model' => $this->formModel,
            'attribute' => $this->formAttribute,
            'warehouses' => $warehouses,
        ]);
    }

    public function getWarehouses($cityName = null) {

        $cityName = (!empty($cityName)) ? $cityName : $this->defaultCityName;

        $methodProperties = [
            'CityName' => $cityName
        ];

        $data = [
            'apiKey' => $this->token,
            'modelName' => $this->modelName,
            'calledMethod' => $this->calledMethod,
            'language' => $this->language,
            'methodProperties' => $methodProperties
        ];

        $post = json_encode($data);

        $result = file_get_contents($this->url, null, stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => "Content-type: application/x-www-form-urlencoded;\r\n",
                'content' => $post,
            ]
        ]));

        return $result;
    }

}