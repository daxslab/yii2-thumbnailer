Thumbnailer
===========
Extension to generate thumbnails for images of any size

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

Configure the View component into the main configuration file of your application:

    'components' => [
        ...
        'thumbnailer' => [
            'class' => 'daxslab\thumbnailer\Thumbnailer',
            'thumbnailsBaseUrl' => '/assets/thumbnails',
        ],
        ...
    ]

Usage
-----

Once the extension is configured, simply use it in your views by:

    <?= Html::img(Yii::$app->thumbnailer->get($imageUrl, 400, 400)) ?>