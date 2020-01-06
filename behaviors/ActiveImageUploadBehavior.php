<?php
namespace sky\yii\behaviors;

use yii\db\BaseActiveRecord;
use yii\imagine\Image;
use yii\helpers\FileHelper;
use yii\web\UploadedFile;
use Yii;

class ActiveImageUploadBehavior extends \sky\yii\behaviors\ActiveUploadBehavior
{
    public $cropOptions = false;
    
    public $resizeOptions = false;
    
    public $thumbnailOptions = false;
    
    public static function resizeType()
    {
        return [
            'sm' => [200, 200],
            'md' => [768, 768],
            'lg' => [1000, 1000],
        ];
    }
    
    public function upload($event)
    {
        $file = $this->owner->{$this->fileAttribute};
        if ($file && $this->cropOptions) {
            static::crop($file, $this->cropOptions);
        }
        if ($file && $this->resizeOptions) {
            static::resize($file, $this->resizeOptions);
        }
        if ($file && $this->thumbnailOptions) {
            static::thumbnail($file, $this->thumbnailOptions);
        }
        return parent::upload($event);
    }


    /**
     * 
     * @param UploadedFile $file
     * @return UploadedFile[]
     */
    public static function multiResize(UploadedFile $file)
    {
        $result = [];
        $unique = Yii::$app->security->generateRandomString(8);
        foreach (static::resizeType() as $type => $size) {
            $path = Yii::getAlias("@app/runtime/img/{$unique}/");
            if (!is_dir($path)) {
                FileHelper::createDirectory($path);
            }
            $fileName = "{$file->baseName}-{$type}.{$file->extension}";
            $resizePath = $path . $fileName; 
            $image = Image::resize($file->tempName, $size[0], $size[1])->save($resizePath);
            UploadedFile::getInstanceByName($resizePath);
            $result[$type] = new UploadedFile([
                'name' => $fileName,
                'tempName' => $resizePath,
                'type' => $file->type,
            ]);
            
        }
        return $result;
    }
    
    public static function resize(UploadedFile $file, $options)
    {
        $options = array_merge([
            'size' => [],
            'saveTemp' => $file->tempName,
        ], $options);
        $resizeImage = Image::resize($file->tempName, $options['size'][0], $options['size'][1]);
        $saveTemp = $options['saveTemp'] . '.' . $file->extension;
        if ($resizeImage->save($saveTemp)) {
            $file->tempName = $saveTemp;
        }
    }
    
    public static function crop(UploadedFile $file, $options)
    {
        $options = array_merge([
            'size' => [],
            'saveTemp' => $file->tempName,
            'start' => [0,0],
        ], $options);
        $cropImage = Image::crop($file->tempName, $options['size'][0], $options['size'][1], $options['start']);
        $saveTemp = $options['saveTemp'] . '.' . $file->extension;
        if ($cropImage->save($saveTemp)) {
            $file->tempName = $saveTemp;
        }
    }
    
    public static function thumbnail(UploadedFile $file, $options)
    {
        $options = array_merge([
            'size' => [],
            'saveTemp' => $file->tempName,
        ], $options);
        $thumbImage = Image::thumbnail($file->tempName, $options['size'][0], $options['size'][1]);
        $saveTemp = $options['saveTemp'] . '.' . $file->extension;
        if ($thumbImage->save($saveTemp)) {
            $file->tempName = $saveTemp;
        }
    }
}