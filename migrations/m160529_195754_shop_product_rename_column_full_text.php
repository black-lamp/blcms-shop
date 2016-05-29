<?php

use yii\db\Migration;

class m160529_195754_shop_product_rename_column_full_text extends Migration
{
    public function safeUp()
    {
        $this->renameColumn('shop_product', 'full-text', 'full_text');
    }

    public function safeDown()
    {
        $this->renameColumn('shop_product', 'full_text', 'full-text');
    }
}
