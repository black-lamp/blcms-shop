<?php
use  bl\cms\shop\common\entities\CategoryTranslation;
use bl\multilang\entities\Language;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\widgets\Pjax;

/* @var $categories CategoryTranslation */
/* @var $languages Language[] */

$this->title = 'Products list';
?>

<?php Pjax::begin([
    'linkSelector' => '.product-nav',
    'enablePushState' => false
]) ?>
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <i class="glyphicon glyphicon-list"></i>
                <?= 'Products list' ?>
            </div>
            <div class="panel-body">
                <table class="table table-hover">
                    <?php if (!empty($products)): ?>
                        <thead>
                        <tr>
                            <th class="col-lg-1"><?='Position'; ?></th>
                            <th class="col-lg-3"><?= 'Title' ?></th>
                            <th class="col-lg-2"><?= 'Category' ?></th>
                            <th class="col-lg-3"><?= 'Description' ?></th>
                            <?php if(count($languages) > 1): ?>
                                <th class="col-lg-3"><?= 'Language' ?></th>
                            <?php endif; ?>
                            <th>Edit</th>
                            <th>Delete</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($products as $product): ?>
                            <tr>
                                <td class="text-center">
                                    <?= $product->position ?>
                                    <a href="<?= Url::to([
                                        'up',
                                        'id' => $product->id
                                    ]) ?>" class="product-nav glyphicon glyphicon-arrow-up text-primary pull-left">
                                    </a>
                                    <a href="<?= Url::to([
                                        'down',
                                        'id' => $product->id
                                    ]) ?>" class="product-nav glyphicon glyphicon-arrow-down text-primary pull-left">
                                    </a>
                                </td>
                                <td>
                                    <?= $product->translation->title ?>
                                </td>
                                <td>
                                    <?php if(!empty($product->category)): ?>
                                        <?= $product->category->translation->title ?>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if(!empty($product->category)): ?>
                                        <?= $product->translation->description ?>
                                    <?php endif; ?>
                                </td>

                                <td>
                                    <?php if(count($languages) > 1): ?>
                                        <?php $translations = ArrayHelper::index($product->translations, 'language_id') ?>
                                        <?php foreach ($languages as $language): ?>
                                            <a href="<?= Url::to([
                                                'save',
                                                'productId' => $product->id,
                                                'languageId' => $language->id
                                            ]) ?>"
                                               type="button"
                                               class="btn btn-<?= !empty($translations[$language->id]) ? 'primary' : 'danger'
                                               ?> btn-xs"><?= $language->name ?></a>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </td>


                                <td>
                                    <a href="<?= Url::to([
                                        'save',
                                        'productId' => $product->id,
                                        'languageId' => $product->translation->language->id
                                    ])?>" class="glyphicon glyphicon-edit text-warning btn btn-default btn-sm">
                                    </a>
                                </td>

                                <td>
                                    <a href="<?= Url::to([
                                        'remove',
                                        'id' => $product->id
                                    ])?>" class="glyphicon glyphicon-remove text-danger btn btn-default btn-sm">
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    <?php endif; ?>
                </table>
                <!-- TODO: languageId -->
                <a href="<?= Url::to(['/shop/product/save', 'languageId' => Language::getCurrent()->id]) ?>"
                   class="btn btn-primary pull-right">
                    <i class="fa fa-user-plus"></i> <?= 'Add' ?>
                </a>
            </div>
        </div>
    </div>
</div>
<?php Pjax::end() ?>
