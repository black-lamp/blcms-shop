<?php

use bl\cms\shop\common\components\rbac\ProductOwnerRule;
use yii\db\Migration;

class m160822_043749_rbac_init extends Migration
{
    public function up()
    {
        $auth = Yii::$app->authManager;

        /*Add the rule*/
        $rule = new ProductOwnerRule;
        $auth->add($rule);

        /*Add permissions*/
        $createProduct = $auth->createPermission('createProduct');
        $createProduct->description = 'Create product';
        $auth->add($createProduct);

        $updateProduct = $auth->createPermission('updateProduct');
        $updateProduct->description = 'Update product';
        $auth->add($updateProduct);

        $deleteProduct = $auth->createPermission('deleteProduct');
        $deleteProduct->description = 'Delete product';
        $auth->add($deleteProduct);


        $updateOwnProduct = $auth->createPermission('updateOwnProduct');
        $updateOwnProduct->description = 'Update own product';
        $updateOwnProduct->ruleName = $rule->name;
        $auth->add($updateOwnProduct);

        $deleteOwnProduct = $auth->createPermission('deleteOwnProduct');
        $deleteOwnProduct->description = 'Delete own product';
        $deleteOwnProduct->ruleName = $rule->name;
        $auth->add($deleteOwnProduct);


        $createProductWithoutModeration = $auth->createPermission('createProductWithoutModeration');
        $createProductWithoutModeration->description = 'Create product without moderation';
        $auth->add($createProductWithoutModeration);

        $updateProductWithoutModeration = $auth->createPermission('updateProductWithoutModeration');
        $updateProductWithoutModeration->description = 'Update product without moderation';
        $auth->add($updateProductWithoutModeration);

        $deleteProductWithoutModeration = $auth->createPermission('deleteProductWithoutModeration');
        $deleteProductWithoutModeration->description = 'Delete product without moderation';
        $auth->add($deleteProductWithoutModeration);

        
        /*Add roles*/
        $productPartner = $auth->createRole('productPartner');
        $productPartner->description = 'Product Partner';
        $auth->add($productPartner);
        $auth->addChild($productPartner, $createProduct);
        $auth->addChild($productPartner, $updateProduct);
        $auth->addChild($productPartner, $deleteProduct);
        
        $auth->addChild($updateOwnProduct, $updateProduct);
        $auth->addChild($deleteOwnProduct, $deleteProduct);
        
        $productManager = $auth->createRole('productManager');
        $productManager->description = 'Product Manager';
        $auth->add($productManager);
        $auth->addChild($productManager, $createProductWithoutModeration);
        $auth->addChild($productManager, $updateProductWithoutModeration);
        $auth->addChild($productManager, $deleteProductWithoutModeration);
        $auth->addChild($productManager, $productPartner);

    }

    public function down()
    {
        Yii::$app->authManager->removeAll();

    }
}
