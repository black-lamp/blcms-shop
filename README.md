**Applying migrations:**
```php
- php yii migrate --migrationPath=@yii/rbac/migrations
- php yii migrate --migrationPath=@vendor/black-lamp/blcms-shop/migrations
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
