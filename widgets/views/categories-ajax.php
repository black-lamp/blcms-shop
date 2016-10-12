<?php
/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 * This view is used for ajax rendering in TreeWidget
 *
 * @var $categories
 * @var $level
 */
use yii\helpers\Url;

?>

<?php if (!empty($categories)) : ?>
    <ul data-level="<?= $level + 1; ?>">
        <?php foreach ($categories as $category) : ?>
            <li>
                <a href="<?= Url::toRoute(['/shop/category/show', 'id' => $category->id]); ?>">
                    <?php if (!$level == 1) : ?>
                        <i class="category-icon"></i>
                        <span>
                            <?= $category->translation->title; ?>
                        </span>
                    <?php else : ?>
                        <span style="margin-left:<?= $level * 15; ?>px">
                            <?= $category->translation->title; ?>
                        </span>
                    <?php endif; ?>
                </a>
                <?php if (!empty($category->children)) : ?>
                    <i class="fa fa-toggle-down pull-right category-toggle closed" id="<?= $category->id; ?>"></i>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>