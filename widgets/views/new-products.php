<?php
/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 *
 * @var $products \bl\cms\shop\common\entities\Product[]
 */
use bl\multilang\entities\Language;
use yii\bootstrap\Html;
use yii\helpers\Url;

?>

<table class="table table-hover table-striped table-bordered">
    <tr>
        <th class="text-center"><?= Yii::t('shop', 'Title'); ?></th>
        <th><?= Yii::t('shop', 'Owner'); ?></th>

        <th><?= Yii::t('shop', 'Category'); ?></th>
    </tr>
    <?php foreach ($products as $product) : ?>
        <tr>
            <td class="text-center">
                <?= Html::a(
                    $product->translation->title,
                    Url::toRoute(['/shop/product/save', 'id' => $product->id, 'languageId' => Language::getCurrent()->id])
                ); ?>
            </td>
            <td>
                <?= (!empty($product->ownerProfile->name)) ? $product->ownerProfile->name : '';?>
                <?= (!empty($product->ownerProfile->surname)) ? $product->ownerProfile->surname : ''; ?>
            </td>
            <td>
                <?= (!empty($product->category->translation->title)) ? $product->category->translation->title : ''; ?>
            </td>
        </tr>
    <?php endforeach; ?>
</table>

