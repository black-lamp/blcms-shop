<?php

use yii\db\Migration;

/**
 * Handles adding anchor_name to table `product_translation`.
 */
class m160728_094915_add_anchor_name_column_to_product_translation_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->addColumn('shop_product_translation', 'anchor_name', 'string');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropColumn('shop_product_translation', 'anchor_name');
    }
}
