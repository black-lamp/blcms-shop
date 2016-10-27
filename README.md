**Applying migrations:**
**!Important: this migrations must be applied after Dectrium-User module migrations.**
```php
- php yii migrate --migrationPath=@yii/rbac/migrations
- php yii migrate --migrationPath=@vendor/black-lamp/blcms-shop/migrations
```

For cart module:
```php
- php yii migrate --migrationPath=@vendor/black-lamp/blcms-cart/migrations
```

**Configuration for Imagable module:**
```php
use bl\cms\shop\backend\components\CreateImageImagine;

'shop_imagable' => [
            'class' => 'bl\imagable\Imagable',
            'imageClass' => CreateImageImagine::className(),
            'nameClass' => 'bl\imagable\name\CRC32Name',
            'imagesPath' => '@frontend/web/images',
            'categories' => [
                'origin' => false,
                'category' => [
                    'shop-product' => [
                        'origin' => false,
                        'size' => [
                            'big' => [
                                'width' => 1500,
                                'height' => 500
                            ],
                            'thumb' => [
                                'width' => 500,
                                'height' => 500,
                            ],
                            'small' => [
                                'width' => 150,
                                'height' => 150
                            ]
                        ]
                    ],
                    'shop-vendors' => [
                        'origin' => false,
                        'size' => [
                            'big' => [
                                'width' => 1500,
                                'height' => 500
                            ],
                            'thumb' => [
                                'width' => 320,
                                'height' => 240,
                            ],
                            'small' => [
                                'width' => 150,
                                'height' => 150
                            ]
                        ]
                    ],
                    'cover' => [
                        'origin' => false,
                        'size' => [
                            'big' => [
                                'width' => 1500,
                                'height' => 500
                            ],
                            'thumb' => [
                                'width' => 500,
                                'height' => 500,
                            ],
                            'small' => [
                                'width' => 150,
                                'height' => 150
                            ]
                        ]
                    ],
                    'thumbnail' => [
                        'origin' => false,
                        'size' => [
                            'big' => [
                                'width' => 1500,
                                'height' => 500
                            ],
                            'thumb' => [
                                'width' => 500,
                                'height' => 500,
                            ],
                            'small' => [
                                'width' => 150,
                                'height' => 150
                            ]
                        ]
                    ],
                    'menu_item' => [
                        'origin' => false,
                        'size' => [
                            'big' => [
                                'width' => 1500,
                                'height' => 500
                            ],
                            'thumb' => [
                                'width' => 500,
                                'height' => 500,
                            ],
                            'small' => [
                                'width' => 150,
                                'height' => 150
                            ]
                        ]
                    ]
                ]
            ]
        ],
```

### Add module to your backend config
```php
    'modules' => [
    	...
        'shop' => [
            'class' => 'bl\cms\shop\backend\Module'
        ],
        ...
    ],
    
    'bootstrap' => [
        'bl\cms\shop\backend\components\events\PartnersEvents'
    ],
```

### Add module to your frontend config
```php
    'modules' => [
    	...
        'shop' => [
            'class' => 'bl\cms\shop\frontend\Module'
        ],
        ...
    ],
    
    'components' => [
        ...
        'urlManager' => [
            ...
            'rules' => [
                ...
                [
                    'class' => bl\cms\shop\UrlRule::className(),
                    'prefix' => 'shop'
                ]
            ],
            ...
        ],
        ...
    ]

    'bootstrap' => [
        'bl\cms\shop\frontend\components\events\PartnersEvents'
    ],
```

### Filtration widget
To use the widget, you must have set up relations in the models. For example in model Product:
```php
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProductCountry()
    {
        return $this->hasOne(ProductCountry::className(), ['id' => 'country_id']);
    }
```


**REQUIRES**

- PHP-extensions: file-info, imagick, intl


**Roles and its permissions:**

_attributeManager_
- addAttributeValue
- deleteAttribute
- saveAttribute
- viewAttributeList

_countryManager_
- saveCountry
- viewCountryList
- deleteCountry

_currencyManager_
- updateCurrency
- viewCurrencyList
- deleteCurrency

_deliveryMethodManager_
- saveDeliveryMethod
- viewDeliveryMethodList
- deleteDeliveryMethod

_filterManager_
- deleteFilter
- saveFilter
- viewFilterList


_orderManager_
- deleteOrder
- deleteOrderProduct
- viewOrder
- viewOrderList

_orderStatusManager_
- saveOrderStatus 
- viewOrderStatusList
- deleteOrderStatus

_productAvailabilityManager_
- saveProductAvailability
- viewProductAvailabilityList
- deleteProductAvailability

_productManager_
- createProduct
- createProductWithoutModeration
- deleteOwnProduct
- deleteProduct
- updateOwnProduct
- updateProduct
- viewCompleteProductList
- viewProductList

_productPartner_
- accessAdminPanel
- createProduct
- createProductWithoutModeration
- deleteOwnProduct
- deleteProduct
- updateOwnProduct
- updateProduct
- viewCompleteProductList
- viewProductList

_shopCategoryManager_
- saveShopCategory
- viewShopCategoryList

_vendorManager_
- saveVendor
- viewVendorList
- deleteVendor

_shopAdministrator_
extends permissions from all managers. 







