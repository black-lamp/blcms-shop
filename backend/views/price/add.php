<?php
use bl\cms\shop\common\entities\Product;
use bl\cms\shop\common\entities\ProductPrice;
use bl\cms\shop\common\entities\ProductPriceTranslation;
use bl\cms\shop\common\entities\SaleType;
use bl\multilang\entities\Language;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\Pjax;

/* @var Product $product */
/* @var ProductPrice $priceModel */
/* @var ProductPriceTranslation $priceTranslationModel */
/* @var ProductPrice[] $priceList */
/* @var Language[] $languages */
/* @var Language $selectedLanguage */
?>
<? Pjax::begin([
    'enablePushState' => false,
    'timeout' => 5000,
    'clientOptions' => [
        'push' => false,
        'replaceRedirect' => false
    ]
]); ?>
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <i class="glyphicon glyphicon-list"></i>
                    <?= 'Product Prices' ?>
                </div>
                <div class="panel-body">
                    <? if(count($languages) > 1): ?>
                        <div class="dropdown">
                            <button class="btn btn-warning btn-xs dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                <?= $selectedLanguage->name ?>
                                <span class="caret"></span>
                            </button>
                            <? if(count($languages) > 1): ?>
                                <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
                                    <? foreach($languages as $language): ?>
                                        <li>
                                            <?= Html::a(
                                                $language->name,
                                                [
                                                    'price/add',
                                                    'productId' => $product->id,
                                                    'languageId' => $language->id
                                                ]
                                            ) ?>
                                        </li>
                                    <? endforeach; ?>
                                </ul>
                            <? endif; ?>
                        </div>
                    <? endif; ?>
                    <? $priceForm = ActiveForm::begin([
                        'action' => [
                            'price/add',
                            'productId' => $product->id,
                            'languageId' => $language->id
                        ],
                        'method' => 'post',
                        'options' => [
                            'id' => 'add-price-form',
                            'data-pjax' => true
                        ]
                    ]) ?>
                    <div class="row">
                        <div class="col-md-3">
                            <?= $priceForm->field($priceTranslationModel, 'title') ?>
                        </div>
                        <div class="col-md-3">
                            <?= $priceForm->field($priceModel, 'price')->textInput(['type' => 'number', 'step' => '0.01']) ?>
                        </div>
                        <div class="col-md-2">
                            <?= $priceForm->field($priceModel, 'sale_type_id')
                                ->dropDownList(
                                    ['' => '--none--'] +
                                    ArrayHelper::map(SaleType::find()->asArray()->all(), 'id', 'title')
                                )
                                ->label('Sale Type')
                            ?>
                        </div>
                        <div class="col-md-3">
                            <?= $priceForm->field($priceModel, 'sale')->textInput(['type' => 'number', 'step' => '0.01']) ?>
                        </div>
                        <div class="col-md-1">
                            <?= Html::submitButton('Add', ['class' => 'btn btn-primary'])  ?>
                        </div>
                    </div>
                    <? $priceForm->end() ?>

                    <? if(!empty($priceList)): ?>
                        <table class="table table-bordered">
                            <thead>
                            <tr>
                                <th class="col-md-3">Title</th>
                                <th class="col-md-3">Price</th>
                                <th class="col-md-2">Sale Type</th>
                                <th class="col-md-3">Sale</th>
                                <th class="col-md-1"></th>
                            </tr>
                            </thead>
                            <tbody>
                            <? foreach($priceList as $price): ?>
                                <tr>
                                    <? if(!empty($price->translation)): ?>
                                        <td><?= $price->translation->title ?></td>
                                    <? endif; ?>
                                    <td><?= $price->price ?></td>
                                    <td><?= $price->type->title ?></td>
                                    <td><?= $price->sale ?></td>
                                    <td>
                                        <?= Html::a('', [
                                                'price/remove',
                                                'priceId' => $price->id,
                                                'productId' =>$product->id,
                                                'languageId' => $language->id
                                            ], [
                                                'class' => 'glyphicon glyphicon-remove text-danger btn btn-default btn-sm'
                                            ]
                                        ) ?>
                                    </td>
                                </tr>
                            <? endforeach; ?>
                            </tbody>
                        </table>
                    <? endif; ?>
                </div>
            </div>
        </div>
    </div>
<?php Pjax::end(); ?>
