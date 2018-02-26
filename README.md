Yii2 Thumbnailer
================

[![Build Status](https://secure.travis-ci.org/daxslab/yii2-thumbnailer.png)](http://travis-ci.org/daxslab/yii2-thumbnailer)
[![Latest Stable Version](https://poser.pugx.org/daxslab/yii2-thumbnailer/v/stable.svg)](https://packagist.org/packages/daxslab/yii2-thumbnailer)
[![Total Downloads](https://poser.pugx.org/daxslab/yii2-thumbnailer/downloads)](https://packagist.org/packages/daxslab/yii2-thumbnailer)
[![Latest Unstable Version](https://poser.pugx.org/daxslab/yii2-thumbnailer/v/unstable.svg)](https://packagist.org/packages/daxslab/yii2-thumbnailer)
[![License](https://poser.pugx.org/daxslab/yii2-thumbnailer/license.svg)](https://packagist.org/packages/daxslab/yii2-thumbnailer)

Yii2 component to generate image thumnails of any size.

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist daxslab/yii2-thumbnailer "*"
```

or add

```
"daxslab/yii2-thumbnailer": "*"
```

to the require section of your `composer.json` file.

Configuration
-------------

The basic configuration only requires adding the component to the application:

```php
'components' => [
    //...
    'thumbnailer' => [
        'class' => 'daxslab\thumbnailer\Thumbnailer',
    ],
    //...
]
```

Besides that a default value is always provided, all the options can be configured.

```php
'components' => [
    //...
    'thumbnailer' => [
        'class' => 'daxslab\thumbnailer\Thumbnailer',
        'defaultWidth' => 500,
        'defaultHeight' => 500,
        'thumbnailsBasePath' => '@webroot/assets/thumbs',
        'thumbnailsBaseUrl' => '@web/assets/thumbs',
        'enableCaching' => true, //defaults to false but is recommended
    ],
    //...
]
```

Usage
-----

Once the extension is configured, simply use it in your views by:

```php
//Generates thumbnail with default values specified in the configuration
Html::img(Yii::$app->thumbnailer->get($imageUrl));

//Generates a 400x400 pixels thumbnail and 60% quality 
Html::img(Yii::$app->thumbnailer->get($imageUrl, 400, 400));

//Generates a 400x400 pixels thumbnail and 10% quality
Html::img(Yii::$app->thumbnailer->get($imageUrl, 400, 400, 10));

//Generates a 400x400 pixels thumbnail, 10% quality and not cropping the image
//but inserting it into a box with the specified dimensions.
Html::img(Yii::$app->thumbnailer->get($imageUrl, 400, 400, 10, ManipulatorInterface::THUMBNAIL_INSET));
```

Proudly made by [Daxslab](http://daxslab.com).