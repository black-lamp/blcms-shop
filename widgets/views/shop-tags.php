<?php
/**
 * @var View $this
 * @var string $title
 * @var \bl\cms\shop\common\entities\Category[] $categories
 */

use yii\helpers\Html;
use yii\web\View;

?>

<?php if (!empty($categories)): ?>
    <div class="tagcloud">
        <?php if (!empty($title)): ?>
        <h2>
                <?= $title ?>
        </h2>
        <?php endif; ?>
        <div class="tagcloud-wrapper">
            <ul>
                <?php foreach ($categories as $category): ?>
                    <li>
                        <?= Html::a(
                            mb_strtolower($category->translation->title) ?? '',
                            $category->getUrl()
                        ); ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
<?php endif; ?>
