<?php

use yii\db\Migration;

class m160915_091719_add_filter_type_column extends Migration
{
    public function up()
    {
        $this->dropColumn('shop_filters', 'filter_by_vendor');
        $this->dropColumn('shop_filters', 'filter_by_country');
        $this->addColumn('shop_filters', 'filter_type', $this->integer());
        $this->addColumn('shop_filters', 'input_type', $this->integer());


        $this->createTable('shop_filter_type', [
            'id' => $this->primaryKey(),
            'class_name' => $this->string(),
            'column' => $this->string(),
            'displaying_column' => $this->string(),
            'title' => $this->string()
        ]);
        $this->insert('shop_filter_type', [
            'class_name' => 'bl\cms\shop\common\entities\Vendor',
            'column' => 'vendor_id',
            'displaying_column' =>'title',
            'title' => 'Filter by vendor'
        ]);
        $this->insert('shop_filter_type', [
            'class_name' => 'bl\cms\shop\common\entities\ProductCountry',
            'column' => 'country_id',
            'displaying_column' =>'translation.title',
            'title' => 'Filter by country'
        ]);

        $this->createTable('shop_filter_input_type', [
            'id' => $this->primaryKey(),
            'title' => $this->string()
        ]);
        $this->insert('shop_filter_input_type', [
            'title' => 'Drop down list'
        ]);
        $this->insert('shop_filter_input_type', [
            'title' => 'Radio button'
        ]);
        $this->insert('shop_filter_input_type', [
            'title' => 'Checkbox'
        ]);

        $this->addForeignKey('shop_filters_filter_type_shop_filter_type_id',
            'shop_filters', 'filter_type', 'shop_filter_type', 'id');
        $this->addForeignKey('shop_filters_input_type_shop_filter_input_type_id',
            'shop_filters', 'input_type', 'shop_filter_input_type', 'id');
    }

    public function down()
    {
        $this->dropForeignKey('shop_filters_input_type_shop_filter_input_type_id',
            'shop_filters');
        $this->dropForeignKey('shop_filters_filter_type_shop_filter_type_id',
            'shop_filters'
        );
        $this->dropTable('shop_filter_input_type');
        $this->dropTable('shop_filter_type');

        $this->dropColumn('shop_filters', 'input_type');
        $this->dropColumn('shop_filters', 'filter_type');

        $this->addColumn('shop_filters', 'filter_by_country', $this->boolean());
        $this->addColumn('shop_filters', 'filter_by_vendor', $this->boolean());

    }

}
