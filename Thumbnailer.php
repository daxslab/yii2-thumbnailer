<?php

/**
 * @link https://github.com/daxslab/yii2-thumbnailer
 * @copyright Copyright (c) 2008 Daxslab (www.daxslab.com)
 * @author Gabriel Alejandro Lopez Lopez <glpz@daxslab.com>
 * @license http://opensource.org/licenses/mit-license.php The MIT License (MIT)
 * @package yii2-thumbnailer
 */

namespace daxslab\thumbnailer;

use Imagine\Image\ManipulatorInterface;
use Yii;
use yii\base\Component;
use yii\base\Exception;
use yii\base\InvalidParamException;
use yii\di\Instance;
use yii\helpers\FileHelper;
use yii\helpers\Url;
use yii\imagine\Image as Imagine;

/**
 * Image thumbnailer for Yii2.
 * Generates image thumbnails of any size.
 *
 * @author Gabriel Alejandro Lopez Lopez <glpz@daxslab.com>
 */
class Thumbnailer extends Component
{

    /**
     * @var int default width of the generated thumbnail.
     */
    public $defaultWidth = 300;

    /**
     * @var int|null default height of the generated thumbnail. If null uses the same than [[defaultWidth]]
     */
    public $defaultHeight = null;

    /**
     * @var int default quality of the generated thumbnail.
     */
    public $defaultQuality = 60;

    /**
     * @var string root path to store the generated thumbnails.
     */
    public $thumbnailsPath = '@webroot/assets/thumbnails';

    /**
     * @var string base URL to retrieve the thumbnails.
     * @see $thumbnailPath
     */
    public $thumbnailsBaseUrl = '@web/assets/thumbnails';

    /**
     * @var CacheInterface|array|string the cache object or the application component ID of the cache object.
     * The thumbnails url will be cached using this cache object.
     * Note, that to enable caching you have to set [[enableCaching]] to `true`, otherwise setting this property has no effect.
     *
     * After the Thumbnailer object is created, if you want to change this property, you should only assign
     * it with a cache object.
     *
     * Starting from version 2.0.2, this can also be a configuration array for creating the object.
     * @see cachingDuration
     * @see enableCaching
     */
    public $cache = 'cache';

    /**
     * @var int the time in seconds that the messages can remain valid in cache.
     * Use 0 to indicate that the cached data will never expire.
     * @see enableCaching
     */
    public $cachingDuration = 0;

    /**
     * @var bool whether to enable caching translated messages
     */
    public $enableCaching = false;

    /**
     * Initializes the Thumbnailer component.
     * Configured [[cache]] component would be initialized.
     * @throws InvalidConfigException if [[cache]] is invalid.
     */
    public function init()
    {
        parent::init();
        if ($this->enableCaching) {
            $this->cache = Instance::ensure($this->cache, 'yii\caching\CacheInterface');
        }
    }


    /**
     * Tries to load a thumbnail URL from [[cache]]. If not possible, calls the thumbnail generator.
     *
     * @param $url @see Thumbnailer::generateThumbnail
     * @param null $width @see Thumbnailer::generateThumbnail
     * @param null $height @see Thumbnailer::generateThumbnail
     * @param null $quality @see Thumbnailer::generateThumbnail
     * @param string $mode @see Thumbnailer::generateThumbnail
     * @return string the thumbnail URL
     */
    public function get(
        $url,
        $width = null,
        $height = null,
        $quality = null,
        $mode = ManipulatorInterface::THUMBNAIL_OUTBOUND
    ) {
        if (Url::isRelative($url)) {
            $host = Yii::$app->request->hostInfo;
            $url = Yii::getAlias("{$host}/{$url}");
        }

        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            throw new InvalidParamException(Yii::t('app', '$url expects a valid URL'));
        }

        $this->defaultHeight = $this->defaultHeight ?: $this->defaultWidth;

        $width = $width ?: $this->defaultWidth;
        $height = $height ?: $this->defaultHeight;
        $quality = $quality ?: $this->defaultQuality;

        if ($this->enableCaching) {
            $key = [$url, $width, $height, $quality];
            $thumbnailUrl = $this->cache->get($key);
            if ($thumbnailUrl == false) {
                $thumbnailUrl = $this->generateThumbnail($url, $width, $height, $quality, $mode);
                $this->cache->set($key, $thumbnailUrl, $this->cachingDuration);
            }

            return $thumbnailUrl;
        }

        return $this->generateThumbnail($url, $width, $height, $quality, $mode);
    }

    /**
     * Generates a thumbnail for the image specified by $url with size according to $with and $height
     *
     * @param string $url URL of the image to generate a thumbnail for..
     * @param int|null $width width of the resulting thumbnail. Defaults to $defaultWidth if NULL.
     * @param int|null $height height of the resulting thumbnail. Defaults to $defaultHeight if NULL.
     * @param int|null $quality quality of the resulting thumbnail. Defaults to $defaultQuality if NULL.
     * @param string|null $mode mode to create the thumbnail.
     * @return string URL of the generated thumbnail.
     */
    protected function generateThumbnail(
        $url,
        $width = null,
        $height = null,
        $quality = null,
        $mode = ManipulatorInterface::THUMBNAIL_OUTBOUND
    ) {
        $filename = basename($url);
        $thumbnailPath = Yii::getAlias("$this->thumbnailsPath/{$width}x{$height}/{$filename}");

        try {
            $imageData = @file_get_contents($url);
            if ($imageData) {
                FileHelper::createDirectory(dirname($thumbnailPath));
                file_put_contents($thumbnailPath, $imageData, true);
                Imagine::thumbnail($thumbnailPath, $width, $height, $mode)
                    ->save($thumbnailPath, ['quality' => $quality]);
            } else {
                return $url;
            }
        } catch (Exception $e) {
            return $url;
        }

        return Yii::getAlias(str_replace(Yii::getAlias($this->thumbnailsPath), $this->thumbnailsBaseUrl,
            $thumbnailPath));
    }

}
