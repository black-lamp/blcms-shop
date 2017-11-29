<?php
/**
 * @author Gutsulyak Vadim <guts.vadim@gmail.com>
 */
use kartik\slider\Slider;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>

<div class="shop-filter">
    <?php $form = ActiveForm::begin([
        'action' => array_merge(
            ['/' . Yii::$app->controller->getRoute()],
            array_diff_key(Yii::$app->request->getQueryParams(), $shopFilterModel->valuesToArray())
        ),
        'method' => 'get',
    ]); ?>
    <div class="row">
        <div class="col-md-12">
        <?= Html::submitButton(Yii::t('frontend.shop.filter', 'Filter') . ' >>', ['class' => 'btn btn-success pull-right btn-i']) ?>
        <div class="clearfix"></div>

        </div>
    </div>
    <?php if (!empty($shopFilterModel->minPrice && !empty($shopFilterModel->maxPrice))): ?>
        <div class="form-group price-filter">
            <label><?= Yii::t('frontend.shop.filter.price', 'Price') ?></label>
            <div class="clearfix"></div>
            <?= Slider::widget([
                'name' => 'slider',
                'sliderColor' => Slider::TYPE_SUCCESS,
                'handleColor' => Slider::TYPE_SUCCESS,
                'pluginOptions' => [
                    'orientation' => 'horizontal',
                    'handle' => 'round',
                    'min' => $shopFilterModel->minPrice,
                    'max' => $shopFilterModel->maxPrice,
                    'step' => 0.01,
                    'range' => true,
                ],
                'pluginEvents' => [
                    "slideStop" => "function(slideEvt) { $('#filterPriceFrom').val(slideEvt.value[0]); $('#filterPriceTo').val(slideEvt.value[1]); }",
                    "slide" => "function(slideEvt) { $('#filterPriceFrom').val(slideEvt.value[0]); $('#filterPriceTo').val(slideEvt.value[1]); }",
                ],
                'value' => $shopFilterModel->pfrom . ',' . $shopFilterModel->pto,
                'options' => [
                    'form' => 'excluded',
                ]
            ]); ?>
            <div class="clearfix"></div>
            <?= Yii::t('frontend.shop.filter.price', 'from') ?>
            <?= Html::input('number', 'pfrom', $shopFilterModel->pfrom, [
                'class' => 'form-control input-sm price-input',
                'step' => '0.01',
                'id' => 'filterPriceFrom'
            ]) ?>
            <?= Yii::t('frontend.shop.filter.price', 'to') ?>
            <?= Html::input('number', 'pto', $shopFilterModel->pto, [
                'class' => 'form-control input-sm price-input',
                'step' => '0.01',
                'id' => 'filterPriceTo'
            ]) ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($vendors)): ?>
        <div class="form-group vendor-filter">
            <label><?= Yii::t('frontend.shop.filter', 'Vendor') ?></label>
            <div class="covered">
                <?php foreach ($vendors as $vendor): ?>
                    <div class="checkbox checkbox-success">
                        <?= Html::checkbox('vendors[]', in_array(strval($vendor->id), $shopFilterModel->vendors), [
                            'value' => $vendor->id,
                            'id' => 'vendor-filter-' . $vendor->id
                        ]) ?>
                        <label for="<?= 'vendor-filter-' . $vendor->id ?>">
                            <?= $vendor->title ?>
                        </label>
                    </div>
                <?php endforeach; ?>
            </div>
            <?php if (!empty(count($vendors) > 8)): ?>
                <a href="#" class="toggle-cover">
                    <span class="show">
                        <i class="fa fa-angle-down"></i>
                            <?= Yii::t('frontend.shop.filter', 'Show all') ?>
                    </span>
                    <span class="hide">
                        <i class="fa fa-angle-up"></i>
                        <?= Yii::t('frontend.shop.filter', 'Hide') ?>
                    </span>
                </a>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($availabilities)): ?>
        <div class="form-group vendor-filter">
            <label><?= Yii::t('frontend.shop.filter', 'Availability') ?></label>
            <?php foreach ($availabilities as $availability): ?>
                <div class="checkbox checkbox-success">
                    <?= Html::checkbox('availabilities[]', in_array(strval($availability->id), $shopFilterModel->availabilities), [
                        'value' => $availability->id,
                        'id' => 'availability-filter-' . $availability->id
                    ]) ?>
                    <label for="<?= 'availability-filter-' . $availability->id ?>">
                        <?= $availability->translation->title ?>
                    </label>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($categoryFilterParams)): ?>
        <?php foreach ($categoryFilterParams as $categoryFilterParam): ?>
            <?php if (!empty($categoryFilterParam) && !empty($categoryFilterParam->params)): ?>
                <div class="form-group vendor-filter">
                    <label><?= $categoryFilterParam->translation->title ?></label>
                    <div class="covered">
                        <?php foreach ($categoryFilterParam->params as $param): ?>
                            <div class="checkbox checkbox-success">
                                <?= Html::checkbox($categoryFilterParam->key . '[]', is_array($shopFilterModel->params[$categoryFilterParam->key]) ? in_array(strval($param['value']), $shopFilterModel->params[$categoryFilterParam->key]) : false, [
                                    'value' => $param['value'],
                                    'id' => $categoryFilterParam->key . '-filter-' . $param['value']
                                ]) ?>
                                <label
                                    for="<?= $categoryFilterParam->key . '-filter-' . $param['value'] ?>">
                                    <?= $param['value'] ?>
                                </label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <?php if (!empty(count($categoryFilterParam->params) > 8)): ?>
                        <a href="#" class="toggle-cover">
                            <span class="show">
                                <i class="fa fa-angle-down"></i>
                                <?= Yii::t('frontend.shop.filter', 'Show all') ?>
                            </span>
                            <span class="hide">
                                <i class="fa fa-angle-up"></i>
                                <?= Yii::t('frontend.shop.filter', 'Hide') ?>
                            </span>
                        </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>
    <?php endif; ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('frontend.shop.filter', 'Filter') . ' >>', ['class' => 'btn btn-success pull-right btn-i']) ?>
        <div class="clearfix"></div>
    </div>

    <?php ActiveForm::end(); ?>
</div>
