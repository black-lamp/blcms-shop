<?php

use yii\db\Migration;

class m180225_125128_add_column_product_barcode_liquidity_inpack extends Migration
{
    public function safeUp()
    {
        $this->addColumn('shop_product', 'barcode', $this->integer());
        $this->addColumn('shop_product', 'inpack', $this->integer());
        $this->addColumn('shop_product', 'liquidity', $this->float());
        return true;
    }

    public function safeDown()
    {
        $this->dropColumn('shop_product', 'barcode');
        $this->dropColumn('shop_product', 'inpack');
        $this->dropColumn('shop_product', 'liquidity');
        return true;
    }
}