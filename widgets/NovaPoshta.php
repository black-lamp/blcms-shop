<?php
namespace bl\cms\shop\widgets;
use bl\cms\shop\widgets\assets\NovaPoshtaAsset;
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
        $areas = json_decode($this->getAreas());
        $warehouses = json_decode($this->getWarehouses());

        if (!empty($warehouses)) {
            return $this->render('nova-poshta', [
                'language' => $this->language,
                'model' => $this->formModel,
                'attribute' => $this->formAttribute,
                'areas' => $areas->data,
                'warehouses' => $warehouses->data
            ]);
        }
        else return false;
    }


    public function getAreas() {

        return $this->getResponse('Address', 'getAreas');
    }

    public function getWarehouses() {

        return $this->getResponse('Address', 'getWarehouses');
    }

    private function getResponse($modelName, $calledMethod, $methodProperties = null) {

        $data = [
            'apiKey' => $this->token,
            'modelName' => $modelName,
            'calledMethod' => $calledMethod,
            'language' => $this->language,
            'methodProperties' => $methodProperties
        ];

        $post = json_encode($data);

//        $result = file_get_contents($this->url, null, stream_context_create([
//            'http' => [
//                'method' => 'POST',
//                'header' => "Content-type: application/x-www-form-urlencoded;\r\n",
//                'content' => $post,
//            ]
//        ]));

        $ch = curl_init($this->url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        //curl_setopt($ch, CURLOPT_PROXY, "101.0.20.222:3128");
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_SSLVERSION,3);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        $result = curl_exec($ch);
        curl_close($ch);


        return $result;
    }
}