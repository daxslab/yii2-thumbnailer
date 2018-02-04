<?php
/**
 * Created by WebStorm.
 * User: glpz
 * Date: 7/08/17
 * Time: 22:48
 */

namespace daxslab\thumbnailer;


use Imagine\Image\ManipulatorInterface;
use Yii;
use yii\base\Component;
use yii\base\Exception;
use yii\helpers\FileHelper;
use yii\helpers\Url;
use yii\imagine\Image as Imagine;

class Thumbnailer extends Component
{

    /**
    * Where the thumbnails are stored
    */
    public $thumbnailsPath = '@webroot/assets/thumbnails';
    public $thumbnailsBaseUrl = null;

    public function get($url, $width, $height = null, $quality = 60)
    {
        if (Url::isRelative($url)) {
            $host = Yii::$app->request->hostInfo;
            $url = Yii::getAlias("{$host}/{$url}");
        }

        $height = $height ?: $width;
        $filename = basename($url);
        $thumbnailPath = Yii::getAlias("$this->thumbnailsPath/{$width}x{$height}/{$filename}");

        if (!file_exists($thumbnailPath)) {
            try{
                $imageData = @file_get_contents($url);
                if($imageData){
                    FileHelper::createDirectory(dirname($thumbnailPath));
                    file_put_contents($thumbnailPath, $imageData, true);
                    Imagine::thumbnail($thumbnailPath, $width, $height, ManipulatorInterface::THUMBNAIL_OUTBOUND)
                        ->save($thumbnailPath, ['quality' => 60]);
                }else{
                    return $url;
                }
            }catch (Exception $e){
                return $url;
            }
        }

        return str_replace(Yii::getAlias($this->thumbnailsPath), $this->thumbnailsBaseUrl, $thumbnailPath);
    }

}
