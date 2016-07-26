<?php

use yii\db\Migration;

class m160726_091126_add_column_in_stock_for_shop_product_table extends Migration
{
    public function up()
    {
        $this->addColumn('shop_product', 'in_stock', $this->boolean()->defaultValue(1));
    }

    public function down()
    {
        $this->dropColumn('shop_product', 'in_stock');
    }
}
